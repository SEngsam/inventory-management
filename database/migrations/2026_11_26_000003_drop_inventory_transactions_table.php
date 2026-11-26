<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory_transactions')) {
            return;
        }

        $rows = DB::table('inventory_transactions')->count();

        if ($rows > 0) {
            throw new RuntimeException(
                "inventory_transactions contains {$rows} rows. Export or reconcile them before migrating; "
                .'stock_movements is now the source of truth for stock history.'
            );
        }

        Schema::drop('inventory_transactions');
    }

    public function down(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->enum('type', ['purchase', 'sale']);
            $table->integer('quantity');
            $table->timestamp('performed_at')->nullable();
            $table->timestamps();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
