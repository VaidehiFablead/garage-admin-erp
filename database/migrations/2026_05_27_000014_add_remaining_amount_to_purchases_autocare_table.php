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
        Schema::table('purchases_autocare', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases_autocare', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases_autocare', function (Blueprint $table) {
            $table->dropColumn('remaining_amount');
        });
    }
};
