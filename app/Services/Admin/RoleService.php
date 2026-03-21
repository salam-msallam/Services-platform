<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * @return Collection<int, Role>
     */
    public function listRolesWithPermissionCount(): Collection
    {
        return Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{name: string}  $data
     */
    public function createRole(array $data): Role
    {
        return Role::query()->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
    }

    /**
     * @param  array{name: string}  $data
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'],
        ]);

        return $role->fresh();
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteRole(Role $role): void
    {
        if ($role->name === 'super-admin') {
            throw new AuthorizationException(__('admin.super_admin_role_cannot_be_deleted'));
        }

        DB::transaction(static function () use ($role): void {
            $role->delete();
        });
    }
}
