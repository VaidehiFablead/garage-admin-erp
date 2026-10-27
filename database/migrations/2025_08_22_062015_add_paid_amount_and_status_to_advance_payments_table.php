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
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->default(0)->after('amount');
            $table->enum('status', ['pending', 'partial', 'cleared'])->default('pending')->after('paid_amount');
        });
    }

    public function down()
    {
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'status']);
        });
    }
};
