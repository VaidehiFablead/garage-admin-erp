<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['id' => 1,  'module' => 'Items'],
            // ['id' => 2,  'module' => 'Sales & Orders'],
            ['id' => 3,  'module' => 'Purchase'],
            ['id' => 4,  'module' => 'Customers'],
            ['id' => 5,  'module' => 'Vendors / Suppliers'],
            ['id' => 6,  'module' => 'Expenses'],
            // ['id' => 7,  'module' => 'Reports'],
            ['id' => 8,  'module' => 'Staff'],
            ['id' => 9,  'module' => 'Attendance'],
            ['id' => 10, 'module' => 'Payroll'],
            ['id' => 11, 'module' => 'Leave'],
            ['id' => 12, 'module' => 'Appointments'],
            ['id' => 13, 'module' => 'Account Ledger'],
            ['id' => 14, 'module' => 'Settings'],
            ['id' => 15, 'module' => 'Inventory'],
            ['id' => 16, 'module' => 'Job Cards'],
            // ['id' => 17, 'module' => 'Quotations'],
            ['id' => 18, 'module' => 'Categories'],
            ['id' => 19, 'module' => 'Brands'],
            ['id' => 20, 'module' => 'Labour Services'],
            ['id' => 21, 'module' => 'Service Category'],
            ['id' => 22, 'module' => 'Make & Model'],
            ['id' => 23, 'module' => 'Advance Pay'],
            ['id' => 24, 'module' => 'Daily Register'],
            ['id' => 25, 'module' => 'Credit Notes'],
            ['id' => 26, 'module' => 'Debit Notes'],
            // ['id' => 27, 'module' => 'Sales Return'],
            // ['id' => 28, 'module' => 'Purchase Return'],
            // ['id' => 29, 'module' => 'Custom Invoice'],
            ['id' => 30, 'module' => 'GST Reports'],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['id' => $module['id']],
                [
                    'module' => $module['module'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
