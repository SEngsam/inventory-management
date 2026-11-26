<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;

function invoiceLine(Product $product, int $quantity): array
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

it('creates an invoice with its items and totals', function () {
    $customer = Customer::factory()->create();
    $first = Product::factory()->withStock(10)->create(['price' => 10]);
    $second = Product::factory()->withStock(10)->create(['price' => 15]);

    $invoice = app(InvoiceService::class)->createFromSale(
        ['customer_id' => $customer->id],
        [invoiceLine($first, 2), invoiceLine($second, 3)],
    );

    expect($invoice)
        ->toBeInstanceOf(Invoice::class)
        ->customer_id->toBe($customer->id)
        ->invoice_number->toStartWith('INV-')
        ->and((float) $invoice->total)->toBe(65.0)
        ->and($invoice->items)->toHaveCount(2);

    expect($invoice->items[0])->product_id->toBe($first->id)->quantity->toBe(2);
    expect($invoice->items[1])->product_id->toBe($second->id)->quantity->toBe(3);

    expect($first)->toHaveStock(8);
    expect($second)->toHaveStock(7);
});

it('numbers invoices sequentially', function () {
    $product = Product::factory()->withStock(10)->create();
    $service = app(InvoiceService::class);

    $numbers = collect(range(1, 3))->map(fn () => $service
        ->createFromSale(['customer_id' => Customer::factory()->create()->id], [invoiceLine($product, 1)])
        ->invoice_number);

    expect($numbers->all())->toBe([
        'INV-'.now()->year.'-000001',
        'INV-'.now()->year.'-000002',
        'INV-'.now()->year.'-000003',
    ]);
});
