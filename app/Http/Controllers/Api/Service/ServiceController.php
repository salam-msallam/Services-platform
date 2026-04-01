<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Service;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Resources\Service\ServiceResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Service\ServiceStoreService;
use Illuminate\Http\JsonResponse;

class ServiceController
{
    public function __construct(protected ServiceStoreService $serviceStoreService) {}

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

        return ApiResponse::success(
            ServiceResource::make($service)->toArray($request),
            __('api.service_created'),
        );
    }
}
