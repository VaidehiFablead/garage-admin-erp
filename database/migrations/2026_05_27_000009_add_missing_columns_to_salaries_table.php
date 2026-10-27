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
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'month')) {
                $table->integer('month')->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('salaries', 'year')) {
                $table->integer('year')->nullable()->after('month');
            }
            if (!Schema::hasColumn('salaries', 'old_advance_pay')) {
                $table->decimal('old_advance_pay', 8, 2)->default(0)->after('total_salary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['month', 'year', 'old_advance_pay']);
        });
    }
};
