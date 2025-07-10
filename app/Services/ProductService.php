<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductService
{
    public function create(string $name, float $price, int $categoryId,string $sku): Product
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Product name is required");
        }

        if ($price <= 1) {
            throw new InvalidArgumentException("Product price must be greater than 1");
        }
        if (! $categoryId) {
            throw new InvalidArgumentException("Category ID is required");
        }


        if (Product::where('name', $name)->exists()) {
            throw new InvalidArgumentException("Product name must be unique");
        }

        return DB::transaction(function () use ($name, $price, $categoryId,$sku) {
            return Product::create([
                'name'  => $name,
                'price' => $price,
                 'sku' => $sku,
                'stock_quantity' => 0,
                'category_id' => $categoryId,
            ]);
        });
    }
}
