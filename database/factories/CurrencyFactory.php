<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->currencyCode(),
            'symbol' => $this->faker->randomElement(['$', '€', '£', '¥']),
            'rate' => $this->faker->randomFloat(4, 0.1, 5),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true, 'rate' => 1]);
    }
}
