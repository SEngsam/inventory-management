<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(StockService $stock): void
    {
        $products = [];

        $products[] = [Product::create([
            'name' => 'iPhone 15 Pro Max',
            'sku' => 'SKU-'.Str::random(6),
            'image' => 'product_images/iphone15.png',
            'category_id' => 1,
            'brand_id' => 1,
            'tax_value' => 5,  // This could be a fixed amount (5 for example)
            'tax_type' => 'percent',  // This defines that tax_value is a percentage
            'description' => 'Apple’s flagship smartphone with advanced camera and A17 chip.',
            'price' => 1299.99,
            'warranty_period' => 365,  // Changed to days (12 months = 365 days)
            'has_warranty' => true,
            'threshold_stock' => 5,  // Adding the low stock threshold
            'has_imei' => true,
        ]), 50];

        $products[] = [Product::create([
            'name' => 'Samsung Galaxy S24 Ultra',
            'sku' => 'SKU-'.Str::random(6),
            'image' => 'product_images/s24ultra.png',
            'category_id' => 1,
            'brand_id' => 2,
            'tax_value' => 10,  // This is a fixed tax value
            'tax_type' => 'fixed',  // Tax is fixed, no percent
            'description' => 'High-end Android smartphone with S Pen and powerful zoom.',
            'price' => 1199.00,
            'warranty_period' => 730,  // Changed to days (24 months = 730 days)
            'has_warranty' => true,
            'threshold_stock' => 3,  // Adding the low stock threshold
            'has_imei' => true,
        ]), 30];

        foreach ($products as [$product, $quantity]) {
            $stock->opening($product, $quantity);
        }
    }
}
