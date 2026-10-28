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
            $totalBranchCount = \App\Models\User::whereIn('role', ['admin', 'sub-admin'])->where('isDeleted', 0)->count();
            if ($totalBranchCount >= $branchLimit) {
                $allowedSub = max(0, $branchLimit - 1);
                return redirect()->route('subbranch.list')->with('error', "Maximum branch limit reached. Your plan allows only {$branchLimit} total branch(es) (1 Main Branch + {$allowedSub} Sub-branch).");
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