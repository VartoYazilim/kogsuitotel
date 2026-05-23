<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Admin şifre disiplini — Filament EditProfile + Laravel auth flow'lar
        // bu kuralları otomatik kullanır. CLAUDE.md "Admin user şifresi güçlü"
        // direktifi en az 12 char + harf+rakam karışımı.
        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers());
    }
}
