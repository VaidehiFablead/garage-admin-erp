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
        Schema::create('advance_payments', function (Blueprint $table) {
          $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('method'); // e.g., Cash, Bank Transfer
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};