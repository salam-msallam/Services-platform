<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\BusinessAccountRequestNotification;
use App\Notifications\NewReportNotification;
use App\Notifications\ServiceRequestNotification;
use App\Models\AdminDeviceToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Persist admin browser device tokens.
     */
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

    public function notifyPendingBusinessAccountReview(int $businessAccountId): void
    {
        $notification = new BusinessAccountRequestNotification($businessAccountId);

        $this->notifyAdminsByRoles(
            roles: ['super-admin', 'business-auditor'],
            notification: $notification,
        );
    }

    public function notifyPendingServiceReview(int $serviceId): void
    {
        $notification = new ServiceRequestNotification($serviceId);

        $this->notifyAdminsByRoles(
            roles: ['super-admin', 'service-moderator'],
            notification: $notification,
        );
    }

    public function notifyNewReport(int $reportId): void
    {
        $notification = new NewReportNotification($reportId);

        // Report resolution is permission-gated in the admin UI.
        $admins = $this->eligibleAdminsByPermission('resolve reports');

        foreach ($admins as $adminUser) {
            $adminUser->notify($notification);
        }
    }

    /**
     * @param  array<int,string>  $roles
     */
    private function notifyAdminsByRoles(array $roles, Notification $notification): void
    {
        $admins = $this->eligibleAdminsByRoles($roles);

        foreach ($admins as $adminUser) {
            $adminUser->notify($notification);
        }
    }

    /**
     * @param  array<int,string>  $roles
     * @return Collection<int,User>
     */
    private function eligibleAdminsByRoles(array $roles): Collection
    {
        return User::query()
            ->where('type', 'admin')
            ->whereHas('admin')
            ->role($roles, 'web')
            ->get();
    }

    private function eligibleAdminsByPermission(string $permission): Collection
    {
        return User::query()
            ->where('type', 'admin')
            ->whereHas('admin')
            ->permission($permission, 'web')
            ->get();
    }
}
