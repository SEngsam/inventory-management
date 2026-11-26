<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('products')
            ->where('stock_quantity', '>', 0)
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($now) {
                DB::table('stock_movements')->insert($products->map(fn ($product) => [
                    'product_id' => $product->id,
                    'type' => 'opening',
                    'quantity' => $product->stock_quantity,
                    'balance_after' => $product->stock_quantity,
                    'note' => 'Opening balance recorded when the stock ledger was introduced',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            });
    }

    public function down(): void
    {
        DB::table('stock_movements')->where('type', 'opening')->delete();
    }
};
