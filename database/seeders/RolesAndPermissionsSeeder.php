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

        $superAdminPermissions = [
            'access admin dashboard',
            'manage admins',
            'manage roles',
            'assign role permissions',
        ];

        foreach ($superAdminPermissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminPermissions = [
            'access admin dashboard',
            'approve business accounts',
            'reject business accounts',
            'approve services',
            'reject services',
            'create user',
            'delete user',
            'manage categories',
            'manage sub-categories',
            'manage dynamic fields',
            'manage reports',
            'manage cities',
            'manage sliders',

        ];

        foreach ($adminPermissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $userPermissions = [
            'manage business account',
            'manage services',
            'manage service requests',
            'add reviews',
            'manage favorites',
            'report services',
        ];

        foreach ($userPermissions as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'api',
            ]);
        }

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::query()->firstOrCreate([
            'name' => 'user',
            'guard_name' => 'api',
        ]);

        $superAdminRole->syncPermissions(array_merge($superAdminPermissions , $adminPermissions));
        $adminRole->syncPermissions($adminPermissions);
        $userRole->syncPermissions($userPermissions);
    }
}

