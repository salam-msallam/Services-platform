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
    public function listPending(): Collection
    {
        return BusinessAccount::query()
            ->where('status', StatusEnum::Pending->value)
            ->with([
                'city',
                'activityType',
            ])
            ->orderByDesc('created_at')
            ->get();
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

