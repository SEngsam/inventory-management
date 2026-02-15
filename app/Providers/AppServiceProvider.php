<?php

namespace App\Providers;

use App\Services\InvoiceNumberService;
use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function () {
            return new SettingsService();
        });
        $this->app->singleton(InvoiceNumberService::class);
    }

 
    public function boot(): void
    {
        //
    }
}
