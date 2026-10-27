<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'alt_phone',
        'state_code',
        'gst_num',
        'low_stock',
        'address',
        'bank_name',
        'branch',
        'ac_no',
        'ifsc_code',
        'currency_position',
        'currency_symbol',
        'logo',
        'favicon',
        'qr_code',
        'office_latitude',
        'office_longitude',
        'radius',
        'location_check_enabled',
        'working_hours',
        'sunday_off',
        'saturday_off', // added for Saturday off toggle
        'grace_period',
        'lunch_break',
        'open_time',
        'close_time',
        'overtime_after_hours',
        'late_mark_after_minutes',
        'half_day_after_hours',
        'auto_absent_after_hours',
        'allow_early_checkout',
        'require_attendance_photo',
        'require_gps_location_for_attendance',
        'allow_overtime_approval',
        'max_overtime_hours_per_day',
        'weekly_off_day',
        'monthly_leave_limit',
        'tax_deduction_amount',
        'salary_exceeds',
        // ERP Dashboard visibility
        'show_erp_dashboard',
        'show_erp_total_collections',
        'show_erp_total_expense',
        'show_erp_pending_payment',
        'show_erp_job_card_bills',
        'show_erp_daily_register',
        'show_erp_appointments',
        'show_erp_attendance',
        'show_erp_job_card_chart',
        'show_erp_daily_data_chart',
        'show_erp_latest_job_card',
        'show_erp_latest_daily_data',
        // HR Dashboard visibility
        'show_hr_dashboard',
        'show_hr_staff_strength',
        'show_hr_active_staff',
        'show_hr_monthly_attendance',
        'show_hr_personal_progress',
        'show_hr_attendance_pattern',
        'show_hr_salary_payroll_trend',
        'show_hr_payroll_snapshot',
        'show_hr_attendance_watch',
        'show_hr_payroll_status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'location_check_enabled' => 'boolean',
        'office_latitude'        => 'float',
        'office_longitude'       => 'float',
        'radius'                 => 'integer',
        'overtime_after_hours'   => 'float',
    ];
}
