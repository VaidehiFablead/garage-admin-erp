<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_message_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facebook_app_configuration_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('template_id')->unique();
            $table->string('name');
            $table->string('status');
            $table->enum('on_off', ['active', 'inactive'])->default('inactive');
            $table->string('use_for_template')->nullable();
            $table->string('language')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->text('components')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('facebook_app_configuration_id')
                  ->references('id')->on('facebook_app_configurations')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['branch_id', 'isDeleted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_message_templates');
    }
};
