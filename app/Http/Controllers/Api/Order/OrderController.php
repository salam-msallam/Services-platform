<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Order\OrderStoreService;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function __construct(protected OrderStoreService $orderStoreService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $order = $this->orderStoreService->store($request->validated());

        return ApiResponse::success(
            OrderResource::make($order)->toArray($request),
            __('api.order_created'),
        );
    }
}
