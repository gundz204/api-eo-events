<?php

namespace App\Providers;

use App\Models\Event;
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
        view()->composer('*', function ($view) {
        if (auth()->check() && auth()->user()->role === 'admin') {
            $view->with('sidebar_events', Event::orderBy('created_at', 'desc')->get());
        }
    });
    }
}
