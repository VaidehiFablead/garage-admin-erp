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
            if (! Schema::hasColumn('settings', 'barcode_type')) {
                $table->string('barcode_type', 50)->default('barcode')->nullable();
            }
            if (! Schema::hasColumn('settings', 'barcode_setting_type')) {
                $table->string('barcode_setting_type', 50)->default('simple')->nullable();
            }
            if (! Schema::hasColumn('settings', 'barcode_detail_option')) {
                $table->string('barcode_detail_option', 50)->default('long')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'barcode_detail_option')) {
                $table->dropColumn('barcode_detail_option');
            }
            if (Schema::hasColumn('settings', 'barcode_setting_type')) {
                $table->dropColumn('barcode_setting_type');
            }
            if (Schema::hasColumn('settings', 'barcode_type')) {
                $table->dropColumn('barcode_type');
            }
        });
    }
};
