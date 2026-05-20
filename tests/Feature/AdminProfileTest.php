<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_authenticated_admin_can_view_profile_page(): void
    {
        $user = $this->createAdminUser('admin@example.com');

        $this->actingAs($user, 'web')
            ->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee(__('admin.profile'))
            ->assertSee('admin@example.com');
    }

    public function test_admin_can_update_name_and_email_without_current_password(): void
    {
        $user = $this->createAdminUser('old@example.com');

        $this->actingAs($user, 'web')
            ->put(route('admin.profile.update'), [
                'name' => 'Updated Admin',
                'email' => 'updated@example.com',
                'current_password' => '',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.profile.edit'))
            ->assertSessionHas('success', __('admin.profile_updated'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Admin',
        ]);

        $this->assertDatabaseHas('admins', [
            'user_id' => $user->id,
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_email_must_be_unique(): void
    {
        $this->createAdminUser('taken@example.com');
        $user = $this->createAdminUser('current@example.com');

        $this->actingAs($user, 'web')
            ->from(route('admin.profile.edit'))
            ->put(route('admin.profile.update'), [
                'name' => $user->name,
                'email' => 'taken@example.com',
            ])
            ->assertRedirect(route('admin.profile.edit'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_password_with_current_password(): void
    {
        $user = $this->createAdminUser('admin@example.com', 'old-password');

        $this->actingAs($user, 'web')
            ->put(route('admin.profile.update'), [
                'name' => $user->name,
                'email' => 'admin@example.com',
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_admin_cannot_update_password_with_wrong_current_password(): void
    {
        $user = $this->createAdminUser('admin@example.com', 'old-password');

        $this->actingAs($user, 'web')
            ->from(route('admin.profile.edit'))
            ->put(route('admin.profile.update'), [
                'name' => $user->name,
                'email' => 'admin@example.com',
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('admin.profile.edit'))
            ->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('old-password', $user->password));
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.profile.edit'))
            ->assertRedirect(route('admin.login'));
    }

    private function createAdminUser(string $email, string $password = 'password'): User
    {
        $user = User::factory()->create([
            'password' => Hash::make($password),
            'type' => 'admin',
        ]);

        Admin::query()->create([
            'user_id' => $user->id,
            'email' => $email,
            'main_admin' => false,
        ]);

        $user->givePermissionTo('access admin dashboard');

        return $user;
    }
}
