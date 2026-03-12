<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\User;
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

            return $user->admin()->create([
                'email' => $email,
                'main_admin' => false,
            ]);
        });
    }
}
