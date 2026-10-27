<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('user_details', 'joining_date')) {
                $table->date('joining_date')->nullable()->after('face_photo');
            }
            if (!Schema::hasColumn('user_details', 'shift_time')) {
                $table->string('shift_time')->nullable()->after('joining_date');
            }
            if (!Schema::hasColumn('user_details', 'working_location')) {
                $table->string('working_location')->nullable()->after('shift_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['joining_date', 'shift_time', 'working_location']);
        });
    }
};
