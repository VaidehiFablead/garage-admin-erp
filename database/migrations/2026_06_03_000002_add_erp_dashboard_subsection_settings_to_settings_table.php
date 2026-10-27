<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'show_erp_total_collections',
            'show_erp_total_expense',
            'show_erp_pending_payment',
            'show_erp_job_card_bills',
            'show_erp_daily_register',
            'show_erp_appointments',
            'show_erp_attendance',
            'show_erp_job_card_chart',
            'show_erp_daily_data_chart',
            'show_erp_latest_job_card',
            'show_erp_latest_daily_data',
        ];

        foreach ($columns as $column) {
            if (! Schema::hasColumn('settings', $column)) {
                Schema::table('settings', function (Blueprint $table) use ($column) {
                    $table->tinyInteger($column)->default(1);
                });
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'show_erp_total_collections',
            'show_erp_total_expense',
            'show_erp_pending_payment',
            'show_erp_job_card_bills',
            'show_erp_daily_register',
            'show_erp_appointments',
            'show_erp_attendance',
            'show_erp_job_card_chart',
            'show_erp_daily_data_chart',
            'show_erp_latest_job_card',
            'show_erp_latest_daily_data',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('settings', $column)) {
                Schema::table('settings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
