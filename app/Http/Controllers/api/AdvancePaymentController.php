<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdvancePayment;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdvancePaymentController extends Controller
{

    public function getFilters(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userRole = $user->role ?? '';
        $branchId = null;

        $selectedSubAdminId = $request->input('selectedSubAdminId');

        // Role-based branch filtering logic
        if ($userRole === 'admin') {
            if (!empty($selectedSubAdminId)) {
                $branchId = $selectedSubAdminId;
            } else {
                $branchId = $user->id;
            }
        } elseif ($userRole === 'sub-admin') {
            // Sub-admin's own branch
            $branchId = $user->id;
        }

        // Build staff filter query
        $staffQuery = DB::table('users')
            // ->join('users', 'advance_payments.staff_id', '=', 'users.id')
            ->select('users.id', 'users.name')
            ->where('users.role', 'staff');

        if ($branchId) {
            $staffQuery->where('users.branch_id', $branchId);
        }

        $staffNames = $staffQuery->distinct()->orderBy('users.name')->get();

        // Get available years from payments
        $yearsQuery = AdvancePayment::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year');

        if ($branchId) {
            $yearsQuery->where('branch_id', $branchId);
        }

        $years = $yearsQuery->pluck('year');

        return response()->json([
            'staff' => $staffNames,
            'years' => $years
        ]);
    }





    // public function index()
    // {
    //     $payments = AdvancePayment::with('staff:id,name')->latest()->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $payments
    //     ]);
    // }

    public function index(Request $request)
    { 
        // dd($request->all());      
        $user = Auth::guard('api')->user();
        $userRole = $user->role;
        $branchId = null;
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);

        if ($perPage <= 0) {
            $perPage = 10;
        }

        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');

        // 1. If Admin
        if ($userRole === 'admin') {
            if ($request->filled('selectedSubAdminId')) {
                // Show selected sub-admin's data
                $subAdmin = \App\Models\User::where('id', $request->selectedSubAdminId)
                    ->where('role', 'sub-admin')
                    ->first();

                if ($subAdmin) {
                    $branchId = $subAdmin->id;
                }
            } else {
                // Show admin's own data
                $branchId = $user->id;
            }
        }

        // 2. If Sub-admin → always own data
        if ($userRole === 'sub-admin') {
            $branchId = $user->id;
        }

        // Base query
        $paymentsQuery = AdvancePayment::with('staff:id,name')->where('isDeleted',0)->latest();

        // Filter by branch
        if ($branchId) {
            $paymentsQuery->where('branch_id', $branchId);
        }

        // Staff filter
        if ($request->filled('staff_id')) {
            $paymentsQuery->where('staff_id', $request->staff_id);
        }

        // Year filter
        if ($request->filled('year')) {
            $paymentsQuery->whereYear('created_at', $request->year);
        }

        // Month filter
        if ($request->filled('month')) {
            $paymentsQuery->whereMonth('created_at', $request->month);
        }

        // Payment mode filter
        if ($request->filled('payment_mode')) {
            $paymentsQuery->where('payment_mode', $request->payment_mode);
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $paymentsQuery->whereBetween('payment_date', [
                $request->from_date,
                $request->to_date
            ]);
        } elseif ($request->filled('from_date')) {
            $paymentsQuery->whereDate('payment_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $paymentsQuery->whereDate('payment_date', '<=', $request->to_date);
        }

        // $totalsQuery = clone $paymentsQuery;
        $totalsQuery = AdvancePayment::where('isDeleted', 0);

        if ($branchId) {
            $totalsQuery->where('branch_id', $branchId);
        }

        if ($request->filled('staff_id')) {
            $totalsQuery->where('staff_id', $request->staff_id);
        }

        if ($request->filled('year')) {
            $totalsQuery->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $totalsQuery->whereMonth('created_at', $request->month);
        }

        if ($request->filled('payment_mode')) {
            $totalsQuery->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $totalsQuery->whereBetween('payment_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $summary = $totalsQuery->selectRaw('
            COALESCE(SUM(amount), 0) as total_amount,
            COALESCE(SUM(paid_amount), 0) as total_paid_amount
        ')->first();

        $totalRecords = null;
        if ($applyPagination) {
            $totalRecords = (clone $paymentsQuery)->count();
            $payments = $paymentsQuery
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
        } else {
            $payments = $paymentsQuery->get();
        }
        $payments = $payments->map(function ($payment) {
        $paid = $payment->paid_amount ?? 0;

            return [
                'id'          => $payment->id,
                'staff_id'    => $payment->staff_id,
                'staff_name'  => $payment->staff->name ?? 'N/A',
                'amount'      => $payment->amount,                 // original advance
                'paid_amount' => $paid,            // already deducted
                'pending'     => $payment->amount - $paid, // 👈 remaining
                'status'      => $payment->status,
                'date'        => $payment->date,
                'method'      => $payment->method,
                'reason'      => $payment->reason,
                'branch_id'   => $payment->branch_id,
            ];
        });

        $totalAmount = (float) ($summary->total_amount ?? 0);
        $totalPaidAmount = (float) ($summary->total_paid_amount ?? 0);

        $response = [
            'status' => true,
            'data' => $payments,
            'summary' => [
                'total_amount' => $totalAmount,
                'total_paid_amount' => $totalPaidAmount,
                'total_pending_amount' => max(0, $totalAmount - $totalPaidAmount),
            ],
        ];

        if ($applyPagination) {
            $lastPage = $perPage > 0 ? (int) ceil($totalRecords / $perPage) : 1;
            $response['pagination'] = [
                'current_page' => $page,
                'last_page' => max(1, $lastPage),
                'per_page' => $perPage,
                'total' => $totalRecords,
            ];
        }

        return response()->json($response);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'method' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',
            'selectedSubAdminId' => 'nullable|string'

        ]);

        $user = Auth::guard('api')->user(); // or simply Auth::user() if you're using web guard
        $userId = $user->id;
        $userRole = $user->role;
        if ($userRole == 'admin' && (int) $validated['selectedSubAdminId'] > 0) {
            $validated['branch_id'] = (int) $validated['selectedSubAdminId'];
        } else {
            // Fallback to logged-in user's ID
            $validated['branch_id'] = $userId;
        }
        $validated['isDeleted'] = 0;

        // dd($userId);
        // $test = $validated['branch_id'];
        // dd($user->id);
        $payment = AdvancePayment::create($validated);

        return response()->json([
            'status' => "success",
            'message' => 'Advance Payment Details Created',
            'data' => $payment
        ]);
    }


    public function show($id)
    {
        $payment = AdvancePayment::with(['staff'])->findOrFail($id);
        return response()->json($payment);
    }

    public function update(Request $request, $id)
    {
        $payment = AdvancePayment::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'method' => 'required|string|max:255',
            'reason' => 'nullable|string|max:255',

        ]);

        $payment->update($validated);

        return response()->json(['message' => 'Advance Payment Updated', 'data' => $payment]);
    }

    public function destroy($id)
    {
        $payment = AdvancePayment::findOrFail($id);
        $payment->isDeleted = 1;
        $payment->save();

        return response()->json(['message' => 'Advance Payment marked as deleted']);
    }
    // public function exportAdvancePayments(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     $userRole = $user->role ?? '';
    //     $userBranchId = $user->id ?? null;
    //     // dd($userRole, $userBranchId);
    //     $selectedSubAdminId = $request->input('selectedSubAdminId') ?? null;
    //     $staffId = $request->input('staff_id');
    //     $year    = $request->input('year');
    //     $month   = $request->input('month');

    //     // Query via model with relationship
    //     $query = AdvancePayment::with('staff:id,name,branch_id');

    //     // Role-based filtering
    //     if ($userRole === 'admin') {
    //         if (!empty($selectedSubAdminId)) {
    //             $query->whereHas('staff', function ($q) use ($selectedSubAdminId) {
    //                 $q->where('branch_id', $selectedSubAdminId);
    //             });
    //         }
    //     } else {
    //         $query->whereHas('staff', function ($q) use ($userBranchId) {
    //             $q->where('branch_id', $userBranchId);
    //         });
    //     }

    //     // Apply filters if given
    //     if (!empty($staffId)) {
    //         $query->where('staff_id', $staffId);
    //     }
    //     if (!empty($year)) {
    //         $query->whereYear('created_at', $year);
    //     }
    //     if (!empty($month)) {
    //         $query->whereMonth('created_at', $month);
    //     }

    //     $payments = $query->get();

    //     if ($payments->isEmpty()) {
    //         return response()->json(['status' => false, 'message' => 'No data found for the given filters.'], 404);
    //     }

    //     // Create spreadsheet
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Headers
    //     $sheet->setCellValue('A1', 'Staff Name');
    //     $sheet->setCellValue('B1', 'Amount');
    //     $sheet->setCellValue('C1', 'Method');
    //     $sheet->setCellValue('D1', 'Date');
    //     $sheet->setCellValue('E1', 'Reason');
    //     $sheet->getStyle('A1:E1')->getFont()->setBold(true);

    //     // Fill rows
    //     $row = 2;
    //     foreach ($payments as $payment) {
    //         $sheet->setCellValue('A' . $row, $payment->staff->name ?? 'N/A');
    //         $sheet->setCellValue('B' . $row, $payment->amount);
    //         $sheet->setCellValue('C' . $row, $payment->method);
    //         $sheet->setCellValue('D' . $row, $payment->created_at->format('Y-m-d'));
    //         $sheet->setCellValue('E' . $row, $payment->reason ?? 'N/A');
    //         $row++;
    //     }

    //     // Writer
    //     $writer = new Xlsx($spreadsheet);
    //     $fileName = 'AdvancePayments_' . date('Ymd_His') . '.xlsx';

    //     return response()->streamDownload(function () use ($writer) {
    //         $writer->save('php://output');
    //     }, $fileName, [
    //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //         'Access-Control-Expose-Headers' => 'Content-Disposition'
    //     ]);
    // }

    public function exportAdvancePayments(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userRole = $user->role ?? '';
        $userBranchId = $user->id ?? null;

        $selectedSubAdminId = $request->input('selectedSubAdminId');
        $staffId = $request->input('staff_id');
        $year    = $request->input('year');
        $month   = $request->input('month');

        $query = AdvancePayment::with('staff:id,name,branch_id')->where('isDeleted',0);

        // Role-based filtering
        if (!empty($selectedSubAdminId)) {
            // Admin exporting selected sub-admin's branch
            $query->whereHas('staff', function ($q) use ($selectedSubAdminId) {
                $q->where('branch_id', $selectedSubAdminId);
            });
        } elseif ($userRole === 'sub-admin' || $userRole === 'admin') {
            // Sub-admin/admin exporting own branch
            $query->whereHas('staff', function ($q) use ($userBranchId) {
                $q->where('branch_id', $userBranchId);
            });
        }
        // else no role-based filtering

        // Apply filters only if they are not empty
        if (!empty($staffId)) {
            $query->where('staff_id', $staffId);
        }
        if (!empty($year)) {
            $query->whereYear('created_at', $year);
        }
        if (!empty($month)) {
            $query->whereMonth('created_at', $month);
        }

        $payments = $query->get();

        // if ($payments->isEmpty()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'No data found for the given filters.'
        //     ], 404);
        // }

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Staff Name');
        $sheet->setCellValue('B1', 'Amount');
        $sheet->setCellValue('C1', 'Method');
        $sheet->setCellValue('D1', 'Date');
        $sheet->setCellValue('E1', 'Reason');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Fill rows
        $row = 2;
        foreach ($payments as $payment) {
            $sheet->setCellValue('A' . $row, $payment->staff->name ?? 'N/A');
            $sheet->setCellValue('B' . $row, $payment->amount);
            $sheet->setCellValue('C' . $row, $payment->method);
            $sheet->setCellValue('D' . $row, $payment->created_at->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $payment->reason ?? 'N/A');
            $row++;
        }

        // Download file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'AdvancePayments_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Access-Control-Expose-Headers' => 'Content-Disposition'
        ]);
    }
}
