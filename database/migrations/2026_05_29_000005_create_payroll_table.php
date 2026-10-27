<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payroll')) {
            Schema::create('payroll', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('leave_type')->nullable();  // FK to leave_type
                $table->string('month_year');                          // format: Y-m  e.g. 2026-05

                // Leave tracking
                $table->float('total_leaves')->default(0);
                $table->float('total_half_day')->default(0);
                $table->float('total_paid_leaves')->default(0);
                $table->float('used_paid_leaves')->default(0);
                $table->float('remaining_paid_leaves')->default(0);

                // Salary
                $table->decimal('salary_amount', 10, 2)->default(0);
                $table->decimal('tax_deduction', 10, 2)->default(0);
                $table->decimal('salary_deduction', 10, 2)->default(0);
                $table->decimal('bonuses', 10, 2)->default(0);        // used for advance deduction
                $table->decimal('net_salary', 10, 2)->default(0);

                // Bank details
                $table->string('acc_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('acc_in_name')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('branch_code')->nullable();

                // Payment
                $table->date('payment_date')->nullable();
                $table->string('payment_status')->default('paid');    // paid, pending, etc.

                // Hours
                $table->float('worked_hours')->default(0);
                $table->float('overtime_pay')->default(0);
                $table->float('total_overtime_hours')->default(0);

                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('leave_type')->references('id')->on('leave_type')->onDelete('set null');

                // Prevent duplicate payroll for same employee + month
                $table->unique(['user_id', 'month_year']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
