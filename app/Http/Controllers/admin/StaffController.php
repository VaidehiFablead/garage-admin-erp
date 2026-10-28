<?php

namespace App\Http\Controllers\admin;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function staff_list(Request $request)
    {
        return view('staff/stafflist');
    }
    public function add_staff(Request $request)
    {
        $userLimit = tenant_user_limit();
        if ($userLimit !== null && $userLimit > 0) {
            $staffCount = \App\Models\User::whereIn('role', ['staff', 'Staff'])->where('isDeleted', 0)->count();
            if ($staffCount >= $userLimit) {
                return redirect()->route('staff.list')->with('error', "Staff limit exceeded. Your plan allows only {$userLimit} staff members.");
            }
        }

        $modules = Module::all();
        return view('staff/addstaff',compact('modules'));
    }
    public function edit_staff(Request $request)
    {
        $modules = Module::all();
        // dd($modules);
        return view('staff/editstaff', compact('modules'));
    }
    public function staff_report(Request $request)
    {
        return view('staff/staffreport');
    }
    public function staff_view($id)
    {
        return view('staff.view_staff', ['id' => $id]);
    }
}
