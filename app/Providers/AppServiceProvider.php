<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\InvoiceNumberService;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function () {
            return new SettingsService;
        });
        $this->app->singleton(InvoiceNumberService::class);
    }

    public function boot(): void
    {
        Relation::morphMap([
            'invoice' => Invoice::class,
            'purchase' => Purchase::class,
            'sale' => Sale::class,
            'sale_return' => SaleReturn::class,
        ]);
    }
}
