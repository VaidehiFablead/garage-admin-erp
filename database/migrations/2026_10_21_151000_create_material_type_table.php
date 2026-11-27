<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('material_type')) {
            Schema::create('material_type', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('material_id');
                $table->unsignedBigInteger('type_id');
                $table->timestamps();

                $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
                $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');

                $table->unique(['material_id', 'type_id']);
                $table->index('material_id');
                $table->index('type_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_type');
    }
};
