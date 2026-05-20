<?php

declare(strict_types=1);

namespace App\Services\BusinessAccount;

use App\Enums\StatusEnum;
use App\Exceptions\BusinessAccount\BusinessAccountActivityTypeAlreadyExistsException;
use App\Exceptions\BusinessAccount\BusinessAccountForbiddenOrNotFoundException;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BusinessAccountService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * @return Collection<int, BusinessAccount>
     */
    public function listForUser(User $user): Collection
    {
        return $user->businessAccounts()
            ->with(['city', 'activityType'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, UploadedFile>  $documents
     */
    public function store(User $user, array $payload, array $images = [], array $documents = []): BusinessAccount
    {
        $businessAccount = DB::transaction(function () use ($user, $payload, $images, $documents): BusinessAccount {
            $activityTypeId = (int) $payload['activity_type_id'];

            $alreadyExists = $user->businessAccounts()
                ->where('activity_type_id', $activityTypeId)
                ->where('status',StatusEnum::Accepted)
                ->exists();

            if ($alreadyExists) {
                throw new BusinessAccountActivityTypeAlreadyExistsException(__('api.business_account_activity_type_already_exists'));
            }

            try {
                $businessAccount = $user->businessAccounts()->create([
                    'name' => $payload['name'],
                    'description' => $payload['description'] ?? null,
                    'activities' => $payload['activities'],
                    'license_number' => $payload['license_number'],
                    'city_id' => $payload['city_id'],
                    'x' => $payload['x'],
                    'y' => $payload['y'],
                    'activity_type_id' => $payload['activity_type_id'],
                    'status' => StatusEnum::Pending,
                ]);

                foreach ($images as $image) {
                    $businessAccount->addMedia($image)->toMediaCollection('images');
                }

                foreach ($documents as $document) {
                    $businessAccount->addMedia($document)->toMediaCollection('documents');
                }

                return $businessAccount;
            } catch (QueryException) {
                throw new BusinessAccountActivityTypeAlreadyExistsException(__('api.business_account_activity_type_already_exists'));
            }
        });

        $this->notificationService->notifyPendingBusinessAccountReview($businessAccount->id);

        return $businessAccount->load(['city', 'activityType']);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, UploadedFile>  $documents
     */
    public function update(
        User $user,
        BusinessAccount $businessAccount,
        array $payload,
        array $images = [],
        array $documents = []
    ): BusinessAccount {
        $this->ensureOwnedByUser($user, $businessAccount);

        $updated = DB::transaction(function () use ($user, $businessAccount, $payload, $images, $documents): BusinessAccount {
            $activityTypeId = array_key_exists('activity_type_id', $payload)
                ? (int) $payload['activity_type_id']
                : (int) $businessAccount->activity_type_id;

            $getOrExisting = static function (string $key, mixed $existing) use ($payload): mixed {
                return array_key_exists($key, $payload) ? $payload[$key] : $existing;
            };

            $name = $getOrExisting('name', $businessAccount->name);
            $description = $getOrExisting('description', $businessAccount->description);
            $activities = $getOrExisting('activities', $businessAccount->activities);
            $licenseNumber = $getOrExisting('license_number', $businessAccount->license_number);
            $cityId = $getOrExisting('city_id', $businessAccount->city_id);
            $x = $getOrExisting('x', $businessAccount->x);
            $y = $getOrExisting('y', $businessAccount->y);

            $alreadyExists = $user->businessAccounts()
                ->where('activity_type_id', $activityTypeId)
                ->whereKeyNot($businessAccount->getKey())
                ->exists();

            if ($alreadyExists) {
                throw new BusinessAccountActivityTypeAlreadyExistsException(__('api.business_account_activity_type_already_exists'));
            }

            try {
                $businessAccount->update([
                    'name' => $name,
                    'description' => $description,
                    'activities' => $activities,
                    'license_number' => $licenseNumber,
                    'city_id' => $cityId,
                    'x' => $x,
                    'y' => $y,
                    'activity_type_id' => $activityTypeId,
                    'status' => StatusEnum::Pending,
                ]);

                foreach ($images as $image) {
                    $businessAccount->addMedia($image)->toMediaCollection('images');
                }

                foreach ($documents as $document) {
                    $businessAccount->addMedia($document)->toMediaCollection('documents');
                }
            } catch (QueryException) {
                throw new BusinessAccountActivityTypeAlreadyExistsException(__('api.business_account_activity_type_already_exists'));
            }

            return $businessAccount->fresh(['city', 'activityType']);
        });

        $this->notificationService->notifyPendingBusinessAccountReview($updated->id);

        return $updated;
    }

    public function delete(User $user, BusinessAccount $businessAccount): void
    {
        $this->ensureOwnedByUser($user, $businessAccount);

        DB::transaction(function () use ($businessAccount): void {
            BusinessAccount::destroy($businessAccount->getKey());
        });
    }

    public function restore(User $user, int $businessAccountId): BusinessAccount
    {
        $businessAccount = BusinessAccount::query()
            ->withTrashed()
            ->findOrFail($businessAccountId);

        $this->ensureOwnedByUser($user, $businessAccount);

        DB::transaction(function () use ($businessAccount): void {
            $businessAccount->restore();
        });

        return $businessAccount->fresh(['city', 'activityType']) ?? $businessAccount;
    }

    private function ensureOwnedByUser(User $user, BusinessAccount $businessAccount): void
    {
        if ($businessAccount->user_id !== $user->id) {
            throw new BusinessAccountForbiddenOrNotFoundException(__('api.business_account_forbidden_or_not_found'));
        }
    }
}
