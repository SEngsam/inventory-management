<?php

namespace Tests\Feature\Stock;

use App\Exceptions\InsufficientStockException;
use App\Livewire\Invoices\CreateInvoice;
use App\Livewire\Invoices\InvoiceList;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class InvoiceStockTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    private function invoice(Product $product, int $quantity): Invoice
    {
        return app(InvoiceService::class)->createFromSale(['customer_id' => Customer::factory()->create()->id], [[
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => 10,
            'tax_value' => null,
            'tax_type' => 'percent',
            'discount' => 0,
            'line_tax' => 0,
            'line_total' => 10 * $quantity,
        ]]);
    }

    public function test_issuing_an_invoice_deducts_stock(): void
    {
        $product = $this->productWithStock(10);

        $this->invoice($product, 4);

        $this->assertStock(6, $product);
    }

    public function test_an_invoice_cannot_oversell(): void
    {
        $product = $this->productWithStock(3);

        $this->expectException(InsufficientStockException::class);

        try {
            $this->invoice($product, 4);
        } finally {
            $this->assertStock(3, $product);
            $this->assertSame(0, Invoice::count());
        }
    }

    public function test_deleting_invoices_puts_stock_back(): void
    {
        $product = $this->productWithStock(10);
        $invoice = $this->invoice($product, 4);

        Livewire::test(InvoiceList::class)
            ->set('selectedInvoices', [$invoice->id])
            ->call('deleteSelected');

        $this->assertStock(10, $product);
        $this->assertModelMissing($invoice);
    }

    public function test_the_create_invoice_form_reports_insufficient_stock(): void
    {
        $product = $this->productWithStock(1);

        Livewire::test(CreateInvoice::class)
            ->set('selectedCustomer', Customer::factory()->create()->id)
            ->set('items', [['product_id' => $product->id, 'quantity' => 2]])
            ->call('createInvoice')
            ->assertHasErrors('items');

        $this->assertStock(1, $product);
    }
}
