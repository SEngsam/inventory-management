<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = $this->app->make(InvoiceService::class);
    }

    /** @test */
    public function it_creates_invoice_with_customer_and_multiple_items()
    {
        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create(['price' => 10, 'stock_quantity' => 10]);
        $product2 = Product::factory()->create(['price' => 15, 'stock_quantity' => 10]);

        $lines = [
            $this->line($product1, 2),
            $this->line($product2, 3),
        ];

        $invoice = $this->invoiceService->createFromSale(['customer_id' => $customer->id], $lines);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($customer->id, $invoice->customer_id);
        $this->assertEquals(65, $invoice->total); // 20 + 45
        $this->assertCount(2, $invoice->items);

        $this->assertEquals($product1->id, $invoice->items[0]->product_id);
        $this->assertEquals(2, $invoice->items[0]->quantity);
        $this->assertEquals(10, $invoice->items[0]->unit_price);

        $this->assertEquals($product2->id, $invoice->items[1]->product_id);
        $this->assertEquals(3, $invoice->items[1]->quantity);
        $this->assertEquals(15, $invoice->items[1]->unit_price);

        $this->assertEquals(8, $product1->fresh()->stock_quantity);
        $this->assertEquals(7, $product2->fresh()->stock_quantity);
    }

    private function line(Product $product, int $quantity): array
    {
        return [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'tax_value' => null,
            'tax_type' => 'percent',
            'discount' => 0,
            'line_tax' => 0,
            'line_total' => $product->price * $quantity,
        ];
    }
}
