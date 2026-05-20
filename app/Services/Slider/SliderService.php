<?php

declare(strict_types=1);

namespace App\Services\Slider;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SliderService
{
    public function createSlider(array $data): Slider
    {
        return DB::transaction(function () use ($data): Slider {
            $slider = Slider::query()->create([
                'title' => $data['title'],
                'status' => true,
            ]);

            $image = $data['image'] ?? null;

            if ($image instanceof UploadedFile) {
                $slider->addMedia($image)->toMediaCollection('scroll_bar_image');
            }

            return $slider->load('media');
        });
    }

    public function getAllSliders(): Collection
    {
        return Slider::query()
            ->with('media')
            ->latest()
            ->get();
    }

    public function updateSlider(Slider $slider, array $data): Slider
    {
        return DB::transaction(function () use ($slider, $data): Slider {
            $slider->update([
                'title' => $data['title'],
                'status' => (bool) $data['status'],
            ]);

            $image = $data['image'] ?? null;

            if ($image instanceof UploadedFile) {
                $slider->addMedia($image)->toMediaCollection('scroll_bar_image');
            }

            return $slider->fresh()->load('media');
        });
    }

    public function deleteSlider(Slider $slider): void
    {
        $slider->delete();
    }

    public function getDailyRotatingSlider(): ?Slider
    {
        $total = Slider::query()
            ->where('status', true)
            ->count();

        if ($total === 0) {
            return null;
        }

        $index = (int) (floor(now()->timestamp / 86400) % $total);

        return Slider::query()
            ->where('status', true)
            ->with('media')
            ->orderBy('id')
            ->skip($index)
            ->first();
    }
}
