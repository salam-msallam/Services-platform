<?php

use App\Http\Controllers\Api\Auth\AppUserAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth/app')->group(function (): void {
    Route::post('register', [AppUserAuthController::class, 'register'])
        ->name('auth.app.register');
    Route::post('verify-otp', [AppUserAuthController::class, 'verifyOtp'])
        ->name('auth.app.verify-otp');
    Route::post('login', [AppUserAuthController::class, 'login'])
        ->name('auth.app.login');
    Route::post('logout', [AppUserAuthController::class, 'logout'])
        ->middleware('auth:api')
        ->name('auth.app.logout');
});
