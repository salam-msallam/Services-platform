<?php

use App\Http\Controllers\Api\Auth\AppUserAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth/app')->group(function (): void {
    Route::post('request-otp', [AppUserAuthController::class, 'requestOtp'])->name('auth.app.request-otp');
    Route::post('verify-otp', [AppUserAuthController::class, 'verifyOtp'])->name('auth.app.verify-otp');
});
