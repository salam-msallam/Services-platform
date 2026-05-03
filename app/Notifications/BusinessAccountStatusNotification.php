<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\StatusEnum;
use App\Models\BusinessAccount;
use Illuminate\Notifications\Notification;

class BusinessAccountStatusNotification extends Notification
{
    public function __construct(
        private readonly BusinessAccount $businessAccount,
        private readonly StatusEnum|string $status,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        $accountId = (int) $this->businessAccount->getKey();
        $statusValue = $this->status instanceof StatusEnum ? $this->status->value : $this->status;

        return [
            'title' => (string) __("api.notification_business_account_{$statusValue}_title"),
            'message' => (string) __("api.notification_business_account_{$statusValue}_message"),
            'type' => 'business_account_status_updated',
            'target_id' => $accountId,
            'url' => "/business-accounts/{$accountId}",
        ];
    }
}
