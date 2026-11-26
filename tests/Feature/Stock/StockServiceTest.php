<?php

namespace Tests\Feature\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stock = app(StockService::class);
    }

    private function product(int $stock = 0): Product
    {
        $product = Product::factory()->create(['stock_quantity' => 0]);
        $this->stock->opening($product, $stock);

        return $product->fresh();
    }

    private function assertLedgerMatchesStock(Product ...$products): void
    {
        foreach ($products as $product) {
            $this->assertSame(
                $product->fresh()->stock_quantity,
                (int) StockMovement::where('product_id', $product->id)->sum('quantity'),
                "Ledger and stock_quantity differ for product {$product->id}",
            );
        }
    }

    public function test_adding_stock_records_a_movement_and_updates_the_product(): void
    {
        $product = $this->product(10);
        $this->actingAs($user = User::factory()->create());

        $movement = $this->stock->add(StockMovementType::Purchase, [$product->id => 5])->sole();

        $this->assertSame(15, $product->fresh()->stock_quantity);
        $this->assertSame(StockMovementType::Purchase, $movement->type);
        $this->assertSame(5, $movement->quantity);
        $this->assertSame(15, $movement->balance_after);
        $this->assertSame($user->id, $movement->user_id);
        $this->assertLedgerMatchesStock($product);
    }

    public function test_removing_stock_stores_a_negative_quantity(): void
    {
        $product = $this->product(10);
        $sale = Sale::factory()->create();

        $movement = $this->stock->remove(StockMovementType::Sale, [$product->id => 3], $sale)->sole();

        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->assertSame(-3, $movement->quantity);
        $this->assertSame('sale', $movement->reference_type);
        $this->assertSame($sale->id, $movement->reference_id);
        $this->assertTrue($movement->reference->is($sale));
        $this->assertLedgerMatchesStock($product);
    }

    public function test_removing_more_than_available_is_rejected_without_partial_writes(): void
    {
        $plenty = $this->product(10);
        $scarce = $this->product(2);
        $movementsBefore = StockMovement::count();

        try {
            $this->stock->remove(StockMovementType::Sale, [$plenty->id => 5, $scarce->id => 3]);
            $this->fail('Expected an InsufficientStockException.');
        } catch (InsufficientStockException $e) {
            $this->assertSame([[
                'product_id' => $scarce->id,
                'product' => $scarce->name,
                'available' => 2,
                'requested' => 3,
            ]], $e->shortages);
            $this->assertArrayHasKey('items', $e->errors());
        }

        $this->assertSame(10, $plenty->fresh()->stock_quantity);
        $this->assertSame(2, $scarce->fresh()->stock_quantity);
        $this->assertSame($movementsBefore, StockMovement::count());
    }

    public function test_insufficient_stock_is_a_validation_error(): void
    {
        $product = $this->product(1);

        $this->expectException(ValidationException::class);
        $this->stock->remove(StockMovementType::Sale, [$product->id => 2]);
    }

    public function test_quantities_must_be_positive(): void
    {
        $product = $this->product(5);

        $this->expectException(InvalidArgumentException::class);
        $this->stock->remove(StockMovementType::Sale, [$product->id => 0]);
    }

    public function test_unknown_products_are_rejected(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->stock->add(StockMovementType::Purchase, [999 => 1]);
    }

    public function test_reversing_a_document_cancels_its_net_effect_with_the_same_type(): void
    {
        $a = $this->product(10);
        $b = $this->product(10);
        $sale = Sale::factory()->create();

        $this->stock->remove(StockMovementType::Sale, [$a->id => 4, $b->id => 1], $sale);
        $reversal = $this->stock->reverse($sale, 'Sale deleted');

        $this->assertSame(10, $a->fresh()->stock_quantity);
        $this->assertSame(10, $b->fresh()->stock_quantity);
        $this->assertCount(2, $reversal);
        $this->assertTrue($reversal->every(fn ($m) => $m->type === StockMovementType::Sale && $m->quantity > 0));
        $this->assertSame([], $this->stock->netFor($sale));

        // Reversing again is a no-op because the net effect is already zero.
        $this->assertCount(0, $this->stock->reverse($sale));
        $this->assertLedgerMatchesStock($a, $b);
    }

    public function test_reversing_added_stock_that_was_already_used_is_rejected(): void
    {
        $product = $this->product(0);
        $purchase = Purchase::factory()->create();

        $this->stock->add(StockMovementType::Purchase, [$product->id => 5], $purchase);
        $this->stock->remove(StockMovementType::Sale, [$product->id => 4]);

        $this->expectException(InsufficientStockException::class);
        $this->stock->reverse($purchase);
    }

    public function test_set_level_records_the_difference_as_an_adjustment(): void
    {
        $product = $this->product(10);

        $movement = $this->stock->setLevel($product, 7, 'Stock count');

        $this->assertSame(StockMovementType::Adjustment, $movement->type);
        $this->assertSame(-3, $movement->quantity);
        $this->assertSame('Stock count', $movement->note);
        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->assertNull($this->stock->setLevel($product, 7));
        $this->assertLedgerMatchesStock($product);
    }

    public function test_opening_stock_of_zero_records_nothing(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 0]);

        $this->assertNull($this->stock->opening($product, 0));
        $this->assertSame(0, StockMovement::count());
    }
}
