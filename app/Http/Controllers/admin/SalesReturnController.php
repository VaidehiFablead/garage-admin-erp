<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\Setting;
use App\Models\User;

class SalesReturnController extends Controller
{
    public function createsales_return(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role;
        $userBranchId = $user->id; // Make sure this is the branch_id
        $subAdminId = session('selectedSubAdminId');
        // dd($subAdminId);
        if (!empty($userRole === 'admin') && !empty($subAdminId)) {
            $invoiceNumbers = Order::where('isDeleted', 0)
                ->where('branch_id', $subAdminId)
                ->pluck('order_number');
        } elseif ($userRole === 'sub-admin') {;
            $invoiceNumbers = Order::where('isDeleted', 0)
                ->where('branch_id', $userBranchId) // ✅ filter directly by orders.branch_id
                ->pluck('order_number');
            // dd($invoiceNumbers);
        } elseif ($userRole === 'admin') {
            $invoiceNumbers = Order::where('isDeleted', 0)
                ->where('branch_id', $userBranchId)
                // ✅ filter directly by orders.branch_id
                ->pluck('order_number');
        } else {
            $invoiceNumbers = collect(); // return empty if unauthorized
        }
        // echo "<pre>";
        // print_r($invoiceNumbers);die;
        $settings = \DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $TaxRate = TaxRate::where('status', 'active')->get();

        return view('salesreturn/createsalesreturn', compact(
            'invoiceNumbers',
            'TaxRate',
            'currencySymbol',
            'currencyPosition'
        ));
    }



    public function createsales_returns(Request $request)
    {
        return view('salesreturn/createsalesreturns');
    }
    public function editsales_return(Request $request)
    {
        return view('salesreturn/editsalesreturn');
    }
    public function editsales_returns(Request $request)
    {
        return view('salesreturn/editsalesreturns');
    }
    public function salesreturn_list(Request $request)
    {
        return view('salesreturn/salesreturnlist');
    }
    public function salesreturn_lists(Request $request)
    {
        return view('salesreturn/salesreturnlists');
    }
}
