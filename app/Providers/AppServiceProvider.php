<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Pagination\Paginator;

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
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();


        // force https in production
        if (app()->environment('production')){
            URL::forceScheme('https');
        }

        // Share active period globally
        if (!app()->runningInConsole()) {
            try {
                $activePeriod = \App\Models\Period::where('status', 'aktif')->first();
                \Illuminate\Support\Facades\View::share('activePeriod', $activePeriod);
            } catch (\Exception $e) {
                // Fail silently if table doesn't exist yet
            }
        }
    }
}
