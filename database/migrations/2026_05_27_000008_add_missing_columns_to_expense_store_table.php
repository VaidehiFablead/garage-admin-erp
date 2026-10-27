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
        Schema::table('expense_store', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_store', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('jobcard_id');
            }
            if (!Schema::hasColumn('expense_store', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_store', function (Blueprint $table) {
            $table->dropColumn(['vendor_name', 'payment_method']);
        });
    }
};
