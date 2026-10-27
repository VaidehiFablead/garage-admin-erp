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
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();

                $table->string('name');

                $table->decimal('price', 10, 2)->nullable();
                $table->decimal('discount_percent', 5, 2)->default(0.00);
                $table->decimal('final_price', 10, 2)->nullable();

                $table->string('duration')->nullable();

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->string('subtitle')->nullable();

                $table->integer('user_limit')->nullable();
                $table->integer('branch_limit')->nullable();
                $table->integer('storage_limit')->nullable();

                $table->boolean('is_active')->default(0);

                $table->unsignedBigInteger('sub_branch_id')->nullable();

                $table->longText('features')->nullable();

                $table->decimal('total_amount', 10, 2)->nullable();

                $table->timestamps();
            });
        } else {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'discount_percent')) {
                    $table->decimal('discount_percent', 5, 2)->default(0.00)->after('price');
                }
                if (!Schema::hasColumn('plans', 'final_price')) {
                    $table->decimal('final_price', 10, 2)->nullable()->after('discount_percent');
                }
                if (!Schema::hasColumn('plans', 'start_date')) {
                    $table->date('start_date')->nullable()->after('duration');
                }
                if (!Schema::hasColumn('plans', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('plans', 'features')) {
                    $table->longText('features')->nullable()->after('sub_branch_id');
                }
                if (!Schema::hasColumn('plans', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable()->after('features');
                }

                $table->decimal('price', 10, 2)->nullable()->change();
                $table->string('duration')->nullable()->change();
                $table->integer('user_limit')->nullable()->change();
                $table->integer('branch_limit')->nullable()->change();
                $table->integer('storage_limit')->nullable()->change();
                $table->boolean('is_active')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['discount_percent', 'final_price', 'start_date', 'end_date', 'features', 'total_amount'] as $column) {
                    if (Schema::hasColumn('plans', $column)) {
                        $columnsToDrop[] = $column;
                    }
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
