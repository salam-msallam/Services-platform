<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Http\Requests\Order\IndexReceivedOrdersRequest;
use App\Http\Requests\Order\MyOrdersRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateMyOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController
{
    public function __construct(protected OrderService $orderService) {}

    public function indexReceived(IndexReceivedOrdersRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $result = $this->orderService->indexReceived($user, $request->validated());

        $payload = [
            'items' => OrderResource::collection($result['items'])->resolve(),
            'pagination' => $result['pagination'],
        ];

        return ApiResponse::success($payload, __('api.orders_received_fetched'));
    }

    public function indexMyOrders(MyOrdersRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $result = $this->orderService->indexMyOrders($user, $request->validated());

        $payload = [
            'items' => OrderResource::collection($result['items'])->resolve(),
            'pagination' => $result['pagination'],
        ];

        return ApiResponse::success($payload, __('api.my_orders_fetched'));
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $order = $this->orderService->store($request->validated());

        return ApiResponse::success(
            OrderResource::make($order)->toArray($request),
            __('api.order_created'),
        );
    }

    public function accept(Order $order, Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        try {
            $updatedOrder = $this->orderService->accept($order, $user);
        } catch (DomainException $exception) {
            return ApiResponse::error(__('api.order_status_update_not_allowed'), [], 422);
        }

        if (! $updatedOrder instanceof Order) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        return ApiResponse::success(
            OrderResource::make($updatedOrder)->toArray($request),
            __('api.order_accepted'),
        );
    }

    public function reject(Order $order, \Illuminate\Http\Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        try {
            $updatedOrder = $this->orderService->reject($order, $user);
        } catch (DomainException $exception) {
            return ApiResponse::error(__('api.order_status_update_not_allowed'), [], 422);
        }

        if (! $updatedOrder instanceof Order) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        return ApiResponse::success(
            OrderResource::make($updatedOrder)->toArray($request),
            __('api.order_rejected'),
        );
    }

    public function updateMyOrder(UpdateMyOrderRequest $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        try {
            $updatedOrder = $this->orderService->updateMyOrder($order, $user, $request->validated());
        } catch (DomainException $exception) {
            return ApiResponse::error(__('api.order_update_not_allowed'), [], 422);
        }

        if (! $updatedOrder instanceof Order) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        return ApiResponse::success(
            OrderResource::make($updatedOrder)->toArray($request),
            __('api.order_updated'),
        );
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        try {
            $deleted = $this->orderService->delete($order, $user);
        } catch (DomainException $exception) {
            return ApiResponse::error(__('api.order_delete_not_allowed'), [], 403);
        }

        if (! $deleted) {
            return ApiResponse::error(__('auth.unauthorized'), [], 403);
        }

        return ApiResponse::success([], __('api.order_deleted'));
    }
}
