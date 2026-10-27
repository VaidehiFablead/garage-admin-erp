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
            $table->text('shop_address')->nullable();
            $table->string('office_latitude')->nullable();
            $table->string('office_longitude')->nullable();
            $table->integer('radius')->default(200);
            $table->boolean('location_check_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'shop_address',
                'office_latitude',
                'office_longitude',
                'radius',
                'location_check_enabled'
            ]);
        });
    }
};
