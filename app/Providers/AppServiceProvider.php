<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            $appUrl = rtrim((string) config('app.url', ''), '/');
            $host = strtolower((string) parse_url($appUrl, PHP_URL_HOST));

            if ($host !== '' && !in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                URL::forceRootUrl($appUrl);

                if (str_starts_with($appUrl, 'https://')) {
                    URL::forceScheme('https');
                }
            }
        }
    }
}
