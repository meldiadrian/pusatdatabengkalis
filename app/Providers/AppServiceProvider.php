<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Model::unguard();

        // Rate limiter untuk halaman login Filament
        // Lapisan pertahanan tambahan di atas reCAPTCHA
        RateLimiter::for('filament-login', function (Request $request) {
            return [
                // Batas per IP: maksimal 5 request per menit
                Limit::perMinute(5)->by($request->ip()),

                // Batas per kombinasi email+IP: maksimal 3 per menit
                // Mencegah credential stuffing pada satu akun
                Limit::perMinute(3)->by(
                    $request->input('data.email', 'guest') . '|' . $request->ip()
                ),
            ];
        });
    }
}
