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
        Schema::create('department', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');

            // Overtime Settings
            $table->boolean('enable_overtime')->default(false);

            // fixed, multiplier, percentage (optional)
            $table->string('overtime_rate_type')->nullable();

            // Example: 1.5x, 2x
            $table->decimal('overtime_multiplier', 5, 2)->default(1.00);

            // Minimum overtime minutes required to count
            $table->integer('min_overtime_count_in_minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department');
    }
};
