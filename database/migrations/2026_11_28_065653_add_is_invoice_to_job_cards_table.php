<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('job_cards', 'is_invoice')) {
            Schema::table('job_cards', function (Blueprint $table) {
                $table->boolean('is_invoice')->default(false)->after('quotation_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_cards', 'is_invoice')) {
            Schema::table('job_cards', function (Blueprint $table) {
                $table->dropColumn('is_invoice');
            });
        }
    }
};
