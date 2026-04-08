<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Service;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\Service\ServiceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Service;
use App\Models\User;
use App\Services\Service\ServiceStoreService;
use App\Services\Service\ServiceUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController
{
    public function __construct(
        protected ServiceStoreService $serviceStoreService,
        protected ServiceUpdateService $serviceUpdateService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $services = Service::query()
            ->with(['businessAccount', 'category', 'subCategory', 'city', 'media'])
            ->withAvg('evaluations', 'rating')
            ->withCount('evaluations')
            ->whereHas('businessAccount', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        return ApiResponse::success(
            ServiceResource::collection($services)->resolve(),
            __('api.services_fetched'),
        );
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) $user->id) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        $service->load(['businessAccount', 'category', 'subCategory', 'city', 'media']);
        $service->loadAvg('evaluations', 'rating');
        $service->loadCount('evaluations');

        return ApiResponse::success(
            ServiceResource::make($service)->toArray($request),
            __('api.service_fetched'),
        );
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $validated = $request->validated();
        $mainImage = $request->file('main_image');
        $images = $request->file('images', []);

        unset($validated['main_image'], $validated['images']);

        $service = $this->serviceStoreService->store(
            $validated,
            $mainImage,
            is_array($images) ? $images : [],
        );
        $service->loadAvg('evaluations', 'rating');
        $service->loadCount('evaluations');

        return ApiResponse::success(
            ServiceResource::make($service)->toArray($request),
            __('api.service_created'),
        );
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $service->loadMissing('businessAccount');

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) $user->id) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        $validated = $request->validated();
        $mainImage = $request->file('main_image');
        $images = $request->file('images', []);

        unset($validated['main_image'], $validated['images']);

        $images = is_array($images) ? $images : [];

        if ($validated === [] && $mainImage === null && $images === []) {
            return ApiResponse::error(__('api.service_update_no_changes'), [], 422);
        }

        $updated = $this->serviceUpdateService->update(
            $service,
            $validated,
            $mainImage,
            $images,
        );
        $updated->loadAvg('evaluations', 'rating');
        $updated->loadCount('evaluations');

        return ApiResponse::success(
            ServiceResource::make($updated)->toArray($request),
            __('api.service_updated'),
        );
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $service->loadMissing('businessAccount');

        if (! $service->businessAccount || (int) $service->businessAccount->user_id !== (int) $user->id) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        $service->delete();

        return ApiResponse::success([], __('api.service_deleted'));
    }
}
