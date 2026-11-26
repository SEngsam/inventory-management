<?php

use App\Livewire\DashboardPanel;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Livewire\Livewire;

it('renders with an empty database', function () {
    Livewire::test(DashboardPanel::class)->assertOk();
});

it('summarises products, customers and sales', function () {
    $product = Product::factory()->withStock(20)->create(['name' => 'Top Seller']);
    Customer::factory()->count(2)->create();

    app(SaleService::class)->save([
        'reference_no' => 'SL-DASH',
        'sale_date' => now()->toDateString(),
        'status' => Sale::STATUS_COMPLETED,
    ], [['product_id' => $product->id, 'quantity' => 4, 'unit_price' => 25]]);

    Livewire::test(DashboardPanel::class)
        ->assertSet('productCount', 1)
        ->assertSee('SL-DASH')
        ->assertSee('Top Seller');
});
