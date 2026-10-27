<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobCard;
use Illuminate\Support\Facades\Auth;

class AccountLedgerController extends Controller
{
    public function index(Request $request)
    {
        return view('account_ledger.list');
    }

    // public function add(Request $request)
    // {
    //     $user = Auth::user();
    //     $role = $user->role;
    //     $userBranchId = $user->id; // Or $user->branch_id if you have that column
    //     $selectedSubAdminId = session('selectedSubAdminId');

    //     $customersQuery = JobCard::query()
    //         ->leftJoin('users', 'job_cards.customer_id', '=', 'users.id')
    //         ->selectRaw('
    //         job_cards.customer_id as id,
    //         COALESCE(job_cards.customer_name, users.name) as name,
    //         job_cards.subbranch_id
    //     ')
    //         ->where(function ($q) {
    //             $q->whereNotNull('job_cards.customer_id')
    //                 ->orWhereNotNull('job_cards.customer_name');
    //         });

    //     // 🔹 Role-based filtering
    //     if ($role === 'sub-admin') {
    //         // Sub-admin can only see their own branch
    //         $customersQuery->where('job_cards.subbranch_id', $userBranchId);
    //     } elseif ($role === 'admin') {
    //         if (!empty($selectedSubAdminId)) {
    //             // Admin is viewing a selected sub-admin's branch
    //             $subAdmin = User::find($selectedSubAdminId);
    //             if ($subAdmin) {
    //                 $customersQuery->where('job_cards.subbranch_id', $subAdmin->id);
    //             }
    //         } else {
    //             // Admin is viewing their own branch
    //             $customersQuery->where('job_cards.subbranch_id', $userBranchId);
    //         }
    //     }

    //     $customers = $customersQuery->distinct()->get();

    //     // Get years for dropdown
    //     $years = JobCard::selectRaw('YEAR(created_at) as year')
    //         ->distinct()
    //         ->orderBy('year', 'desc')
    //         ->pluck('year');

    //     return view('account_ledger.add', compact('customers', 'years'));
    // }

    public function add(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $userBranchId = $user->id; // Or $user->branch_id
        $selectedSubAdminId = session('selectedSubAdminId');

        // ---- Customers Logic (your existing code) ----
        $customersFromJobCards = JobCard::query()
            ->leftJoin('users', 'job_cards.customer_id', '=', 'users.id')
            ->selectRaw('
            job_cards.customer_id as id,
            COALESCE(job_cards.customer_name, users.name) as name,
            job_cards.subbranch_id
        ')
            ->where(function ($q) {
                $q->whereNotNull('job_cards.customer_id')
                    ->orWhereNotNull('job_cards.customer_name');
            });

        if ($role === 'sub-admin') {
            $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromJobCards->where('job_cards.subbranch_id', $subAdmin->id);
                }
            } else {
                $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
            }
        }

        $customersFromJobCards = $customersFromJobCards->get();

        $customersFromUsers = User::query()
            ->select('id', 'name', 'branch_id as subbranch_id')
            ->where('role', 'customer');

