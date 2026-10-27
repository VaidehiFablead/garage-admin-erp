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
        Schema::create('purchases_autocare', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->string('vendor_phone', 20)->nullable();
            $table->string('bill_no', 50)->nullable();
            $table->string('invoice_no', 50)->nullable();

            $table->decimal('payable_amount', 10, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'online', 'cashonline'])->nullable();
            $table->enum('paid_type', ['full', 'partial'])->nullable();

            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('upi_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);

            $table->text('comment')->nullable();

            $table->unsignedBigInteger('sub_admin_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_autocare');
    }
};
