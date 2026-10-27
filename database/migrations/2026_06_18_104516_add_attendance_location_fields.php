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
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('checkin_method')->nullable()->after('status');

            $table->string('check_in_location_name')->nullable()->after('late_minutes');
            $table->string('check_in_location')->nullable()->after('check_in_location_name');
            $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in_location');
            $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');

            $table->string('check_out_location_name')->nullable()->after('check_in_longitude');
            $table->string('check_out_location')->nullable()->after('check_out_location_name');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_location');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
             $table->dropColumn([
                'checkin_method',
                'check_in_location_name',
                'check_in_location',
                'check_in_latitude',
                'check_in_longitude',
                'check_out_location_name',
                'check_out_location',
                'check_out_latitude',
                'check_out_longitude',
            ]);
        });
    }
};
