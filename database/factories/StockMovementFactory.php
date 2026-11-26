<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Creates a ledger row without updating the product's stock_quantity.
 * Only use it to test the ledger itself; anything that should keep stock
 * consistent must go through StockService.
 *
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 20);

        return [
            'product_id' => Product::factory(),
            'type' => StockMovementType::Adjustment,
            'quantity' => $quantity,
            'balance_after' => $quantity,
        ];
    }
}
