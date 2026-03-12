<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'type' => 'admin',
        ]);

        $admin=$user->admin()->create([
            'email' => 'admin@example.com',
            'main_admin' => true,
        ]);
    }
}
