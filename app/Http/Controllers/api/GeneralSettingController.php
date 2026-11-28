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

        // Ensure columns exist on tenant databases
        static::ensureBankColumnsExist();

        $request->validate([
            'shop_name' => 'required|string',
            'gst_num' => 'nullable',
            'state_code' => 'nullable|string',
            'low_stock' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required',
            'alt_phone' => 'nullable',
            'address' => 'required|string',
            'bank_name' => 'nullable|string',
            'branch' => 'nullable|string',
            'ac_no' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'bank_name_without_gst' => 'nullable|string',
            'branch_without_gst' => 'nullable|string',
            'ac_no_without_gst' => 'nullable|string',
            'ifsc_code_without_gst' => 'nullable|string',
            'currency_symbol' => 'required|string',
            'currency_position' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'remove_logo' => 'nullable',
            'remove_favicon' => 'nullable',
            'remove_qr_code' => 'nullable',
            'remove_signature' => 'nullable',
            'cin_no' => 'nullable|string|max:100',
            'invoice_size' => 'nullable|in:small,big,half',
            'send_mail' => 'nullable|boolean',
            'appointment_reminder_hours_before' => 'nullable|integer|min:1',
            'enable_variant_product' => 'nullable|boolean',
            'default_language' => 'nullable|string|max:10',
            'enable_calculator' => 'nullable|boolean',
            'enable_multiple_price' => 'nullable|boolean',
            'logo_width' => 'nullable|string|max:50',
            'logo_height' => 'nullable|string|max:50',
            'customer_whatsapp_message' => 'nullable|boolean',
            'admin_whatsapp_message' => 'nullable|boolean',
        ]);

        // Find branch-specific settings
        $settings = Setting::where('branch_id', $branchId)->first();
        if (!$settings) {
            $settings = new Setting();
            $settings->branch_id = $branchId;
        }

        // Assign values
        $settings->gst_num = $request->gst_num;
        $settings->cin_no = $request->cin_no;
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
        $settings->bank_name_without_gst = $request->bank_name_without_gst;
        $settings->branch_without_gst = $request->branch_without_gst;
        $settings->ac_no_without_gst = $request->ac_no_without_gst;
        $settings->ifsc_code_without_gst = $request->ifsc_code_without_gst;
        $settings->currency_position = $request->currency_position;
        $settings->currency_symbol = $request->currency_symbol;

        // File uploads & removals
        if ($request->remove_logo == 1) {
            $settings->logo = null;
        } elseif ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        if ($request->remove_favicon == 1) {
            $settings->favicon = null;
        } elseif ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            $settings->favicon = $faviconPath;
        }
        if ($request->remove_qr_code == 1) {
            $settings->qr_code = null;
        } elseif ($request->hasFile('qr_code')) {
            $qrCodePath = $request->file('qr_code')->store('qr_codes', 'public');
            $settings->qr_code = $qrCodePath;
        }

        if ($request->remove_qr_code_without_gst == 1) {
            $settings->qr_code_without_gst = null;
        } elseif ($request->hasFile('qr_code_without_gst')) {
            $qrCodeWithoutGstPath = $request->file('qr_code_without_gst')->store('qr_codes', 'public');
            $settings->qr_code_without_gst = $qrCodeWithoutGstPath;
        }

        if ($request->remove_signature == 1) {
            $settings->signature = null;
        } elseif ($request->hasFile('signature')) {
            $settings->signature = $request->file('signature')->store('signatures', 'public');
        }

        $settings->office_latitude = $request->office_latitude;
        $settings->office_longitude = $request->office_longitude;
        $settings->radius = $request->radius ?? 200;
        $settings->shop_address = $request->address;
        if ($request->has('invoice_size')) {
            $settings->invoice_size = $request->invoice_size;
        }
        if ($request->has('send_mail')) {
            $settings->send_mail = (int) $request->send_mail;
        }
        if ($request->has('appointment_reminder_hours_before')) {
            $settings->appointment_reminder_hours_before = $request->appointment_reminder_hours_before ?: 3;
        }
        if ($request->has('enable_variant_product')) {
            $settings->enable_variant_product = (int) $request->enable_variant_product;
        }
        if ($request->filled('default_language')) {
            $settings->default_language = $request->default_language;
        }
        if ($request->has('enable_calculator')) {
            $settings->enable_calculator = (int) $request->enable_calculator;
        }
        if ($request->has('enable_multiple_price')) {
            $settings->enable_multiple_price = (int) $request->enable_multiple_price;
        }
        if ($request->has('logo_width')) {
            $settings->logo_width = $request->logo_width;
        }
        if ($request->has('logo_height')) {
            $settings->logo_height = $request->logo_height;
        }
        if ($request->has('customer_whatsapp_message')) {
            $settings->customer_whatsapp_message = (int) $request->customer_whatsapp_message;
        }
        if ($request->has('admin_whatsapp_message')) {
            $settings->admin_whatsapp_message = (int) $request->admin_whatsapp_message;
        }

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

    public function updateBarcodeSettings(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        $branchId = $authUser->id;
        if ($authUser->role === 'admin' && $request->filled('selectedSubAdminId') && $request->selectedSubAdminId !== 'null') {
            $branchId = (int) $request->selectedSubAdminId;
        }

        $validator = Validator::make($request->all(), [
            'barcode_type'          => 'nullable|in:barcode,qrcode',
            'barcode_setting_type'  => 'nullable|in:simple,detailed',
            'barcode_detail_option' => 'nullable|in:long,short',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $settings = Setting::where('branch_id', $branchId)->first();
        if (!$settings) {
            $settings = new Setting();
            $settings->branch_id = $branchId;
        }

        if ($request->has('barcode_type')) {
            $settings->barcode_type = $request->barcode_type;
        }
        if ($request->has('barcode_setting_type')) {
            $settings->barcode_setting_type = $request->barcode_setting_type;
            if ($request->barcode_setting_type === 'simple') {
                $settings->barcode_detail_option = null;
            }
        }
        if ($request->has('barcode_detail_option') && $request->barcode_setting_type === 'detailed') {
            $settings->barcode_detail_option = $request->barcode_detail_option;
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Barcode settings updated successfully',
            'settings' => $settings,
            'branch_id' => $branchId
        ]);
    }

    public function updateOrderFormatSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            if ($subAdmin) {
                $selectedSubAdminId = $subAdmin->id;
            } else {
                $selectedSubAdminId = $userBranchId;
            }
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'with_gst_format'           => 'nullable|string|max:100',
            'without_gst_format'        => 'nullable|string|max:100',
            'rental_format'             => 'nullable|string|max:100',
            'advance_payment_format'    => 'nullable|string|max:100',
            'quotation_format'          => 'nullable|string|max:100',
            'quotation_with_gst_format' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);

        if ($request->has('with_gst_format')) {
            $settings->with_gst_format = $request->with_gst_format;
        }
        if ($request->has('without_gst_format')) {
            $settings->without_gst_format = $request->without_gst_format;
        }
        if ($request->has('rental_format')) {
            $settings->rental_format = $request->rental_format;
        }
        if ($request->has('advance_payment_format')) {
            $settings->advance_payment_format = $request->advance_payment_format;
        }
        if ($request->has('quotation_format')) {
            $settings->quotation_format = $request->quotation_format;
        }
        if ($request->has('quotation_with_gst_format')) {
            $settings->quotation_with_gst_format = $request->quotation_with_gst_format;
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Order number format updated successfully',
            'settings' => $settings,
            'branch_id' => $selectedSubAdminId,
        ]);
    }

    /**
     * Ensure without_gst bank details columns exist in tenant settings table.
     */
    public static function ensureBankColumnsExist(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        try {
            $cols = [
                'bank_name_without_gst',
                'branch_without_gst',
                'ac_no_without_gst',
                'ifsc_code_without_gst',
                'qr_code_without_gst',
            ];
            $missing = [];
            foreach ($cols as $c) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('settings', $c)) {
                    $missing[] = "`$c` VARCHAR(255) NULL";
                }
            }
            if (!empty($missing)) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE `settings` ADD COLUMN ' . implode(', ADD COLUMN ', $missing));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not alter settings table: ' . $e->getMessage());
        }
    }

    public function updateDeliveryTerms(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            $selectedSubAdminId = $subAdmin ? $subAdmin->id : $userBranchId;
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'terms_condition_eng' => 'nullable|string',
            'terms_condition_guj' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        $settings->terms_condition_eng = $request->terms_condition_eng;
        $settings->terms_condition_guj = $request->terms_condition_guj;
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Delivery terms & conditions updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateBankDetails(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            $selectedSubAdminId = $subAdmin ? $subAdmin->id : $userBranchId;
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'bank_name'             => 'nullable|string',
            'branch'                => 'nullable|string',
            'ac_no'                 => 'nullable|string',
            'ifsc_code'             => 'nullable|string',
            'bank_name_without_gst' => 'nullable|string',
            'branch_without_gst'    => 'nullable|string',
            'ac_no_without_gst'     => 'nullable|string',
            'ifsc_code_without_gst' => 'nullable|string',
            'qr_code'               => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'qr_code_without_gst'   => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        $settings->bank_name             = $request->bank_name;
        $settings->branch                = $request->branch;
        $settings->ac_no                 = $request->ac_no;
        $settings->ifsc_code             = $request->ifsc_code;
        $settings->bank_name_without_gst = $request->bank_name_without_gst;
        $settings->branch_without_gst    = $request->branch_without_gst;
        $settings->ac_no_without_gst     = $request->ac_no_without_gst;
        $settings->ifsc_code_without_gst = $request->ifsc_code_without_gst;

        if ($request->remove_qr_code == 1) {
            $settings->qr_code = null;
        } elseif ($request->hasFile('qr_code')) {
            $settings->qr_code = $request->file('qr_code')->store('qr_codes', 'public');
        }

        if ($request->remove_qr_code_without_gst == 1) {
            $settings->qr_code_without_gst = null;
        } elseif ($request->hasFile('qr_code_without_gst')) {
            $settings->qr_code_without_gst = $request->file('qr_code_without_gst')->store('qr_codes', 'public');
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Bank details & QR code updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateSalesSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            $selectedSubAdminId = $subAdmin ? $subAdmin->id : $userBranchId;
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'advance_receipt'        => 'nullable|boolean',
            'rental'                 => 'nullable|boolean',
            'draft'                  => 'nullable|boolean',
            'financial_year'         => 'nullable|boolean',
            'tds_apply'              => 'nullable|boolean',
            'exchange_apply'         => 'nullable|boolean',
            'delivery_cost_apply'    => 'nullable|boolean',
            'other_charges_apply'    => 'nullable|boolean',
            'labour_apply'           => 'nullable|boolean',
            'remarks_apply'          => 'nullable|boolean',
            'show_assign_staff'      => 'nullable|boolean',
            'show_order_type'        => 'nullable|boolean',
            'enable_manufacture'     => 'nullable|boolean',
            'show_bank_details'      => 'nullable|boolean',
            'show_qr_code'           => 'nullable|boolean',
            'sales_discount'         => 'nullable|string|in:all,overall,product_wise',
            'default_payment_method' => 'nullable|string',
            'barcode_type'           => 'nullable|in:barcode,qrcode',
            'barcode_setting_type'   => 'nullable|in:simple,detailed',
            'barcode_detail_option'  => 'nullable|in:long,short',
            'payment_methods'        => 'nullable|array',
            'payment_methods.*'      => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);

        foreach ([
            'advance_receipt', 'rental', 'draft', 'financial_year', 'tds_apply',
            'exchange_apply', 'delivery_cost_apply', 'other_charges_apply',
            'labour_apply', 'remarks_apply', 'show_assign_staff', 'show_order_type',
            'enable_manufacture', 'show_bank_details', 'show_qr_code'
        ] as $boolField) {
            if ($request->has($boolField)) {
                $settings->{$boolField} = (int) $request->{$boolField};
            }
        }

        if ($request->has('sales_discount')) {
            $settings->sales_discount = $request->sales_discount;
        }
        if ($request->has('default_payment_method')) {
            $settings->default_payment_method = $request->default_payment_method;
        }
        if ($request->has('barcode_type')) {
            $settings->barcode_type = $request->barcode_type;
        }
        if ($request->has('barcode_setting_type')) {
            $settings->barcode_setting_type = $request->barcode_setting_type;
            if ($request->barcode_setting_type === 'simple') {
                $settings->barcode_detail_option = null;
            }
        }
        if ($request->has('barcode_detail_option') && $request->barcode_setting_type === 'detailed') {
            $settings->barcode_detail_option = $request->barcode_detail_option;
        }
        if ($request->has('payment_methods')) {
            $settings->payment_methods = $request->payment_methods;
        } elseif ($request->hasAny(['advance_receipt', 'rental', 'draft', 'financial_year', 'tds_apply', 'exchange_apply', 'delivery_cost_apply', 'other_charges_apply', 'labour_apply', 'remarks_apply', 'show_assign_staff', 'show_order_type', 'enable_manufacture', 'show_bank_details', 'show_qr_code', 'sales_discount'])) {
            $settings->payment_methods = [];
        }

        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Sales settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateEventCalendarSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            $selectedSubAdminId = $subAdmin ? $subAdmin->id : $userBranchId;
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'enable_event_calendar' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        if ($request->has('enable_event_calendar')) {
            $settings->enable_event_calendar = (int) $request->enable_event_calendar;
        }
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Event Calendar settings updated successfully',
            'settings' => $settings,
        ]);
    }

    public function updateLayoutSettings(Request $request)
    {
        $user         = Auth::guard('api')->user();
        $role         = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === null || $selectedSubAdminId === '') {
            $selectedSubAdminId = null;
        }

        if ($role === 'admin' && !empty($selectedSubAdminId)) {
            $subAdmin = User::where('id', $selectedSubAdminId)->first();
            $selectedSubAdminId = $subAdmin ? $subAdmin->id : $userBranchId;
        } else {
            $selectedSubAdminId = $userBranchId;
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($value === 'undefined' || $value === 'null') {
                unset($input[$key]);
            }
        }
        $request->replace($input);

        $validator = Validator::make($request->all(), [
            'pos_layout'  => 'required|in:layout1,layout2',
            'logo_width'  => 'nullable|string|max:50',
            'logo_height' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = Setting::firstOrNew(['branch_id' => $selectedSubAdminId]);
        $settings->pos_layout = $request->pos_layout;
        if ($request->has('logo_width')) {
            $settings->logo_width = $request->logo_width;
        }
        if ($request->has('logo_height')) {
            $settings->logo_height = $request->logo_height;
        }
        $settings->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Layout settings updated successfully',
            'settings' => $settings,
        ]);
    }
}
