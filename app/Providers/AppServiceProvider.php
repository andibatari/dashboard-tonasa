<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Jika lingkungan bukan lokal (i.e., di DigitalOcean) dan APP_URL menggunakan https,
        // paksa Laravel untuk menghasilkan URL menggunakan https://
        if ($this->app->environment('production') && config('app.url') === env('APP_URL')) {
            URL::forceScheme('https');
        }
        \Carbon\Carbon::setLocale('id'); // Tambahkan ini
    }
}
