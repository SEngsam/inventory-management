<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'code' => 'ELEC'],
            ['name' => 'Furniture', 'code' => 'FURN'],
            ['name' => 'Stationery', 'code' => 'STAT'],
            ['name' => 'Clothing', 'code' => 'CLOT'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
