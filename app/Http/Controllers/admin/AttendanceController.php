<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = User::where('role', 'Staff')->where('isDeleted', 0);

    //     if ($request->has('search')) {
    //         $query->where('name', 'like', '%' . $request->search . '%');
    //     }

    //     $staffUsers = $query->get();

    //     $monthInput = $request->input('month', now()->format('Y-m')); // e.g., "2025-08"
    //     $month = \Carbon\Carbon::parse($monthInput)->format('m'); // "08"
    //     $year = \Carbon\Carbon::parse($monthInput)->format('Y');  // "2025"

    //     $currentMonth = $monthInput; // pass "2025-08" back to view

    //     $attendances = Attendance::whereMonth('date', $month)
    //         ->whereYear('date', $year)
    //         ->get()
    //         ->groupBy(function ($item) {
    //             return $item->user_id . '_' . $item->date;
    //         });

    //     return view('attendance.view', compact('staffUsers', 'attendances', 'currentMonth', 'year', 'month'));
    // }

    // public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     $role = $user->role;
    //     $userBranchId = $user->id;
    //     $subAdminId = session('selectedSubAdminId');

    //     // Date range
    //     $monthInput = $request->input('month', now()->format('Y-m'));
    //     $month = \Carbon\Carbon::parse($monthInput)->format('m');
    //     $year = \Carbon\Carbon::parse($monthInput)->format('Y');
    //     $currentMonth = $monthInput;

    //     // Attendance query with month/year filter
    //     $attendanceQuery = Attendance::whereMonth('date', $month)
    //         ->whereYear('date', $year);

    //     $attendances = $attendanceQuery->get()->groupBy(function ($item) {
    //         return $item->user_id . '_' . $item->date;
    //     });

    //     // Staff query (default → all Staff)
    //     $staffQuery = User::where('role', 'Staff')
    //         ->where('isDeleted', 0);

    //     // Search filter
    //     if ($request->has('search') && !empty($request->search)) {
    //         $staffQuery->where('name', 'like', '%' . $request->search . '%');
    //     }

    //     // Branch filter logic
    //     if (!empty($subAdminId)) {
    //         $staffQuery->where('branch_id', $subAdminId);
    //     } elseif ($role == 'sub-admin') {
    //         $staffQuery->where('branch_id', $userBranchId);
    //     } else {
    //         $staffQuery->where('branch_id', $userBranchId);
    //     }

    //     $staffUsers = $staffQuery->get();

    //     return view('attendance.view', compact('staffUsers', 'attendances', 'currentMonth', 'year', 'month'));
    // }
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $userBranchId = $user->id;
        $subAdminId = session('selectedSubAdminId');

        // Date range
        $monthInput = $request->input('month', now()->format('Y-m'));
        $month = Carbon::parse($monthInput)->format('m');
        $year = Carbon::parse($monthInput)->format('Y');
        $currentMonth = $monthInput;

        // ✅ Get total days in selected month
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        // Attendance query with month/year filter
        $attendanceQuery = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year);

        $attendances = $attendanceQuery->get()->groupBy(function ($item) {
            return $item->user_id . '_' . $item->date;
        });

        // Staff query
        $staffQuery = User::query();

        if ($role == 'Staff') {
            $staffQuery->where('id', $user->id);
        } else {
            $staffQuery->where('role', 'Staff')->where('isDeleted', 0);

            if ($request->has('search') && !empty($request->search)) {
                $staffQuery->where('name', 'like', '%' . $request->search . '%');
            }

            if (!empty($subAdminId)) {
                $staffQuery->where('branch_id', $subAdminId);
            } elseif ($role == 'sub-admin') {
                $staffQuery->where('branch_id', $userBranchId);
            } else {
                $staffQuery->where('branch_id', $userBranchId);
            }
        }

        $staffUsers = $staffQuery->get();

        return view('attendance.view', compact('staffUsers', 'attendances', 'currentMonth', 'year', 'month', 'daysInMonth'));
    }


    public function add(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $userBranchId = $user->id;
        $subAdminId = session('selectedSubAdminId');
        if (!empty($subAdminId)) {
            $query = User::where('role', 'Staff')->where('isDeleted', 0)->where('branch_id', $subAdminId);
            $staffUsers = $query->get();
        } elseif ($role == 'sub-admin') {
            $query = User::where('role', 'Staff')->where('isDeleted', 0)->where('branch_id', $userBranchId);
            $staffUsers = $query->get();
        } else {
            $query = User::where('role', 'Staff')->where('isDeleted', 0)->where('branch_id', $userBranchId);
            $staffUsers = $query->get();
        }
        return view('attendance.add', compact('staffUsers'));
    }
}
