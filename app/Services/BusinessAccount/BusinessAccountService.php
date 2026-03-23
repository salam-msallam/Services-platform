<?php

declare(strict_types=1);

namespace App\Services\BusinessAccount;

use App\Enums\StatusEnum;
use App\Models\BusinessAccount;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BusinessAccountService
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, UploadedFile>  $documents
     */
    public function store(User $user, array $payload, array $images = [], array $documents = []): BusinessAccount
    {
        $businessAccount = DB::transaction(function () use ($user, $payload, $images, $documents): BusinessAccount {
            $businessAccount = $user->businessAccounts()->create([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'activities' => $payload['activities'],
                'license_number' => $payload['license_number'],
                'city_id' => $payload['city_id'],
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
        });

        return $businessAccount->load(['city', 'activityType']);
    }
}
