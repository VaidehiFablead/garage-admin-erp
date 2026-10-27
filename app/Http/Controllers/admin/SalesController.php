<?php

namespace App\Http\Controllers\admin;

use App\Models\AutoCarModel;
use App\Models\PaymentStore;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
// use App\Models\DiscountGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function exportOrdersPDF(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // Sub-admin id (from dropdown or session fallback)
        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? session('selectedSubAdminId') ?? $user->id;

        // ✅ branch-wise setting
        $setting = Setting::where('branch_id', $selectedSubAdminId)->first();

        try {
            $query = Order::with(['user:id,name,phone', 'biller:id,name'])
                ->where('isDeleted', 0)
                ->where('type', 'Sales');

            // 🔹 Year filter
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            // 🔹 Month filter
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            // 🔹 Exact Date filter (DD-MM-YYYY or YYYY-MM-DD)
            if ($request->filled('date')) {
                $inputDate = trim($request->date);
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $inputDate)) {
                    [$day, $month, $year] = explode('-', $inputDate);
                    $formattedDate = "$year-$month-$day";
                } else {
                    $formattedDate = $inputDate;
                }
                $query->whereDate('created_at', $formattedDate);
            }

            // 🔹 Customer filter
            if ($request->filled('customerId')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', $request->customerId);
                });
            }


            // 🔹 Role-wise filter
            if ($role === 'sub-admin') {
                $query->where('created_by', $userId);
            } elseif ($role === 'admin' && $selectedSubAdminId) {
                $query->where('created_by', $selectedSubAdminId);
            } else {
                $query->where('created_by', $userId);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // ✅ load PDF view
            $pdf = Pdf::loadView('sales.orders_pdf', [
                'orders' => $orders,
                'setting' => $setting,
            ])->setPaper('A4', 'landscape');

            return $pdf->download('Sales_' . date('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function sales_list(Request $request)
    {

        return view('sales/saleslist');
    }
    public function add_sales(Request $request)
    {

        return view('sales/add-sales');
    }
    public function getHistory1($job_card_id)
    {
        $history = PaymentStore::where('order_id', $job_card_id)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
    public function edit_sales($id)
    {
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;
        $userRole = $user->role;
        $userBranchId = $user->branch_id ?? null;
        // dd($userRole);
        $subAdminId = session('selectedSubAdminId');
        // dd($subAdminId);
        $sales = Order::with(['order_items.product'])->find($id);

        // $discountGroups = DiscountGroup::where('isDeleted', 0)->get();

        // dd($discountGroups);


        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Sales record not found.');
        }

        $update_id = $id;
        if (!empty($userId) && !empty($userRole == 'sub-admin')) {
            $usernames = User::where('role', 'customer')->where('branch_id', $userId)->get();
            $category = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('isDeleted', 0)
                ->where('branch_id', $userId)
                ->get();
        } else if (!empty($userRole == 'admin') && !empty($subAdminId)) {
            $usernames = User::where('role', 'customer')->where('branch_id', $subAdminId)->get();
            $category = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('isDeleted', 0)
                ->where('branch_id', $subAdminId)
                ->get();
        } else if (strtolower($userRole) == 'staff') {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userBranchId)->get();
            $taxRates = TaxRate::where('status', 'active')->get();
            $customers = User::where('role', 'customer')->where('isDeleted', 0)->where('branch_id', $userBranchId)->get();
        } else {
            $usernames = User::where('role', 'customer')->get();
            $category = Category::where('isDeleted', 0)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('isDeleted', 0)
                ->where('branch_id', $userId)
                ->get();
        }
        $TaxRate = TaxRate::where('status', 'active')->where('branch_id',  $subAdminId)->get();
        $setting = Setting::first();
        $model = AutoCarModel::query()
            ->select('id', 'model_name',)
            ->where('branch_id', $subAdminId)
            ->get();
        // dd($sales->discount);

        return view('sales/edit-sales', compact('sales', 'TaxRate', 'category', 'usernames', 'products', 'update_id', 'setting','model'));
    }
    public function sales_details($id)
    {
        $setting = Setting::first();

        $currencySymbol = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        $sales = Order::find($id);

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $compenyinfo = Setting::all();

        $taxIds = $sales->tax_id;

        // Case 1: If it's a JSON string (e.g. "[1,2]" or ["1","2"])
        if (is_string($taxIds)) {
            $taxIds = json_decode($taxIds, true) ?? [];
        }

        // Case 2: If it's already an array
        if (!is_array($taxIds)) {
            $taxIds = [];
        }

        // Convert all values to integers
        $taxIds = array_map('intval', $taxIds);

        // Now safe to use in query
        $taxes = TaxRate::whereIn('id', $taxIds)->get();



        $orderItems = OrderItem::where('order_id', $id)->get();
        $totalAmount = $orderItems->sum('total_amount');

        $view_id = $id; // define view_id for blade

        return view('sales/sales-details', compact(
            'view_id',
            'sales',
            'taxes',
            'totalAmount',
            'compenyinfo',
            'setting',
            'currencySymbol',
            'currencyPosition'
        ));
    }
    public function salse_invoice($id)
    {
        $view_id = $id;
        $sales = Order::with('carModel')->find($view_id);
        $user = Auth::user();
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        $setting = Setting::where('branch_id', $subAdminId)->first();

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        // ✅ Fetch taxes only if gst_option = 'with'
        $taxes = collect(); // default empty collection
        if ($sales->gst_option === 'with') {
            $taxIds = json_decode($sales->tax_id, true);
            if (!empty($taxIds)) {
                $taxes = TaxRate::whereIn('id', $taxIds)->get();
            }
        }

        $orderItems = OrderItem::where('order_id', $view_id)->get();
        $totalAmount = $orderItems->sum('total_amount');

        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;
        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;
        
        return view('sales/salse-invoice', compact('view_id', 'sales', 'taxes', 'totalAmount', 'setting', 'userAddress'));
    }

    public function salse_invoice_pdf($id)
    {
        $view_id = $id;
        $sales = Order::with('carModel')->find($view_id);
        $user = Auth::user();
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        $setting = Setting::where('branch_id', $subAdminId)->first();

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        // Fetch user data (assuming 'user_id' in orders table)
        $user = $sales->user_id ? User::find($sales->user_id) : null;

        // Helper function for currency formatting
        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        // ✅ Subtotal
        $orderItems = OrderItem::where('order_id', $view_id)->get();
        $subtotal = $orderItems->sum('total_amount');

        // ✅ Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount = ($discountPercent / 100) * $subtotal;
        $afterDiscount = $subtotal - $discountAmount;

        // ✅ Tax calculation only if gst_option = 'with'
        $taxDetails = [];
        if ($sales->gst_option === 'with') {
            $taxIds = json_decode($sales->tax_id, true) ?? [];
            if (!empty($taxIds)) {
                $taxes = TaxRate::whereIn('id', $taxIds)->get();
                foreach ($taxes as $tax) {
                    $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
                    $taxDetails[] = [
                        'name' => $tax->tax_name,
                        'rate' => $tax->tax_rate,
                        'amount' => $taxAmount,
                        'formatted_amount' => $formatCurrency($taxAmount)
                    ];
                }
            }
        }

        // ✅ Final total
        $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

        // ✅ Prepare formatted values
        $formattedSubtotal = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount = $formatCurrency($afterDiscount);
        $formattedFinalTotal = $formatCurrency($finalTotal);

        // ✅ Retrieve customer data
        $customer = $sales->customer ? $sales->customer->toArray() : [
            'name' => 'walk-in-customer',
            'email' => '',
            'phone' => '',
            'address' => optional($user->userDetail)->address ?? '',
        ];

        // ✅ Prepare data for view
        $pdfData = [
            'view_id' => $view_id,
            'sales' => $sales,
            'setting' => $setting,
            'orderItems' => $orderItems,
            'salesItems' => $orderItems,
            'customer' => [
                'name' => $user->name ?? 'walk-in-customer',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'address' => optional($user->userDetail)->address ?? 'arga',
            ],
            'user' => $user ? $user->toArray() : null,
            'subtotal' => $formattedSubtotal,
            'discountAmount' => $formattedDiscountAmount,
            'afterDiscount' => $formattedAfterDiscount,
            'finalTotal' => $formattedFinalTotal,
            'taxDetails1' => $taxDetails
        ];

        // ✅ Load and render PDF
        $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

        return $pdf->stream('invoice_' . $view_id . '.pdf');
    }


    public function salse_invoice_pdf_view($id)
    {
        $view_id = $id;
        $sales = Order::with('carModel')->find($view_id);
        $user = Auth::user();
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        $setting = Setting::where('branch_id', $subAdminId)->first();

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        // Fetch user data (assuming 'user_id' in orders table)
        $user = $sales->user_id ? User::find($sales->user_id) : null;

        // Helper function for currency formatting
        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        // ✅ Subtotal
        $orderItems = OrderItem::where('order_id', $view_id)->get();
        $subtotal = $orderItems->sum('total_amount');

        // ✅ Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount = ($discountPercent / 100) * $subtotal;
        $afterDiscount = $subtotal - $discountAmount;

        // ✅ Tax calculation only if gst_option = 'with'
        $taxDetails = [];
        if ($sales->gst_option === 'with') {
            $taxIds = json_decode($sales->tax_id, true) ?? [];
            if (!empty($taxIds)) {
                $taxes = TaxRate::whereIn('id', $taxIds)->get();
                foreach ($taxes as $tax) {
                    $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
                    $taxDetails[] = [
                        'name' => $tax->tax_name,
                        'rate' => $tax->tax_rate,
                        'amount' => $taxAmount,
                        'formatted_amount' => $formatCurrency($taxAmount)
                    ];
                }
            }
        }

        // ✅ Final total
        $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

        // ✅ Prepare formatted values
        $formattedSubtotal = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount = $formatCurrency($afterDiscount);
        $formattedFinalTotal = $formatCurrency($finalTotal);

        // ✅ Retrieve customer data
        $customer = $sales->customer ? $sales->customer->toArray() : [
            'name' => 'walk-in-customer',
            'email' => '',
            'phone' => '',
            'address' => optional($user->userDetail)->address ?? '',
        ];

        // ✅ Prepare data for view
        $pdfData = [
            'view_id' => $view_id,
            'sales' => $sales,
            'setting' => $setting,
            'orderItems' => $orderItems,
            'salesItems' => $orderItems,
            'customer' => [
                'name' => $user->name ?? 'walk-in-customer',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'address' => optional($user->userDetail)->address ?? 'arga',
            ],
            'user' => $user ? $user->toArray() : null,
            'subtotal' => $formattedSubtotal,
            'discountAmount' => $formattedDiscountAmount,
            'afterDiscount' => $formattedAfterDiscount,
            'finalTotal' => $formattedFinalTotal,
            'taxDetails1' => $taxDetails
        ];

        // ✅ Load and render PDF
        $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

        return $pdf->download('invoice_' . $view_id . '.pdf');
    }


    public function sales_report(Request $request)
    {
        return view('sales/salesreport');
    }

    public function pos()
    {
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;
        $userBranchId = $user->branch_id ?? null;
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        $setting = Setting::first();
        // $discountGroups = DiscountGroup::where('isDeleted', 0)->get();

        $currency_symbol = $setting->currency_symbol ?? '₹'; // fallback if null
        $currency_position = $setting->currency_position ?? 'left'; // fallback if null
        if (!empty($userRole == 'sub-admin') && $userId) {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $taxRates = TaxRate::where('status', 'active')->where('branch_id', $userId)->get();
            $customers = User::where('role', 'customer')->where('branch_id', $userId)->where('isDeleted', 0)->get();
        } else if (!empty($userRole == 'admin') && !empty($subAdminId)) {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $taxRates = TaxRate::where('status', 'active')->where('branch_id', $subAdminId)->get();
            $customers = User::where('role', 'customer')->where('branch_id', $subAdminId)->where('isDeleted', 0)->get();
        } else if (strtolower($userRole) == 'staff') {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userBranchId)->get();
            $taxRates = TaxRate::where('status', 'active')->where('branch_id', $userBranchId)->get();
            $customers = User::where('role', 'customer')->where('isDeleted', 0)->where('branch_id', $userBranchId)->get();
        } else {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $taxRates = TaxRate::where('status', 'active')->where('branch_id', $userId)->get();
            $customers = User::where('role', 'customer')->where('isDeleted', 0)->where('branch_id', $userId)->get();
        }

        $vendors = User::query()
            ->select('id', 'name', 'phone', 'branch_id as subbranch_id')
            ->where('role', 'vendor')
            ->where('branch_id', $subAdminId)
            ->get();

        $staff = User::query()
            ->select('id', 'name', 'phone', 'branch_id as subbranch_id')
            ->whereIn('role', ['Staff', 'staff'])
            ->where('branch_id', $subAdminId)
            ->get();

            $model = AutoCarModel::query()
            ->select('id', 'model_name',)
            ->where('branch_id', $subAdminId)
            ->get();


        return view('sales.pos', compact('categories', 'taxRates', 'customers', 'vendors', 'currency_symbol', 'currency_position', 'setting', 'staff', 'model'));
    }
    public function sale_report($ids)
    {
        $idsArray = explode(',', $ids);
        $sales = OrderItem::with('product.category', 'invoice')->whereIn('id', $idsArray)->get();

        if ($sales->isEmpty()) {
            return redirect()->back()->with('error', 'No purchase data found.');
        }

        // Get settings
        $settings = Setting::first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $subtotal = $sales->sum('total_amount');

        // Get tax rates
        $taxRates = TaxRate::where('status', 'active')->get();
        $discountPercent = 0;
        $discountAmount = 0;

        // Get discount
        foreach ($sales as $sale) {
            if ($sale->invoice) {
                $discountPercent = $sale->invoice->discount ?? 0;
                $discountAmount += ($sale->total_amount * $discountPercent) / 100;
            }
        }

        $subtotalAfterDiscount = $subtotal - $discountAmount;

        // Calculate tax on discounted amount
        $taxDetails = [];
        $totalTaxAmount = 0;
        foreach ($taxRates as $tax) {
            $amount = $subtotalAfterDiscount * ($tax->tax_rate / 100);
            $taxDetails[] = [
                'name' => $tax->tax_name,
                'rate' => $tax->tax_rate,
                'amount' => $amount,
                'formatted_amount' => $currencyPosition === 'left'
                    ? $currencySymbol . number_format($amount, 2)
                    : number_format($amount, 2) . $currencySymbol,
            ];
            $totalTaxAmount += $amount;
        }

        $totalAmount = $subtotalAfterDiscount + $totalTaxAmount;

        // Format monetary values
        $formattedSubtotal = $currencyPosition === 'left'
            ? $currencySymbol . number_format($subtotal, 2)
            : number_format($subtotal, 2) . $currencySymbol;

        $formattedDiscountAmount = $currencyPosition === 'left'
            ? $currencySymbol . number_format($discountAmount, 2)
            : number_format($discountAmount, 2) . $currencySymbol;

        $formattedTotalAmount = $currencyPosition === 'left'
            ? $currencySymbol . number_format($totalAmount, 2)
            : number_format($totalAmount, 2) . $currencySymbol;

        return view('sales.sale_report', compact(
            'sales',
            'settings',
            'subtotal',
            'formattedSubtotal',
            'taxDetails',
            'discountAmount',
            'formattedDiscountAmount',
            'discountPercent',
            'totalTaxAmount',
            'totalAmount',
            'formattedTotalAmount',
            'currencySymbol',
            'currencyPosition'
        ));
    }
    public function exportOrders(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;
        // $selectedSubAdminId = $request->query('selectedSubAdminId');
        // Sub-admin id (from dropdown or session fallback)
        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? session('selectedSubAdminId') ?? $user->id;

        $year = $request->query('year');
        $month = $request->query('month');
        $date = $request->query('date');
        $customerId = $request->query('customerId');

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        try {
            $query = Order::with(['user:id,name,phone', 'biller:id,name'])
                ->where('isDeleted', 0)
                ->where('type', 'Sales');

            // 🔹 Year filter
            if (!empty($year)) {
                $query->whereYear('created_at', $year);
            }

            // 🔹 Month filter
            if (!empty($month)) {
                $query->whereMonth('created_at', $month);
            }

            // 🔹 Exact Date filter
            if (!empty($date)) {
                // Convert DD-MM-YYYY to YYYY-MM-DD
                $dateParts = explode('-', $date);
                if (count($dateParts) === 3) {
                    $formattedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                    $query->whereDate('created_at', $formattedDate);
                }
            }

            // 🔹 Customer filter
            if ($request->filled('customerId')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', $request->customerId);
                });
            }


            // 🔹 Role wise filter
            if ($role === 'sub-admin') {
                $query->where('created_by', $userId);
            } elseif ($role === 'admin' && $selectedSubAdminId) {
                $query->where('created_by', $selectedSubAdminId);
            } else {
                $query->where('created_by', $userId);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // Generate Excel same as before...
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Order Number');
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'Customer Name');
            $sheet->setCellValue('D1', 'Biller Name');
            $sheet->setCellValue('E1', 'Total Amount');
            $sheet->setCellValue('F1', 'Payment Status');
            $sheet->setCellValue('G1', 'Payment Method');
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);

            $row = 2;
            foreach ($orders as $order) {
                $sheet->setCellValue('A' . $row, $order->order_number ?? 'N/A');
                $sheet->setCellValue('B' . $row, $order->created_at->format('Y-m-d'));
                $sheet->setCellValue('C' . $row, $order->user->name ?? 'N/A');
                $sheet->setCellValue('D' . $row, $order->biller->name ?? 'N/A');
                $sheet->setCellValue('E' . $row, $order->total_amount ?? 0);
                $sheet->setCellValue('F' . $row, $order->payment_status ?? 'N/A');
                $sheet->setCellValue('G' . $row, $order->payment_method ?? 'N/A');
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Sales_' . date('Ymd_His') . '.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Access-Control-Expose-Headers' => 'Content-Disposition'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
