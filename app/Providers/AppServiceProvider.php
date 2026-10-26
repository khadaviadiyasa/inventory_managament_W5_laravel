<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Markup pagination Bootstrap 5 (di-style oleh public/css/app.css, tanpa Tailwind/Vite).
        Paginator::useBootstrapFive();
    }
}
