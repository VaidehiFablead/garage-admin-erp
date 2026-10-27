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
            $table->integer('late_mark_after_minutes')->nullable();
            $table->float('half_day_after_hours')->nullable();
            $table->float('auto_absent_after_hours')->nullable();
            $table->string('allow_early_checkout')->default('no')->nullable();
            $table->string('require_attendance_photo')->default('no')->nullable();
            $table->string('require_gps_location_for_attendance')->default('no')->nullable();
            $table->string('allow_overtime_approval')->default('no')->nullable();
            $table->float('max_overtime_hours_per_day')->nullable();
            $table->string('weekly_off_day')->nullable();
            $table->integer('monthly_leave_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'late_mark_after_minutes',
                'half_day_after_hours',
                'auto_absent_after_hours',
                'allow_early_checkout',
                'require_attendance_photo',
                'require_gps_location_for_attendance',
                'allow_overtime_approval',
                'max_overtime_hours_per_day',
                'weekly_off_day',
                'monthly_leave_limit'
            ]);
        });
    }
};
