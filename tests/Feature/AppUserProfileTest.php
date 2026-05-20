<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AppUser;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppUserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_app_user_can_fetch_profile(): void
    {
        $user = $this->createAppUser('Old Name', '+963944111222');

        $this->actingAs($user, 'api')
            ->getJson(route('auth.app.profile.show'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Old Name')
            ->assertJsonPath('data.phone', '+963944111222');
    }

    public function test_app_user_can_update_name(): void
    {
        $user = $this->createAppUser('Old Name', '+963944111222');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.profile.update'), [
                'name' => 'New Name',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('api.profile_updated'))
            ->assertJsonPath('data.profile.name', 'New Name')
            ->assertJsonPath('data.phone_verification_required', false)
            ->assertJsonPath('data.pending_phone', null);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_phone_update_sends_otp_without_changing_stored_phone(): void
    {
        $this->mockOtpSender();
        $user = $this->createAppUser('User Name', '+963944111222');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.profile.update'), [
                'phone' => '+963944333444',
            ])
            ->assertOk()
            ->assertJsonPath('message', __('api.phone_update_otp_sent'))
            ->assertJsonPath('data.profile.phone', '+963944111222')
            ->assertJsonPath('data.phone_verification_required', true)
            ->assertJsonPath('data.pending_phone', '+963944333444');

        $this->assertDatabaseHas('app_users', [
            'user_id' => $user->id,
            'phone' => '+963944111222',
        ]);

        $this->assertDatabaseHas('otp_verifications', [
            'user_id' => $user->id,
            'identifier' => '+963944333444',
            'verified_at' => null,
        ]);
    }

    public function test_phone_verification_updates_phone_and_verified_at(): void
    {
        $this->mockOtpSender();
        $user = $this->createAppUser('User Name', '+963944111222');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.profile.update'), [
                'phone' => '+963944333444',
            ])
            ->assertOk();

        $otp = OtpVerification::query()
            ->where('identifier', '+963944333444')
            ->latest()
            ->firstOrFail();

        $this->actingAs($user, 'api')
            ->postJson(route('auth.app.profile.phone.verify'), [
                'phone' => '+963944333444',
                'otp' => $otp->otp_code,
            ])
            ->assertOk()
            ->assertJsonPath('message', __('api.phone_updated'))
            ->assertJsonPath('data.phone', '+963944333444');

        $this->assertDatabaseHas('app_users', [
            'user_id' => $user->id,
            'phone' => '+963944333444',
        ]);

        $this->assertNotNull($user->appUser()->first()?->phone_verified_at);
    }

    public function test_duplicate_phone_is_rejected(): void
    {
        $this->createAppUser('Taken User', '+963944555666');
        $user = $this->createAppUser('User Name', '+963944111222');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.profile.update'), [
                'phone' => '+963944555666',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone');
    }

    public function test_password_update_succeeds_and_new_password_can_login(): void
    {
        $user = $this->createAppUser('User Name', '+963944111222', 'old-password');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertOk()
            ->assertJsonPath('message', __('api.password_updated'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_wrong_current_password_fails_and_old_password_remains(): void
    {
        $user = $this->createAppUser('User Name', '+963944111222', 'old-password');

        $this->actingAs($user, 'api')
            ->patchJson(route('auth.app.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('old-password', $user->password));
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->getJson(route('auth.app.profile.show'))
            ->assertUnauthorized();
    }

    public function test_admin_user_cannot_use_app_user_profile_endpoints(): void
    {
        $admin = User::factory()->create([
            'type' => 'admin',
        ]);

        Admin::query()->create([
            'user_id' => $admin->id,
            'email' => 'admin@example.com',
            'main_admin' => false,
        ]);

        $this->actingAs($admin, 'api')
            ->getJson(route('auth.app.profile.show'))
            ->assertForbidden();
    }

    private function createAppUser(string $name, string $phone, string $password = 'password'): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'password' => Hash::make($password),
            'type' => 'app_user',
        ]);

        AppUser::query()->create([
            'user_id' => $user->id,
            'phone' => $phone,
            'phone_verified_at' => now(),
        ]);

        return $user;
    }

    private function mockOtpSender(): void
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);
    }
}
