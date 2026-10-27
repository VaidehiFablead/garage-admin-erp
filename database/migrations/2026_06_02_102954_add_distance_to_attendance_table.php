<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::table('attendance', function (Blueprint $table) {
//             $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_in_longitude');
//             $table->decimal('check_out_distance', 8, 2)->nullable()->after('check_out_longitude');
//         });
//     }

//     /** 
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::table('attendance', function (Blueprint $table) {
//             $table->dropColumn(['check_in_distance', 'check_out_distance']);
//         });
//     }
// };
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->decimal('check_in_distance', 8, 2)->nullable();
            $table->decimal('check_out_distance', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['check_in_distance', 'check_out_distance']);
        });
    }
};
