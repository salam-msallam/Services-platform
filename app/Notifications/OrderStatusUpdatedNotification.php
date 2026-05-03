<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\StatusEnum;
use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    public function __construct(
        private readonly Order $order,
        private readonly StatusEnum|string $status,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $orderId = (int) $this->order->getKey();
        $statusValue = $this->status instanceof StatusEnum ? $this->status->value : $this->status;

        return [
            'title' => (string) __("api.notification_order_{$statusValue}_title"),
            'message' => (string) __("api.notification_order_{$statusValue}_message"),
            'type' => 'order_status_updated',
            'target_id' => $orderId,
            'url' => "/orders/{$orderId}",
        ];
    }
}
