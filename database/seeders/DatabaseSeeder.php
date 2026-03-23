<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'type' => 'app_user',
        ]);

        $role = Role::query()
            ->where('name', 'user')
            ->where('guard_name', 'api')
            ->first();

        if ($role !== null) {
            $user->syncRoles([$role]);
        }
    }
}
