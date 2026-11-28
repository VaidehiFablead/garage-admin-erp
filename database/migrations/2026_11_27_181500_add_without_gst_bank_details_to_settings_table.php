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
            if (!Schema::hasColumn('settings', 'bank_name_without_gst')) {
                $table->string('bank_name_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'branch_without_gst')) {
                $table->string('branch_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'ac_no_without_gst')) {
                $table->string('ac_no_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'ifsc_code_without_gst')) {
                $table->string('ifsc_code_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'qr_code_without_gst')) {
                $table->string('qr_code_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'show_bank_details')) {
                $table->boolean('show_bank_details')->default(true);
            }
            if (!Schema::hasColumn('settings', 'show_qr_code')) {
                $table->boolean('show_qr_code')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'bank_name_without_gst',
                'branch_without_gst',
                'ac_no_without_gst',
                'ifsc_code_without_gst',
                'qr_code_without_gst',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
