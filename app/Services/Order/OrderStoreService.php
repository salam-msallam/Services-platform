<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Enums\StatusEnum;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderStoreService
{
    /**
     * @param  array<string, mixed>  $data
     */
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
}
