<?php

use App\Exceptions\InsufficientStockException;
use App\Livewire\Invoices\CreateInvoice;
use App\Livewire\Invoices\InvoiceList;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Livewire\Livewire;

function issueInvoice(Product $product, int $quantity): Invoice
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

it('deducts stock when an invoice is issued', function () {
    $product = Product::factory()->withStock(10)->create();

    issueInvoice($product, 4);

    expect($product)->toHaveStock(6);
});

it('cannot oversell through an invoice', function () {
    $product = Product::factory()->withStock(3)->create();

    expect(fn () => issueInvoice($product, 4))->toThrow(InsufficientStockException::class);

    expect($product)->toHaveStock(3)
        ->and(Invoice::count())->toBe(0);
});

it('puts stock back when invoices are deleted', function () {
    $product = Product::factory()->withStock(10)->create();
    $invoice = issueInvoice($product, 4);

    Livewire::test(InvoiceList::class)
        ->set('selectedInvoices', [$invoice->id])
        ->call('deleteSelected');

    expect($product)->toHaveStock(10);
    $this->assertModelMissing($invoice);
});

describe('create invoice form', function () {
    it('issues an invoice and redirects to it', function () {
        $product = Product::factory()->withStock(5)->create(['price' => 20, 'tax_value' => 10, 'tax_type' => 'percent']);

        Livewire::test(CreateInvoice::class)
            ->set('selectedCustomer', Customer::factory()->create()->id)
            ->set('items', [['product_id' => $product->id, 'quantity' => 2]])
            ->call('createInvoice')
            ->assertHasNoErrors()
            ->assertRedirect(route('invoices.show', Invoice::sole()));

        expect((float) Invoice::sole()->total)->toBe(44.0)
            ->and($product)->toHaveStock(3);
    });

    it('reports insufficient stock', function () {
        $product = Product::factory()->withStock(1)->create();

        Livewire::test(CreateInvoice::class)
            ->set('selectedCustomer', Customer::factory()->create()->id)
            ->set('items', [['product_id' => $product->id, 'quantity' => 2]])
            ->call('createInvoice')
            ->assertHasErrors('items');

        expect($product)->toHaveStock(1);
    });

    it('requires a customer and valid items', function () {
        Livewire::test(CreateInvoice::class)
            ->set('items', [['product_id' => null, 'quantity' => 0]])
            ->call('createInvoice')
            ->assertHasErrors([
                'selectedCustomer' => 'required',
                'items.0.product_id' => 'required',
                'items.0.quantity' => 'min',
            ]);
    });
});
