<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationDeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_register_fcm_device_token(): void
    {
        $user = User::factory()->create([
            'type' => 'admin',
        ]);

        Admin::query()->create([
            'user_id' => $user->id,
            'email' => 'reviewer@example.com',
            'main_admin' => false,
        ]);

        $user->assignRole('super-admin');

        $this->actingAs($user, 'web');

        $response = $this->postJson(route('admin.notifications.device-token'), [
            'device_token' => 'test-fcm-registration-token',
            'platform' => 'web',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('admin_device_tokens', [
            'device_token' => 'test-fcm-registration-token',
            'platform' => 'web',
        ]);
    }

    public function test_same_token_moves_to_current_admin(): void
    {
        $userA = User::factory()->create(['type' => 'admin']);
        $adminA = Admin::query()->create([
            'user_id' => $userA->id,
            'email' => 'a@example.com',
            'main_admin' => false,
        ]);
        $userA->assignRole('super-admin');

        $userB = User::factory()->create(['type' => 'admin']);
        $adminB = Admin::query()->create([
            'user_id' => $userB->id,
            'email' => 'b@example.com',
            'main_admin' => false,
        ]);
        $userB->assignRole('super-admin');

        $this->actingAs($userA, 'web');
        $this->postJson(route('admin.notifications.device-token'), [
            'device_token' => 'shared-token',
            'platform' => 'android',
        ])->assertOk();

        $this->actingAs($userB, 'web');
        $this->postJson(route('admin.notifications.device-token'), [
            'device_token' => 'shared-token',
            'platform' => 'ios',
        ])->assertOk();

        $this->assertDatabaseHas('admin_device_tokens', [
            'device_token' => 'shared-token',
            'admin_id' => $adminB->id,
            'platform' => 'ios',
        ]);

        $this->assertDatabaseMissing('admin_device_tokens', [
            'device_token' => 'shared-token',
            'admin_id' => $adminA->id,
        ]);
    }
}
