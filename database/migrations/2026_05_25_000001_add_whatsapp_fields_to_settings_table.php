<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'customer_whatsapp_message')) {
                $table->string('customer_whatsapp_message')->nullable()->default('on');
            }
            if (! Schema::hasColumn('settings', 'admin_whatsapp_message')) {
                $table->string('admin_whatsapp_message')->nullable()->default('on')->after('customer_whatsapp_message');
            }
            if (! Schema::hasColumn('settings', 'appointment_reminder_hours_before')) {
                $table->integer('appointment_reminder_hours_before')->nullable()->default(3)->after('admin_whatsapp_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_whatsapp_message',
                'admin_whatsapp_message',
                'appointment_reminder_hours_before',
            ]);
        });
    }
};
