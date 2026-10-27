<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // leave_type table
        if (!Schema::hasTable('leave_type')) {
            Schema::create('leave_type', function (Blueprint $table) {
                $table->id();
                $table->string('leave_type');
                $table->integer('number_of_leaves')->default(0);
                $table->boolean('allow_half_day')->default(false);
                $table->boolean('requires_approval')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // leaves table
        if (!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->id();
                $table->string('firstname')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('no_of_day')->default(0);
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('leave_id')->nullable(); // FK to leave_type
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('leave_id')->references('id')->on('leave_type')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('leave_type');
    }
};
