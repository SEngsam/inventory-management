<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])
                ->default('issued');

            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); 

            $table->decimal('subtotal', 10, 2)->unsigned()->default(0);
            $table->decimal('tax_total', 10, 2)->unsigned()->default(0);
            $table->decimal('discount_total', 10, 2)->unsigned()->default(0);
            $table->decimal('total', 10, 2)->unsigned()->default(0);

            $table->timestamp('issued_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
