<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $userManagerPermissions = [
            'view users',
            'create user',
            'edit user',
            'delete user',
        ];

        $businessAuditorPermissions = [
            'access admin dashboard',
            'approve business accounts',
            'reject business accounts',
        ];

        $serviceModeratorPermissions = [
            'approve services',
            'reject services',
        ];

        $contentManagerPermissions = [
            'manage categories',
            'manage sub-categories',
            'manage dynamic fields',
            'manage cities',
            'manage sliders',
            'manage activity types',
        ];

        $adminPanelCorePermissions = [
            'manage admins',
            'manage roles',
            'assign role permissions',
        ];

        $allWebPermissions = array_values(array_unique(array_merge(
            $userManagerPermissions,
            $businessAuditorPermissions,
            $serviceModeratorPermissions,
            $contentManagerPermissions,
            $adminPanelCorePermissions,
        )));

        foreach ($allWebPermissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $regularUserPermissions = [
            'manage business account',
            'manage services',
            'manage service requests',
            'add reviews',
            'manage favorites',
            'report services',
        ];

        foreach ($regularUserPermissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'api',
            ]);
        }

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $userManagerRole = Role::query()->firstOrCreate([
            'name' => 'user-manager',
            'guard_name' => 'web',
        ]);

        $businessAuditorRole = Role::query()->firstOrCreate([
            'name' => 'business-auditor',
            'guard_name' => 'web',
        ]);

        $serviceModeratorRole = Role::query()->firstOrCreate([
            'name' => 'service-moderator',
            'guard_name' => 'web',
        ]);

        $contentManagerRole = Role::query()->firstOrCreate([
            'name' => 'content-manager',
            'guard_name' => 'web',
        ]);

        $adminManagerRole = Role::query()->firstOrCreate([
            'name' => 'admin-manager',
            'guard_name' => 'web',
        ]);

        $rolePermissionManagerRole = Role::query()->firstOrCreate([
            'name' => 'role-permission-manager',
            'guard_name' => 'web',
        ]);

        $regularUserRole = Role::query()->firstOrCreate([
            'name' => 'user',
            'guard_name' => 'api',
        ]);

        $userManagerRole->syncPermissions($userManagerPermissions);
        $businessAuditorRole->syncPermissions($businessAuditorPermissions);
        $serviceModeratorRole->syncPermissions($serviceModeratorPermissions);
        $contentManagerRole->syncPermissions($contentManagerPermissions);
        $adminManagerRole->syncPermissions(['manage admins']);
        $rolePermissionManagerRole->syncPermissions(['manage roles', 'assign role permissions']);
        $superAdminRole->syncPermissions($allWebPermissions);
        $regularUserRole->syncPermissions($regularUserPermissions);
    }
}

