<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::create([
            'name' => 'Samsung',
            'description' => 'A leading electronics and smartphone manufacturer.',
            'image' => 'brands/samsung.png',
        ]);

        Brand::create([
            'name' => 'Apple',
            'description' => 'Premium brand for smartphones, tablets, and computers.',
            'image' => 'brands/apple.png',
        ]);

        Brand::create([
            'name' => 'Sony',
            'description' => 'Known for audio, video, and gaming electronics.',
            'image' => 'brands/sony.png',
        ]);
    }
}