        if ($role === 'sub-admin') {
            $customersFromUsers->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromUsers->where('branch_id', $subAdmin->id);
                }
            } else {
                $customersFromUsers->where('branch_id', $userBranchId);
            }
        }

        $customersFromUsers = $customersFromUsers->get();

        $customers = $customersFromJobCards
            ->merge($customersFromUsers)
            ->unique('id')
            ->values();

        // ---- Vendors Logic (only role = vendor) ----
        $vendors = User::query()
            ->select('id', 'name', 'branch_id')
            ->where('role', 'vendor');

        if ($role === 'sub-admin') {
            $vendors->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $vendors->where('branch_id', $subAdmin->id);
                }
            } else {
                $vendors->where('branch_id', $userBranchId);
            }
        }

        $vendors = $vendors->get();

        // Years for dropdown
        $years = JobCard::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('account_ledger.add', compact('customers', 'vendors', 'years'));
    }
    public function pendingPayment(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $userBranchId = $user->id; // Or $user->branch_id
        $selectedSubAdminId = session('selectedSubAdminId');

        // ---- Customers Logic (your existing code) ----
        $customersFromJobCards = JobCard::query()
            ->leftJoin('users', 'job_cards.customer_id', '=', 'users.id')
            ->selectRaw('
            job_cards.customer_id as id,
            COALESCE(job_cards.customer_name, users.name) as name,
            job_cards.subbranch_id
        ')
            ->where(function ($q) {
                $q->whereNotNull('job_cards.customer_id')
                    ->orWhereNotNull('job_cards.customer_name');
            });


        if ($role === 'sub-admin') {
            $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromJobCards->where('job_cards.subbranch_id', $subAdmin->id);
                }
                $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
            }
        }

        $customersFromJobCards = $customersFromJobCards->get();

        $customersFromUsers = User::query()
            ->select('id', 'name', 'branch_id as subbranch_id')
            ->where('role', 'customer');

        if ($role === 'sub-admin') {
            $customersFromUsers->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromUsers->where('branch_id', $subAdmin->id);
                }
            } else {
                $customersFromUsers->where('branch_id', $userBranchId);
            }
        }

        $customersFromUsers = $customersFromUsers->get();

        $customers = $customersFromJobCards
            ->merge($customersFromUsers)
            ->unique('id')
            ->values();

        // ---- Vendors Logic (only role = vendor) ----
        $vendors = User::query()
            ->select('id', 'name', 'branch_id')
            ->where('role', 'vendor');

        if ($role === 'sub-admin') {
            $vendors->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $vendors->where('branch_id', $subAdmin->id);
                }
            } else {
                $vendors->where('branch_id', $userBranchId);
            }
        }

        $vendors = $vendors->get();

        // Years for dropdown
        $years = JobCard::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('account_ledger.pending_payment', compact('customers', 'vendors', 'years'));
    }
    public function paid_payments(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $userBranchId = $user->id; // Or $user->branch_id
        $selectedSubAdminId = session('selectedSubAdminId');

        // ---- Customers Logic (your existing code) ----
        $customersFromJobCards = JobCard::query()
            ->leftJoin('users', 'job_cards.customer_id', '=', 'users.id')
            ->selectRaw('
            job_cards.customer_id as id,
            COALESCE(job_cards.customer_name, users.name) as name,
            job_cards.subbranch_id
        ')
            ->where(function ($q) {
                $q->whereNotNull('job_cards.customer_id')
                    ->orWhereNotNull('job_cards.customer_name');
            });

        if ($role === 'sub-admin') {
            $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromJobCards->where('job_cards.subbranch_id', $subAdmin->id);
                }
            } else {
                $customersFromJobCards->where('job_cards.subbranch_id', $userBranchId);
            }
        }

        $customersFromJobCards = $customersFromJobCards->get();

        $customersFromUsers = User::query()
            ->select('id', 'name', 'branch_id as subbranch_id')
            ->where('role', 'customer');

        if ($role === 'sub-admin') {
            $customersFromUsers->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $customersFromUsers->where('branch_id', $subAdmin->id);
                }
            } else {
                $customersFromUsers->where('branch_id', $userBranchId);
            }
        }

        $customersFromUsers = $customersFromUsers->get();

        $customers = $customersFromJobCards
            ->merge($customersFromUsers)
            ->unique('id')
            ->values();

        // ---- Vendors Logic (only role = vendor) ----
        $vendors = User::query()
            ->select('id', 'name', 'branch_id')
            ->where('role', 'vendor');

        if ($role === 'sub-admin') {
            $vendors->where('branch_id', $userBranchId);
        } elseif ($role === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $subAdmin = User::find($selectedSubAdminId);
                if ($subAdmin) {
                    $vendors->where('branch_id', $subAdmin->id);
                }
            } else {
                $vendors->where('branch_id', $userBranchId);
            }
        }

        $vendors = $vendors->get();

        // Years for dropdown
        $years = JobCard::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('account_ledger.paidpayment', compact('customers', 'vendors', 'years'));
    }
}
