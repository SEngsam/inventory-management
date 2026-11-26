<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(InvoiceService $invoices, StockService $stock): void
    {
        $customer = Customer::factory()->create();

        $products = Product::factory()
            ->count(3)
            ->create([
                'stock_quantity' => 0,
                'threshold_stock' => 5,
                'tax_type' => 'percent',
                'tax_value' => 5,
            ])
            ->each(fn (Product $product) => $stock->opening($product, 200));

        for ($i = 1; $i <= 5; $i++) {
            $lines = $products->map(function (Product $product) {
                $quantity = random_int(1, 5);
                $unitPrice = (float) $product->price;
                $lineTax = $unitPrice * $quantity * ((float) $product->tax_value / 100);

                return [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => round($unitPrice, 2),
                    'tax_value' => round((float) $product->tax_value, 2),
                    'tax_type' => 'percent',
                    'discount' => 0,
                    'line_tax' => round($lineTax, 2),
                    'line_total' => round($unitPrice * $quantity + $lineTax, 2),
                ];
            })->all();

            $invoices->createFromSale([
                'customer_id' => $customer->id,
                'issued_at' => now()->subDays($i),
                'status' => 'issued',
            ], $lines);
        }
    }
}
