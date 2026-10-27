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
            if (!Schema::hasColumn('payment_store', 'remark')) {
                $table->text('remark')->nullable()->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_store', function (Blueprint $table) {
            if (Schema::hasColumn('payment_store', 'remark')) {
                $table->dropColumn('remark');
            }
        });
    }
};
