<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_image')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('order_tax', 8, 2)->nullable();
            $table->enum('tax_type', ['percent', 'fixed'])->default('percent');
            $table->text('description')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->string('warranty_period')->nullable();
            $table->boolean('guarantee')->default(false);
            $table->string('guarantee_period')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('has_imei')->default(false);
            $table->boolean('not_for_selling')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
