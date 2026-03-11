<?php

namespace App\Providers;

use App\Services\Messaging\UltraMsgWhatsAppService;
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
    }
}
