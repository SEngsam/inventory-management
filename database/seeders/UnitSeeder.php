<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $piece = Unit::create([
            'name' => 'Piece',
            'short_code' => 'pc',
            'base_unit_id' => null,
            'operator' => null,
            'operator_value' => null
        ]);

        $kg = Unit::create([
            'name' => 'Kilogram',
            'short_code' => 'kg',
            'base_unit_id' => null,
            'operator' => null,
            'operator_value' => null
        ]);

        // Related Units
        Unit::create([
            'name' => 'Dozen',
            'short_code' => 'dz',
            'base_unit_id' => $piece->id,
            'operator' => '*',
            'operator_value' => 12
        ]);

        Unit::create([
            'name' => 'Gram',
            'short_code' => 'g',
            'base_unit_id' => $kg->id,
            'operator' => '/',
            'operator_value' => 1000
        ]);
    }
}
