<?php

declare(strict_types=1);

namespace App\Http\Resources\Service;

use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;
        $statusValue = $status instanceof StatusEnum ? $status->value : (string) $status;
        $statusLabel = $status instanceof StatusEnum ? $status->label() : null;

        return [
            'id' => $this->id,
            'business_account_id' => $this->business_account_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'city_id' => $this->city_id,
            'title' => $this->getTranslations('title'),
            'description' => $this->description !== null ? $this->getTranslations('description') : null,
            'quantity' => $this->quantity,
            'work_type' => $this->work_type,
            'price' => $this->price,
            'currency' => $this->currency,
            'property_type' => $this->property_type,
            'dynamic_values' => $this->dynamic_values,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'city' => $this->city !== null
                ? [
                    'id' => $this->city->id,
                    'name' => $this->city->getTranslations('name'),
                ]
                : null,
            'category' => $this->category !== null
                ? [
                    'id' => $this->category->id,
                    'name' => $this->category->getTranslations('name'),
                ]
                : null,
            'sub_category' => $this->subCategory !== null
                ? [
                    'id' => $this->subCategory->id,
                    'name' => $this->subCategory->getTranslations('name'),
                ]
                : null,
            'images' => $this->getMedia('images')->map(fn ($media): string => $media->getUrl())->values()->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
