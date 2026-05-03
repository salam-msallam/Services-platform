<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Enums\StatusEnum;
use App\Models\Service;
use App\Models\User;
use App\Notifications\ServiceStatusNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ServiceReviewService
{
    /**
     * @return Collection<int, Service>
     */
    public function listAll(?string $status = null, string $tab = 'active'): Collection
    {
        $query = Service::query()
            ->with([
                'businessAccount' => fn ($q) => $q->withTrashed()->with(['user' => fn ($uq) => $uq->withTrashed()]),
                'category',
                'subCategory',
                'city',
                'media',
            ])
            ->orderByDesc('created_at');

        if ($tab === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
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

    public function findForReview(Service $service): Service
    {
        return $service->load([
            'media',
            'businessAccount' => fn ($q) => $q->withTrashed()->with(['user' => fn ($uq) => $uq->withTrashed()]),
            'category',
            'subCategory',
            'city',
        ]);
    }

    public function accept(Service $service): Service
    {
        return DB::transaction(function () use ($service): Service {
            $service->refresh();

            if ($service->status !== StatusEnum::Pending) {
                return $service;
            }

            $service->update([
                'status' => StatusEnum::Accepted,
            ]);

            $updatedService = $service->fresh()->load(['businessAccount.user', 'category', 'subCategory', 'city']);
            $owner = $updatedService->businessAccount?->user;

            if ($owner instanceof User) {
                $owner->notify(new ServiceStatusNotification($updatedService, StatusEnum::Accepted));
            }

            return $updatedService;
        });
    }

    public function reject(Service $service): Service
    {
        return DB::transaction(function () use ($service): Service {
            $service->refresh();

            if ($service->status !== StatusEnum::Pending) {
                return $service;
            }

            $service->update([
                'status' => StatusEnum::Rejected,
            ]);

            $updatedService = $service->fresh()->load(['businessAccount.user', 'category', 'subCategory', 'city']);
            $owner = $updatedService->businessAccount?->user;

            if ($owner instanceof User) {
                $owner->notify(new ServiceStatusNotification($updatedService, StatusEnum::Rejected));
            }

            return $updatedService;
        });
    }
}

