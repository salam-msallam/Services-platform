<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'type' => 'admin',
        ]);

        $user->admin()->create([
            'email' => 'admin@gmail.com',
            'main_admin' => true,
        ]);

        $permission = Permission::firstOrCreate(
            ['name' => 'manage admins', 'guard_name' => 'web']
        );
        $user->givePermissionTo($permission);
    }
}
