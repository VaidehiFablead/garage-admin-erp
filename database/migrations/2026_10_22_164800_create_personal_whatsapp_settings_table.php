<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('personal_whatsapp_settings')) {
            return;
        }

        Schema::create('personal_whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unique();
            $table->string('send_method', 20)->default('api');
            $table->string('instance_id')->nullable();
            $table->text('api_token')->nullable();
            $table->string('api_url')->nullable();
            $table->string('media_url')->nullable();
            $table->string('connected_phone')->nullable();
            $table->text('message_template')->nullable();
            $table->boolean('send_to_party')->default(true);
            $table->boolean('copy_to_self')->default(false);
            $table->boolean('attach_pdf')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_whatsapp_settings');
    }
};
