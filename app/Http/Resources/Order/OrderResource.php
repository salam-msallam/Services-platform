<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Enums\StatusEnum;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Resources\Service\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status;
        $statusValue = $status instanceof StatusEnum ? $status->value : (string) $status;
        $statusLabel = $status instanceof StatusEnum ? $status->label() : null;

        return [
            'id' => $this->id,
            'business_account_id' => $this->business_account_id,
            'service_id' => $this->service_id,
            'quantity' => $this->quantity,
            'date_of_need' => $this->date_of_need?->format('Y-m-d'),
            'time_of_need' => $this->time_of_need,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'requester' => $this->when(
                $this->relationLoaded('businessAccount') && $this->businessAccount !== null,
                fn () => BusinessAccountResource::make($this->businessAccount),
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
