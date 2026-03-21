<?php

namespace App\Providers;

use App\Services\Messaging\UltraMsgWhatsAppService;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UltraMsgWhatsAppService::class, function (): UltraMsgWhatsAppService {
            return new UltraMsgWhatsAppService(
                config('services.ultramsg.instance_id'),
                config('services.ultramsg.token'),
                config('services.ultramsg.base_url'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(Translatable::class)->fallback(config('app.fallback_locale', 'en'));

        // `guest` uses RedirectIfAuthenticated; default falls back to `/` when no `dashboard`/`home`
        // route exists. Our panel lives at `admin.dashboard`, so send authenticated users there.
        RedirectIfAuthenticated::redirectUsing(function (Request $request): string {
            return route('admin.dashboard');
        });

        // Visiting `/admin` while logged out: send to the admin login route (not `/` / welcome).
        Authenticate::redirectUsing(function (Request $request): string {
            return route('admin.login');
        });
    }
}
