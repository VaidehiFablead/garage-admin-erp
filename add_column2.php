<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('settings', function (Blueprint $table) {
    if (!Schema::hasColumn('settings', 'late_mark_after_minutes')) $table->integer('late_mark_after_minutes')->nullable();
    if (!Schema::hasColumn('settings', 'half_day_after_hours')) $table->float('half_day_after_hours')->nullable();
    if (!Schema::hasColumn('settings', 'auto_absent_after_hours')) $table->float('auto_absent_after_hours')->nullable();
    if (!Schema::hasColumn('settings', 'allow_early_checkout')) $table->string('allow_early_checkout')->default('no')->nullable();
    if (!Schema::hasColumn('settings', 'require_attendance_photo')) $table->string('require_attendance_photo')->default('no')->nullable();
    if (!Schema::hasColumn('settings', 'require_gps_location_for_attendance')) $table->string('require_gps_location_for_attendance')->default('no')->nullable();
    if (!Schema::hasColumn('settings', 'allow_overtime_approval')) $table->string('allow_overtime_approval')->default('no')->nullable();
    if (!Schema::hasColumn('settings', 'max_overtime_hours_per_day')) $table->float('max_overtime_hours_per_day')->nullable();
    if (!Schema::hasColumn('settings', 'weekly_off_day')) $table->string('weekly_off_day')->nullable();
    if (!Schema::hasColumn('settings', 'monthly_leave_limit')) $table->integer('monthly_leave_limit')->nullable();
});

echo "Added all new policy columns to settings table.\n";
