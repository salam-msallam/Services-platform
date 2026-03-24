<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\City;
use App\Services\Admin\CityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CityController extends Controller
{
    public function __construct(protected CityService $cityService) {}

    public function index(): View
    {
        $cities = $this->cityService->listCities();

        return view('admin.cities.index', compact('cities'));
    }

    public function create(): View
    {
        return view('admin.cities.create');
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        $this->cityService->createCity($request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_created'));
    }

    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $this->cityService->updateCity($city, $request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_updated'));
    }

    public function destroy(City $city): RedirectResponse
    {
        $this->cityService->deleteCity($city);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_deleted'));
    }
}
