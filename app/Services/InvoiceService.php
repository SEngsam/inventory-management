<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;

class InvoiceService
{
    public function createFromSale(array $items, Customer $customer): Invoice
    {
        $total = 0;

        foreach ($items as $entry) {
            $product = Product::findOrFail($entry['product_id']);
            $quantity = $entry['quantity'];
            $total += $product->price * $quantity;
        }

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'total'       => $total,
            'issued_at'   => now(),
        ]);

        foreach ($items as $entry) {
            $product = Product::findOrFail($entry['product_id']);

            $invoice->items()->create([
                'product_id' => $product->id,
                'quantity'   => $entry['quantity'],
                'price'      => $product->price,
            ]);
        }

        return $invoice;
    }
}
