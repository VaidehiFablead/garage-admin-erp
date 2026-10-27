<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    public function vendor_list(Request $request)
    {
        return view('Supplier/supplierlist');
    }
    public function add_vendor(Request $request)
    {
        return view('Supplier/addsupplier');
    }
    public function edit_vendor(Request $request)
    {
        return view('Supplier/editsupplier');
    }
    public function vendor_report(Request $request)
    {
        return view('Supplier/supplierreport');
    }
    public function vendor_view($id)
    {
        return view('Supplier/view_supplier', ['id' => $id]);
    }
}
