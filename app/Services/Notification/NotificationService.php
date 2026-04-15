<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Admin;
use App\Models\AdminDeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    public function updateDeviceToken(Admin $admin, string $deviceToken, ?string $platform = null): AdminDeviceToken
    {
        $token = AdminDeviceToken::query()->updateOrCreate(
            ['device_token' => $deviceToken],
            [
                'admin_id' => $admin->id,
                'platform' => $platform,
                'last_used_at' => now(),
            ],
        );

        return $token;
    }

    public function notifyPendingBusinessAccountReview(): void
    {
        $this->sendToReviewerRoles(
            roles: ['super-admin', 'business-auditor'],
            titleKey: 'admin.notification_pending_business_accounts_title',
            bodyKey: 'admin.notification_pending_business_accounts_body',
            data: ['event' => 'pending_business_accounts'],
        );
    }

    public function notifyPendingServiceReview(): void
    {
        $this->sendToReviewerRoles(
            roles: ['super-admin', 'service-moderator'],
            titleKey: 'admin.notification_pending_services_title',
            bodyKey: 'admin.notification_pending_services_body',
            data: ['event' => 'pending_services'],
        );
    }

    private function sendToReviewerRoles(array $roles, string $titleKey, string $bodyKey, array $data): void
    {
        $tokens = $this->deviceTokensForRoles($roles);

        if ($tokens === []) {
            return;
        }

        $title = __($titleKey);
        $body = __($bodyKey);

        try {
            $this->firebaseNotificationService()->sendToTokens($title, $body, $tokens, $data);
        } catch (Throwable $e) {
            Log::error('Failed to send admin FCM notification', [
                'exception' => $e->getMessage(),
                'title_key' => $titleKey,
            ]);
        }
    }
    private function deviceTokensForRoles(array $roles): array
    {
        $userIds = User::query()
            ->where('type', 'admin')
            ->whereHas('admin')
            ->role($roles, 'web')
            ->pluck('id');

        if ($userIds->isEmpty()) {
            return [];
        }

        $adminIds = Admin::query()
            ->whereIn('user_id', $userIds)
            ->pluck('id');

        if ($adminIds->isEmpty()) {
            return [];
        }

        return AdminDeviceToken::query()
            ->whereIn('admin_id', $adminIds)
            ->pluck('device_token')
            ->unique()
            ->filter()
            ->values()
            ->all();
    }

    private function firebaseNotificationService(): FirebaseNotificationService
    {
        return resolve(FirebaseNotificationService::class);
    }
}
