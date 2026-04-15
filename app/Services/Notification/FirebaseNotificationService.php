<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\AdminDeviceToken;
use Illuminate\Support\Facades\Log;
use JsonException;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RegistrationTokens;
use Throwable;

class FirebaseNotificationService
{
    public function __construct(protected Messaging $messaging) {}

    public function sendToTokens(string $title, string $body, array $tokens, array $data = []): void
    {
        $tokens = array_values(array_unique(array_filter($tokens, static fn (string $t): bool => $t !== '')));

        if ($tokens === []) {
            return;
        }

        $stringData = $this->stringifyData(array_merge($data, [
            'title' => $title,
            'body' => $body,
        ]));

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($stringData);

        try {
            $report = $this->messaging->sendMulticast($message, RegistrationTokens::fromValue($tokens));
        } catch (MessagingException|Throwable $e) {
            Log::error('FCM multicast send failed', [
                'exception' => $e->getMessage(),
            ]);

            return;
        }

        $this->pruneInvalidTokens($report->invalidTokens(), $report->unknownTokens());
    }

    private function stringifyData(array $data): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_scalar($value)) {
                $out[(string) $key] = (string) $value;

                continue;
            }

            try {
                $out[(string) $key] = json_encode($value, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }
        }

        return $out;
    }

    private function pruneInvalidTokens(array $invalid, array $unknown): void
    {
        $toRemove = array_values(array_unique(array_merge($invalid, $unknown)));

        if ($toRemove === []) {
            return;
        }

        AdminDeviceToken::query()->whereIn('device_token', $toRemove)->delete();
    }
}
