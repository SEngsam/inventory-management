<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run()
{
    $customer = Customer::factory()->create();

    $products = Product::factory()->count(3)->create();

    for ($i = 1; $i <= 5; $i++) {
        $total = 0;
        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'issued_at'   => now()->subDays($i),
            'total'       => 0,
        ]);

        foreach ($products as $product) {
            $quantity = rand(1, 5);
            $price = $product->price;
            $invoice->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $price,
            ]);
            $total += $price * $quantity;
        }

        $invoice->update(['total' => $total]);
    }
}
}
