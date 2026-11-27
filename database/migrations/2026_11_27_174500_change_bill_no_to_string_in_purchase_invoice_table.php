<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_invoice') && Schema::hasColumn('purchase_invoice', 'bill_no')) {
            try {
                DB::statement("ALTER TABLE `purchase_invoice` MODIFY `bill_no` VARCHAR(255) NULL");
            } catch (\Throwable $e) {
                // Ignore if already string or unsupported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
