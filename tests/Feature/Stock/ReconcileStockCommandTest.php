<?php

namespace Tests\Feature\Stock;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class ReconcileStockCommandTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    public function test_it_passes_when_stock_matches_the_ledger(): void
    {
        $this->productWithStock(5);
        Product::factory()->create(['stock_quantity' => 0]);

        $this->artisan('stock:reconcile')
            ->expectsOutputToContain('in sync')
            ->assertSuccessful();
    }

    public function test_it_reports_products_whose_stock_drifted(): void
    {
        $product = $this->productWithStock(5);
        DB::table('products')->where('id', $product->id)->update(['stock_quantity' => 7]);

        $this->artisan('stock:reconcile')
            ->expectsOutputToContain('1 product(s) do not match')
            ->assertFailed();
    }
}
