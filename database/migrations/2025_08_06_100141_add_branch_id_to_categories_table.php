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
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id'); // or after any column you prefer

            // Optional: Add foreign key constraint if there's a branches table
            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // $table->dropForeign(['branch_id']); // uncomment if you added foreign key
            $table->dropColumn('branch_id');
        });
    }
};
