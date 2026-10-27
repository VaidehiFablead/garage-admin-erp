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
        Schema::table('job_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('job_cards', 'bill_no')) {
                $table->string('bill_no')->nullable()->after('status');
            }
            if (!Schema::hasColumn('job_cards', 'next_pending_date')) {
                $table->date('next_pending_date')->nullable()->after('bill_no');
            }
            if (!Schema::hasColumn('job_cards', 'labour_discount')) {
                $table->decimal('labour_discount', 10, 2)->nullable()->after('next_pending_date');
            }
            if (!Schema::hasColumn('job_cards', 'parts_discount')) {
                $table->decimal('parts_discount', 10, 2)->nullable()->after('labour_discount');
            }
            if (!Schema::hasColumn('job_cards', 'isDeleted')) {
                $table->boolean('isDeleted')->default(0)->after('parts_discount');
            }
            if (!Schema::hasColumn('job_cards', 'gst')) {
                $table->string('gst')->nullable()->after('isDeleted');
            }
            if (!Schema::hasColumn('job_cards', 'old_job_card')) {
                $table->string('old_job_card')->nullable()->after('gst');
            }
            if (!Schema::hasColumn('job_cards', 'extra_pay')) {
                $table->decimal('extra_pay', 10, 2)->nullable()->after('old_job_card');
            }
            if (!Schema::hasColumn('job_cards', 'remark_non_printable')) {
                $table->text('remark_non_printable')->nullable()->after('remark');
            }
            if (!Schema::hasColumn('job_cards', 'parts_gst')) {
                $table->json('parts_gst')->nullable()->after('parts_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn([
                'bill_no',
                'next_pending_date',
                'labour_discount',
                'parts_discount',
                'isDeleted',
                'gst',
                'old_job_card',
                'extra_pay',
                'remark_non_printable',
                'parts_gst',
            ]);
        });
    }
};
