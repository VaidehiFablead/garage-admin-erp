<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $branchId = $admin ? $admin->id : 1;

        Setting::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'branch_id'                            => $branchId,
                'name'                                 => 'AutoCare Garage & Service Center',
                'email'                                => 'support@autocaregarage.com',
                'phone'                                => '9825001122',
                'alt_phone'                            => '9825003344',
                'gst_num'                              => '24ABCDE1234F1Z5',
                'state_code'                           => '24',
                'address'                              => 'Plot No. 12-15, Near Ring Road, GIDC Industrial Area',
                'shop_address'                         => 'Plot No. 12-15, Near Ring Road, GIDC Industrial Area, Surat, Gujarat - 395002',
                'bank_name'                            => 'State Bank of India',
                'branch'                               => 'Ring Road Branch, Surat',
                'ac_no'                                => '384920194820',
                'ifsc_code'                            => 'SBIN0001234',
                'currency_symbol'                      => '₹',
                'currency_position'                    => 'left',
                'low_stock'                            => 10,
                'logo'                                 => null,
                'favicon'                              => null,
                'qr_code'                              => null,

                // Location / GPS Settings
                'office_latitude'                      => 21.1702,
                'office_longitude'                     => 72.8311,
                'radius'                               => 500,
                'location_check_enabled'               => false,

                // Work & Attendance Rules
                'open_time'                            => '09:00:00',
                'close_time'                           => '19:00:00',
                'working_hours'                        => '9',
                'grace_period'                         => '15',
                'lunch_break'                          => '60',
                'late_mark_after_minutes'              => 15,
                'half_day_after_hours'                 => 4.0,
                'auto_absent_after_hours'              => 6.0,
                'overtime_after_hours'                 => 9.0,
                'allow_overtime_approval'              => 'yes',
                'max_overtime_hours_per_day'           => 4.0,
                'allow_early_checkout'                 => 'no',
                'require_attendance_photo'             => 'no',
                'require_gps_location_for_attendance'  => 'no',
                'sunday_off'                           => 'yes',
                'saturday_off'                         => 'no',
                'weekly_off_day'                       => 'Sunday',
                'monthly_leave_limit'                  => 2,
                'tax_deduction_amount'                 => 0.00,
                'salary_exceeds'                       => 0.00,

                // ERP Dashboard Settings
                'show_erp_dashboard'                   => 1,
                'show_erp_total_collections'           => 1,
                'show_erp_total_expense'               => 1,
                'show_erp_pending_payment'             => 1,
                'show_erp_job_card_bills'              => 1,
                'show_erp_daily_register'              => 1,
                'show_erp_appointments'                => 1,
                'show_erp_attendance'                  => 1,
                'show_erp_job_card_chart'              => 1,
                'show_erp_daily_data_chart'            => 1,
                'show_erp_latest_job_card'             => 1,
                'show_erp_latest_daily_data'           => 1,

                // HR Dashboard Settings
                'show_hr_dashboard'                    => 1,
                'show_hr_staff_strength'               => 1,
                'show_hr_active_staff'                 => 1,
                'show_hr_monthly_attendance'           => 1,
                'show_hr_personal_progress'            => 1,
                'show_hr_attendance_pattern'           => 1,
                'show_hr_salary_payroll_trend'         => 1,
                'show_hr_payroll_snapshot'             => 1,
                'show_hr_attendance_watch'             => 1,
                'show_hr_payroll_status'               => 1,
            ]
        );
    }
}
