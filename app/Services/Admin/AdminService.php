<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Admin>
     */
    public function listAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return Admin::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createAdmin(string $name, string $email, string $password): Admin
    {
        return DB::transaction(function () use ($name, $email, $password): Admin {
            $user = User::query()->create([
                'name' => $name,
                'password' => $password,
                'type' => 'admin',
            ]);

            $admin = $user->admin()->create([
                'email' => $email,
                'main_admin' => false,
            ]);

            $user->syncRoles(['admin']);

            return $admin;
        });
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteAdmin(Admin $admin, int $actingUserId): void
    {
        if ($admin->user_id === $actingUserId) {
            throw new AuthorizationException(__('admin.cannot_delete_self'));
        }

        if ($admin->main_admin) {
            throw new AuthorizationException(__('admin.cannot_delete_main_admin'));
        }

        if ($admin->user && $admin->user->hasRole('super-admin')) {
            $superAdminCount = User::query()->role('super-admin')->count();

            if ($superAdminCount <= 1) {
                throw new AuthorizationException(__('admin.must_keep_one_super_admin'));
            }
        }

        DB::transaction(function () use ($admin): void {
            $admin->loadMissing('user');
            $admin->user?->delete();
            $admin->delete();
        });
    }
}
