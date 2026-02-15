<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->unsigned()->default(0);
            $table->decimal('tax_value', 8, 2)->unsigned()->nullable(); 
            $table->enum('tax_type', ['percent', 'fixed'])->default('percent');

            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('threshold_stock')->default(5);  

            $table->string('image')->nullable(); 

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();

            $table->boolean('has_warranty')->default(false);
            $table->unsignedInteger('warranty_period')->nullable();  

            $table->boolean('has_imei')->default(false);  

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
