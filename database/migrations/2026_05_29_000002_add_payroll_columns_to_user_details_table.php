<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('user_details', 'salary')) {
                $table->decimal('salary', 10, 2)->default(0)->after('country');
            }
            if (!Schema::hasColumn('user_details', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('salary');
            }
            if (!Schema::hasColumn('user_details', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('designation_id');
            }
            if (!Schema::hasColumn('user_details', 'face_photo')) {
                $table->string('face_photo')->nullable()->after('department_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['salary', 'designation_id', 'department_id', 'face_photo']);
        });
    }
};
