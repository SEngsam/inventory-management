<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('unit_price', 10, 2)->unsigned();

            $table->decimal('tax_value', 8, 2)->unsigned()->nullable();
            $table->enum('tax_type', ['percent', 'fixed'])->default('percent');

            $table->decimal('discount', 10, 2)->unsigned()->default(0);

            $table->decimal('line_tax', 10, 2)->unsigned()->default(0);
            $table->decimal('line_total', 10, 2)->unsigned()->default(0);

            $table->timestamps();

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
