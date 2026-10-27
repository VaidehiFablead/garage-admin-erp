<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToExpenseTypesTable extends Migration
{
    public function up()
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id'); // without foreign key
        });
    }

    public function down()
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
