<?php

use App\Http\Controllers\Api\Auth\AppUserAuthController;
use App\Http\Controllers\Api\Auth\AppUserProfileController;
use App\Http\Controllers\Api\BusinessAccount\BusinessAccountController;
use App\Http\Controllers\Api\Evaluation\EvaluationsController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Report\ReportController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Service\ServiceIndexController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Favorite\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('current-slider', [SliderController::class, 'showCurrent'])
    ->name('api.current-slider');

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
    Route::middleware('auth:api')->group(function (): void {
        Route::get('profile', [AppUserProfileController::class, 'show'])
            ->name('auth.app.profile.show');
        Route::patch('profile', [AppUserProfileController::class, 'update'])
            ->name('auth.app.profile.update');
        Route::patch('password', [AppUserProfileController::class, 'updatePassword'])
            ->name('auth.app.password.update');
        Route::post('profile/phone/verify', [AppUserProfileController::class, 'verifyPhone'])
            ->name('auth.app.profile.phone.verify');
    });
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
    Route::post('business-accounts/{businessAccountId}/restore', [BusinessAccountController::class, 'restore'])
        ->name('business-accounts.restore');

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
    Route::post('services/{serviceId}/restore', [ServiceController::class, 'restore'])
        ->name('services.restore');

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
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])
        ->name('orders.destroy');
    Route::post('orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::post('evaluations', [EvaluationsController::class, 'store'])
        ->name('evaluations.store');

    Route::post('favorite',[FavoriteController::class,'toggle'])
        ->name('favorite.store');

    Route::post('report',[ReportController::class,'store'])
       ->name('report.store');

    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-as-read');
});
