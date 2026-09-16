<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alat;
use App\Observers\AlatObserver;

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
        Alat::observe(AlatObserver::class);
    }
}
