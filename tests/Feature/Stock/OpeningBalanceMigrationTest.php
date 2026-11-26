<?php

namespace Tests\Feature\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OpeningBalanceMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_an_opening_movement_for_each_product_with_stock(): void
    {
        $stocked = Product::factory()->create(['stock_quantity' => 42]);
        $empty = Product::factory()->create(['stock_quantity' => 0]);

        $migration = require database_path('migrations/2026_11_26_000002_backfill_opening_stock_movements.php');
        $migration->up();

        $movement = StockMovement::where('product_id', $stocked->id)->sole();
        $this->assertSame(StockMovementType::Opening, $movement->type);
        $this->assertSame(42, $movement->quantity);
        $this->assertSame(42, $movement->balance_after);

        $this->assertDatabaseMissing('stock_movements', ['product_id' => $empty->id]);
    }

    public function test_dropping_inventory_transactions_refuses_to_discard_rows(): void
    {
        $migration = require database_path('migrations/2026_11_26_000003_drop_inventory_transactions_table.php');
        $migration->down();

        DB::table('inventory_transactions')->insert([
            'product_id' => Product::factory()->create()->id,
            'type' => 'sale',
            'quantity' => 1,
        ]);

        $this->expectException(\RuntimeException::class);
        $migration->up();
    }
}
