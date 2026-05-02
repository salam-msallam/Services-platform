<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BusinessAccountRequestNotification extends Notification
{
    public function __construct(
        private readonly int $businessAccountId,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $id = $this->businessAccountId;

        return [
            'title' => (string) __('admin.notification_pending_business_accounts_title'),
            'message' => (string) __('admin.notification_pending_business_accounts_body'),
            'target_id' => $id,
            'type' => 'business_account',
            'url' => "/admin/business-accounts/{$id}",
        ];
    }
}

