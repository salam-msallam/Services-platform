<?php

use App\Http\Controllers\Web\Admin\ActivityTypeController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminProfileController;
use App\Http\Controllers\Web\Admin\AppUserController;
use App\Http\Controllers\Web\Admin\BusinessAccountReviewController;
use App\Http\Controllers\Web\Admin\CategoryController;
use App\Http\Controllers\Web\Admin\CityController;
use App\Http\Controllers\Web\Admin\NotificationController;
use App\Http\Controllers\Web\Admin\ReportController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\RolePermissionController;
use App\Http\Controllers\Web\Admin\ServiceReviewController;
use App\Http\Controllers\Web\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Web\Admin\SubCategoryController;
use App\Http\Controllers\Web\FirebaseMessagingSwController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\SliderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/firebase-messaging-sw.js', FirebaseMessagingSwController::class)
    ->name('firebase.messaging.sw');

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ar'], strict: true)) {
        Session::put('locale', $lang);
    }

    return redirect()->back();
})->name('locale');

Route::get('current-slider', [SliderController::class, 'showCurrent'])
    ->name('current-slider');

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('locale/{lang}', function (string $lang) {
            if (in_array($lang, ['en', 'ar'], strict: true)) {
                Session::put('locale', $lang);
            }

            return redirect()->back();
        })->name('locale');

        Route::middleware('guest')->group(function (): void {
            Route::get('login', [LoginController::class, 'showLoginForm'])
                ->name('login');

            Route::post('login', [LoginController::class, 'login'])
                ->name('login.post');
        });

        Route::middleware('auth')->group(function (): void {
            Route::post('logout', [LoginController::class, 'logout'])
                ->name('logout');

            Route::middleware('permission:access admin dashboard')->group(function (): void {
                Route::get('/', [AdminDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::get('profile', [AdminProfileController::class, 'edit'])
                    ->name('profile.edit');

                Route::put('profile', [AdminProfileController::class, 'update'])
                    ->name('profile.update');

                Route::post('notifications/device-token', [NotificationController::class, 'updateDeviceToken'])
                    ->name('notifications.device-token');

                Route::post('notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead'])
                    ->name('notifications.mark-as-read');

                Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
                    ->name('notifications.mark-all-as-read');

                Route::middleware('permission:manage admins')->group(function (): void {
                    Route::get('admins', [AdminController::class, 'index'])
                        ->name('admins.index');
                    Route::get('admins/create', [AdminController::class, 'create'])
                        ->name('admins.create');
                    Route::post('admins', [AdminController::class, 'store'])
                        ->name('admins.store');
                    Route::get('admins/{admin}/edit', [AdminController::class, 'edit'])
                        ->name('admins.edit');
                    Route::put('admins/{admin}', [AdminController::class, 'update'])
                        ->name('admins.update');
                    Route::delete('admins/{admin}', [AdminController::class, 'destroy'])
                        ->name('admins.destroy');
                });

                Route::middleware('permission:view users')->group(function (): void {
                    Route::get('app-users', [AppUserController::class, 'index'])
                        ->name('app-users.index');
                });

                Route::middleware('permission:create user')->group(function (): void {
                    Route::get('app-users/create', [AppUserController::class, 'create'])
                        ->name('app-users.create');
                    Route::post('app-users', [AppUserController::class, 'store'])
                        ->name('app-users.store');
                });

                Route::middleware('permission:edit user')->group(function (): void {
                    Route::get('app-users/{appUser}/edit', [AppUserController::class, 'edit'])
                        ->name('app-users.edit');
                    Route::put('app-users/{appUser}', [AppUserController::class, 'update'])
                        ->name('app-users.update');
                });

                Route::middleware('permission:delete user')->group(function (): void {
                    Route::delete('app-users/{appUser}', [AppUserController::class, 'destroy'])
                        ->name('app-users.destroy');
                    Route::post('app-users/{appUserId}/restore', [AppUserController::class, 'restore'])
                        ->name('app-users.restore');
                });

                Route::middleware('permission:manage roles')->group(function (): void {
                    Route::get('roles', [RoleController::class, 'index'])
                        ->name('roles.index');
                    Route::get('roles/create', [RoleController::class, 'create'])
                        ->name('roles.create');
                    Route::post('roles', [RoleController::class, 'store'])
                        ->name('roles.store');
                    Route::get('roles/{role}', [RoleController::class, 'show'])
                        ->name('roles.show');
                    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
                        ->name('roles.edit');
                    Route::put('roles/{role}', [RoleController::class, 'update'])
                        ->name('roles.update');
                    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
                        ->name('roles.destroy');
                });

                Route::middleware('permission:manage activity types')->group(function (): void {
                    Route::get('activity-types', [ActivityTypeController::class, 'index'])
                        ->name('activity-types.index');
                    Route::get('activity-types/create', [ActivityTypeController::class, 'create'])
                        ->name('activity-types.create');
                    Route::post('activity-types', [ActivityTypeController::class, 'store'])
                        ->name('activity-types.store');
                    Route::get('activity-types/{activityType}/edit', [ActivityTypeController::class, 'edit'])
                        ->name('activity-types.edit');
                    Route::put('activity-types/{activityType}', [ActivityTypeController::class, 'update'])
                        ->name('activity-types.update');
                    Route::delete('activity-types/{activityType}', [ActivityTypeController::class, 'destroy'])
                        ->name('activity-types.destroy');
                });

                Route::middleware('permission:manage cities')->group(function (): void {
                    Route::get('cities', [CityController::class, 'index'])
                        ->name('cities.index');
                    Route::get('cities/create', [CityController::class, 'create'])
                        ->name('cities.create');
                    Route::post('cities', [CityController::class, 'store'])
                        ->name('cities.store');
                    Route::get('cities/{city}/edit', [CityController::class, 'edit'])
                        ->name('cities.edit');
                    Route::put('cities/{city}', [CityController::class, 'update'])
                        ->name('cities.update');
                    Route::delete('cities/{city}', [CityController::class, 'destroy'])
                        ->name('cities.destroy');
                });

                Route::middleware('permission:manage categories')->group(function (): void {
                    Route::get('categories', [CategoryController::class, 'index'])
                        ->name('categories.index');
                    Route::get('categories/create', [CategoryController::class, 'create'])
                        ->name('categories.create');
                    Route::post('categories', [CategoryController::class, 'store'])
                        ->name('categories.store');
                    Route::get('categories/{category}', [CategoryController::class, 'show'])
                        ->name('categories.show');
                    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
                        ->name('categories.edit');
                    Route::put('categories/{category}', [CategoryController::class, 'update'])
                        ->name('categories.update');
                    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
                        ->name('categories.destroy');
                });

                Route::middleware('permission:manage sub-categories')->group(function (): void {
                    Route::get('sub-categories', [SubCategoryController::class, 'index'])
                        ->name('sub-categories.index');
                    Route::get('sub-categories/create', [SubCategoryController::class, 'create'])
                        ->name('sub-categories.create');
                    Route::post('sub-categories', [SubCategoryController::class, 'store'])
                        ->name('sub-categories.store');
                    Route::get('sub-categories/{subCategory}', [SubCategoryController::class, 'show'])
                        ->name('sub-categories.show');
                    Route::get('sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])
                        ->name('sub-categories.edit');
                    Route::put('sub-categories/{subCategory}', [SubCategoryController::class, 'update'])
                        ->name('sub-categories.update');
                    Route::delete('sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])
                        ->name('sub-categories.destroy');
                });

                Route::get('sliders', [AdminSliderController::class, 'index'])
                    ->middleware('permission:view-sliders')
                    ->name('sliders.index');

                Route::post('sliders', [AdminSliderController::class, 'store'])
                    ->middleware('permission:create-sliders')
                    ->name('sliders.store');

                Route::put('sliders/{slider}', [AdminSliderController::class, 'update'])
                    ->middleware('permission:edit-sliders')
                    ->name('sliders.update');

                Route::delete('sliders/{slider}', [AdminSliderController::class, 'destroy'])
                    ->middleware('permission:delete-sliders')
                    ->name('sliders.destroy');

                Route::middleware('role:super-admin|business-auditor')->group(function (): void {
                    Route::get('business-accounts', [BusinessAccountReviewController::class, 'index'])
                        ->name('business-accounts.index');
                    Route::get('business-accounts/{businessAccount}', [BusinessAccountReviewController::class, 'show'])
                        ->name('business-accounts.show');

                    Route::post('business-accounts/{businessAccount}/accept', [BusinessAccountReviewController::class, 'accept'])
                        ->middleware('permission:approve business accounts')
                        ->name('business-accounts.accept');

                    Route::post('business-accounts/{businessAccount}/reject', [BusinessAccountReviewController::class, 'reject'])
                        ->middleware('permission:reject business accounts')
                        ->name('business-accounts.reject');
                });

                Route::middleware('role:super-admin|service-moderator')->group(function (): void {
                    Route::get('services', [ServiceReviewController::class, 'index'])
                        ->name('services.index');
                    Route::get('services/{service}', [ServiceReviewController::class, 'show'])
                        ->name('services.show');

                    Route::post('services/{service}/accept', [ServiceReviewController::class, 'accept'])
                        ->middleware('permission:approve services')
                        ->name('services.accept');

                    Route::post('services/{service}/reject', [ServiceReviewController::class, 'reject'])
                        ->middleware('permission:reject services')
                        ->name('services.reject');
                });

                Route::middleware('role:super-admin|business-auditor')->group(function (): void {
                    Route::get('reports', [ReportController::class, 'index'])
                        ->name('reports.index');
                    Route::get('reports/{report}', [ReportController::class, 'show'])
                        ->name('reports.show');

                    Route::post('reports/{report}/resolve', [ReportController::class, 'resolve'])
                        ->name('reports.resolve');
                });

                Route::middleware('permission:assign role permissions')->group(function (): void {
                    Route::get('roles/{role}/permissions', [RolePermissionController::class, 'edit'])
                        ->name('roles.permissions.edit');
                    Route::put('roles/{role}/permissions', [RolePermissionController::class, 'update'])
                        ->name('roles.permissions.update');
                });
            });
        });
    });
