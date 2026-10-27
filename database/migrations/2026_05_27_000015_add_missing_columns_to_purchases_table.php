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
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'product_gst_details')) {
                $table->json('product_gst_details')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('purchases', 'product_gst_total')) {
                $table->decimal('product_gst_total', 10, 2)->default(0)->after('product_gst_details');
            }
            if (!Schema::hasColumn('purchases', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('amount_total');
            }
            if (!Schema::hasColumn('purchases', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchases', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('invoice_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'product_gst_details',
                'product_gst_total',
                'discount_amount',
                'discount_percent',
                'created_by',
            ]);
        });
    }
};
