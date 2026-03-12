<?php

namespace App\Services\Auth;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    public function attemptLogin(string $email, string $password, bool $remember): ?string
    {
        $admin = Admin::query()
            ->with('user')
            ->where('email', $email)
            ->first();

        if (! $admin || $admin->user?->type !== 'admin') {
            return 'These credentials do not match our records.';
        }

        $credentials = [
            'id' => $admin->user_id,
            'password' => $password,
        ];

        if (! Auth::guard('web')->attempt($credentials, $remember)) {
            return 'These credentials do not match our records.';
        }

        return null;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}

