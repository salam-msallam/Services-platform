<?php

declare(strict_types=1);

namespace App\Http\Resources\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTranslations('title'),
            'image_url' => $this->getFirstMediaUrl('scroll_bar_image') ?: null,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
