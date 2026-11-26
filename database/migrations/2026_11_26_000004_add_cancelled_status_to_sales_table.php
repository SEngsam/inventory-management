<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The sale form offers a "cancelled" status, but the column only allowed
 * "completed" and "pending", so cancelling a sale failed on MySQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['completed', 'pending'])->default('completed')->change();
        });
    }
};
