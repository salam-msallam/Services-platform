<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::middleware('guest')->group(function (): void {
            Route::get('login', [LoginController::class, 'showLoginForm'])
                ->name('login');

            Route::post('login', [LoginController::class, 'login'])
                ->name('login.post');
        });

        Route::middleware('auth')->group(function (): void {
            Route::post('logout', [LoginController::class, 'logout'])
                ->name('logout');

            Route::get('/', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::middleware('permission:manage admins')->group(function (): void {
                Route::get('admins', [AdminController::class, 'index'])
                    ->name('admins.index');
                Route::get('admins/create', [AdminController::class, 'create'])
                    ->name('admins.create');
                Route::post('admins', [AdminController::class, 'store'])
                    ->name('admins.store');
            });
        });
    });
