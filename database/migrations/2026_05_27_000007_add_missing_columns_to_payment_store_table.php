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
            if (!Schema::hasColumn('payment_store', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('jobcard_id');
            }
            if (!Schema::hasColumn('payment_store', 'purchase_id')) {
                $table->unsignedBigInteger('purchase_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('payment_store', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payment_store', 'cash_amount')) {
                $table->decimal('cash_amount', 12, 2)->default(0)->after('payment_type');
            }
            if (!Schema::hasColumn('payment_store', 'upi_amount')) {
                $table->decimal('upi_amount', 12, 2)->default(0)->after('cash_amount');
            }
            if (!Schema::hasColumn('payment_store', 'emi_month')) {
                $table->integer('emi_month')->nullable()->after('upi_amount');
            }
            if (!Schema::hasColumn('payment_store', 'remaining_amount')) {
                $table->decimal('remaining_amount', 12, 2)->default(0)->after('emi_month');
            }
            if (!Schema::hasColumn('payment_store', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('remaining_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_store', function (Blueprint $table) {
            $table->dropColumn([
                'order_id',
                'purchase_id',
                'payment_type',
                'cash_amount',
                'upi_amount',
                'emi_month',
                'remaining_amount',
                'discount_amount',
            ]);
        });
    }
};
