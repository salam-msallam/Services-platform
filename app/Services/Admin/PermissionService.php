<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * @return Collection<int, Permission>
     */
    public function listPermissionsForGuard(string $guardName = 'web'): Collection
    {
        return Permission::query()
            ->where('guard_name', $guardName)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<int>
     */
    public function getAssignedPermissionIds(Role $role): array
    {
        return $role->permissions()->pluck('id')->all();
    }

    /**
     * @param  array{permissions?: list<int>}  $data
     */
    public function syncRolePermissions(Role $role, array $data, string $guardName = 'web'): void
    {
        $permissionIds = $data['permissions'] ?? [];

        $permissionNames = Permission::query()
            ->whereIn('id', $permissionIds)
            ->where('guard_name', $guardName)
            ->pluck('name')
            ->all();

        DB::transaction(static function () use ($role, $permissionNames): void {
            $role->syncPermissions($permissionNames);
        });
    }
}
