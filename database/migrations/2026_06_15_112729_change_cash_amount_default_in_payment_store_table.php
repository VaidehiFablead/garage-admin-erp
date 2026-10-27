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
        Schema::table('payment_store', function (Blueprint $table) {
            $table->decimal('cash_amount', 12, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_store', function (Blueprint $table) {
            $table->decimal('cash_amount', 12, 2)->default(0.00)->nullable(false)->change();
        });
    }
};
