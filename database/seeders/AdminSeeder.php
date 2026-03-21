<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $user = User::query()->firstOrCreate(
            ['name' => 'Admin', 'type' => 'admin'],
            ['password' => Hash::make('password')],
        );

        $user->admin()->updateOrCreate(
            ['user_id' => $user->id],
            ['email' => 'admin@gmail.com', 'main_admin' => true],
        );

        $user->syncRoles(['super-admin']);
    }
}
