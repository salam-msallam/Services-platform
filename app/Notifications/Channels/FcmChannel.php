<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Services\Notification\FirebaseNotificationService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function __construct(
        private readonly FirebaseNotificationService $firebaseNotificationService,
    ) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toDatabase')) {
            return;
        }

        $payload = $notification->toDatabase($notifiable);

        $tokens = [];
        if (method_exists($notifiable, 'routeNotificationForFcm')) {
            $tokens = $notifiable->routeNotificationForFcm($notification);
        }

        if (! is_array($tokens) || $tokens === []) {
            return;
        }

        $this->firebaseNotificationService->sendToTokens(
            title: (string) ($payload['title'] ?? ''),
            body: (string) ($payload['message'] ?? ''),
            tokens: $tokens,
            data: [
                'target_id' => $payload['target_id'] ?? null,
                'type' => $payload['type'] ?? null,
                'url' => $payload['url'] ?? null,
            ],
        );
    }
}

