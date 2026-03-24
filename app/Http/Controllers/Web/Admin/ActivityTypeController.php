<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityTypeRequest;
use App\Http\Requests\Admin\UpdateActivityTypeRequest;
use App\Models\ActivityType;
use App\Services\Admin\ActivityTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivityTypeController extends Controller
{
    public function __construct(protected ActivityTypeService $activityTypeService) {}

    public function index(): View
    {
        $activityTypes = $this->activityTypeService->listActivityTypes();

        return view('admin.activity-types.index', compact('activityTypes'));
    }

    public function create(): View
    {
        return view('admin.activity-types.create');
    }

    public function store(StoreActivityTypeRequest $request): RedirectResponse
    {
        $this->activityTypeService->createActivityType($request->validated());

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', __('admin.activity_type_created'));
    }

    public function edit(ActivityType $activityType): View
    {
        return view('admin.activity-types.edit', compact('activityType'));
    }

    public function update(UpdateActivityTypeRequest $request, ActivityType $activityType): RedirectResponse
    {
        $this->activityTypeService->updateActivityType($activityType, $request->validated());

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', __('admin.activity_type_updated'));
    }

    public function destroy(ActivityType $activityType): RedirectResponse
    {
        $this->activityTypeService->deleteActivityType($activityType);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', __('admin.activity_type_deleted'));
    }
}
