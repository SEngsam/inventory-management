<?php

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

it('records an opening movement for each product that holds stock', function () {
    $stocked = Product::factory()->create(['stock_quantity' => 42]);
    $empty = Product::factory()->create();

    (require database_path('migrations/2026_11_26_000002_backfill_opening_stock_movements.php'))->up();

    expect(StockMovement::where('product_id', $stocked->id)->sole())
        ->type->toBe(StockMovementType::Opening)
        ->quantity->toBe(42)
        ->balance_after->toBe(42)
        ->and($stocked)->toHaveStock(42);

    $this->assertDatabaseMissing('stock_movements', ['product_id' => $empty->id]);
});

it('refuses to drop inventory_transactions while it holds rows', function () {
    $migration = require database_path('migrations/2026_11_26_000003_drop_inventory_transactions_table.php');
    $migration->down();

    DB::table('inventory_transactions')->insert([
        'product_id' => Product::factory()->create()->id,
        'type' => 'sale',
        'quantity' => 1,
    ]);

    $migration->up();
})->throws(RuntimeException::class);
