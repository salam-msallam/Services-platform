<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Slider\SliderResource;
use App\Http\Responses\ApiResponse;
use App\Services\Slider\SliderService;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    public function __construct(private readonly SliderService $sliderService) {}

    public function showCurrent(): JsonResponse
    {
        $slider = $this->sliderService->getDailyRotatingSlider();

        return ApiResponse::success(
            $slider === null ? null : (new SliderResource($slider))->resolve(),
            __('api.current_slider_fetched')
        );
    }
}
