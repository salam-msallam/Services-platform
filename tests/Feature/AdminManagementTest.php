<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_created_admin_can_login_to_dashboard_without_slider_permissions(): void
    {
        $creator = User::factory()->create(['type' => 'admin']);
        $creator->givePermissionTo(['access admin dashboard', 'manage admins']);

        $role = Role::query()
            ->where('guard_name', 'web')
            ->where('name', 'admin-manager')
            ->firstOrFail();

        $this->actingAs($creator, 'web')
            ->post(route('admin.admins.store'), [
                'name' => 'Limited Admin',
                'email' => 'limited-admin@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role_ids' => [$role->id],
            ])
            ->assertRedirect(route('admin.admins.index'));

        $admin = Admin::query()
            ->where('email', 'limited-admin@example.com')
            ->firstOrFail();

        $adminUser = $admin->user()->firstOrFail();

        $this->assertTrue($adminUser->can('access admin dashboard'));
        $this->assertFalse($adminUser->can('edit-sliders'));

        auth('web')->logout();

        $this->post(route('admin.login.post'), [
            'email' => 'limited-admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk();
    }
}
