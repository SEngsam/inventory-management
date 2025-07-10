<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'iPhone 15 Pro Max',
            'sku' => 'SKU-' . Str::random(6),
            'image' => 'product_images/iphone15.png',
            'category_id' => 1,
            'brand_id' => 1,
            'order_tax' => 5,
            'tax_type' => 'percent',
            'description' => 'Apple’s flagship smartphone with advanced camera and A17 chip.',
            'price' => 1299.99,
            'warranty_period' => '12 months',
            'guarantee' => true,
            'guarantee_period' => '6 months',
            'stock_quantity' => 50,
            'has_imei' => true,
        ]);

        Product::create([
            'name' => 'Samsung Galaxy S24 Ultra',
            'sku' => 'SKU-' . Str::random(6),
            'image' => 'product_images/s24ultra.png',
            'category_id' => 1,
            'brand_id' => 2,
            'order_tax' => 10,
            'tax_type' => 'fixed',
            'description' => 'High-end Android smartphone with S Pen and powerful zoom.',
            'price' => 1199.00,
            'warranty_period' => '24 months',
            'guarantee' => true,
            'guarantee_period' => '12 months',
            'stock_quantity' => 30,
            'has_imei' => true,
        ]);
    }
}
