<?php

declare(strict_types=1);

namespace App\Services\Service;

use App\Enums\StatusEnum;
use App\Models\Service;
use App\Services\Notification\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ServiceUpdateService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * @param  array<int, UploadedFile>  $additionalImages
     */
    public function update(Service $service, array $data, ?UploadedFile $mainImage = null, array $additionalImages = []): Service
    {
        $updated = DB::transaction(function () use ($service, $data, $mainImage, $additionalImages): Service {
            $service->refresh();

            $dynamicValues = $data['dynamic_values'] ?? null;

            if (array_key_exists('dynamic_values', $data) && is_array($dynamicValues) && $dynamicValues === []) {
                $dynamicValues = null;
            }

            if (array_key_exists('dynamic_values', $data)) {
                $data['dynamic_values'] = $dynamicValues;
            }

            if (array_key_exists('title', $data) && is_array($data['title'])) {
                $incoming = array_filter(
                    $data['title'],
                    static fn (mixed $v): bool => $v !== null && $v !== ''
                );
                $data['title'] = array_replace($service->getTranslations('title'), $incoming);
            }

            if (array_key_exists('description', $data) && is_array($data['description'])) {
                $incoming = array_filter(
                    $data['description'],
                    static fn (mixed $v): bool => $v !== null && $v !== ''
                );
                $existing = $service->description !== null
                    ? $service->getTranslations('description')
                    : [];
                $data['description'] = $incoming === [] ? null : array_replace($existing, $incoming);
            }

            // Keep legacy pricing columns in sync while dual-currency fields are primary.
            if (array_key_exists('price_usd', $data)) {
                $data['price'] = $data['price_usd'];
                $data['currency'] = Service::CURRENCY_USD;
            }

            // Any update returns service to pending for moderation.
            $data['status'] = StatusEnum::Pending;

            $service->fill($data);
            $service->save();

            $newMainMedia = null;

            if ($mainImage instanceof UploadedFile) {
                $newMainMedia = $service->addMedia($mainImage)->toMediaCollection('images');
            }

            foreach ($additionalImages as $image) {
                $service->addMedia($image)->toMediaCollection('images');
            }

            // Promote provided main image to first position in the collection.
            if ($newMainMedia instanceof Media) {
                $ids = $service->getMedia('images')
                    ->sortBy('order_column')
                    ->pluck('id')
                    ->values()
                    ->all();

                $ids = array_values(array_unique(array_merge([$newMainMedia->id], array_diff($ids, [$newMainMedia->id]))));

                if ($ids !== []) {
                    Media::setNewOrder($ids);
                }
            }

            return $service->fresh()->load(['businessAccount', 'category', 'subCategory', 'city']);
        });

        $this->notificationService->notifyPendingServiceReview();

        return $updated;
    }
}
