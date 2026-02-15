<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::factory()->create();

        $products = Product::factory()
            ->count(3)
            ->create([
                'stock_quantity' => 200,
                'threshold_stock' => 5,
                'tax_type' => 'percent',
                'tax_value' => 5,
            ]);

        for ($i = 1; $i <= 5; $i++) {

            DB::transaction(function () use ($customer, $products, $i) {

                $invoice = Invoice::create([
                    'invoice_number' => $this->fakeInvoiceNumber(),
                    'customer_id'    => $customer->id,
                    'issued_at'      => now()->subDays($i),
                    'status'         => 'issued',

                    'subtotal'       => 0,
                    'tax_total'      => 0,
                    'discount_total' => 0,
                    'total'          => 0,
                ]);

                $subtotal = 0.0;
                $taxTotal = 0.0;
                $discountTotal = 0.0;

                foreach ($products as $product) {
                    $quantity  = random_int(1, 5);
                    $unitPrice = (float) $product->price;

                    $lineSub = $unitPrice * $quantity;

                    $taxValue = $product->tax_value !== null ? (float) $product->tax_value : null;
                    $taxType  = $product->tax_type ?? 'percent';

                    $lineTax = 0.0;
                    if ($taxValue !== null) {
                        $lineTax = $taxType === 'percent'
                            ? ($lineSub * ($taxValue / 100))
                            : ($taxValue * $quantity);  
                    }

                    $discount = 0.0;  
                    $lineTotal = ($lineSub - $discount) + $lineTax;

                    $invoice->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $quantity,

                        'unit_price' => round($unitPrice, 2),
                        'tax_value'  => $taxValue !== null ? round($taxValue, 2) : null,
                        'tax_type'   => $taxType,
                        'discount'   => round($discount, 2),

                        'line_tax'   => round($lineTax, 2),
                        'line_total' => round($lineTotal, 2),
                    ]);

                    $subtotal += ($lineSub - $discount);
                    $taxTotal += $lineTax;
                    $discountTotal += $discount;

                  
                }

                $invoice->update([
                    'subtotal'       => round($subtotal, 2),
                    'tax_total'      => round($taxTotal, 2),
                    'discount_total' => round($discountTotal, 2),
                    'total'          => round($subtotal + $taxTotal, 2),
                ]);
            });
        }
    }

    private function fakeInvoiceNumber(): string
    {
       
        return 'INV-' . now()->format('Ymd') . '-' . random_int(100000, 999999);
    }
}
