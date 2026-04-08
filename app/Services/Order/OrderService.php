<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const ERROR_ORDER_STATUS_UPDATE_NOT_ALLOWED = 'order_status_update_not_allowed';

    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data): Order {
            return Order::query()->create([
                'business_account_id' => $data['business_account_id'],
                'service_id' => $data['service_id'],
                'quantity' => $data['quantity'],
                'date_of_need' => $data['date_of_need'] ?? null,
                'time_of_need' => $data['time_of_need'] ?? null,
                'status' => StatusEnum::Pending,
            ]);
        });
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

        return $order->refresh()->load('businessAccount');
    }
}
