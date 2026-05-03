<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\StatusEnum;
use App\Models\Service;
use Illuminate\Notifications\Notification;

class ServiceStatusNotification extends Notification
{
    public function __construct(
        private readonly Service $service,
        private readonly StatusEnum|string $status,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $serviceId = (int) $this->service->getKey();
        $statusValue = $this->status instanceof StatusEnum ? $this->status->value : $this->status;

        return [
            'title' => (string) __("api.notification_service_{$statusValue}_title"),
            'message' => (string) __("api.notification_service_{$statusValue}_message"),
            'type' => 'service_status_updated',
            'target_id' => $serviceId,
            'url' => "/services/{$serviceId}",
        ];
    }
}
