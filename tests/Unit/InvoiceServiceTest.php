<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\InvoiceService;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;

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

        $product1 = Product::factory()->create(['price' => 10]);
        $product2 = Product::factory()->create(['price' => 15]);

        $items = [
            ['product_id' => $product1->id, 'quantity' => 2, 'price' => $product1->price],
            ['product_id' => $product2->id, 'quantity' => 3, 'price' => $product2->price],
        ];


        $invoice = $this->invoiceService->createFromSale($items, $customer);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($customer->id, $invoice->customer_id);
        $this->assertEquals(65, $invoice->total); // 20 + 45
        $this->assertCount(2, $invoice->items);

        $this->assertEquals($product1->id, $invoice->items[0]->product_id);
        $this->assertEquals(2, $invoice->items[0]->quantity);
        $this->assertEquals(10, $invoice->items[0]->price);

        $this->assertEquals($product2->id, $invoice->items[1]->product_id);
        $this->assertEquals(3, $invoice->items[1]->quantity);
        $this->assertEquals(15, $invoice->items[1]->price);
    }
}
