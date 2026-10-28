<?php

namespace App\Http\Controllers\admin;
// namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubBranch;

class SubBranchController extends Controller
{
    public function subbranch_list(Request $request)
    {
        return view('subbranch/subbranchlist');
    }
    public function add_subbranch(Request $request)
    {
        $branchLimit = tenant_branch_limit();
        if ($branchLimit !== null && $branchLimit > 0) {
            $subAdminCount = \App\Models\User::where('role', 'sub-admin')->where('isDeleted', 0)->count();
            if ($subAdminCount >= $branchLimit) {
                return redirect()->route('subbranch.list')->with('error', "Maximum branch limit reached. Your plan allows only {$branchLimit} sub-branches.");
            }
        }

        return view('subbranch/addsubbranch');
    }
    public function edit_subbranch(Request $request, $id)
    {
        
        return view('subbranch/editsubbranch');
    }
   
    public function view_subbranch(Request $request, $id)
    {
        return view('subbranch/viewsubbranch');
    }
}