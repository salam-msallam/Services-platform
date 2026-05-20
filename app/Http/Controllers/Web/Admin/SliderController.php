<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Http\Requests\Admin\UpdateSliderRequest;
use App\Models\Slider;
use App\Services\Slider\SliderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SliderController extends Controller
{
    public function __construct(private readonly SliderService $sliderService) {}

    public function index(): View
    {
        $sliders = $this->sliderService->getAllSliders();
        $currentSlider = $this->sliderService->getDailyRotatingSlider();

        return view('admin.sliders.index', compact('sliders', 'currentSlider'));
    }

    public function store(StoreSliderRequest $request): RedirectResponse
    {
        $this->sliderService->createSlider($request->validated());

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_created'));
    }

    public function update(UpdateSliderRequest $request, Slider $slider): RedirectResponse
    {
        $this->sliderService->updateSlider($slider, $request->validated());

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_updated'));
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        Gate::authorize('delete-sliders');

        $this->sliderService->deleteSlider($slider);

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_deleted'));
    }
}
