<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
             DB::table('modules')
            ->whereIn('module', [
                'Sales & Orders',
                'Reports',
                'Quotations',
                'Sales Return',
                'Purchase Return',
                'Custom Invoice',
            ])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         $modules = [
            ['id' => 2,  'module' => 'Sales & Orders'],
            ['id' => 7,  'module' => 'Reports'],
            ['id' => 17, 'module' => 'Quotations'],
            ['id' => 27, 'module' => 'Sales Return'],
            ['id' => 28, 'module' => 'Purchase Return'],
            ['id' => 29, 'module' => 'Custom Invoice'],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['id' => $module['id']],
                [
                    'module' => $module['module'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
};
