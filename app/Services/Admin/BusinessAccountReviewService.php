<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Enums\StatusEnum;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Notifications\BusinessAccountStatusNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BusinessAccountReviewService
{
    /**
     * @return Collection<int, BusinessAccount>
     */
    public function listAll(?string $status = null, string $tab = 'active'): Collection
    {
        $query = BusinessAccount::query()
            ->with([
                'city',
                'activityType',
                'user' => fn ($userQuery) => $userQuery->withTrashed(),
            ])
            ->orderByDesc('created_at');

        if ($tab === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at')
                ->whereHas('user');
        }

        if (in_array($status, [
            StatusEnum::Pending->value,
            StatusEnum::Accepted->value,
            StatusEnum::Rejected->value,
        ], true)) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function findForReview(BusinessAccount $businessAccount): BusinessAccount
    {
        return $businessAccount->load([
            'media',
            'city',
            'activityType',
            'user',
        ]);
    }

    public function accept(BusinessAccount $businessAccount): BusinessAccount
    {
        return DB::transaction(function () use ($businessAccount): BusinessAccount {
            $businessAccount->refresh();

            if ($businessAccount->status !== StatusEnum::Pending) {
                return $businessAccount;
            }

            $businessAccount->update([
                'status' => StatusEnum::Accepted,
            ]);

            $updatedBusinessAccount = $businessAccount->fresh()->load(['city', 'activityType', 'user']);
            $owner = $updatedBusinessAccount->user;

            if ($owner instanceof User) {
                $owner->notify(new BusinessAccountStatusNotification($updatedBusinessAccount, StatusEnum::Accepted));
            }

            return $updatedBusinessAccount;
        });
    }

    public function reject(BusinessAccount $businessAccount): BusinessAccount
    {
        return DB::transaction(function () use ($businessAccount): BusinessAccount {
            $businessAccount->refresh();

            if ($businessAccount->status !== StatusEnum::Pending) {
                return $businessAccount;
            }

            $businessAccount->update([
                'status' => StatusEnum::Rejected,
            ]);

            $updatedBusinessAccount = $businessAccount->fresh()->load(['city', 'activityType', 'user']);
            $owner = $updatedBusinessAccount->user;

            if ($owner instanceof User) {
                $owner->notify(new BusinessAccountStatusNotification($updatedBusinessAccount, StatusEnum::Rejected));
            }

            return $updatedBusinessAccount;
        });
    }
}
