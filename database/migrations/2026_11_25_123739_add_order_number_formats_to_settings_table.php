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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'with_gst_format')) {
                $table->string('with_gst_format')->nullable()->default('INV-{YYYY}-{0000}');
            }
            if (!Schema::hasColumn('settings', 'rental_format')) {
                $table->string('rental_format')->nullable()->default('R-{0000}');
            }
            if (!Schema::hasColumn('settings', 'advance_payment_format')) {
                $table->string('advance_payment_format')->nullable()->default('ADV-{0000}');
            }
            if (!Schema::hasColumn('settings', 'quotation_format')) {
                $table->string('quotation_format')->nullable()->default('Q-{YYYY}-{0000}');
            }
            if (!Schema::hasColumn('settings', 'quotation_with_gst_format')) {
                $table->string('quotation_with_gst_format')->nullable()->default('QGST-{YYYY}-{0000}');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [];
            foreach (['with_gst_format', 'rental_format', 'advance_payment_format', 'quotation_format', 'quotation_with_gst_format'] as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
