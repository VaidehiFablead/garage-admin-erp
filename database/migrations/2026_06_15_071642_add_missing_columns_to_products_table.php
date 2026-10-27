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
        Schema::table('products', function (Blueprint $table) {
             $table->integer('created_by')->nullable()->after('branch_id');

            $table->integer('unit_id')->nullable()->after('quantity');

            $table->string('hsn_code')->nullable()->after('unit_id');

            $table->string('gst_option')->nullable()->after('hsn_code');

            $table->text('product_gst')->nullable()->after('gst_option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'created_by',
                'unit_id',
                'hsn_code',
                'gst_option',
                'product_gst',
            ]);
        });
    }
};
