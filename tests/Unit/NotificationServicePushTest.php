<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\AdminDeviceToken;
use App\Models\User;
use App\Services\Notification\FirebaseNotificationService;
use App\Services\Notification\NotificationService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServicePushTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_notify_pending_business_accounts_sends_to_business_auditor_tokens(): void
    {
        $businessAccountId = 123;

        $mock = $this->createMock(FirebaseNotificationService::class);
        $mock->expects($this->once())
            ->method('sendToTokens')
            ->with(
                $this->isType('string'),
                $this->isType('string'),
                $this->equalTo(['ba-token']),
                $this->equalTo([
                    'target_id' => $businessAccountId,
                    'type' => 'business_account',
                    'url' => "/admin/business-accounts/{$businessAccountId}",
                ]),
            );
        $this->app->instance(FirebaseNotificationService::class, $mock);

        $user = User::factory()->create(['type' => 'admin']);
        $admin = Admin::query()->create([
            'user_id' => $user->id,
            'email' => 'auditor@example.com',
            'main_admin' => false,
        ]);
        $user->assignRole('business-auditor');

        AdminDeviceToken::query()->create([
            'admin_id' => $admin->id,
            'device_token' => 'ba-token',
            'platform' => 'web',
        ]);

        $this->app->make(NotificationService::class)->notifyPendingBusinessAccountReview($businessAccountId);
    }

    public function test_notify_pending_services_sends_to_service_moderator_tokens(): void
    {
        $serviceId = 456;

        $mock = $this->createMock(FirebaseNotificationService::class);
        $mock->expects($this->once())
            ->method('sendToTokens')
            ->with(
                $this->isType('string'),
                $this->isType('string'),
                $this->equalTo(['svc-token']),
                $this->equalTo([
                    'target_id' => $serviceId,
                    'type' => 'service',
                    'url' => "/admin/services/{$serviceId}",
                ]),
            );
        $this->app->instance(FirebaseNotificationService::class, $mock);

        $user = User::factory()->create(['type' => 'admin']);
        $admin = Admin::query()->create([
            'user_id' => $user->id,
            'email' => 'mod@example.com',
            'main_admin' => false,
        ]);
        $user->assignRole('service-moderator');

        AdminDeviceToken::query()->create([
            'admin_id' => $admin->id,
            'device_token' => 'svc-token',
            'platform' => 'web',
        ]);

        $this->app->make(NotificationService::class)->notifyPendingServiceReview($serviceId);
    }
}
