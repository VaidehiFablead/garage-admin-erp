<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('branch_id');
            $table->integer('present');
            $table->integer('absent');
            $table->integer('extra_present');
            $table->decimal('advance_pay', 8, 2);
            $table->decimal('salary', 8, 2);
            $table->decimal('extra_amount', 8, 2);
            $table->decimal('total_salary', 8, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
