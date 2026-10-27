<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use PDF;

class SalesController1 extends Controller
{
    public function sales_list(Request $request)
    {

        return view('sales/saleslist');
    }
    public function add_sales(Request $request)
    {

        return view('sales/add-sales');
    }
    public function edit_sales($id)
    {
        $user = auth()->user();
        $userRole = $user->role ?? '';
        $userId = $user->id ?? null;
        $userRole = $user->role;
        // dd($userRole);
        $subAdminId = session('selectedSubAdminId');
        // dd($subAdminId);
        $sales = Order::with(['order_items.product'])->find($id);

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Sales record not found.');
        }

        $update_id = $id;
        if (!empty($userId) && !empty($userRole == 'sub-admin')) {
            $usernames = User::where('role', 'customer')->where('branch_id', $userId)->get();
            $category = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('branch_id', $userId)
                ->get();
        } else if (!empty($userRole == 'admin') && !empty($subAdminId)) {
            $usernames = User::where('role', 'customer')->where('branch_id', $subAdminId)->get();
            $category = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('branch_id', $subAdminId)
                ->get();
        } else {
            $usernames = User::where('role', 'customer')->get();
            $category = Category::where('isDeleted', 0)->get();
            $products = Product::where('status', 'active')
                ->where('availablility', 'in_stock')
                ->where('branch_id', $userId)
                ->get();
        }
        $TaxRate = TaxRate::where('status', 'active')->get();
        $setting = Setting::first();

        return view('sales/edit-sales', compact('sales', 'TaxRate', 'category', 'usernames', 'products', 'update_id', 'setting'));
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

        $taxIds = json_decode($sales->tax_id, true);
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
        $sales = Order::find($view_id);
        $setting = Setting::first(); // Get currency info

        if (!$sales) {
            return redirect()->route('sales.list')->with('error', 'Order not found.');
        }

        $taxIds = json_decode($sales->tax_id, true);
        $taxes = TaxRate::whereIn('id', $taxIds)->get();
        $orderItems = OrderItem::where('order_id', $view_id)->get();
        $totalAmount = $orderItems->sum('total_amount');

        $user = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

        $userAddress = $user && $user->userDetail ? $user->userDetail->address : null;

