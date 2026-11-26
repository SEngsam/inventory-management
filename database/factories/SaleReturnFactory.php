<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SaleReturn>
 */
class SaleReturnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference_no' => 'RET-'.Str::upper(Str::random(8)),
            'sale_id' => Sale::factory(),
            'return_date' => $this->faker->dateTimeBetween('-1 month'),
            'total' => 0,
        ];
    }
}
