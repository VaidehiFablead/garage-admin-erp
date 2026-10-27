<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_rules', function (Blueprint $table) {
            $table->id();

            // Payroll
            $table->boolean('enable_payroll')->default(false);
            $table->string('payroll_type')->nullable();
            $table->decimal('working_hours_per_day', 5, 2)->default(8.00);
            $table->boolean('include_holidays_in_working_days')->default(false);
            $table->decimal('half_day_hours', 5, 2)->nullable();

            // Sunday
            $table->boolean('sunday_off')->default(true);
            $table->string('sunday_pay_type')->nullable();

            // Saturday
            $table->boolean('saturday_off_enabled')->default(false);
            $table->string('saturday_off_type')->nullable();
            $table->string('saturday_off_pattern')->nullable();
            $table->boolean('saturday_half_day_enabled')->default(false);
            $table->string('saturday_half_day_pattern')->nullable();
            $table->string('saturday_pay_type')->nullable();

            // Holidays
            $table->integer('yearly_holidays')->default(0);

            // Tax
            $table->boolean('enable_tax')->default(false);
            $table->string('tax_type')->nullable();
            $table->decimal('tax', 5, 2)->default(0.00);
            $table->decimal('salary_above_tax', 10, 2)->default(0.00);

            // Shift timings
            $table->integer('lunch_break')->nullable()->comment('in minutes');
            $table->time('start_time')->nullable();
            $table->time('half_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('grace_period')->default(0)->comment('in minutes');

            // Overtime
            $table->boolean('enable_overtime')->default(false);
            $table->decimal('overtime_multiplier', 5, 2)->default(1.00);
            $table->string('overtime_rate_type')->nullable();
            $table->integer('min_overtime_count_in_minutes')->default(0);

            // PF
            $table->boolean('enable_pf')->default(false);
            $table->decimal('employee_pf', 5, 2)->default(0.00);
            $table->decimal('employer_pf', 5, 2)->default(0.00);

            // ESI
            $table->boolean('enable_esi')->default(false);
            $table->decimal('employee_esi', 5, 2)->default(0.00);
            $table->decimal('employer_esi', 5, 2)->default(0.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_rules');
    }
};
