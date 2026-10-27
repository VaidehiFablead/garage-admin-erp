<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // HR section master toggle
            $table->tinyInteger('show_hr_dashboard')->default(1);

            // HR subsection toggles
            $table->tinyInteger('show_hr_staff_strength')->default(1);
            $table->tinyInteger('show_hr_active_staff')->default(1);
            $table->tinyInteger('show_hr_monthly_attendance')->default(1);
            $table->tinyInteger('show_hr_personal_progress')->default(1);
            $table->tinyInteger('show_hr_attendance_pattern')->default(1);
            $table->tinyInteger('show_hr_salary_payroll_trend')->default(1);
            $table->tinyInteger('show_hr_payroll_snapshot')->default(1);
            $table->tinyInteger('show_hr_attendance_watch')->default(1);
            $table->tinyInteger('show_hr_payroll_status')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_hr_dashboard',
                'show_hr_staff_strength',
                'show_hr_active_staff',
                'show_hr_monthly_attendance',
                'show_hr_personal_progress',
                'show_hr_attendance_pattern',
                'show_hr_salary_payroll_trend',
                'show_hr_payroll_snapshot',
                'show_hr_attendance_watch',
                'show_hr_payroll_status',
            ]);
        });
    }
};
