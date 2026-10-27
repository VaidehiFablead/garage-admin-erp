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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'car_model')) {
                $table->unsignedBigInteger('car_model')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'vehicle_no')) {
                $table->string('vehicle_no')->nullable()->after('car_model');
            }
            if (!Schema::hasColumn('orders', 'user_type')) {
                $table->string('user_type')->nullable()->after('vehicle_no');
            }
            if (!Schema::hasColumn('orders', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'cash_amount')) {
                $table->decimal('cash_amount', 10, 2)->default(0)->after('payment_type');
            }
            if (!Schema::hasColumn('orders', 'online_amount')) {
                $table->decimal('online_amount', 10, 2)->default(0)->after('cash_amount');
            }
            if (!Schema::hasColumn('orders', 'emi_duration')) {
                $table->string('emi_duration')->nullable()->after('online_amount');
            }
            if (!Schema::hasColumn('orders', 'emi_amount')) {
                $table->decimal('emi_amount', 10, 2)->default(0)->after('emi_duration');
            }
            if (!Schema::hasColumn('orders', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('emi_amount');
            }
            if (!Schema::hasColumn('orders', 'type')) {
                $table->string('type')->nullable()->after('remaining_amount');
            }
            if (!Schema::hasColumn('orders', 'remark')) {
                $table->text('remark')->nullable()->after('type');
            }
            if (!Schema::hasColumn('orders', 'gst_option')) {
                $table->string('gst_option')->nullable()->after('remark');
            }
            if (!Schema::hasColumn('orders', 'paid_by_date')) {
                $table->date('paid_by_date')->nullable()->after('gst_option');
            }
            if (!Schema::hasColumn('orders', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('paid_by_date');
            }
            if (!Schema::hasColumn('orders', 'date')) {
                $table->date('date')->nullable()->after('staff_id');
            }
            if (!Schema::hasColumn('orders', 'next_warranty_date')) {
                $table->date('next_warranty_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('orders', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('next_warranty_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'car_model',
                'vehicle_no',
                'user_type',
                'payment_type',
                'cash_amount',
                'online_amount',
                'emi_duration',
                'emi_amount',
                'remaining_amount',
                'type',
                'remark',
                'gst_option',
                'paid_by_date',
                'staff_id',
                'date',
                'next_warranty_date',
                'order_id',
            ]);
        });
    }
};
