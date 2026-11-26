<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => Str::title($this->faker->unique()->words(2, true)),
            'type' => 'standard',
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'tax_type' => 'percent',
            'stock_quantity' => 0,
            'threshold_stock' => 5,
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
        ];
    }

    public function withStock(int $quantity): static
    {
        return $this->afterCreating(function (Product $product) use ($quantity) {
            app(StockService::class)->opening($product, $quantity);
            $product->refresh();
        });
    }
}
