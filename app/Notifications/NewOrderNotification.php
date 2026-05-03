<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    public function __construct(
        private readonly Order $order,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $orderId = (int) $this->order->getKey();

        return [
            'title' => (string) __('api.notification_new_order_title'),
            'message' => (string) __('api.notification_new_order_message'),
            'type' => 'new_order',
            'target_id' => $orderId,
            'url' => "/orders/{$orderId}",
        ];
    }
}
