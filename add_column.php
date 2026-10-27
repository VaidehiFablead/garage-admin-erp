<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('settings', 'saturday_off')) {
    Schema::table('settings', function (Blueprint $table) {
        $table->enum('saturday_off', ['yes', 'no'])->default('no')->after('sunday_off');
    });
    echo "Added saturday_off column to settings table.\n";
} else {
    echo "Column already exists.\n";
}
