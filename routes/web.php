<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\ActivityTypeController;
use App\Http\Controllers\Web\Admin\CityController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\RolePermissionController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ar'], strict: true)) {
        Session::put('locale', $lang);
    }

    return redirect()->back();
})->name('locale');

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

                Route::middleware('permission:manage admins')->group(function (): void {
                    Route::get('admins', [AdminController::class, 'index'])
                        ->name('admins.index');
                    Route::get('admins/create', [AdminController::class, 'create'])
                        ->name('admins.create');
                    Route::post('admins', [AdminController::class, 'store'])
                        ->name('admins.store');
                    Route::delete('admins/{admin}', [AdminController::class, 'destroy'])
                        ->name('admins.destroy');
                });

                Route::middleware('permission:manage roles')->group(function (): void {
                    Route::get('roles', [RoleController::class, 'index'])
                        ->name('roles.index');
                    Route::get('roles/create', [RoleController::class, 'create'])
                        ->name('roles.create');
                    Route::post('roles', [RoleController::class, 'store'])
                        ->name('roles.store');
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

                Route::middleware('permission:assign role permissions')->group(function (): void {
                    Route::get('roles/{role}/permissions', [RolePermissionController::class, 'edit'])
                        ->name('roles.permissions.edit');
                    Route::put('roles/{role}/permissions', [RolePermissionController::class, 'update'])
                        ->name('roles.permissions.update');
                });
            });
        });
    });
