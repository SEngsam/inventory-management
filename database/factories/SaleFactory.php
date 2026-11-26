<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => 'SL-'.Str::upper(Str::random(8)),
            'sale_date' => $this->faker->dateTimeBetween('-3 months'),
            'status' => Sale::STATUS_COMPLETED,
            'customer_id' => Customer::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => Sale::STATUS_PENDING]);
    }
}
