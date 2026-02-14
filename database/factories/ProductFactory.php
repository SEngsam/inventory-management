<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;


    public function definition()
    {
        return [
            'name'     => $this->faker->word(),
               'sku' => 'SKU-' . Str::random(length: 6),
            'price'    => $this->faker->randomFloat(2, 1, 100),
            'stock_quantity' => $this->faker->numberBetween(0, 50),
            'category_id' => Category::factory(),
        ];
    }
}
