<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AdvancePayment;
use App\Models\Salary;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaryController extends Controller
{

    // public function index(Request $request)
    // {
    //     // dd($request->all());
    //     $user = Auth::guard('api')->user();
    //     $role = $user->role;
    //     $userBranchId = $user->id; // ✅ fixed: should be branch_id
    //     $selectedSubAdminId = $request->query('selectedSubAdminId');

    //     // $year = $request->input('year', Carbon::now()->year);
    //     $year = $request->year;
    //     $month = $request->month;
    //     // $month = $request->input('month', Carbon::now()->month);
    //     $staffId = $request->input('staff_id');

    //     $query = User::where('role', 'Staff')->where('isDeleted', 0);

    //     if (!empty($selectedSubAdminId)) {
    //         $query->where('branch_id', $selectedSubAdminId);
    //     } elseif ($role == 'sub-admin') {
    //         $query->where('branch_id', $userBranchId);
    //     } else {
    //         $query->where('branch_id', $userBranchId);
    //     }

    //     if (!empty($staffId)) {
    //         $query->where('id', $staffId);
    //     }

    //     $staffList = $query->get();
    //     $workingDaysInMonth = $this->getWorkingDays($year, $month);
    //     $data = [];

    //     foreach ($staffList as $staff) {
    //         // ✅ Get salary record either by month/year (new) or created_at (old)
    //         $salary = Salary::where('staff_id', $staff->id)
    //             ->where('month', $month)
    //             ->where('year', $year)
    //             ->first();

    //         // ✅ Get all advances (not cleared yet)
    //         $advances = AdvancePayment::where('staff_id', $staff->id)
    //             ->where('status', '!=', 'cleared')
    //             ->where('isDeleted', '=', 0)
    //             ->get();

    //         $totalAdvance1 = $advances->sum('amount') - $advances->sum('paid_amount') ;
    //         $totalAdvance = $advances->sum('amount');
    //         $advancePaidBack = $advances->sum('paid_amount');
    //         $pendingAdvance = max(0, $totalAdvance - $advancePaidBack);

    //         if ($salary) {
    //             // ✅ Salary is already paid
    //             $paidAdvance = $salary->advance_pay ?? 0;

    //             $data[] = [
    //                 'staff_id'        => $staff->id,
    //                 'salary_id'       => $salary->id,
    //                 'staff_name'      => $staff->name,
    //                 'month'           => $month,
    //                 'year'            => $year,
    //                 'present'         => $salary->present,
    //                 'absent'          => $salary->absent,
    //                 'extra_present'   => $salary->extra_present,
    //                 'extra_amount'    => $salary->extra_amount,
    //                 'monthly_salary'  => $salary->salary,
    //                 'pending_advance' => $salary->old_advance_pay - $salary->advance_pay, // ✅ historical snapshot
    //                 'paid_advance'    => $salary->advance_pay,
    //                 'total_salary'    => $salary->total_salary,
    //                 'old_advance_pay'    => $salary->old_advance_pay,
    //                 'status'          => 'Paid',
    //             ];
    //         } else {
    //             // ✅ Salary is pending — calculate attendance dynamically
    //             $monthlyAttendance = Attendance::where('user_id', $staff->id)
    //                 ->whereMonth('date', $month)
    //                 ->whereYear('date', $year)
    //                 ->get();

    //             $regularPresent = $monthlyAttendance->where('status', 'P')->where('extraday', 0)->count();
    //             $extraPresent   = $monthlyAttendance->where('status', 'P')->where('extraday', 1)->count();
    //             $halfDays       = $monthlyAttendance->where('status', 'H')->count();

    //             $halfDayEquivalent = $halfDays * 0.5;
    //             $totalPresent = $regularPresent + $halfDayEquivalent;
    //             $absent = max(0, $workingDaysInMonth - ($totalPresent + $extraPresent));

    //         // dd($absent);
    //             $absent = (int) floor($absent); // ✅ ensure integer

    //             $data[] = [
    //                 'staff_id'        => $staff->id,
    //                 'staff_name'      => $staff->name,
    //                 'month'           => $month,
    //                 'year'            => $year,
    //                 'present'         => $totalPresent + $extraPresent,
    //                 'absent'          => $absent + $halfDayEquivalent,
    //                 'extra_present'   => $extraPresent,
    //                 'monthly_salary'  => $staff->salary,
    //                 'pending_advance' => $totalAdvance1, // ✅ all still pending
    //                 'paid_advance'    => 0,
    //                 'total_salary'    => $staff->salary,
    //                 'status'          => 'Pending',
    //             ];
    //             // if(isset($staffId) && $staffId == 1226)
    //             // {
    //             //     dd('hh',$absent,$halfDayEquivalent);
    //             // }
    //         }
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $data
    //     ]);
    // }

    public function index(Request $request)
    {
        // dd($request->page); 
        $user               = Auth::guard('api')->user();
        $role               = $user->role;
        $userBranchId       = $user->id;
        $selectedSubAdminId = $request->query('selectedSubAdminId');

        // Get pagination parameters
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search', '');

        $year  = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $staffId = $request->input('staff_id');

        $query = User::where('role', 'staff')->where('isDeleted', 0);

        if (!empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);
        } elseif ($role == 'sub-admin') {
            $query->where('branch_id', $userBranchId);
        } else {
            $query->where('branch_id', $userBranchId);
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($staffId)) {
            $query->where('id', $staffId);
        }

        // Get total count for pagination
        $totalStaff = $query->count();

        // Get paginated staff list
        $staffList = $query->orderBy('name', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $workingDaysInMonth = (int) $this->getWorkingDays($year, $month);
        $data = [];

        foreach ($staffList as $staff) {
            // Get salary record either by month/year
            $salary = Salary::where('staff_id', $staff->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            // Get all advances (not cleared yet)
            $advances = AdvancePayment::where('staff_id', $staff->id)
                ->where('status', '!=', 'cleared')
                ->get();

            // $totalAdvance1   = $advances->sum('amount') - $advances->sum('paid_amount');
            // $totalAdvance1 = (float)$advances->sum('amount') - (float)$advances->sum('paid_amount');
            $totalAdvance    = (float)$advances->sum('amount');
            $advancePaidBack = (float)$advances->sum(function ($row) {
                return (float) ($row->paid_amount ?? 0);
            });
            $totalAdvance1   = $totalAdvance - $advancePaidBack;
            $pendingAdvance  = max(0, $totalAdvance - $advancePaidBack);

            if ($salary) {
                // Salary is already paid
                $paidAdvance = $salary->advance_pay ?? 0;

                $data[] = [
                    'staff_id'        => $staff->id,
                    'salary_id'       => $salary->id,
                    'staff_name'      => $staff->name,
                    'month'           => $month,
                    'year'            => $year,
                    'present'         => $salary->present,
                    'absent'          => $salary->absent,
                    'extra_present'   => $salary->extra_present,
                    'extra_amount'    => $salary->extra_amount,
                    'monthly_salary'  => $salary->salary,
                    'pending_advance' => $pendingAdvance,
                    'paid_advance'    => $salary->advance_pay,
                    'total_salary'    => $salary->total_salary,
                    'old_advance_pay' => $salary->old_advance_pay,
                    'status'          => 'Paid',
                ];
            } else {
                // Salary is pending — calculate attendance dynamically
                $monthlyAttendance = Attendance::where('user_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $regularPresent = $monthlyAttendance->where('status', 'P')->where('extraday', 0)->count();
                $extraPresent   = $monthlyAttendance->where('status', 'P')->where('extraday', 1)->count();
                $halfDays       = $monthlyAttendance->where('status', 'H')->count();

                $halfDayEquivalent = $halfDays * 0.5;
                $totalPresent      = $regularPresent + $halfDayEquivalent;
                $absent            = max(0, $workingDaysInMonth - ($totalPresent + $extraPresent));
                $absent = (int) floor($absent);

                $data[] = [
                    'staff_id'        => $staff->id,
                    'staff_name'      => $staff->name,
                    'month'           => $month,
                    'year'            => $year,
                    'present'         => $totalPresent + $extraPresent,
                    'absent'          => $absent,
                    'extra_present'   => $extraPresent,
                    'monthly_salary'  => $staff->salary,
                    'pending_advance' => $totalAdvance1,
                    'paid_advance'    => 0,
                    'total_salary'    => $staff->salary,
                    'status'          => 'Pending',
                ];
            }
        }

        // Return paginated response
        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => (int)$page,
                'last_page' => ceil($totalStaff / $perPage),
                'per_page' => (int)$perPage,
                'total' => $totalStaff,
                'from' => $data ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $data ? (($page - 1) * $perPage) + count($data) : 0,
            ]
        ]);
    }


 private function getWorkingDays($year, $month)
    {
        if (empty($year) || empty($month) || $month < 1 || $month > 12) {
            return 0; // or throw custom error
        }

        return cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
    }


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'staff_id' => 'required|exists:users,id',
    //         'present' => 'required|integer|min:0',
    //         'absent' => 'required|integer|min:0',
    //         'extra_present' => 'required|integer|min:0',
    //         'salary' => 'required|numeric|min:1',
    //         'extra_amount' => 'required|numeric|min:0',
    //         'advance_pay' => 'required|numeric|min:0',
    //         'total_salary' => 'required|numeric|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error.',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // ✅ Block payment if no present days
    //     if ((int)$request->present === 0) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Payment cannot be processed because present days are 0.'
    //         ], 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $user = Auth::guard('api')->user();
    //         $salary = new Salary();
    //         $salary->staff_id = $request->staff_id;
    //         $salary->branch_id = $request->selectedSubAdminId ?? $user->id;
    //         $salary->present = $request->present;
    //         $salary->absent = $request->absent;
    //         $salary->extra_present = $request->extra_present;
    //         $salary->advance_pay = $request->advance_pay;
    //         $salary->salary = $request->salary;
    //         $salary->extra_amount = $request->extra_amount;
    //         $salary->total_salary = $request->total_salary;
    //         $salary->status = 'paid';
    //         $salary->save();

    //         // Deduct Advance if applicable
    //         if ($request->advance_pay > 0) {
    //             $advance = AdvancePayment::where('staff_id', $request->staff_id)->first();
    //             if ($advance) {
    //                 $remaining = $advance->amount - $request->advance_pay;
    //                 if ($remaining <= 0) {
    //                     $advance->delete();
    //                 } else {
    //                     $advance->amount = $remaining;
    //                     $advance->save();
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         $pdf = $this->generateStaffPDF($request);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Salary paid and advance updated successfully.',
    //             'pdf' => base64_encode($pdf)
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong while processing salary.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'staff_id'      => 'required|exists:users,id',
            'present'       => 'required|min:0',
            'monthselected' => 'required',
            'selectedYear1' => 'required',
            'absent'        => 'required',
            'extra_present' => 'required|integer|min:0',
            'salary'        => 'required|numeric|min:1',
            'extra_amount'  => 'required|numeric|min:0',
            'advance_pay'   => 'required|numeric|min:0',
            'total_salary'  => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ✅ Block payment if no present days
        // ✅ Block payment if no present AND no extra days
        if ((int)$request->present === 0 && (int)$request->extra_present === 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment cannot be processed because both present days and extra present days are 0.'
            ], 422);
        }


        DB::beginTransaction();

        try {
            $user = Auth::guard('api')->user();

            // ✅ Save salary record
            $salary = new Salary();
            $salary->staff_id      = $request->staff_id;
            // $salary->branch_id     = $request->selectedSubAdminId ?? $user->branch_id;
            $salary->branch_id = $request->selectedSubAdminId ?? $user->id;
            $salary->month       = $request->monthselected;
            $salary->year       = $request->selectedYear1;
            $salary->present       = $request->present;
            $salary->absent        = $request->absent;
            $salary->extra_present = $request->extra_present;
            $salary->advance_pay   = $request->advance_pay;
            $salary->salary        = $request->salary;
            $salary->extra_amount  = $request->extra_amount;
            $salary->total_salary  = $request->total_salary;
            $salary->old_advance_pay  = $request->old_advance_pay;
            $salary->status        = 'paid';
            $salary->save();

            // ✅ Deduct advance if applicable
            // // ✅ Deduct advance if applicable
            // if ($request->advance_pay > 0) {
            //     $advance = AdvancePayment::where('staff_id', $request->staff_id)
            //         ->where('status', '!=', 'cleared')
            //         ->first();

            //     if ($advance) {
            //         // Remaining advance balance
            //         $remaining = $advance->amount - $advance->paid_amount;

            //         // 🚫 Block if user tries to deduct more than remaining
            //         if ($request->advance_pay > $remaining) {
            //             DB::rollBack();
            //             return response()->json([
            //                 'status'  => false,
            //                 'message' => "Advance deduction cannot exceed remaining balance of {$remaining}."
            //             ], 422);
            //         }

            //         // ✅ Otherwise update paid amount
            //         $advance->paid_amount += $request->advance_pay;

            //         // Check remaining balance again
            //         $remaining = $advance->amount - $advance->paid_amount;

            //         if ($remaining <= 0) {
            //             $advance->status = 'cleared';
            //             $advance->paid_amount = $advance->amount;
            //         } else {
            //             $advance->status = 'pending';
            //         }

            //         $advance->save();
            //     }
            // }
            // ✅ Deduct advance if applicable
            if ($request->advance_pay > 0) {
                $advances = AdvancePayment::where('staff_id', $request->staff_id)
                    ->where('status', '!=', 'cleared')
                    ->orderBy('date', 'asc') // oldest first
                    ->get();

                $totalRemaining = $advances->sum(function ($adv) {
                    return $adv->amount - $adv->paid_amount;
                });

                // 🚫 Block if deduction more than total remaining
                if ($request->advance_pay > $totalRemaining) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => false,
                        'message' => "Advance deduction cannot exceed remaining balance of {$totalRemaining}."
                    ], 422);
                }

                $deduction = $request->advance_pay;

                foreach ($advances as $advance) {
                    $remaining = $advance->amount - $advance->paid_amount;

                    if ($deduction <= 0) break;

                    if ($deduction >= $remaining) {
                        // clear this advance
                        $advance->paid_amount = $advance->amount;
                        $advance->status = 'cleared';
                        $deduction -= $remaining;
                    } else {
                        // partially pay this advance
                        $advance->paid_amount += $deduction;
                        $advance->status = 'pending';
                        $deduction = 0;
                    }

                    $advance->save();
                }
            }


            DB::commit();

            // ✅ Generate salary slip PDF
            $pdf = $this->generateStaffPDF($request);

            return response()->json([
                'status'  => true,
                'message' => 'Salary paid and advance updated successfully.',
                'pdf'     => base64_encode($pdf)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while processing salary.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        // ✅ Validate inputs
        $validator = Validator::make($request->all(), [
            'staff_id'      => 'required|exists:users,id',
            // 'present'       => 'required|min:0',
            'monthselected' => 'required',
            'selectedYear1' => 'required',
            // 'absent'        => 'required|integer|min:0',
            // 'extra_present' => 'required|integer|min:0',
            'salary'        => 'required|numeric|min:1',
            'extra_amount'  => 'required|numeric|min:0',
            'advance_pay'   => 'required|numeric|min:0',
            'total_salary'  => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // dd($request->all());

        // ✅ Block payment if no present days or extra present
        // if ((int)$request->present === 0 && (int)$request->extra_present === 0) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Payment cannot be processed because both present days and extra present days are 0.'
        //     ], 422);
        // }

        DB::beginTransaction();

        try {
            $user = Auth::guard('api')->user();

            // ✅ Find existing salary record
            $salary = Salary::find($id);

            if (!$salary) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Salary record not found.'
                ], 404);
            }

            // ✅ If advance changed, handle adjustment
            if ($salary->advance_pay != $request->advance_pay) {
                // Reverse old advance if needed
                if ($salary->advance_pay > 0) {
                    $this->reverseAdvance($salary->staff_id, $salary->advance_pay);
                }

                // Apply new advance
                if ($request->advance_pay > 0) {
                    $advances = AdvancePayment::where('staff_id', $request->staff_id)
                        ->where('status', '!=', 'cleared')
                        ->orderBy('date', 'asc')
                        ->get();

                    $totalRemaining = $advances->sum(function ($adv) {
                        return $adv->amount - $adv->paid_amount;
                    });

                    if ($request->advance_pay > $totalRemaining) {
                        DB::rollBack();
                        return response()->json([
                            'status'  => false,
                            'message' => "Advance deduction cannot exceed remaining balance of {$totalRemaining}."
                        ], 422);
                    }

                    $deduction = $request->advance_pay;
                    foreach ($advances as $advance) {
                        $remaining = $advance->amount - $advance->paid_amount;
                        if ($deduction <= 0) break;

                        if ($deduction >= $remaining) {
                            $advance->paid_amount = $advance->amount;
                            $advance->status = 'cleared';
                            $deduction -= $remaining;
                        } else {
                            $advance->paid_amount += $deduction;
                            $advance->status = 'pending';
                            $deduction = 0;
                        }

                        $advance->save();
                    }
                }
            }

            // ✅ Update salary record
            $salary->update([
                'staff_id'      => $request->staff_id,
                'branch_id'     => $request->selectedSubAdminId ?? $user->id,
                'month'         => $request->monthselected,
                'year'          => $request->selectedYear1,
                // 'present'       => $request->present,
                // 'absent'        => $request->absent,
                // 'extra_present' => $request->extra_present,
                'advance_pay'   => $request->advance_pay,
                'salary'        => $request->salary,
                'extra_amount'  => $request->extra_amount,
                'total_salary'  => $request->total_salary,
            ]);

            DB::commit();

            // ✅ Generate updated PDF
            $pdf = $this->generateStaffPDF($request);

            return response()->json([
                'status'  => true,
                'message' => 'Salary record updated successfully.',
                'pdf'     => base64_encode($pdf)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while updating salary.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Helper to reverse previous advance deduction
     */
    private function reverseAdvance($staffId, $amount)
    {
        $advances = AdvancePayment::where('staff_id', $staffId)
            ->orderBy('date', 'desc')
            ->get();

        $remaining = $amount;

        foreach ($advances as $advance) {
            if ($remaining <= 0) break;

            $alreadyPaid = $advance->paid_amount;

            if ($alreadyPaid > 0) {
                $revert = min($remaining, $alreadyPaid);
                $advance->paid_amount -= $revert;

                // ✅ Revert status if cleared
                if ($advance->paid_amount < $advance->amount) {
                    $advance->status = 'pending';
                }

                $advance->save();
                $remaining -= $revert;
            }
        }
    }


    public function generateStaffPDF(Request $request)
    {
        $staffId = $request->input('staff_id');
        $month   = $request->month ?? $request->monthselected;
        $year    = $request->year ?? $request->selectedYear1;
        $staff   = User::find($staffId);
        $branchId = $request->input('selectedSubAdminId') ?? (auth()->user()->branch_id ?? null);

        if (!$staff) {
            return response()->json(['status' => false, 'message' => 'Staff not found.'], 404);
        }

        $salary = Salary::where('staff_id', $staffId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
            

        if (!$salary) {
            return response()->json(['status' => false, 'message' => 'Salary not generated yet.'], 404);
        }

        // ✅ Fetch attendance for half-day calculation
        $monthlyAttendance = Attendance::where('user_id', $staffId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $halfDays = $monthlyAttendance->where('status', 'H')->count();
        $halfDayEquivalent = $halfDays * 0.5;

        // ✅ Fetch total advances for the month
        $totalAdvance = AdvancePayment::where('staff_id', $staffId)->where('isDeleted', '=', 0)->where('status', '!=', 'cleared')
            // ->whereMonth('date', $month)
            // ->whereYear('date', $year)
            ->sum('amount');


        


         

        // ✅ Paid & Pending advance (same logic as index)
        $paidAdvance    = $salary->advance_pay ?? 0;
        $pendingAdvance = max(0, $totalAdvance - $paidAdvance);
//    dd($pendingAdvance);
        // ✅ Branch-wise settings first, fallback to default/global
        $settings = null;
        if ($branchId) {
            $settings = Setting::where('branch_id', $branchId)->first();
        }
        if (!$settings) {
            $settings = Setting::first(); // fallback
        }
        // dd($salary->present,$halfDayEquivalent);

        $data = [[
            'staff_id'        => $staff->id,
            'staff_name'      => $staff->name,
            'present'         => $salary->present, // ✅ include half-days
            'absent'          => $salary->absent,
            'extra_present'   => $salary->extra_present,
            'half_days'       => $halfDays,  // ✅ add separate field for PDF clarity
            'monthly_salary'  => $salary->salary,
            'extra_amount'    => $salary->extra_amount,
            'paid_advance'    => $paidAdvance,
            // 'pending_advance' => $pendingAdvance,
            'pending_advance' => $salary->old_advance_pay - $paidAdvance,
            'total_salary'    => $salary->total_salary,
            'old_advance_pay'    => $salary->old_advance_pay,
            'status'          => $salary->status,
        ]];

        $pdf = PDF::loadView('salary.staff_pdf', compact('data', 'month', 'year', 'settings'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('salary_slip.pdf');
    }

    public function getSalaryYears()
    {
        $yearsFromSalaries = Salary::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $currentYear = now()->year;

        if (!in_array($currentYear, $yearsFromSalaries)) {
            $yearsFromSalaries[] = $currentYear;
        }

        sort($yearsFromSalaries); // optional: ascending order

        return response()->json([
            'status' => true,
            'years' => $yearsFromSalaries
        ]);
    }

    public function getActiveStaff(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userBranchId = $user->id; // ✅ should use branch_id, not id
        $selectedSubAdminId = $request->query('selectedSubAdminId');


        $query = User::where('role', 'staff')
            ->where('isDeleted', 0);

        // ✅ Correct variable name here
        if (!empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);
        } elseif ($role == 'sub-admin') {
            $query->where('branch_id', $userBranchId);
        } else {
            $query->where('branch_id', $userBranchId);
        }

        // For debugging only
        // dd($selectedSubAdminId);

        $staff = $query->select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'staff' => $staff
        ]);
    }

    public function export(Request $request)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
    }

    $year = $request->input('year', Carbon::now()->year);
    $month = $request->input('month', Carbon::now()->month);
    $staffId = $request->input('staff_id');
    $selectedSubAdminId = $request->input('selectedSubAdminId');

    $role = $user->role;
    $userBranchId = $user->id;
    $branchId = $selectedSubAdminId ?? ($user->branch_id ?? null);

    // Base query for staff
    $query = User::where('role', 'staff')->where('isDeleted', 0);

    // Branch filtering
    if ($selectedSubAdminId) {
        $query->where('branch_id', $selectedSubAdminId);
    } elseif ($role === 'sub-admin') {
        $query->where('branch_id', $userBranchId);
    } else {
        $query->where('branch_id', $userBranchId);
    }

    // Staff-specific filter
    if ($staffId) {
        $query->where('id', $staffId);
    }

    $staffList = $query->get();
    if ($staffList->isEmpty()) {
        return response()->json(['status' => false, 'message' => 'No staff found.'], 404);
    }

    $rows = [];
    // Header
    $rows[] = [
        'Staff ID', 'Staff Name', 'Present', 'Absent', 'Extra Present', 
        'Monthly Salary', 'Extra Amount', 'Paid Advance', 'Pending Advance', 'Total Salary', 'Status'
    ];

    foreach ($staffList as $staff) {
        // $salary = Salary::where('staff_id', $staff->id)
        //                 ->where('month', $month)
        //                 ->where('year', $year)
        //                 ->first();

        // $totalAdvance = AdvancePayment::where('staff_id', $staff->id)->where('isDeleted', '=', 0)->where('status', '!=', 'cleared')
        //                     ->sum('amount');

        $salary = Salary::where('staff_id', $staff->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // ✅ Get all advances (not cleared yet)
        $advances = AdvancePayment::where('staff_id', $staff->id)
            ->where('status', '!=', 'cleared')
            ->where('isDeleted', '=', 0)
            ->get();

        $totalAdvance1 = $advances->sum('amount') - $advances->sum('paid_amount') ;
        $totalAdvance = $advances->sum('amount');
        $advancePaidBack = $advances->sum('paid_amount');
        $pendingAdvance = max(0, $totalAdvance - $advancePaidBack);

        if ($salary) {
            $paidAdvance = $salary->advance_pay ?? 0;
            $historicalPending = ($salary->old_advance_pay ?? 0) - $paidAdvance;

            $rows[] = [
                $staff->id,
                $staff->name,
                $salary->present,
                $salary->absent,
                $salary->extra_present,
                $salary->salary,
                $salary->extra_amount,
                $paidAdvance,
                $historicalPending,
                $salary->total_salary,
                'Paid'
            ];
        } else {
            // Pending staff → calculate attendance
            $monthlyAttendance = Attendance::where('user_id', $staff->id)
                ->when($month, fn($q) => $q->whereMonth('date', $month))
                ->when($year, fn($q) => $q->whereYear('date', $year))
                ->get();

            $regularPresent = $monthlyAttendance->where('status', 'P')->where('extraday', 0)->count();
            $extraPresent   = $monthlyAttendance->where('status', 'P')->where('extraday', 1)->count();
            $halfday        = $monthlyAttendance->where('status', 'H')->count();

            $totalPresent = $regularPresent + ($halfday * 0.5);
            $workingDays = $this->getWorkingDays($year, $month);
            $absent = max(0, $workingDays - $totalPresent);

            $rows[] = [
                $staff->id,
                $staff->name,
                $totalPresent,
                $absent,
                $extraPresent,
                $staff->salary,
                0,              // extra amount
                0,              // paid advance
                $pendingAdvance,  // pending advance
                $staff->salary, // total salary
                'Pending'
            ];
        }
    }
    
    // Generate Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($rows, null, 'A1');

    // Style header
    $sheet->getStyle('A1:K1')->getFont()->setBold(true);
    $sheet->getStyle('A1:K' . count($rows))->getBorders()->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = "salary_export_{$month}_{$year}.xlsx";

    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}



    public function generatePDF(Request $request)
    {
        
        $staffId = $request->input('staff_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userBranchId = $user->id;
        $branchId = $request->input('selectedSubAdminId') ?? (auth()->user()->branch_id ?? null);

        $settings = Setting::where('branch_id', $branchId)->first();
        if (!$settings) {
            $settings = Setting::first(); // fallback to default
        }

        // Base query for staff
        $query = User::where('role', 'staff')->where('isDeleted', 0);

        // Branch-wise filtering
        if (!empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);
        } elseif ($role === 'sub-admin') {
            $query->where('branch_id', $userBranchId);
        } else {
            $query->where('branch_id', $userBranchId);
        }

        // Staff-specific filter
        if (!empty($staffId)) {
            $query->where('id', $staffId);
        }

        $staffList = $query->get();
        if ($staffList->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No staff found for the selected criteria.'], 404);
        }

        $data = [];

        foreach ($staffList as $staff) {
            // Salary record exists
            // $salary = Salary::where('staff_id', $staff->id)
            // ->where('month', $month)
            // ->where('year', $year)
            // ->first();

            // // ✅ Get total advances for this staff in that month
            // $totalAdvance = AdvancePayment::where('staff_id', $staff->id)->where('isDeleted', '=', 0)->where('status', '!=', 'cleared')
            //     // ->when($month, fn($q) => $q->whereMonth('date', $month))
            //     // ->when($year, fn($q) => $q->whereYear('date', $year))
            //     ->sum('amount');

            $salary = Salary::where('staff_id', $staff->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            // ✅ Get all advances (not cleared yet)
            $advances = AdvancePayment::where('staff_id', $staff->id)
                ->where('status', '!=', 'cleared')
                ->where('isDeleted', '=', 0)
                ->get();

            $totalAdvance1 = $advances->sum('amount') - $advances->sum('paid_amount') ;
            $totalAdvance = $advances->sum('amount');
            $advancePaidBack = $advances->sum('paid_amount');
            $pendingAdvance = max(0, $totalAdvance - $advancePaidBack);
                
                
                if ($salary) {
                    $paidAdvance    = $salary->advance_pay ?? 0;
                    // $pendingAdvance = max(0, $totalAdvance - $paidAdvance);

                   
                   

                $data[] = [
                    'staff_id'        => $staff->id,
                    'staff_name'      => $staff->name,
                    'present'         => $salary->present,
                    'absent'          => $salary->absent,
                    'extra_present'   => $salary->extra_present,
                    'monthly_salary'  => $salary->salary,
                    'extra_amount'    => $salary->extra_amount,
                    'paid_advance'    => $paidAdvance,
                    'pending_advance' => $salary->old_advance_pay - $paidAdvance,
                    'total_salary'    => $salary->total_salary,
                    'status'          => 'Paid',
                ];
            } else {
                // Pending staff → calculate attendance
                $monthlyAttendance = Attendance::where('user_id', $staff->id)
                    ->when($month, fn($q) => $q->whereMonth('date', $month))
                    ->when($year, fn($q) => $q->whereYear('date', $year))
                    ->get();

                $regularPresent = $monthlyAttendance->where('status', 'P')->where('extraday', 0)->count();
                $extraPresent   = $monthlyAttendance->where('status', 'P')->where('extraday', 1)->count();
                $halfday        = $monthlyAttendance->where('status', 'H')->count(); // ✅ Half Day

                // ✅ Add half-day as 0.5 present
                $totalPresent   = $regularPresent + ($halfday * 0.5);

                $workingDays    = $this->getWorkingDays($year, $month);
                $absent         = max(0, $workingDays - $totalPresent);

                $paidAdvance    = 0;  // no salary yet
                // $pendingAdvance = $totalAdvance;

                $data[] = [
                    'staff_id'        => $staff->id,
                    'staff_name'      => $staff->name,
                    'present'         => $totalPresent,  // ✅ half-day merged into present
                    'absent'          => $absent,
                    'extra_present'   => $extraPresent,
                    'monthly_salary'  => $staff->salary,
                    'extra_amount'    => 0,
                    'paid_advance'    => $paidAdvance,
                    'pending_advance' => $pendingAdvance,
                    'total_salary'    => $staff->salary,
                    'status'          => 'Pending',
                ];
            }
        }
        

        // Generate PDF
        $pdf = PDF::loadView('salary.pdf', [
            'data' => $data,
            'month' => $month,
            'year' => $year,
            'settings' => $settings,
        ])->setPaper('A4', 'portrait');

        return $pdf->download("salary_export_{$month}_{$year}.pdf");
    }


    public function getAdvanceHistory(Request $request)
    {
        $staffId = $request->query('staff_id');

        if (!$staffId) {
            return response()->json(['status' => false, 'message' => 'Staff ID required'], 400);
        }

        $history = AdvancePayment::where('staff_id', $staffId)->where('isDeleted', '=', 0)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'history' => $history
        ]);
    }
}
