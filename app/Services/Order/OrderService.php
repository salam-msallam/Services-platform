<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use DomainException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const ERROR_ORDER_STATUS_UPDATE_NOT_ALLOWED = 'order_status_update_not_allowed';

    private const ERROR_ORDER_UPDATE_NOT_ALLOWED = 'order_update_not_allowed';

    private const ERROR_ORDER_DELETE_NOT_ALLOWED = 'order_delete_not_allowed';

    public function store(array $data): Order
    {
        $order = DB::transaction(function () use ($data): Order {
            return Order::query()->create([
                'business_account_id' => $data['business_account_id'],
                'service_id' => $data['service_id'],
                'quantity' => $data['quantity'],
                'date_of_need' => $data['date_of_need'] ?? null,
                'time_of_need' => $data['time_of_need'] ?? null,
                'status' => StatusEnum::Pending,
            ]);
        });

        $order->loadMissing('service.businessAccount.user');
        $businessOwner = $order->service?->businessAccount?->user;

        if ($businessOwner instanceof User) {
            $businessOwner->notify(new NewOrderNotification($order));
        }

        return $order;
    }

    public function indexReceived(User $user, array $validated): array
    {
        $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 15;
        $perPage = max(1, min(50, $perPage));

        $query = Order::query()
            ->whereHas('service', function ($q) use ($user): void {
                $q->whereHas('businessAccount', function ($q2) use ($user): void {
                    $q2->where('user_id', $user->id);
                });
            })
            ->orderByDesc('created_at');

        if (isset($validated['status'])) {
            $status = $validated['status'];
            $statusValue = $status instanceof StatusEnum ? $status->value : (string) $status;
            $query->where('status', $statusValue);
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return [
            'items' => $paginator->getCollection(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    public function indexMyOrders(User $user, array $validated): array
    {
        $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 15;
        $perPage = max(1, min(50, $perPage));

        $query = Order::query()
            ->whereHas('businessAccount', function ($q) use ($user): void {
                $q->where('user_id', $user->id);
            })
            ->with('businessAccount')
            ->orderByDesc('created_at');

        if (isset($validated['status'])) {
            $status = $validated['status'];
            $statusValue = $status instanceof StatusEnum ? $status->value : (string) $status;
            $query->where('status', $statusValue);
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return [
            'items' => $paginator->getCollection(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    public function accept(Order $order, User $user): ?Order
    {
        return $this->updateStatus($order, $user, StatusEnum::Accepted);
    }

    public function reject(Order $order, User $user): ?Order
    {
        return $this->updateStatus($order, $user, StatusEnum::Rejected);
    }

    public function updateMyOrder(Order $order, User $user, array $data): ?Order
    {
        $order->loadMissing('businessAccount');

        $ownerId = $order->businessAccount?->user_id;
        if ((int) $ownerId !== (int) $user->id) {
            return null;
        }

        if ($order->status !== StatusEnum::Pending) {
            throw new DomainException(self::ERROR_ORDER_UPDATE_NOT_ALLOWED);
        }

        $payload = [];

        if (array_key_exists('business_account_id', $data)) {
            $payload['business_account_id'] = $data['business_account_id'];
        }

        if (array_key_exists('quantity', $data)) {
            $payload['quantity'] = $data['quantity'];
        }

        if (array_key_exists('date_of_need', $data)) {
            $payload['date_of_need'] = $data['date_of_need'];
        }

        if (array_key_exists('time_of_need', $data)) {
            $payload['time_of_need'] = $data['time_of_need'];
        }

        if ($payload !== []) {
            $order->update($payload);
        }

        return $order->refresh()->load('businessAccount');
    }

    public function delete(Order $order, User $user): bool
    {
        $order->loadMissing('businessAccount');

        $ownerId = $order->businessAccount?->user_id;
        if ((int) $ownerId !== (int) $user->id) {
            return false;
        }

        if ($order->status == StatusEnum::Accepted) {
            throw new DomainException(self::ERROR_ORDER_DELETE_NOT_ALLOWED);
        }

        return (bool) $order->forceDelete();
    }

    private function updateStatus(Order $order, User $user, StatusEnum $status): ?Order
    {
        $order->loadMissing('service.businessAccount');

        $ownerId = $order->service?->businessAccount?->user_id;
        if ((int) $ownerId !== (int) $user->id) {
            return null;
        }

        if ($order->status !== StatusEnum::Pending) {
            throw new DomainException(self::ERROR_ORDER_STATUS_UPDATE_NOT_ALLOWED);
        }

        $order->update(['status' => $status]);

        $updatedOrder = $order->refresh()->load('businessAccount.user');
        $orderOwner = $updatedOrder->businessAccount?->user;

        if ($orderOwner instanceof User) {
            $orderOwner->notify(new OrderStatusUpdatedNotification($updatedOrder, $status));
        }

        return $updatedOrder;
    }
}
