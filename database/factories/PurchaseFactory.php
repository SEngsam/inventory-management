<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => 'PO-'.Str::upper(Str::random(8)),
            'supplier_id' => Supplier::factory(),
            'purchase_date' => $this->faker->dateTimeBetween('-3 months'),
            'status' => Purchase::STATUS_RECEIVED,
        ];
    }

    public function ordered(): static
    {
        return $this->state(['status' => Purchase::STATUS_ORDERED]);
    }
}
