<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

    /**
     * @param  list<int>  $roleIds
     */
    public function createAdmin(string $name, string $email, string $password, array $roleIds): Admin
    {
        return DB::transaction(function () use ($name, $email, $password, $roleIds): Admin {
            $user = User::query()->create([
                'name' => $name,
                'password' => $password,
                'type' => 'admin',
            ]);

            $admin = $user->admin()->create([
                'email' => $email,
                'main_admin' => false,
            ]);

            $roleNames = Role::query()
                ->where('guard_name', 'web')
                ->whereIn('id', $roleIds)
                ->pluck('name')
                ->all();

            $user->syncRoles($roleNames);
            $user->givePermissionTo('access admin dashboard');

            return $admin;
        });
    }

    /**
     * @param  array{name: string, email: string, password?: string|null, role_ids: list<int>}  $data
     *
     * @throws AuthorizationException
     */
    public function updateAdmin(Admin $admin, array $data): Admin
    {
        $admin->loadMissing('user.roles');

        return DB::transaction(function () use ($admin, $data): Admin {
            $user = $admin->user;

            if ($user === null) {
                throw new AuthorizationException(__('admin.credentials_error'));
            }

            $user->update([
                'name' => $data['name'],
            ]);

            $admin->update([
                'email' => $data['email'],
            ]);

            if (! empty($data['password'])) {
                $user->update([
                    'password' => $data['password'],
                ]);
            }

            if ($admin->main_admin) {
                $currentRoleIds = $user->roles()->pluck('roles.id')->map(fn ($id): int => (int) $id)->sort()->values()->all();
                $incomingRoleIds = collect($data['role_ids'])->map(fn ($id): int => (int) $id)->sort()->values()->all();

                if ($currentRoleIds !== $incomingRoleIds) {
                    throw new AuthorizationException(__('admin.cannot_edit_main_admin_roles'));
                }
            }

            $roleNames = Role::query()
                ->where('guard_name', 'web')
                ->whereIn('id', $data['role_ids'])
                ->pluck('name')
                ->all();

            $user->syncRoles($roleNames);
            $user->givePermissionTo('access admin dashboard');

            return $admin->fresh(['user.roles']);
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
