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
        Schema::table('labour_services', function (Blueprint $table) {
            if (!Schema::hasColumn('labour_services', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labour_services', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