        return view('sales/salse-invoice', compact('view_id', 'sales', 'taxes', 'totalAmount', 'setting', 'userAddress'));
    }

    public function salse_invoice_pdf($id)
    {
        $view_id = $id;
        $sales = Order::find($view_id);
        $setting = Setting::first();

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

        // Fetch tax rates
        $taxIds = json_decode($sales->tax_id, true) ?? [];
        $taxes = TaxRate::whereIn('id', $taxIds)->get();

        // Fetch order items
        $orderItems = OrderItem::where('order_id', $view_id)->get();

        // Subtotal
        $subtotal = $orderItems->sum('total_amount');

        // Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount = ($discountPercent / 100) * $subtotal;
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax amounts
        $taxDetails = [];
        foreach ($taxes as $tax) {
            $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
            $taxDetails[] = [
                'name' => $tax->tax_name,
                'rate' => $tax->tax_rate,
                'amount' => $taxAmount,
                'formatted_amount' => $formatCurrency($taxAmount)
            ];
        }

        // Final total
        $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

        // Prepare formatted values
        $formattedSubtotal = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount = $formatCurrency($afterDiscount);
        $formattedFinalTotal = $formatCurrency($finalTotal);

        // Retrieve customer data (assuming $sales has a customer_id relationship)
        $customer = $sales->customer ? $sales->customer->toArray() : [
            'name' => 'walk-in-customer',
            'email' => '',
            'phone' => '',
            'address' => optional($user->userDetail)->address ?? '',
        ];

        // Prepare data for view, now also including user data
        $pdfData = [
            'view_id' => $view_id,
            'sales' => $sales,
            'setting' => $setting,
            'orderItems' => $orderItems,
            'salesItems' => $orderItems,
            'customer' => [
                'name'    => $user->name ?? 'walk-in-customer',
                'email'   => $user->email ?? '',
                'phone'   => $user->phone ?? '',
                'address' => optional($user->userDetail)->address ?? 'arga',
            ],
            'user' => $user ? $user->toArray() : null,  // include user data
            'subtotal' => $formattedSubtotal,
            'discountAmount' => $formattedDiscountAmount,
            'afterDiscount' => $formattedAfterDiscount,
            'finalTotal' => $formattedFinalTotal,
            'taxDetails' => $taxDetails
        ];

        // Load and render PDF
        $pdf = PDF::loadView('sales.salse-invoice-pdf', $pdfData);

        return $pdf->stream('invoice_' . $view_id . '.pdf');
    }

    public function salse_invoice_pdf_view($id)
    {
        $view_id = $id;
        $sales = Order::find($view_id);
        $setting = Setting::first();

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

        // Fetch tax rates
        $taxIds = json_decode($sales->tax_id, true) ?? [];
        $taxes = TaxRate::whereIn('id', $taxIds)->get();

        // Fetch order items
        $orderItems = OrderItem::where('order_id', $view_id)->get();

        // Subtotal
        $subtotal = $orderItems->sum('total_amount');

        // Discount
        $discountPercent = $sales->discount ?? 0;
        $discountAmount = ($discountPercent / 100) * $subtotal;
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax amounts
        $taxDetails = [];
        foreach ($taxes as $tax) {
            $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
            $taxDetails[] = [
                'name' => $tax->tax_name,
                'rate' => $tax->tax_rate,
                'amount' => $taxAmount,
                'formatted_amount' => $formatCurrency($taxAmount)
            ];
        }

        // Final total
        $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');

        // Prepare formatted values
        $formattedSubtotal = $formatCurrency($subtotal);
        $formattedDiscountAmount = $formatCurrency($discountAmount);
        $formattedAfterDiscount = $formatCurrency($afterDiscount);
        $formattedFinalTotal = $formatCurrency($finalTotal);

        // Retrieve customer data (assuming $sales has a customer_id relationship)
        $customer = $sales->customer ? $sales->customer->toArray() : [
            'name' => 'walk-in-customer',
            'email' => '',
            'phone' => '',
            'address' => optional($user->userDetail)->address ?? '',
        ];

        // Prepare data for view, now also including user data
        $pdfData = [
            'view_id' => $view_id,
            'sales' => $sales,
            'setting' => $setting,
            'orderItems' => $orderItems,
            'salesItems' => $orderItems,
            'customer' => [
                'name'    => $user->name ?? 'walk-in-customer',
                'email'   => $user->email ?? '',
                'phone'   => $user->phone ?? '',
                'address' => optional($user->userDetail)->address ?? 'arga',
            ],
            'user' => $user ? $user->toArray() : null,  // include user data
            'subtotal' => $formattedSubtotal,
            'discountAmount' => $formattedDiscountAmount,
            'afterDiscount' => $formattedAfterDiscount,
            'finalTotal' => $formattedFinalTotal,
            'taxDetails' => $taxDetails
        ];

        // Load and render PDF
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
        $userRole = $user->role;
        // dd($userRole);
        $subAdminId = session('selectedSubAdminId');
        // dd($subAdminId);
        $setting = Setting::first(); // Assuming only one settings row

        $currency_symbol = $setting->currency_symbol ?? '₹'; // fallback if null
        $currency_position = $setting->currency_position ?? 'left'; // fallback if null
        if (!empty($userRole == 'sub-admin') && $userId) {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $taxRates = TaxRate::where('status', 'active')->get();
            $customers = User::where('role', 'customer')->where('branch_id', $userId)->where('isDeleted', 0)->get();
        } else if (!empty($userRole == 'admin') && !empty($subAdminId)) {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $subAdminId)->get();
            $taxRates = TaxRate::where('status', 'active')->get();
            $customers = User::where('role', 'customer')->where('branch_id', $subAdminId)->where('isDeleted', 0)->get();
        } else {
            $categories = Category::where('isDeleted', 0)->where('branch_id', $userId)->get();
            $taxRates = TaxRate::where('status', 'active')->get();
            $customers = User::where('role', 'customer')->where('isDeleted', 0)->where('branch_id', $userId)->get();
        }
        return view('sales.pos', compact('categories', 'taxRates', 'customers', 'currency_symbol', 'currency_position', 'setting'));
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
}
