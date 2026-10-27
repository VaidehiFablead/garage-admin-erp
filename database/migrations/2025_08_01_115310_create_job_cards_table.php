<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('model')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('km_hours')->nullable();
            $table->string('due_km_hours')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_card_number')->nullable();
            $table->string('category')->nullable();
            $table->string('technician')->nullable();
            $table->date('date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('labour_details')->nullable();
            $table->json('labours_gst')->nullable();
            $table->text('parts_details')->nullable();
            $table->decimal('total_labour', 10, 2)->nullable();
            $table->decimal('total_parts', 10, 2)->nullable();
            $table->decimal('total_subtotal_amount', 10, 2)->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_printable')->default(0)->nullable(); // Explicitly nullable for consistency
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('subbranch_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('online_amount', 10, 2)->nullable();
            $table->decimal('cash_amount', 10, 2)->nullable();
            $table->integer('emi_months')->nullable();
            $table->string('emi_duration')->nullable();
            $table->string('order_number')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->enum('type', ['jobCard', 'dailyData'])->nullable();
            $table->date('record_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('status')->nullable();
            $table->string('gst_no')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_cards');
    }
};
