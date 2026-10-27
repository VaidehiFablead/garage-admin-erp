<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{
    
    public function show(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        // Default: take branch from logged-in user
        $branchId = $authUser->id;

        // If Admin and selectedSubAdminId is passed, override
        if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId') && $request->selectedSubAdminId !== 'null') {
            $branchId = (int) $request->selectedSubAdminId;
        }

        // Fetch settings for that branch
        $settings = Setting::where('branch_id', $branchId)->first();

        return response()->json([
            'settings' => $settings,
            'branch_id' => $branchId
        ]);
    }

    public function update(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        // Branch logic
        $branchId = $authUser->id;
        if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId') && $request->selectedSubAdminId !== 'null') {
            $branchId = (int) $request->selectedSubAdminId;
        }
        // dd($branchId);  // Debugging: Check the branch ID

        $request->validate([
            'shop_name' => 'required|string',
            'gst_num' => 'nullable',
            'state_code' => 'nullable|string',
            'low_stock' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required',
            'alt_phone' => 'nullable',
            'address' => 'required|string',
            'bank_name' => 'required|string',
            'branch' => 'required|string',
            'ac_no' => 'required|string',
            'ifsc_code' => 'required|string',
            'currency_symbol' => 'required|string',
            'currency_position' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ]);

        // Find branch-specific settings
        $settings = Setting::where('branch_id', $branchId)->first();
        if (!$settings) {
            $settings = new Setting();
            $settings->branch_id = $branchId;
        }

        // Assign values
        $settings->gst_num = $request->gst_num;
        $settings->state_code = $request->state_code;
        $settings->low_stock = $request->low_stock;
        $settings->name = $request->shop_name;
        $settings->email = $request->email;
        $settings->phone = $request->phone;
        $settings->alt_phone = $request->alt_phone;
        $settings->address = $request->address;
        $settings->bank_name = $request->bank_name;
        $settings->branch = $request->branch;
        $settings->ac_no = $request->ac_no;
        $settings->ifsc_code = $request->ifsc_code;
        $settings->currency_position = $request->currency_position;
        $settings->currency_symbol = $request->currency_symbol;

        // File uploads
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            $settings->favicon = $faviconPath;
        }
        if ($request->hasFile('qr_code')) {
            $qrCodePath = $request->file('qr_code')->store('qr_codes', 'public');
            $settings->qr_code = $qrCodePath;
        }

        $settings->office_latitude = $request->office_latitude;
        $settings->office_longitude = $request->office_longitude;
        $settings->radius = $request->radius ?? 200;
        $settings->shop_address = $request->address;

        $settings->save();

        return response()->json([
            // 'message' => "Settings updated successfully for branch {$branchId}",
            'message' => "Settings updated successfully",
            'branch_id' => $branchId
        ]);
    }
    public function updateCompanyRules(Request $request)
    {
        $user               = Auth::guard('api')->user();
        $role               = $user->role;
        $userBranchId       = $user->id;
        $selectedSubAdminId = $request->selectedSubAdminId;

        // 🔹 Role-based branch selection
        if ($role === 'admin' && !empty($selectedSubAdminId) && $selectedSubAdminId !== 'null') {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId; // fallback
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        // 🔹 Normalize time fields — strip AM/PM suffix if browser sends 12hr format
        foreach (['open_time', 'close_time'] as $field) {
            if ($request->filled($field)) {
                $raw = trim($request->input($field));
                // Convert "09:42 AM" / "07:06 PM" -> "09:42" / "19:06"
                if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $raw, $m)) {
                    $hour   = (int) $m[1];
                    $minute = $m[2];
                    $period = strtoupper($m[3]);
                    if ($period === 'AM' && $hour === 12) $hour = 0;
                    if ($period === 'PM' && $hour !== 12) $hour += 12;
                    $request->merge([$field => sprintf('%02d:%02d', $hour, $minute)]);
                }
            }
        }

        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'working_hours'                       => 'nullable',
            'sunday_off'                          => 'required|in:yes,no',
            'saturday_off'                        => 'required|in:yes,no',
            'grace_period'                        => 'nullable',
            'lunch_break'                         => 'nullable',
            'open_time'                           => 'required|date_format:H:i',
            'close_time'                          => 'required|date_format:H:i',
            'overtime_after_hours'                => 'nullable|numeric|min:0',
            'location_check_enabled'              => 'nullable|in:0,1',
            'late_mark_after_minutes'             => 'nullable|integer|min:0',
            'half_day_after_hours'                => 'nullable|numeric|min:0',
            'auto_absent_after_hours'             => 'nullable|numeric|min:0',
            'allow_early_checkout'                => 'nullable|in:yes,no',
            'require_attendance_photo'            => 'nullable|in:yes,no',
            'require_gps_location_for_attendance' => 'nullable|in:yes,no',
            'allow_overtime_approval'             => 'nullable|in:yes,no',
            'max_overtime_hours_per_day'          => 'nullable|numeric|min:0',
            'weekly_off_day'                      => 'nullable|string',
            'monthly_leave_limit'                 => 'nullable|integer|min:0',
            'tax_deduction_amount'                => 'nullable|numeric|min:0',
            'salary_exceeds'                      => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // 🔹 Fetch Setting for Branch
        $settings = Setting::where('branch_id', $selectedSubAdminId)->first();
        // dd($settings);  

        if (! $settings) {
            return response()->json([
                'status'  => false,
                'message' => 'Settings not found for this branch',
            ], 404);
        }

        // 🔹 Update Company Rules
        $settings->update([
            'working_hours'                       => $request->working_hours,
            'sunday_off'                          => $request->sunday_off,
            'saturday_off'                        => $request->saturday_off,
            'grace_period'                        => $request->grace_period,
            'lunch_break'                         => $request->lunch_break,
            'open_time'                           => $request->open_time,
            'close_time'                          => $request->close_time,
            'overtime_after_hours'                => $request->overtime_after_hours ?: null,
            'location_check_enabled'              => (int) ($request->location_check_enabled ?? 0),
            'late_mark_after_minutes'             => $request->late_mark_after_minutes ?: null,
            'half_day_after_hours'                => $request->half_day_after_hours ?: null,
            'auto_absent_after_hours'             => $request->auto_absent_after_hours ?: null,
            'allow_early_checkout'                => $request->allow_early_checkout ?? 'no',
            'require_attendance_photo'            => $request->require_attendance_photo ?? 'no',
            'require_gps_location_for_attendance' => $request->require_gps_location_for_attendance ?? 'no',
            'allow_overtime_approval'             => $request->allow_overtime_approval ?? 'no',
            'max_overtime_hours_per_day'          => $request->max_overtime_hours_per_day ?: null,
            'weekly_off_day'                      => $request->weekly_off_day,
            'monthly_leave_limit'                 => $request->monthly_leave_limit ?: null,
            'tax_deduction_amount'                => $request->tax_deduction_amount ?: null,
            'salary_exceeds'                      => $request->salary_exceeds ?: null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Company rules updated successfully',
            'data'    => $settings,
        ]);
    }

    public function updateDashboardSettings(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        $branchId = $authUser->id;
        if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId')) {
            $branchId = (int) $request->selectedSubAdminId;
        }

        $settings = Setting::where('branch_id', $branchId)->first();
        if (!$settings) {
            $settings = new Setting();
            $settings->branch_id = $branchId;
        }

        // ERP master toggle
        $settings->show_erp_dashboard           = (int) $request->input('show_erp_dashboard', 1);
        $settings->show_erp_total_collections   = (int) $request->input('show_erp_total_collections', 1);
        $settings->show_erp_total_expense       = (int) $request->input('show_erp_total_expense', 1);
        $settings->show_erp_pending_payment     = (int) $request->input('show_erp_pending_payment', 1);
        $settings->show_erp_job_card_bills      = (int) $request->input('show_erp_job_card_bills', 1);
        $settings->show_erp_daily_register      = (int) $request->input('show_erp_daily_register', 1);
        $settings->show_erp_appointments        = (int) $request->input('show_erp_appointments', 1);
        $settings->show_erp_attendance          = (int) $request->input('show_erp_attendance', 1);
        $settings->show_erp_job_card_chart      = (int) $request->input('show_erp_job_card_chart', 1);
        $settings->show_erp_daily_data_chart    = (int) $request->input('show_erp_daily_data_chart', 1);
        $settings->show_erp_latest_job_card     = (int) $request->input('show_erp_latest_job_card', 1);
        $settings->show_erp_latest_daily_data   = (int) $request->input('show_erp_latest_daily_data', 1);

        // HR master + subsections
        $settings->show_hr_dashboard            = (int) $request->input('show_hr_dashboard', 1);
        $settings->show_hr_staff_strength       = (int) $request->input('show_hr_staff_strength', 1);
        $settings->show_hr_active_staff         = (int) $request->input('show_hr_active_staff', 1);
        $settings->show_hr_monthly_attendance   = (int) $request->input('show_hr_monthly_attendance', 1);
        $settings->show_hr_personal_progress    = (int) $request->input('show_hr_personal_progress', 1);
        $settings->show_hr_attendance_pattern   = (int) $request->input('show_hr_attendance_pattern', 1);
        $settings->show_hr_salary_payroll_trend = (int) $request->input('show_hr_salary_payroll_trend', 1);
        $settings->show_hr_payroll_snapshot     = (int) $request->input('show_hr_payroll_snapshot', 1);
        $settings->show_hr_attendance_watch     = (int) $request->input('show_hr_attendance_watch', 1);
        $settings->show_hr_payroll_status       = (int) $request->input('show_hr_payroll_status', 1);

        $settings->save();

        return response()->json([
            'status'  => true,
            'message' => 'Dashboard settings updated successfully',
        ]);
    }
}
