<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('attendance', 'check_in_distance')) {
    Schema::table('attendance', function (Blueprint $table) {
        $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_in_longitude');
        $table->decimal('check_out_distance', 8, 2)->nullable()->after('check_out_longitude');
    });
    echo "Columns added successfully.\n";
} else {
    echo "Columns already exist.\n";
}
