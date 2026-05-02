<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification
{
    public function __construct(
        private readonly int $reportId,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $id = $this->reportId;

        return [
            'title' => 'New report submitted',
            'message' => 'A new report requires review.',
            'target_id' => $id,
            'type' => 'report',
            'url' => "/admin/reports/{$id}",
        ];
    }
}

