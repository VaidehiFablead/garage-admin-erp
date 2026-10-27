<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->tinyInteger('show_erp_dashboard')->default(1)->after('monthly_leave_limit');
            $table->tinyInteger('show_erp_total_collections')->default(1)->after('show_erp_dashboard');
            $table->tinyInteger('show_erp_total_expense')->default(1)->after('show_erp_total_collections');
            $table->tinyInteger('show_erp_pending_payment')->default(1)->after('show_erp_total_expense');
            $table->tinyInteger('show_erp_job_card_bills')->default(1)->after('show_erp_pending_payment');
            $table->tinyInteger('show_erp_daily_register')->default(1)->after('show_erp_job_card_bills');
            $table->tinyInteger('show_erp_appointments')->default(1)->after('show_erp_daily_register');
            $table->tinyInteger('show_erp_attendance')->default(1)->after('show_erp_appointments');
            $table->tinyInteger('show_erp_job_card_chart')->default(1)->after('show_erp_attendance');
            $table->tinyInteger('show_erp_daily_data_chart')->default(1)->after('show_erp_job_card_chart');
            $table->tinyInteger('show_erp_latest_job_card')->default(1)->after('show_erp_daily_data_chart');
            $table->tinyInteger('show_erp_latest_daily_data')->default(1)->after('show_erp_latest_job_card');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_erp_dashboard',
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
            ]);
        });
    }
};
