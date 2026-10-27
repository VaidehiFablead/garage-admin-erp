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
        Schema::table('settings', function (Blueprint $table) {
            
            $table->string('bank_name')->nullable()->after('address');
            $table->string('branch')->nullable()->after('bank_name');
            $table->string('ac_no')->nullable()->after('branch');
            $table->string('ifsc_code')->nullable()->after('ac_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'branch',
                'ac_no',
                'ifsc_code',
            ]);
        });
    }
};
