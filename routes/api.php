<?php

use App\Http\Controllers\Api\Auth\AppUserAuthController;
use App\Http\Controllers\Api\BusinessAccount\BusinessAccountController;
use App\Http\Controllers\Api\Evaluation\EvaluationsController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Service\ServiceIndexController;
use App\Http\Controllers\Favorite\FavoriteController;
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
    Route::patch('business-accounts/{businessAccount}', [BusinessAccountController::class, 'update'])
        ->name('business-accounts.update');
    Route::delete('business-accounts/{businessAccount}', [BusinessAccountController::class, 'destroy'])
        ->name('business-accounts.destroy');

    Route::get('services', [ServiceController::class, 'index'])
        ->name('services.index');
    Route::get('services/browse', ServiceIndexController::class)
        ->name('services.browse');
    Route::post('services', [ServiceController::class, 'store'])
        ->name('services.store');
    Route::get('services/{service}', [ServiceController::class, 'show'])
        ->name('services.show');
    // PUT and PATCH both hit update (Postman/tools sometimes default to PUT).
    Route::patch('services/{service}', [ServiceController::class, 'update'])
        ->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])
        ->name('services.destroy');

    Route::get('orders/received', [OrderController::class, 'indexReceived'])
        ->name('orders.received');
    Route::get('orders/my', [OrderController::class, 'indexMyOrders'])
        ->name('orders.my');
    Route::patch('orders/{order}/accept', [OrderController::class, 'accept'])
        ->name('orders.accept');
    Route::patch('orders/{order}/reject', [OrderController::class, 'reject'])
        ->name('orders.reject');
    Route::patch('orders/{order}', [OrderController::class, 'updateMyOrder'])
        ->name('orders.update-my');
    Route::post('orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::post('evaluations', [EvaluationsController::class, 'store'])
        ->name('evaluations.store');

    Route::post('favorite',[FavoriteController::class,'toggle'])
        ->name('favorite.store');
});
