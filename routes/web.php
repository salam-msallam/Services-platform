<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

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
