<?php

declare(strict_types=1);

namespace App\Http\Resources\BusinessAccount;

use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;
        $statusValue = $status instanceof StatusEnum ? $status->value : (string) $status;
        $statusLabel = $status instanceof StatusEnum ? $status->label() : null;

        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->description !== null ? $this->getTranslations('description') : null,
            'activities' => $this->activities !== null ? $this->getTranslations('activities') : null,
            'license_number' => $this->license_number,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'city' => $this->city !== null
                ? [
                    'id' => $this->city->id,
                    'name' => $this->city->getTranslations('name'),
                    'x' => $this->city->x,
                    'y' => $this->city->y,
                ]
                : null,
            'activity_type' => $this->activityType !== null
                ? [
                    'id' => $this->activityType->id,
                    'name' => $this->activityType->getTranslations('name'),
                ]
                : null,
            'images' => $this->getMedia('images')->map(fn ($media): string => $media->getUrl())->values()->all(),
            'documents' => $this->getMedia('documents')->map(fn ($media): string => $media->getUrl())->values()->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
