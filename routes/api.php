<?php

use App\Http\Controllers\Api\Auth\AppUserAuthController;
use App\Http\Controllers\Api\BusinessAccount\BusinessAccountController;
use App\Http\Controllers\Api\Service\ServiceController;
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

Route::middleware('auth:api')->group(function (): void {
    Route::get('business-accounts', [BusinessAccountController::class, 'index'])
        ->name('business-accounts.index');
    Route::post('business-accounts', [BusinessAccountController::class, 'store'])
        ->name('business-accounts.store');
    Route::put('business-accounts/{businessAccount}', [BusinessAccountController::class, 'update'])
        ->name('business-accounts.update');
    Route::delete('business-accounts/{businessAccount}', [BusinessAccountController::class, 'destroy'])
        ->name('business-accounts.destroy');

    Route::post('services', [ServiceController::class, 'store'])
        ->name('services.store');
});
