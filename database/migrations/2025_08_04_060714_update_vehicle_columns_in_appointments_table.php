<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVehicleColumnsInAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Convert string to integer — only works safely if the table is empty or handled carefully
            $table->unsignedBigInteger('vehicle_model')->nullable()->change();
            $table->unsignedBigInteger('vehicle_brand')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('vehicle_model')->nullable()->change();
            $table->string('vehicle_brand')->nullable()->change();
        });
    }
}

