<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ServiceRequestNotification extends Notification
{
    public function __construct(
        private readonly int $serviceId,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $id = $this->serviceId;

        return [
            'title' => (string) __('admin.notification_pending_services_title'),
            'message' => (string) __('admin.notification_pending_services_body'),
            'target_id' => $id,
            'type' => 'service',
            'url' => "/admin/services/{$id}",
        ];
    }
}

