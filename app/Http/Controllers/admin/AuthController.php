<?php

namespace App\Http\Controllers\admin;

// use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchases;
use App\Models\Order;
use App\Models\Expense;
use App\Models\User;
use App\Models\Appointment;
use App\Models\PaymentStore;
use App\Models\JobCard;
// use App\Models\Appointment;
// use App\Models\PaymentStore;
use App\Models\PurchaseInvoice;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function signin()
    {

        return view('signin');
    }
    public function forgetpassword()
    {

        return view('forgetpassword');
    }

    public function dashboard(Request $request)
    {
        
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;
        $currentMonth = Carbon::now()->month;

        // Get user info
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId   = $user->id ?? null;

        if (strtolower($userRole) === 'staff') {
            return redirect()->route('auth.staff-dashboard');
        }

        // Determine branch ID filter
        // $selectedSubAdminId = $request->query('sub_branch_id');
        $selectedSubAdminId = session('selectedSubAdminId');
        if (!empty($selectedSubAdminId)) {
            $filterId = (int) $selectedSubAdminId; // Explicitly provided branch
        } else {
            $filterId = $userId; // Always own branch if no selection
        }
        

        $filter = $request->get('filter', 'today');

        // Set date range based on filter
        switch ($filter) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            default: // today
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
        }

        // Totals
        $start1 = Carbon::parse($start)->format('Y-m-d');
        $end1   = Carbon::parse($end)->format('Y-m-d');

        // Totals
        $totalJobCards = \DB::table('job_cards')
            ->where('subbranch_id', $filterId)
            ->where('type', 'jobCard')
            ->where('isDeleted', 0)
            ->whereBetween('date', [$start1, $end1])
            ->count();
            // dd($totalJobCards);
        $totalOrdersCount = DB::table('orders')
            ->where('isDeleted', '!=', 1)
            ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
            ->when($filterId, fn($q) => $q->where('branch_id', $filterId))
            ->count();

        $totalDailyData = \DB::table('job_cards')
            ->where('subbranch_id', $filterId)
            ->where('type', 'dailyData')
            ->where('isDeleted', 0)
            ->whereBetween('record_date', [$start1, $end1])
            ->count();

        $totalAppointments = \DB::table('appointments')
            ->where('branch_id', $filterId)
            ->where('is_deleted', 0)
            ->whereBetween('appointment_date', [$start, $end])
            ->count();

        $totalAttendance = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->whereBetween('date', [$start, $end])
            ->count();

        // $totalPresent = \DB::table('attendance')
        //     ->where('branch_id', $filterId)
        //     ->where('status', 'P')
        //     ->whereBetween('date', [$start, $end])
        //     ->count();

        $present = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'P')
            ->whereBetween('date', [$start, $end])
            ->count();

        // ✅ Count half-day
        $halfday = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'H')
            ->whereBetween('date', [$start, $end])
            ->count();

        // ✅ Final total present with half-day as 0.5
        $totalPresent = $present + ($halfday * 0.5);

        $totalAbsent = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'A')
            ->whereBetween('date', [$start, $end])
            ->count();


        // $totalJobcardAmount = \DB::table('job_cards')
        //     ->where('job_cards.subbranch_id', $filterId)
        //     ->where('job_cards.isDeleted', '!=', 1)
        //     ->whereBetween('job_cards.date', [$start1, $end1])
        //     ->sum('job_cards.total_subtotal_amount');

        

        // // 2️⃣ Total payment amount (join with job_cards to filter by branch)
        // $totalPaymentAmount = \DB::table('payment_store')
        //     ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
        //     ->where('job_cards.subbranch_id', $filterId)
        //     ->where('payment_store.isDeleted', '!=', 1)
        //     ->whereBetween('job_cards.date', [$start1, $end1])
        //     ->sum('payment_store.payment_amount');




        // // 3️⃣ Pending amount
        // $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);
        if (!empty($selectedSubAdminId)) {
            // -----------------------------
            // Pending Orders
            // -----------------------------
            $totalOrderAmount = \DB::table('orders')
                ->where('orders.branch_id', $filterId)
                ->where('orders.isDeleted', '!=', 1)
                ->whereBetween('orders.date', [$start1, $end1]) // ✅ use order.date
                ->sum('orders.total_amount');

            $totalPaymentAmount = \DB::table('payment_store')
                ->join('orders', 'payment_store.order_id', '=', 'orders.id')
                ->where('orders.branch_id', $filterId)
                ->where('payment_store.isDeleted', '!=', 1)
                ->whereBetween('orders.date', [$start1, $end1]) // ✅ match order date range
                ->sum('payment_store.payment_amount');

            $pendingAmount = abs($totalOrderAmount - $totalPaymentAmount);
        } else {
            // -----------------------------
            // Pending Job Cards
            // -----------------------------
            $totalJobcardAmount = \DB::table('job_cards')
                ->where('job_cards.subbranch_id', $filterId)
                ->where('job_cards.isDeleted', '!=', 1)
                ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ use jobcard.date
                ->whereNull('job_cards.old_job_card')
                ->sum('job_cards.total_subtotal_amount');

            $totalPaymentAmount = \DB::table('payment_store')
                ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
                ->where('job_cards.subbranch_id', $filterId)
                ->where('payment_store.isDeleted', '!=', 1)
                ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ match jobcard date range
                ->sum('payment_store.payment_amount');

            $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);
        }


        // Decide chart type: Orders if Sub-Admin selected, else JobCard
        if (!empty($selectedSubAdminId)) {
            // Orders chart
            $salesChartThisMonth    = $this->getOrdersDataByMonth($currentMonth, $filterId);
            $salesChartthisyear     = $this->getOrdersDataByYear($currentYear, $filterId);
            $salesChartpreviousyear = $this->getOrdersDataByYear($previousYear, $filterId);
            $salesChartLabel        = "Order";
        } else {
            // Job Card chart
            $salesChartThisMonth    = $this->getSalesDataByMonth($currentMonth, $userRole, $filterId, $userId);
            $salesChartthisyear     = $this->getSalesDataByYear($currentYear, $userRole, $filterId, $userId);
            $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $userRole, $filterId, $userId);
            $salesChartLabel        = "Job Card";
        }
        // $salesChartThisMonth = $this->getSalesDataByMonth($currentMonth, $userRole, $filterId, $userId);
        $purchaseChartThisMonth = $this->getPurchaseDataByMonth($currentMonth, $userRole, $filterId, $userId);

        // $salesChartthisyear = $this->getSalesDataByYear($currentYear, $userRole, $filterId, $userId);
        // $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $userRole, $filterId, $userId);

        $purchaseChartthisyear = $this->getPurchaseDataByYear($currentYear, $userRole, $filterId, $userId);
        $purchaseChartpreviousyear = $this->getPurchaseDataByYear($previousYear, $userRole, $filterId, $userId);

        // Base totals
        $totalPurchaseAmount = PurchaseInvoice::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->sum('grand_total');

        // $totalCollection = DB::table('payment_store')
        //     ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
        //     ->where('payment_store.isDeleted', '!=', 1)
        //     ->whereBetween('payment_store.payment_date', [$start, $end]);

        // if (!empty($filterId)) {
        //     $totalCollection->where('job_cards.subbranch_id', $filterId);
        // }

        // $totalCollection = $totalCollection->sum('payment_store.payment_amount'); // change column name if it's not 'amount'

        // // Apply filters based on role and selection
        // if ($filterId) {
        // }
        $totalCollection = DB::table('payment_store')
            ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->whereBetween('payment_store.payment_date', [$start, $end])
            ->when($filterId, fn($q) => $q->where('job_cards.subbranch_id', $filterId))
            ->sum('payment_store.payment_amount');

        $totalOrders = DB::table('payment_store')
            ->join('orders', 'orders.id', '=', 'payment_store.order_id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->where('orders.isDeleted', '!=', 1)
            ->whereBetween('payment_store.payment_date', [$start1, $end1])
            ->when($filterId, fn($q) => $q->where('orders.branch_id', $filterId))
            ->sum('payment_store.payment_amount');
        
        // ✅ Decide what to show
        if (!empty($filterId) && $filterId != 1) {
            
            $mainAmount = $totalOrders;
            $mainAmountFormatted = number_format($totalOrders, 2);
        } else {
            $mainAmount = $totalCollection;
            $mainAmountFormatted = number_format($totalCollection, 2);
        }



        

        // $totalExpenseAmount = Expense::where('isDeleted', '!=', 1)
        //     ->where('branch_id', $filterId)
        //     ->whereBetween('expense_date', [$start, $end])
        //     ->sum('amount');
        $totalExpenseAmount = Expense::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->whereBetween('expense_date', [
                Carbon::parse($start)->format('Y-m-d'),
                Carbon::parse($end)->format('Y-m-d')
            ])
            ->sum('amount');


        // Customer/vendor counts
        $customerCount = User::where('role', 'customer')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        $vendorCount = User::where('role', 'vendor')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        // Invoice counts
        $purchaseInvoiceCount = PurchaseInvoice::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        $salesInvoiceCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        $quotationCount = DB::table('job_cards')
            ->where('isDeleted', '!=', 1)
            // ->where('branch_id', $filterId)
            ->where('quotation_status', 'job_cards')
            ->count();

        // Recent products
        $recentProducts = Product::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->latest()
            ->take(4)
            ->get();

        // Latest 4 Sales (Job Cards)
        $latestSales = JobCard::where('type', 'jobCard')
            ->with('customer', 'paymentStore')
            ->where('subbranch_id', $filterId)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        // Latest 4 Purchases (Daily Data)
        $latestPurchases = JobCard::where('type', 'dailyData')
            ->with('customer', 'paymentStore')
            ->where('subbranch_id', $filterId)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        
        $salesData = [];
        // $latestSales = collect(); // default empty collection
        // if (in_array($userRole, ['admin', 'sub-admin', 'sales-manager', 'inventory-manager'])) {
        //     $salesData = JobCard::where('type', 'jobCard')
        //         ->where('isDeleted', 0)
        //         ->where('subbranch_id', $filterId)
        //         ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
        //         ->groupByRaw('MONTH(created_at)')
        //         ->pluck('total', 'month')
        //         ->toArray();

        //     // Latest 4 sales (jobCard)
        //     $latestSales = JobCard::where('type', 'jobCard')
        //         ->where('isDeleted', 0)
        //         ->where('subbranch_id', $filterId)
        //         ->orderBy('created_at', 'desc')
        //         ->take(4)
        //         ->get();
        // }
        // $latestSales = collect(); // default empty

        // if (in_array($userRole, ['admin', 'sub-admin', 'sales-manager', 'inventory-manager'])) {
        //     // dd($selectedSubAdminId, $filterId);
        //     if (!empty($selectedSubAdminId)) {
        //         // ✅ Sub Admin selected → show Orders
        //         $latestSales = Order::where('isDeleted', 0)
        //             ->where('branch_id', $filterId)
        //             ->orderBy('created_at', 'desc')
        //             ->take(4)
        //             ->get();

        //         $salesChartLabel = 'Order';
        //     } else {
        //         // ✅ Default → show Job Cards
        //         $latestSales = JobCard::where('type', 'jobCard')
        //             ->where('isDeleted', 0)
        //             ->where('subbranch_id', $filterId)
        //             ->orderBy('created_at', 'desc')
        //             ->take(4)
        //             ->get();

                
        //     }
        //     // dd($latestSales);
        //     $salesChartLabel = 'Job Card';
        // }
        $latestSales = JobCard::where('type', 'jobCard')
            ->with('customer', 'paymentStore')
            ->where('subbranch_id', $filterId)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        $salesChartLabel = "Job Card";


        // Purchase chart & latest 4 purchases from JobCard
        $purchasesData = [];
        $latestPurchases = collect(); // default empty collection
        if (in_array($userRole, ['admin', 'sub-admin', 'purchase-manager', 'inventory-manager'])) {
            $purchasesData = JobCard::where('type', 'dailyData')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
                ->groupByRaw('MONTH(created_at)')
                ->pluck('total', 'month')
                ->toArray();

            // Latest 4 purchases (dailyData)
            $latestPurchases = JobCard::where('type', 'dailyData')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
        }

        // Fill chart arrays for 12 months
        $salesChart = [];
        $purchaseChart = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesChart[]    = (float) ($salesData[$m] ?? 0);
            $purchaseChart[] = (float) ($purchasesData[$m] ?? 0);
        }


        // Settings
        $settings = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';


        $today = Carbon::today()->format('Y-m-d');
        $nextWeek = Carbon::today()->addDays(7);

        $user = Auth::user();
        $subAdminId = session('selectedSubAdminId') ?? $user->id;

        $appointments = Appointment::whereDate('appointment_date', $today)
            ->where('branch_id', $subAdminId)
            ->where('is_deleted', 0)
            ->get();



        // ✅ Fetch Upcoming EMIs based on payment_date + emi_month
        $upcomingEmis = PaymentStore::select(
            'payment_store.*',
            'job_cards.vehicle_no',
            'auto_car_models.model_name as model_name',
            'job_cards.total_subtotal_amount',
            'users.name as customer_name'
        )
            ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
            ->join('users', 'job_cards.customer_id', '=', 'users.id')
            ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
            ->where('payment_store.payment_method', 'EMI')
            ->where('payment_store.isDeleted', 0)
            ->where('job_cards.subbranch_id', $subAdminId)
            ->get()
            ->filter(function ($payment) use ($today, $nextWeek) {
                // ✅ Base date should be payment_date (not created_at)
                $baseDate = Carbon::parse($payment->payment_date);

                // Add EMI month(s) to payment_date
                $dueDate = $baseDate->copy()->addMonths($payment->emi_month);

                // Attach formatted due date
                $payment->emi_due_date = $dueDate->format('d-m-Y');

                // ✅ Only include records between today and next 7 days
                return $dueDate->between($today, $nextWeek);
            });

        // ===============================
        // Upcoming Cash/Online Payments (JobCards)
        // ===============================
        $upcomingPayments = JobCard::select(
            'job_cards.*',
            'auto_car_models.model_name as model_name',
            'users.name as customer_name'
        )
            ->join('users', 'job_cards.customer_id', '=', 'users.id')
            ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
            ->whereIn('job_cards.payment_method', ['Cash', 'Online'])
            ->where('job_cards.isDeleted', 0)
            ->where('job_cards.subbranch_id', $subAdminId)
            ->get()
            ->filter(function ($job) use ($today, $nextWeek) {
                if (!$job->next_pending_date) {
                    return false;
                }

                $dueDate = Carbon::parse($job->next_pending_date);
                $job->emi_due_date = $dueDate->format('d-m-Y');

                return $dueDate->between($today, $nextWeek);
            });

        // ✅ Merge EMI + Cash/Online into one collection
        // $upcomingAllPayments = $upcomingEmis->merge($upcomingPayments);
        // ===============================
        // Customers completing 6 months today
        // ===============================
        $customers6Months = JobCard::select(
            'job_cards.*',
            'auto_car_models.model_name as model_name',
            'users.name as customer_name'
        )
            ->join('users', 'job_cards.customer_id', '=', 'users.id')
            ->join('auto_car_models', 'job_cards.model', '=', 'auto_car_models.id')
            ->where('job_cards.isDeleted', 0)
            ->where('job_cards.subbranch_id', $subAdminId)
            ->orderBy('job_cards.id', 'desc') // latest records first
            ->get()
            ->filter(function ($job) use ($today) {
                if (!$job->record_date) {
                    return false;
                }

                $recordDate = Carbon::parse($job->record_date);
                $dueDate = $recordDate->copy()->addMonths(6);

                $job->six_month_due_date = $dueDate->format('d-m-Y');

                return $dueDate->isSameDay($today);
            })
            ->take(5); // ✅ only latest 5


    

        // ─── Dashboard visibility settings ───────────────────────────────────────
        $dashSettings = \App\Models\Setting::where('branch_id', $filterId)->first();

        $showErpDashboardSection     = (bool) ($dashSettings->show_erp_dashboard           ?? 1);
        $showErpTotalCollections     = (bool) ($dashSettings->show_erp_total_collections   ?? 1);
        $showErpTotalExpense         = (bool) ($dashSettings->show_erp_total_expense        ?? 1);
        $showErpPendingPayment       = (bool) ($dashSettings->show_erp_pending_payment      ?? 1);
        $showErpJobCardBills         = (bool) ($dashSettings->show_erp_job_card_bills       ?? 1);
        $showErpDailyRegister        = (bool) ($dashSettings->show_erp_daily_register       ?? 1);
        $showErpAppointments         = (bool) ($dashSettings->show_erp_appointments         ?? 1);
        $showErpAttendance           = (bool) ($dashSettings->show_erp_attendance           ?? 1);
        $showErpJobCardChart         = (bool) ($dashSettings->show_erp_job_card_chart       ?? 1);
        $showErpDailyDataChart       = (bool) ($dashSettings->show_erp_daily_data_chart     ?? 1);
        $showErpLatestJobCard        = (bool) ($dashSettings->show_erp_latest_job_card      ?? 1);
        $showErpLatestDailyData      = (bool) ($dashSettings->show_erp_latest_daily_data    ?? 1);
        $showHrDashboardSection      = (bool) ($dashSettings->show_hr_dashboard            ?? 1);
        $showHrStaffStrength         = (bool) ($dashSettings->show_hr_staff_strength        ?? 1);
        $showHrActiveStaff           = (bool) ($dashSettings->show_hr_active_staff          ?? 1);
        $showHrMonthlyAttendance     = (bool) ($dashSettings->show_hr_monthly_attendance    ?? 1);
        $showHrPersonalProgress      = (bool) ($dashSettings->show_hr_personal_progress     ?? 1);
        $showHrAttendancePattern     = (bool) ($dashSettings->show_hr_attendance_pattern    ?? 1);
        $showHrSalaryPayrollTrend    = (bool) ($dashSettings->show_hr_salary_payroll_trend  ?? 1);
        $showHrPayrollSnapshot       = (bool) ($dashSettings->show_hr_payroll_snapshot      ?? 1);
        $showHrAttendanceWatch       = (bool) ($dashSettings->show_hr_attendance_watch      ?? 1);
        $showHrPayrollStatus         = (bool) ($dashSettings->show_hr_payroll_status        ?? 1);

        // ─── HR data for dashboard ────────────────────────────────────────────────
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd   = Carbon::now()->endOfMonth();
        $today             = Carbon::today();

        // Staff counts
        $totalStaff       = \App\Models\User::where('role', 'Staff')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();
        $activeStaff      = $totalStaff; // active = non-deleted staff
        $newStaffThisMonth = \App\Models\User::where('role', 'Staff')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        // Today attendance
        $todayPresentStaff = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->whereDate('date', $today)
            ->whereIn('status', ['P', 'H'])
            ->count();
        $todayAbsentStaff  = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->whereDate('date', $today)
            ->where('status', 'A')
            ->count();
        $todayUnmarkedStaff = max(0, $totalStaff - $todayPresentStaff - $todayAbsentStaff);

        // Monthly attendance rate
        $workingDaysThisMonth = max(1, Carbon::now()->day);
        $totalExpectedSlots   = $totalStaff * $workingDaysThisMonth;
        $totalPresentSlots    = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->whereBetween('date', [$currentMonthStart->format('Y-m-d'), $today->format('Y-m-d')])
            ->whereIn('status', ['P', 'H'])
            ->count();
        $monthlyAttendanceRate = $totalExpectedSlots > 0
            ? round(($totalPresentSlots / $totalExpectedSlots) * 100, 1)
            : 0;

        // Payroll progress (this month)
        $salaryPaidCount   = \DB::table('salaries')
            ->where('branch_id', $filterId)
            ->whereYear('salary_month', Carbon::now()->year)
            ->whereMonth('salary_month', Carbon::now()->month)
            ->where('status', 'paid')
            ->count();
        $salaryPendingCount = max(0, $totalStaff - $salaryPaidCount);
        $salaryPaidAmount   = \DB::table('salaries')
            ->where('branch_id', $filterId)
            ->whereYear('salary_month', Carbon::now()->year)
            ->whereMonth('salary_month', Carbon::now()->month)
            ->where('status', 'paid')
            ->sum('net_salary');

        // Outstanding advances
        $advanceOutstanding = \DB::table('advance_payments')
            ->where('branch_id', $filterId)
            ->where('status', '!=', 'recovered')
            ->sum('amount');

        // 7-day attendance chart data
        $hrAttendanceLabels  = [];
        $hrAttendancePresent = [];
        $hrAttendanceAbsent  = [];
        $hrAttendanceHalfDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $hrAttendanceLabels[]  = $day->format('d M');
            $hrAttendancePresent[] = (int) \DB::table('attendance')
                ->where('branch_id', $filterId)
                ->whereDate('date', $day)
                ->where('status', 'P')
                ->count();
            $hrAttendanceAbsent[]  = (int) \DB::table('attendance')
                ->where('branch_id', $filterId)
                ->whereDate('date', $day)
                ->where('status', 'A')
                ->count();
            $hrAttendanceHalfDay[] = (int) \DB::table('attendance')
                ->where('branch_id', $filterId)
                ->whereDate('date', $day)
                ->where('status', 'H')
                ->count();
        }

        // 6-month salary payout trend
        $hrSalaryLabels      = [];
        $hrSalaryPaidAmounts = [];
        $hrSalaryPaidCounts  = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $hrSalaryLabels[]      = $month->format('M y');
            $hrSalaryPaidAmounts[] = (float) \DB::table('salaries')
                ->where('branch_id', $filterId)
                ->whereYear('salary_month', $month->year)
                ->whereMonth('salary_month', $month->month)
                ->where('status', 'paid')
                ->sum('net_salary');
            $hrSalaryPaidCounts[]  = (int) \DB::table('salaries')
                ->where('branch_id', $filterId)
                ->whereYear('salary_month', $month->year)
                ->whereMonth('salary_month', $month->month)
                ->where('status', 'paid')
                ->count();
        }

        // Attendance watch (top staff by absent days this month)
        $attendanceWatch = \DB::table('users')
            ->where('users.role', 'Staff')
            ->where('users.isDeleted', '!=', 1)
            ->where('users.branch_id', $filterId)
            ->leftJoin(\DB::raw("(SELECT user_id,
                SUM(CASE WHEN status='P' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status='A' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status='H' THEN 1 ELSE 0 END) as half_days
                FROM attendance
                WHERE branch_id = {$filterId}
                AND date BETWEEN '{$currentMonthStart->format('Y-m-d')}' AND '{$today->format('Y-m-d')}'
                GROUP BY user_id) AS att"), 'att.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                \DB::raw('COALESCE(att.present_days, 0) as present_days'),
                \DB::raw('COALESCE(att.absent_days, 0) as absent_days'),
                \DB::raw('COALESCE(att.half_days, 0) as half_days')
            )
            ->orderByDesc('absent_days')
            ->limit(5)
            ->get();

        // Payroll status rows (this month)
        $payrollStatusRows = \DB::table('users')
            ->where('users.role', 'Staff')
            ->where('users.isDeleted', '!=', 1)
            ->where('users.branch_id', $filterId)
            ->leftJoin(\DB::raw("(SELECT user_id, status, net_salary
                FROM salaries
                WHERE branch_id = {$filterId}
                AND YEAR(salary_month) = " . Carbon::now()->year . "
                AND MONTH(salary_month) = " . Carbon::now()->month . "
                ORDER BY id DESC LIMIT 9999) AS sal"), 'sal.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.phone',
                \DB::raw("COALESCE(sal.status, 'pending') as status"),
                \DB::raw('COALESCE(sal.net_salary, 0) as amount')
            )
            ->limit(5)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        return view('index', compact(
            'appointments',
            'upcomingEmis',
            'upcomingPayments',
            'customers6Months',
            'totalCollection',
            'totalOrders',
            'mainAmount',
            'mainAmountFormatted',
            'pendingAmount',
            'totalPurchaseAmount',
            'totalExpenseAmount',
            'customerCount',
            'vendorCount',
            'quotationCount',
            'purchaseInvoiceCount',
            'salesInvoiceCount',
            'recentProducts',
            'latestSales',
            'latestPurchases',
            'salesChart',
            'purchaseChart',
            'currencySymbol',
            'currencyPosition',
            'salesChartthisyear',
            'salesChartpreviousyear',
            'purchaseChartthisyear',
            'purchaseChartpreviousyear',
            'salesChartThisMonth',
            'salesChartLabel',
            'purchaseChartThisMonth',
            'totalJobCards',
            'totalOrdersCount',
            'totalDailyData',
            'totalAppointments',
            'totalAttendance',
            'totalPresent',
            'totalAbsent',
            // ERP Dashboard visibility flags
            'showErpDashboardSection',
            'showErpTotalCollections',
            'showErpTotalExpense',
            'showErpPendingPayment',
            'showErpJobCardBills',
            'showErpDailyRegister',
            'showErpAppointments',
            'showErpAttendance',
            'showErpJobCardChart',
            'showErpDailyDataChart',
            'showErpLatestJobCard',
            'showErpLatestDailyData',
            // HR Dashboard visibility flags
            'showHrDashboardSection',
            'showHrStaffStrength',
            'showHrActiveStaff',
            'showHrMonthlyAttendance',
            'showHrPersonalProgress',
            'showHrAttendancePattern',
            'showHrSalaryPayrollTrend',
            'showHrPayrollSnapshot',
            'showHrAttendanceWatch',
            'showHrPayrollStatus',
            // HR Dashboard data
            'totalStaff',
            'activeStaff',
            'newStaffThisMonth',
            'todayPresentStaff',
            'todayAbsentStaff',
            'todayUnmarkedStaff',
            'monthlyAttendanceRate',
            'salaryPaidCount',
            'salaryPendingCount',
            'salaryPaidAmount',
            'advanceOutstanding',
            'hrAttendanceLabels',
            'hrAttendancePresent',
            'hrAttendanceAbsent',
            'hrAttendanceHalfDay',
            'hrSalaryLabels',
            'hrSalaryPaidAmounts',
            'hrSalaryPaidCounts',
            'attendanceWatch',
            'payrollStatusRows'
        ));
    }

    public function filterData(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $selectedSubAdminId = $request->selectedSubAdminId;
        $filterId = $request->get('filterId');

        // Date filter
        switch ($filter) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = Carbon::now()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfMonth()->endOfDay();
                break;
            default:
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
        }

        // Decide branch filter value (default to 1)
        $branchId = auth()->user()->id; // default branch
        if (!empty($selectedSubAdminId)) {
            $branchId = $selectedSubAdminId;
        } elseif (!empty($filterId)) {
            $branchId = $filterId;
        }
        $start1 = Carbon::parse($start)->format('Y-m-d');
        $end1   = Carbon::parse($end)->format('Y-m-d');

        // Total Collection (branch_id column in payment_store = `branch_id`)
        $totalCollection = DB::table('payment_store')
            ->join('job_cards', 'job_cards.id', '=', 'payment_store.jobcard_id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->where('job_cards.isDeleted', '!=', 1) // exclude deleted jobcards too
            ->whereBetween('job_cards.date', [$start1, $end1])
            ->when($branchId, fn($q) => $q->where('job_cards.subbranch_id', $branchId))
            ->sum('payment_store.payment_amount');

        // Inside filterData()

        // ✅ Total Orders (from Order Payments in payment_store)
        $totalOrders = DB::table('payment_store')
            ->join('orders', 'orders.id', '=', 'payment_store.order_id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->where('orders.isDeleted', '!=', 1)
            ->whereBetween('payment_store.payment_date', [$start1, $end1]) // payment date for accuracy
            ->when($branchId, fn($q) => $q->where('orders.branch_id', $branchId))
            ->sum('payment_store.payment_amount');

            $totalCollection1 = DB::table('payment_store')
            ->join('job_cards', 'job_cards.id', '=', 'payment_store.jobcard_id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->where('job_cards.type',  'dailyData') // exclude deleted jobcards too
            ->where('job_cards.isDeleted', '!=', 1) // exclude deleted jobcards too
            ->whereBetween('job_cards.date', [$start1, $end1])
            ->when($branchId, fn($q) => $q->where('job_cards.subbranch_id', $branchId))
            ->sum('payment_store.payment_amount');


            // dd($totalCollection1,$totalOrders);

        // Total Job Cards (branch column = `subbranch_id`)
        $totalJobCards = DB::table('job_cards')
            ->where('type', 'jobCard')
            ->where('isDeleted', '!=', 1)
            ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
            ->when($branchId, fn($q) => $q->where('user_id', $branchId))
            ->count();
        // ✅ Total Orders (branch column = branch_id)
        $totalOrdersCount = DB::table('orders')
            ->where('isDeleted', '!=', 1)
            ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        // Total Daily Data (branch column = `subbranch_id`)
        $totalDailyData = DB::table('job_cards')
            ->where('type', 'dailyData')
            ->where('isDeleted', '!=', 1)
            ->whereBetween('record_date', [$start1, $end1])
            ->when($branchId, fn($q) => $q->where('subbranch_id', $branchId))
            ->count();

        // Total Appointments (branch column = `branch_id`)
        $totalAppointments = DB::table('appointments')
            ->where('is_deleted', '!=', 1)
            ->whereBetween('appointment_date', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        // // Attendance (branch column = `branch_id`)
        // $totalPresent = DB::table('attendance')
        //     ->where('status', 'P')
        //     ->whereBetween('date', [$start, $end])
        //     ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
        //     ->count();

        $present = \DB::table('attendance')
            
            ->where('status', 'P')
            ->whereBetween('date', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        // ✅ Count half-day
        $halfday = \DB::table('attendance')
            
            ->where('status', 'H')
            ->whereBetween('date', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

        // ✅ Final total present with half-day as 0.5
        $totalPresent = $present + ($halfday * 0.5);

        $totalAbsent = DB::table('attendance')
            ->where('status', 'A')
            ->whereBetween('date', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->count();

            if (!empty($selectedSubAdminId)) {
            // -----------------------------
            // Pending Orders (branch_id)
            // -----------------------------
            $totalOrderAmount = DB::table('orders')
                ->where('isDeleted', '!=', 1)
                ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->sum('remaining_amount');
 
            $totalPaymentAmount = DB::table('payment_store')
                ->join('orders', 'orders.id', '=', 'payment_store.order_id')
                ->where('payment_store.isDeleted', '!=', 1)
                ->where('orders.isDeleted', '!=', 1)
                ->whereBetween(DB::raw('DATE(payment_store.payment_date)'), [$start1, $end1])
                ->when($branchId, fn($q) => $q->where('orders.branch_id', $branchId))
                ->sum('payment_store.payment_amount');

                $totalJobcardAmount1 = DB::table('job_cards')
                ->where('isDeleted', '!=', 1)
                ->where('type',  'dailyData')
                ->whereNull('job_cards.old_job_card')
                ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
                ->when($branchId, fn($q) => $q->where('subbranch_id', $branchId))
                ->sum('remaining_amount');

                // dd($totalJobcardAmount1);
 
            // $pendingAmount = abs($totalOrderAmount - $totalPaymentAmount);
            $pendingAmount = abs($totalOrderAmount + $totalJobcardAmount1);
        } else {
            // -----------------------------
            // Pending Job Cards (subbranch_id)
            // -----------------------------
            $totalJobcardAmount = DB::table('job_cards')
                ->where('isDeleted', '!=', 1)
                ->whereNull('job_cards.old_job_card')
                ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
                ->when($branchId, fn($q) => $q->where('subbranch_id', $branchId))
                ->sum('remaining_amount');
 
            $totalPaymentAmount = DB::table('payment_store')
                ->join('job_cards', 'job_cards.id', '=', 'payment_store.jobcard_id')
                ->where('payment_store.isDeleted', '!=', 1)
                ->where('job_cards.isDeleted', '!=', 1)
                ->whereBetween(DB::raw('DATE(job_cards.date)'), [$start1, $end1])
                ->when($branchId, fn($q) => $q->where('job_cards.subbranch_id', $branchId))
                ->sum('payment_store.payment_amount');
 
            // $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);
            $pendingAmount = abs($totalJobcardAmount);
        }
 

        


        // $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);

        

        // Expenses (branch column = `branch_id`)
        $totalExpenseAmount = DB::table('expenses')
            ->where('isDeleted', '!=', 1)
            ->whereBetween('created_at', [$start, $end])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        // ✅ Decide what to show: Orders vs Collections
        if (!empty($request->selectedSubAdminId)) {
            // Sub-Admin selected → show Total Orders (payments received for orders)
            $mainAmount = $totalOrders + $totalCollection1;
            $mainAmountFormatted = number_format($mainAmount, 2);
        } else {
            // No Sub-Admin selected → show Total Collections (payments received for jobcards)
            $mainAmount = $totalCollection;
            $mainAmountFormatted = number_format($totalCollection, 2);
        }

        

        return response()->json([
            // 'totalCollection' => $totalCollection,
            // 'totalCollectionFormatted' => number_format($totalCollection, 2),
            'mainAmount' => $mainAmount,
            'mainAmountFormatted' => $mainAmountFormatted,
            'totalExpenseAmount' => $totalExpenseAmount,
            'totalExpenseAmountFormatted' => number_format($totalExpenseAmount, 2),
            'pendingAmount' => $pendingAmount,
            'pendingAmountFormatted' => number_format($pendingAmount, 2),
            'totalJobCards' => $totalJobCards,
            'totalOrdersCount' => $totalOrdersCount,
            'totalDailyData' => $totalDailyData,
            'totalAppointments' => $totalAppointments,
            'totalAbsent' => $totalAbsent,
            'totalPresent' => $totalPresent,
        ]);
    }

    public function getFilteredDashboardData(Request $request)
    {
        $selectedSubAdminId = $request->query('sub_admin_id');
        session(['selectedSubAdminId' => $selectedSubAdminId]);
        
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;
        $currentMonth = Carbon::now()->month;

        // Get user info
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId   = $user->id ?? null;

        // Determine branch ID filter
        $selectedSubAdminId = $selectedSubAdminId;

        if (!empty($selectedSubAdminId)) {
            $filterId = (int) $selectedSubAdminId; // Explicitly provided branch
        } else {
            $filterId = $userId; // Always own branch if no selection
        }

        $filter = $request->get('filter', 'today');

        // Set date range based on filter
        switch ($filter) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            default: // today
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
        }
        $start1 = Carbon::parse($start)->format('Y-m-d');
        $end1   = Carbon::parse($end)->format('Y-m-d');

        // Totals
        $totalJobCards = \DB::table('job_cards')
            ->where('user_id', $filterId)
            ->where('type', 'jobCard')
            ->whereBetween(\DB::raw('DATE(created_at)'), [$start1, $end1])
            ->count();

        // ✅ Total Orders (branch column = branch_id)
        $totalOrdersCount = DB::table('orders')
            ->where('isDeleted', '!=', 1)
            ->whereBetween(DB::raw('DATE(date)'), [$start1, $end1])
            ->when($filterId, fn($q) => $q->where('branch_id', $filterId))
            ->count();

        $totalDailyData = \DB::table('job_cards')
            ->where('subbranch_id', $filterId)
            ->where('type', 'dailyData')
            ->whereBetween('record_date', [$start1, $end1])
            ->count();

        $totalAppointments = \DB::table('appointments')
            ->where('branch_id', $filterId)
            ->whereBetween('appointment_date', [$start, $end])
            ->count();

        $totalAttendance = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->whereBetween('date', [$start, $end])
            ->count();

        $present = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'P')
            ->whereBetween('date', [$start, $end])
            ->count();

        // ✅ Count half-day
        $halfday = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'H')
            ->whereBetween('date', [$start, $end])
            ->count();

        // ✅ Final total present with half-day as 0.5
        $totalPresent = $present + ($halfday * 0.5);

        $totalAbsent = \DB::table('attendance')
            ->where('branch_id', $filterId)
            ->where('status', 'A')
            ->whereBetween('date', [$start, $end])
            ->count();

        // $totalJobcardAmount = \DB::table('job_cards')
        //     ->where('job_cards.subbranch_id', $filterId)
        //     ->where('job_cards.isDeleted', '!=', 1)
        //     ->whereBetween('job_cards.record_date', [$start1, $end1])
        //     ->sum('job_cards.total_subtotal_amount');



        // // 2️⃣ Total payment amount (join with job_cards to filter by branch)
        // $totalPaymentAmount = \DB::table('payment_store')
        //     ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
        //     ->where('job_cards.subbranch_id', $filterId)
        //     ->where('payment_store.isDeleted', '!=', 1)
        //     ->whereBetween('payment_store.payment_date', [$start, $end])
        //     ->sum('payment_store.payment_amount');


        // // 3️⃣ Pending amount
        // $pendingAmount = $totalJobcardAmount - $totalPaymentAmount;
        // if (!empty($selectedSubAdminId)) {
        //     // -----------------------------
        //     // Pending Orders
        //     // -----------------------------
        //     $totalOrderAmount = \DB::table('orders')
        //         ->where('orders.branch_id', $filterId)
        //         ->where('orders.isDeleted', '!=', 1)
        //         ->whereBetween('orders.created_at', [$start, $end])
        //         ->sum('orders.total_amount');

        //     $totalPaymentAmount = \DB::table('payment_store')
        //         ->join('orders', 'payment_store.order_id', '=', 'ord$latestSales = JobCard::with(['customer', 'branch'])ers.id')
        //         ->where('orders.branch_id', $filterId)
        //         ->where('payment_store.isDeleted', '!=', 1)
        //         ->whereBetween('payment_store.payment_date', [$start, $end])
        //         ->sum('payment_store.payment_amount');

        //     $pendingAmount = abs($totalOrderAmount - $totalPaymentAmount);
        

        // } else {
        //     // -----------------------------
        //     // Pending Job Cards
        //     // -----------------------------
        //     $totalJobcardAmount = \DB::table('job_cards')
        //         ->where('job_cards.subbranch_id', $filterId)
        //         ->where('job_cards.isDeleted', '!=', 1)
        //         ->whereBetween('job_cards.date', [$start1, $end1])
        //         ->sum('job_cards.total_subtotal_amount');

        //     $totalPaymentAmount = \DB::table('payment_store')
        //         ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
        //         ->where('job_cards.subbranch_id', $filterId)
        //         ->where('payment_store.isDeleted', '!=', 1)
        //         ->whereBetween('job_cards.date', [$start, $end])
        //         ->sum('payment_store.payment_amount');

        //     $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);
        // }

        if (!empty($selectedSubAdminId)) {
            // -----------------------------
            // Pending Orders
            // -----------------------------
            $totalDailyData1 = \DB::table('job_cards')
                ->where('job_cards.subbranch_id', $filterId)
                ->where('job_cards.isDeleted', '!=', 1)
                ->where('job_cards.type', '=', 'dailyData')
                ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ use jobcard.date
                ->sum('job_cards.remaining_amount');
            $totalOrderAmount = \DB::table('orders')
                ->where('orders.branch_id', $filterId)
                ->where('orders.isDeleted', '!=', 1)
                ->whereBetween('orders.date', [$start1, $end1]) // ✅ use order.date
                ->sum('orders.remaining_amount');

            $totalPaymentAmount = \DB::table('payment_store')
                ->join('orders', 'payment_store.order_id', '=', 'orders.id')
                ->where('orders.branch_id', $filterId)
                ->where('orders.isDeleted', '!=', 1)
                ->where('payment_store.isDeleted', '!=', 1)
                ->whereBetween('orders.date', [$start1, $end1]) // ✅ match order date range
                ->sum('payment_store.payment_amount');

                
                // $pendingAmount = abs($totalOrderAmount - $totalPaymentAmount);
                $pendingAmount = abs($totalOrderAmount + $totalDailyData1);
                
        } else {
            // -----------------------------
            // Pending Job Cards
            // -----------------------------
            $totalJobcardAmount = \DB::table('job_cards')
                ->where('job_cards.subbranch_id', $filterId)
                ->where('job_cards.isDeleted', '!=', 1)
                ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ use jobcard.date
                ->whereNull('job_cards.old_job_card')
                ->sum('job_cards.total_subtotal_amount');

            $totalPaymentAmount = \DB::table('payment_store')
                ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
                ->where('job_cards.subbranch_id', $filterId)
                ->where('payment_store.isDeleted', '!=', 1)
                ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ match jobcard date range
                ->sum('payment_store.payment_amount');

            $pendingAmount = abs($totalJobcardAmount - $totalPaymentAmount);
        }
        

        // Decide chart type: Orders if Sub-Admin selected, else JobCard
        if (!empty($selectedSubAdminId)) {
            // Orders chart
            // $salesChartThisMonth    = $this->getOrdersDataByMonth($currentMonth, $filterId);
            // $salesChartthisyear     = $this->getOrdersDataByYear($currentYear, $filterId);
            // $salesChartpreviousyear = $this->getOrdersDataByYear($previousYear, $filterId);
            // $salesChartLabel        = "Order";
        } else {
            // Job Card chart
            $salesChartThisMonth    = $this->getSalesDataByMonth($currentMonth, $userRole, $filterId, $userId);
            $salesChartthisyear     = $this->getSalesDataByYear($currentYear, $userRole, $filterId, $userId);
            $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $userRole, $filterId, $userId);
            $salesChartLabel        = "Job Card";
        }

        // $salesChartThisMonth = $this->getSalesDataByMonth($currentMonth, $userRole, $filterId, $userId);
        $purchaseChartThisMonth = $this->getPurchaseDataByMonth($currentMonth, $userRole, $filterId, $userId);

        // $salesChartthisyear = $this->getSalesDataByYear($currentYear, $userRole, $filterId, $userId);
        // $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $userRole, $filterId, $userId);

        $purchaseChartthisyear = $this->getPurchaseDataByYear($currentYear, $userRole, $filterId, $userId);
        $purchaseChartpreviousyear = $this->getPurchaseDataByYear($previousYear, $userRole, $filterId, $userId);

        // Base totals
        $totalPurchaseAmount = PurchaseInvoice::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->sum('grand_total');

        $totalCollection = DB::table('payment_store')
            ->join('orders', 'payment_store.order_id', '=', 'orders.id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->whereBetween('payment_store.payment_date', [$start, $end]);

        if (!empty($filterId)) {
            $totalCollection->where('orders.branch_id', $filterId);
        }

        
        $totalCollection = $totalCollection->sum('payment_store.payment_amount'); // change column name if it's not 'amount'

        if ($filterId) {
        }
        $totalDailyData1 = \DB::table('payment_store')
                ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
                // ->where('job_cards.subbranch_id', $filterId)
                // ->where('job_cards.type', 'dailyData')
                // ->where('payment_store.isDeleted', '!=', 1)
                // ->whereBetween('job_cards.date', [$start1, $end1]) // ✅ match jobcard date range
                ->sum('payment_store.payment_amount');
        // ✅ Total Orders (from Order Payments in payment_store)
        $totalOrders = DB::table('payment_store')
            ->join('orders', 'orders.id', '=', 'payment_store.order_id')
            ->where('payment_store.isDeleted', '!=', 1)
            ->where('orders.isDeleted', '!=', 1)
            ->whereBetween('payment_store.payment_date', [$start1, $end1]) // payment date for accuracy
            ->when($filterId, fn($q) => $q->where('orders.branch_id', $filterId))
            ->sum('payment_store.payment_amount');
        
        // ✅ Decide what to show: Orders vs Collections
        if (!empty($request->selectedSubAdminId)) {
            // Sub-Admin selected → show Total Orders (payments received for orders)
            $mainAmount = $totalOrders;
            $mainAmountFormatted = number_format($totalOrders, 2);
        } else {
            // No Sub-Admin selected → show Total Collections (payments received for jobcards)
            $mainAmount = $totalCollection;
            $mainAmountFormatted = number_format($totalCollection, 2);
        }
        // $totalExpenseAmount = Expense::where('isDeleted', '!=', 1)
        //     ->where('branch_id', $filterId)
        //     ->whereBetween('expense_date', [$start, $end])
        //     ->sum('amount');
        $totalExpenseAmount = Expense::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->whereBetween('expense_date', [
                Carbon::parse($start)->format('Y-m-d'),
                Carbon::parse($end)->format('Y-m-d')
            ])
            ->sum('amount');

        // Customer/vendor counts
        $customerCount = User::where('role', 'customer')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        $vendorCount = User::where('role', 'vendor')
            ->where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        // Invoice counts
        $purchaseInvoiceCount = PurchaseInvoice::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        $salesInvoiceCount = Order::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->count();

        // Recent products
        $recentProducts = Product::where('isDeleted', '!=', 1)
            ->where('branch_id', $filterId)
            ->latest()
            ->take(4)
            ->get();

        // Latest Sales
        $latestSales = JobCard::where('type', 'jobCard')
            ->with('customer', 'technicianUser', 'paymentStore')
            ->where('subbranch_id', $filterId)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        $latestPurchases = JobCard::where('type', 'dailyData')
            ->with('customer', 'technicianUser', 'paymentStore')
            ->where('subbranch_id', $filterId)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        

        $salesData = [];
        // $latestSales = collect(); // default empty collection
        // if (in_array($userRole, ['admin', 'sub-admin', 'sales-manager', 'inventory-manager'])) {
        //     $salesData = JobCard::where('type', 'jobCard')
        //         ->with('customer', 'technicianUser', 'paymentStore')
        //         ->where('isDeleted', 0)
        //         ->where('subbranch_id', $filterId)
        //         ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
        //         ->groupByRaw('MONTH(created_at)')
        //         ->pluck('total', 'month')
        //         ->toArray();

        //     // Latest 4 sales (jobCard)
        //     $latestSales = JobCard::where('type', 'jobCard')
        //         ->with('customer', 'technicianUser', 'paymentStore')
        //         ->where('isDeleted', 0)
        //         ->where('subbranch_id', $filterId)
        //         ->orderBy('created_at', 'desc')
        //         ->take(4)
        //         ->get();
        // }
        $salesData = [];
        $latestSales = collect();

        if (in_array($userRole, ['admin', 'sub-admin', 'sales-manager', 'inventory-manager'])) {
            // if ($filterId) {
            //     // Chart Data
            //     $salesData = Order::where('isDeleted', 0)
            //         ->where('branch_id', $filterId)
            //         ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            //         ->groupByRaw('MONTH(created_at)')
            //         ->pluck('total', 'month')
            //         ->toArray();

            //     // Latest Orders
            //     $latestSales = Order::with('user') // eager load user relation
            //         ->where('isDeleted', 0)
            //         ->where('branch_id', $filterId)
            //         ->orderBy('created_at', 'desc')
            //         ->take(4)
            //         ->get();
            // } else {
            //     // Chart Data
            //     $salesData = JobCard::where('type', 'jobCard')
            //         ->where('isDeleted', 0)
            //         ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
            //         ->groupByRaw('MONTH(created_at)')
            //         ->pluck('total', 'month')
            //         ->toArray();

            //     // Latest Job Cards
            //     $latestSales = JobCard::where('type', 'jobCard')
            //         ->with('customer', 'technicianUser', 'paymentStore')
            //         ->where('isDeleted', 0)
            //         ->orderBy('created_at', 'desc')
            //         ->take(4)
            //         ->get();
            // }
            $salesData = JobCard::where('type', 'jobCard')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
                ->groupByRaw('MONTH(created_at)')
                ->pluck('total', 'month')
                ->toArray();

            $latestSales = JobCard::with('customer')
                ->where('type', 'jobCard')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
        }
         $salesChartLabel = "Job Card";


        // Purchase chart & latest 4 purchases from JobCard
        $purchasesData = [];
        $latestPurchases = collect(); // default empty collection
        if (in_array($userRole, ['admin', 'sub-admin', 'purchase-manager', 'inventory-manager'])) {
            $purchasesData = JobCard::where('type', 'dailyData')
                ->with('customer', 'technicianUser', 'paymentStore')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
                ->groupByRaw('MONTH(created_at)')
                ->pluck('total', 'month')
                ->toArray();

            // Latest 4 purchases (dailyData)
            $latestPurchases = JobCard::where('type', 'dailyData')
                ->with('customer', 'technicianUser', 'paymentStore')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
        }

        // Fill chart arrays for 12 months
        $salesChart = [];
        $purchaseChart = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesChart[]    = (float) ($salesData[$m] ?? 0);
            $purchaseChart[] = (float) ($purchasesData[$m] ?? 0);
        }


        // Settings
        $settings = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';


        $today = Carbon::today()->format('Y-m-d');
        $nextWeek = Carbon::today()->addDays(7);

        $user = Auth::user();
        $subAdminId = session('selectedSubAdminId') ?? $user->id;

        $appointments = Appointment::whereDate('appointment_date', $today)
            ->where('branch_id', $subAdminId)
            ->where('is_deleted', 0)
            ->get();
        

        $upcomingEmis = PaymentStore::select(
            'payment_store.*',
            'job_cards.vehicle_no',
            'job_cards.model',
            'users.name as customer_name'
        )
            ->join('job_cards', 'payment_store.jobcard_id', '=', 'job_cards.id')
            ->join('users', 'job_cards.customer_id', '=', 'users.id')
            ->where('payment_store.payment_method', 'EMI')
            ->where('payment_store.isDeleted', 0)
            ->where('job_cards.subbranch_id', $subAdminId)
            ->get()
            ->filter(function ($payment) use ($today, $nextWeek) {
                $dueDate = Carbon::parse($payment->created_at)->addMonths($payment->emi_month);
                $payment->emi_due_date = $dueDate->format('d-m-Y');
                return $dueDate->between($today, $nextWeek);
            });
            // ALWAYS USE JOB CARD
            $salesChartThisMonth    = $this->getSalesDataByMonth($currentMonth, $userRole, $filterId, $userId);
            $salesChartthisyear     = $this->getSalesDataByYear($currentYear, $userRole, $filterId, $userId);
            $salesChartpreviousyear = $this->getSalesDataByYear($previousYear, $userRole, $filterId, $userId);

            $salesChartLabel = "Job Card";
            // $latestSales = JobCard::with(['customer', 'branch']) // make relation if not exist
            //     ->where('type', 'jobCard')
            //     ->where('isDeleted', 0)
            //     ->orderBy('created_at', 'desc')
            //     ->get();
            $latestSales = JobCard::with(['customer', 'branch'])
                ->where('type', 'jobCard')
                ->where('isDeleted', 0)
                ->where('subbranch_id', $filterId) // ✅ MUST ADD
                ->orderBy('created_at', 'desc')
                ->get();

                // dd($filterId);
       $quotationCount = DB::table('orders')
    ->where('isDeleted', '!=', 1)
    ->where('branch_id', $filterId)
    ->where('quotation_status', 'quotation')
    ->count();
        return response()->json([
            'status' => true,
            'data' => [
                'pendingAmount' => $pendingAmount,
                'totalAbsent' => $totalAbsent,
                'totalPresent' => $totalPresent,
                'totalAttendance' => $totalAttendance,
                'totalAppointments' => $totalAppointments,
                'totalDailyData' => $totalDailyData,
                // 'totalCollection' => $totalCollection,
                // ✅ Return both values separately
                'totalCollection' => $totalCollection,
                'totalCollectionFormatted' => number_format($totalCollection, 2),

                // 'totalOrders' => $totalOrders,
                'totalOrdersFormatted' => number_format($totalOrders, 2),

                // ✅ Keep mainAmount for your widget logic
                'mainAmount' => $mainAmount,
                'mainAmountFormatted' => $mainAmountFormatted,
                'totalJobCards' => $totalJobCards,
                'totalOrdersCount' => $totalOrdersCount,
                'totalPurchaseAmount' => $totalPurchaseAmount,
                'totalExpenseAmount' => $totalExpenseAmount,
                'customerCount' => $customerCount,
                'vendorCount' => $vendorCount,
                'purchaseInvoiceCount' => $purchaseInvoiceCount,
                'salesInvoiceCount' => $salesInvoiceCount,
                'quotationCount' => $quotationCount,
                'currencySymbol' => $currencySymbol,
                'currencyPosition' => $currencyPosition,
                // 'salesChartThisMonth' => $salesChartThisMonth,
                'purchaseChartThisMonth' => $purchaseChartThisMonth,
                // 'salesChartthisyear' => $salesChartthisyear,
                // 'salesChartpreviousyear' => $salesChartpreviousyear,
                'salesChartLabel'        => $salesChartLabel,
                'salesChartThisMonth'    => $salesChartThisMonth,
                'salesChartthisyear'     => $salesChartthisyear,
                'salesChartpreviousyear' => $salesChartpreviousyear,
                'purchaseChartthisyear' => $purchaseChartthisyear,
                'purchaseChartpreviousyear' => $purchaseChartpreviousyear,
                'latestSales' => $latestSales,
                'latestPurchases' => $latestPurchases,
                'appointments' => $appointments,
                'upcomingEmis' => $upcomingEmis,
            ]
        ]);
    }


    private function getSalesDataByMonth($month, $userRole = null, $filterId = null, $userId = null)
    {
        $salesDataQuery = JobCard::where('type', 'jobCard')
            ->where('isDeleted', 0)
            ->whereMonth('created_at', $month);

        // Apply branch/subbranch filter
        if ($filterId) {
            $salesDataQuery->where('subbranch_id', $filterId);
        }

        $salesData = $salesDataQuery
            ->selectRaw('DAY(created_at) as day, SUM(total_subtotal_amount) as total')
            ->groupByRaw('DAY(created_at)')
            ->pluck('total', 'day')
            ->toArray();

        // Fill 31-day chart
        $chartData = [];
        for ($d = 1; $d <= 31; $d++) {
            $chartData[] = (float) ($salesData[$d] ?? 0);
        }
        return $chartData;
    }


    private function getPurchaseDataByMonth($month, $userRole = null, $filterId = null, $userId = null)
    {
        $purchaseDataQuery = JobCard::where('type', 'dailyData')
            ->where('isDeleted', 0)
            ->whereMonth('created_at', $month);

        // Apply branch/subbranch filter
        if ($filterId) {
            $purchaseDataQuery->where('subbranch_id', $filterId);
        }

        $purchaseData = $purchaseDataQuery
            ->selectRaw('DAY(created_at) as day, SUM(total_subtotal_amount) as total')
            ->groupByRaw('DAY(created_at)')
            ->pluck('total', 'day')
            ->toArray();

        // Fill 31-day chart
        $chartData = [];
        for ($d = 1; $d <= 31; $d++) {
            $chartData[] = (float) ($purchaseData[$d] ?? 0);
        }
        return $chartData;
    }


    private function getSalesDataByYear($year, $userRole = null, $filterId = null, $userId = null)
    {
        $salesDataQuery = JobCard::where('type', 'jobCard')
            ->where('isDeleted', 0)
            ->whereYear('date', $year);

        if ($filterId) {
            $salesDataQuery->where('subbranch_id', $filterId);
        }

        $salesData = $salesDataQuery
            ->selectRaw('MONTH(date) as month, SUM(total_subtotal_amount) as total')
            ->groupByRaw('MONTH(date)')
            ->pluck('total', 'month')
            ->toArray();

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart[] = (float) ($salesData[$m] ?? 0);
        }
        return $chart;
    }


    private function getPurchaseDataByYear($year, $userRole = null, $filterId = null, $userId = null)
    {
        $purchaseDataQuery = JobCard::where('type', 'dailyData')
            ->where('isDeleted', 0)
            ->whereYear('created_at', $year);

        if ($filterId) {
            $purchaseDataQuery->where('subbranch_id', $filterId);
        }

        $purchaseData = $purchaseDataQuery
            ->selectRaw('MONTH(created_at) as month, SUM(total_subtotal_amount) as total')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart[] = (float) ($purchaseData[$m] ?? 0);
        }
        return $chart;
    }

    private function getOrdersDataByMonth($month, $filterId = null)
    {
        $ordersQuery = Order::where('isDeleted', 0)
            ->whereMonth('created_at', $month);

        if ($filterId) {
            $ordersQuery->where('branch_id', $filterId);
        }

        $ordersData = $ordersQuery
            ->selectRaw('DAY(created_at) as day, SUM(total_amount) as total')
            ->groupByRaw('DAY(created_at)')
            ->pluck('total', 'day')
            ->toArray();

        $chartData = [];
        for ($d = 1; $d <= 31; $d++) {
            $chartData[] = (float) ($ordersData[$d] ?? 0);
        }
        return $chartData;
    }

    private function getOrdersDataByYear($year, $filterId = null)
    {
        $ordersQuery = Order::where('isDeleted', 0)
            ->whereYear('created_at', $year);

        if ($filterId) {
            $ordersQuery->where('branch_id', $filterId);
        }

        $ordersData = $ordersQuery
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart[] = (float) ($ordersData[$m] ?? 0);
        }
        return $chart;
    }


    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }
    public function taxrates()
    {

        return view('taxrates');
    }
    public function currency()
    {
        return view('currency');
    }
    public function ajaxSearch(Request $request)
    {
        $query = $request->get('query');
        $selectedSubAdminId = $request->get('selectedSubAdminId');
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // Helper to format amount
        $formatCurrency = function ($amount) use ($currencySymbol, $currencyPosition) {
            return $currencyPosition === 'left'
                ? $currencySymbol . number_format($amount, 2)
                : number_format($amount, 2) . $currencySymbol;
        };

        // Determine branch_id to filter by
        $branchId = null;
        if (!empty($selectedSubAdminId)) {
            $branchId = $selectedSubAdminId;
        } elseif (!empty($userId) && $userRole === 'sub-admin') {
            $branchId = $userId;
        }

        // Users query
        $usersQuery = User::where('name', 'LIKE', "%{$query}%")
            ->whereIn('role', ['customer', 'vendor']);

        if ($branchId) {
            $usersQuery->where('branch_id', $branchId);
        }

        $users = $usersQuery->take(10)
            ->get(['id', 'name', 'email', 'profile_image', 'role']);

        // Products query
        $productsQuery = Product::where('name', 'LIKE', "%{$query}%");

        if ($branchId) {
            $productsQuery->where('branch_id', $branchId);
        }

        $products = $productsQuery->take(10)
            ->get(['id', 'name', 'price', 'images', 'category_id']);

        // Orders query
        $ordersQuery = Order::with('user:id,name')
            ->where(function ($q) use ($query) {
                $q->where('order_number', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', function ($q2) use ($query) {
                        $q2->where('name', 'LIKE', "%{$query}%");
                    });
            });

        if ($branchId) {
            $ordersQuery->where('branch_id', $branchId);
        }

        $orders = $ordersQuery->take(10)
            ->get(['id', 'order_number', 'user_id', 'total_amount', 'payment_status']);

        // Final results
        $results = [
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image
                        ? asset('/public/storage/' . $user->profile_image)
                        : asset('/public/admin/assets/img/customer/default.jpg'),
                    'role' => $user->role,
                ];
            }),
            'products' => $products->map(function ($product) use ($formatCurrency) {
                $imageFileName = null;
                if (!empty($product->images)) {
                    $imagesArray = json_decode($product->images, true);
                    if (is_array($imagesArray) && count($imagesArray) > 0) {
                        $imageFileName = $imagesArray[0];
                    }
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $formatCurrency($product->price),
                    'category_id' => $product->category_id,
                    'image' => $imageFileName
                        ? asset('/public/storage/' . $imageFileName)
                        : asset('/public/admin/assets/img/product/noimage.png'),
                ];
            }),
            'orders' => $orders->map(function ($order) use ($formatCurrency) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $formatCurrency($order->total_amount),
                    'payment_status' => $order->payment_status,
                    'user_name' => $order->user ? $order->user->name : 'N/A',
                ];
            }),
        ];

        return response()->json($results);
    }
   public function staffDashboard()
    {
        $user = auth()->user();
 
        // Only staff can access this page; others go to main dashboard
        if (strtolower($user->role) !== 'staff') {
            return redirect()->route('auth.dashboard');
        }
 
        $now          = \Carbon\Carbon::now('Asia/Kolkata');
        $currentMonth = $now->month;
        $currentYear  = $now->year;
        $monthName    = $now->format('F Y');
 
        // ── Attendance stats for current month ──────────────────────────────
        $attendanceRecords = \App\Models\Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
 
        $totalPresent = $attendanceRecords->whereIn('status', ['P', 'H'])->unique('date')->count();
        $leaveModel   = new \App\Models\LeaveModel();
        $totalLeave   = $leaveModel->getLeavesCount($user->id, sprintf('%04d-%02d', $currentYear, $currentMonth));
 
        // ── Today's Attendance — Source of Truth: Attendance Table ─────────
        $todayIST = \Carbon\Carbon::now('Asia/Kolkata');
        $todayStr = $todayIST->toDateString();  // e.g. "2026-06-01"
 
        // ── Today's Attendance — Source of Truth: Attendance Table ─────────
        $todayIST = \Carbon\Carbon::now('Asia/Kolkata');
        $todayStr = $todayIST->toDateString();  // e.g. "2026-06-01"
 
        // ── Settings: standard working hours ────────────────────────────
        $settings = \App\Models\Setting::where('branch_id', $user->branch_id ?? 0)->first()
            ?? \App\Models\Setting::first();
 
        $workingHoursStr = ($settings && $settings->working_hours) ? $settings->working_hours : '08:30:00';
        $parts   = explode(':', $workingHoursStr);
        $wHours  = isset($parts[0]) ? (int)$parts[0] : 8;
        $wMins   = isset($parts[1]) ? (int)$parts[1] : 30;
        $wSecs   = isset($parts[2]) ? (int)$parts[2] : 0;
        $standardSeconds = ($wHours * 3600) + ($wMins * 60) + $wSecs;
        if ($standardSeconds <= 0) { $standardSeconds = 30600; $wHours = 8; $wMins = 30; $wSecs = 0; }
        $standardHoursFormatted = sprintf('%02d:%02d:%02d', $wHours, $wMins, $wSecs);
 
        // ── Fetch today's Attendance records ──────────────────────────────
        $todaySessions = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $todayStr)
            ->orderBy('check_in_time', 'asc')
            ->get();
 
        $staffJobCardsCount = JobCard::where('type', 'jobCard')
            ->where('user_id', $user->id)
            ->count();
        $staffDailyDataCount = JobCard::where('type', 'dailyData')
            ->where('user_id', $user->id)
            ->count();
        $recentJobCards = JobCard::where('type', 'jobCard')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        $recentDailyData = JobCard::where('type', 'dailyData')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
 
        // ── Safe defaults (no sessions today = blank state) ───────────────
        $todayCheckInTime        = null;
        $todayCheckInUnix        = null;
        $todayCheckOutTime       = null;
        $todayCheckOutUnix       = null;
        $hoursWorkedFormatted    = '00:00:00';
        $remainingHoursFormatted = $standardHoursFormatted;
        $workedPercentage        = 0;
        $remainingPercentage     = 100;
        $todayStatus             = 'Not Checked In';
        $totalWorkedSeconds      = 0;
        $activeSession           = null; // Maintained for view compatibility
 
        if ($todaySessions->isNotEmpty()) {
 
            // ── Helper: safely parse a time string (H:i:s or H:i) ───────────
            $parseIST = function (string $timeStr) use ($todayStr): \Carbon\Carbon {
                if (strlen($timeStr) <= 5) { $timeStr .= ':00'; }
                return \Carbon\Carbon::parse($todayStr . ' ' . $timeStr, 'Asia/Kolkata');
            };
            
            $firstSession = $todaySessions->first();
 
            // ── Check In ────────────────────────────────────────────────────
            $checkInCarbon    = $parseIST($firstSession->check_in_time);
            $todayCheckInTime = $checkInCarbon->format('h:i A');
            $todayCheckInUnix = $checkInCarbon->timestamp;
            
            $todayStatus = 'Checked Out';
 
            // ── Check Out & Session Summation ───────────────────────────────
            foreach ($todaySessions as $session) {
                if (empty($session->check_in_time)) continue;
                $inCarbon = $parseIST($session->check_in_time);
                
                if (!empty($session->check_out_time)) {
                    $outCarbon = $parseIST($session->check_out_time);
                    $totalWorkedSeconds += max(0, $outCarbon->diffInSeconds($inCarbon));
                    
                    $todayCheckOutTime = $outCarbon->format('h:i A');
                    $todayCheckOutUnix = $outCarbon->timestamp;
                } else {
                    $todayStatus        = 'Checked In';
                    $activeSession      = (object)['id' => $session->id];
                    $totalWorkedSeconds += max(0, $todayIST->diffInSeconds($inCarbon));
                    $todayCheckOutTime  = null;
                    $todayCheckOutUnix  = null;
                }
            }
 
            $remainingSeconds        = max(0, $standardSeconds - $totalWorkedSeconds);
            $hoursWorkedFormatted    = sprintf('%02d:%02d:%02d', intdiv($totalWorkedSeconds, 3600), intdiv($totalWorkedSeconds % 3600, 60), $totalWorkedSeconds % 60);
            $remainingHoursFormatted = sprintf('%02d:%02d:%02d', intdiv($remainingSeconds, 3600), intdiv($remainingSeconds % 3600, 60), $remainingSeconds % 60);
            $workedPercentage        = min(100, round(($totalWorkedSeconds / $standardSeconds) * 100, 2));
            $remainingPercentage     = min(100, round(($remainingSeconds  / $standardSeconds) * 100, 2));
        }
 
        return view('staff_dashboard', compact(
            'user',
            'totalPresent',
            'totalLeave',
            'monthName',
            'currentMonth',
            'currentYear',
            'todayStr',
            'todaySessions',
            'todayCheckInTime',
            'todayCheckOutTime',
            'hoursWorkedFormatted',
            'remainingHoursFormatted',
            'workedPercentage',
            'remainingPercentage',
            'todayStatus',
            'activeSession',
            'standardHoursFormatted',
            'todayCheckInUnix',
            'todayCheckOutUnix',
            'standardSeconds',
            'totalWorkedSeconds',
            'staffJobCardsCount',
            'staffDailyDataCount',
            'recentJobCards',
            'recentDailyData'
        ));
    }

    public function staffDashboardData()
    {
        $user = auth()->user();
        if (!$user || strtolower($user->role) !== 'staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $now          = Carbon::now('Asia/Kolkata');
        $currentMonth = $now->month;
        $currentYear  = $now->year;
        $todayStr     = $now->toDateString();

        $attendanceRecords = \App\Models\Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $totalPresent = $attendanceRecords->whereIn('status', ['P', 'H'])->unique('date')->count();
        $leaveModel   = new \App\Models\LeaveModel();
        $totalLeave   = $leaveModel->getLeavesCount($user->id, sprintf('%04d-%02d', $currentYear, $currentMonth));

        $settings = \App\Models\Setting::where('branch_id', $user->branch_id ?? 0)->first()
            ?? \App\Models\Setting::first();

        $workingHoursStr = ($settings && $settings->working_hours) ? $settings->working_hours : '08:30:00';
        $parts   = explode(':', $workingHoursStr);
        $wHours  = isset($parts[0]) ? (int)$parts[0] : 8;
        $wMins   = isset($parts[1]) ? (int)$parts[1] : 30;
        $wSecs   = isset($parts[2]) ? (int)$parts[2] : 0;
        $standardSeconds = ($wHours * 3600) + ($wMins * 60) + $wSecs;
        if ($standardSeconds <= 0) {
            $standardSeconds = 30600;
            $wHours = 8;
            $wMins = 30;
            $wSecs = 0;
        }

        $todaySessions = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $todayStr)
            ->orderBy('check_in_time', 'asc')
            ->get();

        $staffJobCardsCount = JobCard::where('type', 'jobCard')
            ->where('user_id', $user->id)
            ->count();
        $staffDailyDataCount = JobCard::where('type', 'dailyData')
            ->where('user_id', $user->id)
            ->count();

        $recentJobCards = JobCard::where('type', 'jobCard')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['id', 'job_card_number', 'vehicle_no', 'total_subtotal_amount', 'created_at']);

        $recentDailyData = JobCard::where('type', 'dailyData')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['id', 'job_card_number', 'vehicle_no', 'total_subtotal_amount', 'created_at']);

        $todayStatus             = 'Not Checked In';
        $todayCheckInTime        = null;
        $todayCheckOutTime       = null;
        $hoursWorkedFormatted    = '00:00:00';
        $remainingHoursFormatted = sprintf('%02d:%02d:%02d', $wHours, $wMins, $wSecs);
        $workedPercentage        = 0;
        $remainingPercentage     = 100;
        $totalWorkedSeconds      = 0;

        if ($todaySessions->isNotEmpty()) {
            $parseIST = function (string $timeStr) use ($todayStr): \Carbon\Carbon {
                if (strlen($timeStr) <= 5) {
                    $timeStr .= ':00';
                }
                return \Carbon\Carbon::parse($todayStr . ' ' . $timeStr, 'Asia/Kolkata');
            };

            foreach ($todaySessions as $session) {
                if (empty($session->check_in_time)) {
                    continue;
                }

                $inCarbon = $parseIST($session->check_in_time);
                $todayCheckInTime = $todayCheckInTime ?: $inCarbon->format('h:i A');
                $todayStatus = 'Checked Out';

                if (!empty($session->check_out_time)) {
                    $outCarbon = $parseIST($session->check_out_time);
                    $totalWorkedSeconds += max(0, $outCarbon->diffInSeconds($inCarbon));
                    $todayCheckOutTime = $outCarbon->format('h:i A');
                } else {
                    $todayStatus = 'Checked In';
                    $totalWorkedSeconds += max(0, Carbon::now('Asia/Kolkata')->diffInSeconds($inCarbon));
                }
            }

            $remainingSeconds = max(0, $standardSeconds - $totalWorkedSeconds);
            $hoursWorkedFormatted = sprintf('%02d:%02d:%02d', intdiv($totalWorkedSeconds, 3600), intdiv($totalWorkedSeconds % 3600, 60), $totalWorkedSeconds % 60);
            $remainingHoursFormatted = sprintf('%02d:%02d:%02d', intdiv($remainingSeconds, 3600), intdiv($remainingSeconds % 3600, 60), $remainingSeconds % 60);
            $workedPercentage = min(100, round(($totalWorkedSeconds / $standardSeconds) * 100, 2));
            $remainingPercentage = min(100, round(($remainingSeconds / $standardSeconds) * 100, 2));
        }

        return response()->json([
            'total_present' => $totalPresent,
            'total_leave' => $totalLeave,
            'today_status' => $todayStatus,
            'today_check_in_time' => $todayCheckInTime,
            'today_check_out_time' => $todayCheckOutTime,
            'hours_worked' => $hoursWorkedFormatted,
            'remaining_hours' => $remainingHoursFormatted,
            'worked_percentage' => $workedPercentage,
            'remaining_percentage' => $remainingPercentage,
            'staff_job_cards_count' => $staffJobCardsCount,
            'staff_daily_data_count' => $staffDailyDataCount,
            'recent_job_cards' => $recentJobCards,
            'recent_daily_data' => $recentDailyData,
            'last_updated' => now()->toIso8601String(),
        ]);
    }
}
