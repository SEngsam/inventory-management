<?php

namespace Tests\Unit;

use App\Models\Customer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Services\InventoryService;
use InvalidArgumentException;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = $this->app->make(InventoryService::class);
    }

    /** @test */
    public function it_increases_quantity_on_purchase()
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $tx = $this->inventoryService->process($product->id, 'purchase', 5);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 15,
        ]);

        $this->assertInstanceOf(InventoryTransaction::class, $tx);
        $this->assertEquals('purchase', $tx->type);
        $this->assertEquals(5, $tx->quantity);
    }

    /** @test */
    public function it_decreases_quantity_on_sale()
    {
        $product = Product::factory()->create(['stock_quantity' => 20]);

        $tx = $this->inventoryService->process($product->id, 'sale', 7);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 13,
        ]);

        $this->assertEquals('sale', $tx->type);
        $this->assertEquals(7, $tx->quantity);
    }

    /** @test */
    public function it_throws_exception_if_sale_exceeds_stock()
    {
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->inventoryService->process($product->id, 'sale', 5);
    }

    /** @test */
    public function it_throws_exception_if_quantity_is_zero()
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        $this->inventoryService->process($product->id, 'sale', 0);
    }

    /** @test */
    public function it_links_customer_to_sale_transaction()
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $customer = Customer::factory()->create();

        $tx = $this->inventoryService->process($product->id, 'sale', 3, $customer->id);

        $this->assertEquals($customer->id, $tx->customer_id);
    }
}
