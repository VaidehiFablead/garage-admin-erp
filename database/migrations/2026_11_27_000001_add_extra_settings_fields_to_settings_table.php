<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'terms_condition_eng')) {
                $table->longText('terms_condition_eng')->nullable();
            }
            if (!Schema::hasColumn('settings', 'terms_condition_guj')) {
                $table->longText('terms_condition_guj')->nullable();
            }
            if (!Schema::hasColumn('settings', 'bank_name_without_gst')) {
                $table->string('bank_name_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'branch_without_gst')) {
                $table->string('branch_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'ac_no_without_gst')) {
                $table->string('ac_no_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'ifsc_code_without_gst')) {
                $table->string('ifsc_code_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'qr_code_without_gst')) {
                $table->string('qr_code_without_gst')->nullable();
            }
            if (!Schema::hasColumn('settings', 'show_bank_details')) {
                $table->boolean('show_bank_details')->default(true);
            }
            if (!Schema::hasColumn('settings', 'show_qr_code')) {
                $table->boolean('show_qr_code')->default(true);
            }
            if (!Schema::hasColumn('settings', 'advance_receipt')) {
                $table->boolean('advance_receipt')->default(true);
            }
            if (!Schema::hasColumn('settings', 'rental')) {
                $table->boolean('rental')->default(true);
            }
            if (!Schema::hasColumn('settings', 'draft')) {
                $table->boolean('draft')->default(true);
            }
            if (!Schema::hasColumn('settings', 'financial_year')) {
                $table->boolean('financial_year')->default(true);
            }
            if (!Schema::hasColumn('settings', 'tds_apply')) {
                $table->boolean('tds_apply')->default(false);
            }
            if (!Schema::hasColumn('settings', 'exchange_apply')) {
                $table->boolean('exchange_apply')->default(false);
            }
            if (!Schema::hasColumn('settings', 'delivery_cost_apply')) {
                $table->boolean('delivery_cost_apply')->default(false);
            }
            if (!Schema::hasColumn('settings', 'other_charges_apply')) {
                $table->boolean('other_charges_apply')->default(false);
            }
            if (!Schema::hasColumn('settings', 'labour_apply')) {
                $table->boolean('labour_apply')->default(true);
            }
            if (!Schema::hasColumn('settings', 'remarks_apply')) {
                $table->boolean('remarks_apply')->default(true);
            }
            if (!Schema::hasColumn('settings', 'show_assign_staff')) {
                $table->boolean('show_assign_staff')->default(true);
            }
            if (!Schema::hasColumn('settings', 'show_order_type')) {
                $table->boolean('show_order_type')->default(true);
            }
            if (!Schema::hasColumn('settings', 'enable_manufacture')) {
                $table->boolean('enable_manufacture')->default(false);
            }
            if (!Schema::hasColumn('settings', 'sales_discount')) {
                $table->string('sales_discount')->default('all');
            }
            if (!Schema::hasColumn('settings', 'default_payment_method')) {
                $table->string('default_payment_method')->nullable();
            }
            if (!Schema::hasColumn('settings', 'payment_methods')) {
                $table->json('payment_methods')->nullable();
            }
            if (!Schema::hasColumn('settings', 'enable_event_calendar')) {
                $table->boolean('enable_event_calendar')->default(true)->nullable();
            }
            if (!Schema::hasColumn('settings', 'pos_layout')) {
                $table->string('pos_layout', 50)->default('layout1');
            }
            if (!Schema::hasColumn('settings', 'logo_width')) {
                $table->string('logo_width', 50)->nullable();
            }
            if (!Schema::hasColumn('settings', 'logo_height')) {
                $table->string('logo_height', 50)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'terms_condition_eng', 'terms_condition_guj',
                'bank_name_without_gst', 'branch_without_gst', 'ac_no_without_gst', 'ifsc_code_without_gst', 'qr_code_without_gst',
                'show_bank_details', 'show_qr_code',
                'advance_receipt', 'rental', 'draft', 'financial_year', 'tds_apply', 'exchange_apply',
                'delivery_cost_apply', 'other_charges_apply', 'labour_apply', 'remarks_apply',
                'show_assign_staff', 'show_order_type', 'enable_manufacture', 'sales_discount',
                'default_payment_method', 'payment_methods',
                'enable_event_calendar', 'pos_layout', 'logo_width', 'logo_height',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
