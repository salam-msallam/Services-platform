<?php

declare(strict_types=1);

namespace App\Services\Service;

use App\Enums\StatusEnum;
use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ServiceStoreService
{
    public function store(array $data, UploadedFile $mainImage, array $additionalImages = []): Service
    {
        return DB::transaction(function () use ($data, $mainImage, $additionalImages): Service {
            $dynamicValues = $data['dynamic_values'] ?? null;

            if (is_array($dynamicValues) && $dynamicValues === []) {
                $dynamicValues = null;
            }

            $service = Service::query()->create([
                'business_account_id' => $data['business_account_id'],
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'city_id' => $data['city_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'quantity' => $data['quantity'],
                'work_type' => $data['work_type'],
                'price' => $data['price'],
                'currency' => $data['currency'],
                'property_type' => $data['property_type'],
                'dynamic_values' => $dynamicValues,
                'status' => StatusEnum::Pending,
            ]);

            $service->addMedia($mainImage)->toMediaCollection('images');

            foreach ($additionalImages as $image) {
                $service->addMedia($image)->toMediaCollection('images');
            }

            return $service->load(['businessAccount', 'category', 'subCategory', 'city']);
        });
    }
}
