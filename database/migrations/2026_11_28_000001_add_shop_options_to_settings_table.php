<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'cin_no')) {
                $table->string('cin_no')->nullable();
            }
            if (!Schema::hasColumn('settings', 'invoice_size')) {
                $table->string('invoice_size', 20)->nullable()->default('big');
            }
            if (!Schema::hasColumn('settings', 'send_mail')) {
                $table->boolean('send_mail')->default(true);
            }
            if (!Schema::hasColumn('settings', 'enable_variant_product')) {
                $table->boolean('enable_variant_product')->default(false);
            }
            if (!Schema::hasColumn('settings', 'default_language')) {
                $table->string('default_language', 10)->default('en');
            }
            if (!Schema::hasColumn('settings', 'enable_calculator')) {
                $table->boolean('enable_calculator')->default(false);
            }
            if (!Schema::hasColumn('settings', 'enable_multiple_price')) {
                $table->boolean('enable_multiple_price')->default(false);
            }
            if (!Schema::hasColumn('settings', 'signature')) {
                $table->string('signature')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'cin_no', 'invoice_size', 'send_mail', 'enable_variant_product',
                'default_language', 'enable_calculator', 'enable_multiple_price', 'signature',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
