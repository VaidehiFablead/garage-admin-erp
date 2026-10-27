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
        Schema::table('auto_car_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('auto_car_categories', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_car_categories', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
