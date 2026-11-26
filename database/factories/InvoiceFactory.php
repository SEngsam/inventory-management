<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * An invoice record only. It does not touch stock; use InvoiceService when
 * a test needs the matching stock movements.
 *
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 500);

        return [
            'invoice_number' => 'INV-'.$this->faker->unique()->numerify('########'),
            'status' => 'issued',
            'customer_id' => Customer::factory(),
            'issued_at' => $this->faker->dateTimeBetween('-3 months'),
            'subtotal' => $subtotal,
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => $subtotal,
        ];
    }
}
