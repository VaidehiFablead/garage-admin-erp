<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
     public function up(): void
    {
        DB::table('modules')
            ->where('id', 23)
            ->update([
                'module' => 'Advance Pay',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('modules')
            ->where('id', 23)
            ->update([
                'module' => 'Sub Branch',
                'updated_at' => now(),
            ]);
    }
};
