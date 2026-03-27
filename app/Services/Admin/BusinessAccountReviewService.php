<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Enums\StatusEnum;
use App\Models\BusinessAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BusinessAccountReviewService
{
    /**
     * @return Collection<int, BusinessAccount>
     */
    public function listAll(?string $status = null): Collection
    {
        $query = BusinessAccount::query()
            ->with([
                'city',
                'activityType',
                'user',
            ])
            ->orderByDesc('created_at');

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

            return $businessAccount->fresh()->load(['city', 'activityType']);
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

            return $businessAccount->fresh()->load(['city', 'activityType']);
        });
    }
}

