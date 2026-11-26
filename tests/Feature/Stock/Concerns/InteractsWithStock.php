<?php

namespace Tests\Feature\Stock\Concerns;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;

trait InteractsWithStock
{
    protected function productWithStock(int $quantity, array $attributes = []): Product
    {
        $product = Product::factory()->create(['stock_quantity' => 0, ...$attributes]);
        app(StockService::class)->opening($product, $quantity);

        return $product->fresh();
    }

    protected function assertStock(int $expected, Product $product): void
    {
        $product = $product->fresh();

        $this->assertSame($expected, $product->stock_quantity, "Unexpected stock for product {$product->id}");
        $this->assertSame(
            $expected,
            (int) StockMovement::where('product_id', $product->id)->sum('quantity'),
            "Ledger does not add up to stock_quantity for product {$product->id}",
        );
    }
}
