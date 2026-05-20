<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Slider\SliderService;
use Illuminate\Contracts\View\View;

class SliderController extends Controller
{
    public function __construct(private readonly SliderService $sliderService) {}

    public function showCurrent(): View
    {
        $slider = $this->sliderService->getDailyRotatingSlider();

        return view('current-slider', compact('slider'));
    }
}
