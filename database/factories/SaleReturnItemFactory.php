<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * A line item record only; it does not change stock.
 *
 * @extends Factory<SaleReturnItem>
 */
class SaleReturnItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 1, 100);

        return [
            'sale_return_id' => SaleReturn::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $price,
            'total' => round($quantity * $price, 2),
        ];
    }
}
