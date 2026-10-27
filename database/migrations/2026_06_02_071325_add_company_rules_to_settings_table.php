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
            $table->string('working_hours')->nullable();
            $table->string('sunday_off')->default('no')->nullable();
            $table->string('grace_period')->nullable();
            $table->string('lunch_break')->nullable();
            $table->string('open_time')->nullable();
            $table->string('close_time')->nullable();
            $table->float('overtime_after_hours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'working_hours',
                'sunday_off',
                'grace_period',
                'lunch_break',
                'open_time',
                'close_time',
                'overtime_after_hours'
            ]);
        });
    }
};
