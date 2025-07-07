<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Import View facade
use App\View\Composers\NavigationComposer; // Import your custom composer

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
        // Register the View Composer for the 'layouts.app' view.
        // This means any view extending 'layouts.app' will automatically have
        // $settings, $navigationItems (grouped), $headerNav, $footerNavigateNav, $footerLegalNav available.
        View::composer('layouts.app', NavigationComposer::class);
    }
}