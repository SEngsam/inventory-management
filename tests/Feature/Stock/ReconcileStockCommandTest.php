<?php

use App\Models\Product;
use Illuminate\Support\Facades\DB;

it('passes when every product matches its ledger', function () {
    Product::factory()->withStock(5)->create();
    Product::factory()->create();

    $this->artisan('stock:reconcile')
        ->expectsOutputToContain('in sync')
        ->assertSuccessful();
});

it('fails and lists products whose stock drifted from the ledger', function () {
    $product = Product::factory()->withStock(5)->create();
    DB::table('products')->where('id', $product->id)->update(['stock_quantity' => 7]);

    $this->artisan('stock:reconcile')
        ->expectsOutputToContain('1 product(s) do not match')
        ->expectsOutputToContain($product->sku)
        ->assertFailed();
});
