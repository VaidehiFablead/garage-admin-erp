<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\User;
use App\Models\ExpenseType;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    // public function getExpenses(Request $request)
    // {
    //     $authUser = Auth::guard('api')->user();

    //     $query = Expense::with('expenseType')->where('isDeleted', 0);

    //     // ✅ Role-based filtering
    //     if ($authUser->role === 'admin' && $request->selectedSubAdminId) {
    //         // if ($request->filled('selectedSubAdminId')) {
    //         $query->where('branch_id', (int) $request->selectedSubAdminId);
    //         // }
    //         // If not sent, show all expenses
    //     } elseif ($authUser->role === 'sub-admin') {
    //         $query->where('branch_id', $authUser->id); // Only own branch
    //     } elseif ($authUser->role === 'admin') {
    //         $query->where('branch_id', $authUser->id);
    //     }

    //     // ✅ Date filter
    //     if ($request->has('date')) {
    //         $query->whereDate('expense_date', $request->date);
    //     }

    //     // ✅ Expense type filter
    //     if ($request->has('expense_type_id')) {
    //         $query->where('expense_type_id', $request->expense_type_id);
    //     }

    //     // ✅ Month/year filter
    //     if ($request->has('month')) {
    //         $query->whereMonth('expense_date', $request->month);
    //     }

    //     if ($request->has('year')) {
    //         $query->whereYear('expense_date', $request->year);
    //     }

    //     $query->orderBy('id', 'desc');
    //     $expenses = $query->get();
    //     $totalAmount = (clone $query)->sum('amount');

    //     $settings = DB::table('settings')->first();
    //     $currencySymbol = $settings->currency_symbol ?? '₹';
    //     $currencyPosition = $settings->currency_position ?? 'left';

    //     return response()->json([
    //         'currency_symbol' => $currencySymbol,
    //         'currency_position' => $currencyPosition,
    //         'total_amount' => $totalAmount,
    //         'data' => $expenses
    //     ]);
    // }

     public function getExpenses(Request $request)
    {
        $authUser = Auth::guard('api')->user();

        if (! $authUser) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId       = $authUser->id;
        $userRole     = $authUser->role;
        $userBranchId = $authUser->branch_id;

        /**
         * ✅ Determine the correct branch ID based on role and context
         */
        if ($userRole === 'staff') {
            // Staff should see data from the branch that created them (their parent branch)
            $branch_id = $authUser->created_by;
        } elseif (! empty($request->selectedSubAdminId)) {
            // Admin viewing a specific sub-admin’s data
            $branch_id = $request->selectedSubAdminId;
        } elseif ($userBranchId) {
            // Sub-admin or user with assigned branch_id
            $branch_id = $userBranchId;
        } else {
            // Admin or fallback to own ID
            $branch_id = $userId;
        }

        // ✅ Build the main expense query
        $query = Expense::with('expenseType')
            ->where('isDeleted', 0);

        // ✅ Role-based filtering
        if ($userRole === 'staff') {
            // Staff only see their own created records
            $query->where('created_by', $userId);
        } else {
            // Admin/sub-admin filter by branch_id
            $query->where('branch_id', $branch_id);
        }

        // ✅ Apply filters
        if ($request->filled('date')) {
            $query->whereDate('expense_date', $request->date);
        }

        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('expense_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('expense_date', $request->year);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('expense_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('expenseType', function ($expenseTypeQuery) use ($search) {
                        $expenseTypeQuery->where('type', 'like', "%{$search}%");
                    });

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
                    $q->orWhereDate('expense_date', $search);
                }
            });
        }

        $query->orderBy('id', 'desc');

        $totalAmount = (clone $query)->sum('amount');
        $perPage = (int) $request->input('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;
        $expenses = $query->paginate($perPage);

        // ✅ Fetch branch-specific settings
        $settings = DB::table('settings')
            ->where('branch_id', $branch_id)
            ->first();

        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status'            => true,
            'currency_symbol'   => $currencySymbol,
            'currency_position' => $currencyPosition,
            'total_amount'      => $totalAmount,
            'data'              => $expenses->items(),
            'pagination'        => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ],
        ]);
    }


    public function store(Request $request)
    {

        $authUser = Auth::guard('api')->user();
        $userId = $authUser->id;
        $validator = Validator::make(
            $request->all(),
            [
                'expense_name' => 'required|string|max:255',
                'payment_method' => 'required|string|max:255',
                'expense_date' => 'required|date_format:d-m-Y',
                'amount' => 'required|numeric|min:0',
                'expense_type_id' => 'required|exists:expense_types,id',
                'description' => 'nullable|string',
            ],
            [], // Custom messages array (optional if not needed)
            [   // Custom attribute names
                'expense_type_id' => 'expense type'
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Convert expense_date to Y-m-d for MySQL
        $formattedDate = Carbon::createFromFormat('d-m-Y', $request->expense_date)->format('Y-m-d');
        // dd($request->selectedSubAdminId);
        if (!empty($request->selectedSubAdminId)) {
            $userId = $request->selectedSubAdminId;
        }
        Expense::create([
            'expense_name' => $request->expense_name,
            'expense_date' => $formattedDate,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_type_id' => $request->expense_type_id,
            'description' => $request->description,
            'branch_id' => $userId,
            'created_by' => $userId,
        ]);

        return response()->json(['success' => 'Expense added successfully']);
    }

    public function edit(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        $expense = Expense::with('expenseType')->findOrFail($id);
        $userRole = $user->role ?? null;
        // dd($userRole);
        $query = ExpenseType::where('isDeleted', 0);
        // dd($request->selectedSubAdminId);
        // Role: Admin
        if ($userRole === 'admin') {
            // Admin with sub-admin filter
            if ($request->has('selectedSubAdminId') && !empty($request->selectedSubAdminId)) {
                // Get selected sub-admin's branch_id
                $subAdmin = User::find($request->selectedSubAdminId);
                // dd($subAdmin);
                if ($subAdmin) {
                    $query->where('branch_id', $subAdmin->id);
                }
            }
            // Else: show all expenses (no branch filter)
        }

        // Role: Sub-admin
        elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $user->id);
        }

        $expenseTypes = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => $expense,
            'expenseTypes' => $expenseTypes,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'expense_name' => 'required|string|max:255',
                'payment_method' => 'required|string|max:255',
                'expense_date' => 'required|date_format:d-m-Y',
                'amount' => 'required|numeric|min:0',
                'expense_type_id' => 'required|exists:expense_types,id',
                'description' => 'nullable|string',
            ],
            [], // No custom error messages
            [   // Custom attribute display names
                'expense_type_id' => 'expense type'
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense = Expense::findOrFail($id);
        $expense->update([
            'expense_name' => $request->expense_name,
            'expense_date' => Carbon::createFromFormat('d-m-Y', $request->expense_date)->format('Y-m-d'),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'expense_type_id' => $request->expense_type_id, // update expense_type_id
            'description' => $request->description,
        ]);

        return response()->json(['success' => 'Expense updated successfully.']);
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['error' => 'Expense not found.'], 404);
        }

        // Soft delete by setting isDeleted = 1
        $expense->isDeleted = 1;
        $expense->save();

        return response()->json(['success' => 'Expense deleted successfully.']);
    }
    public function getExpensesReport(Request $request)
    {
        $user = Auth::guard('api')->user();

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $filter = $request->query('filter');

        $expenses = Expense::where('isDeleted', 0);
        // dd($user->id);
        // Role and branch filtering
        if ($user->role === 'admin' && $request->selectedSubAdminId) {
            if ($request->has('selectedSubAdminId') && !empty($request->selectedSubAdminId)) {
                $subAdmin = \App\Models\User::find($request->selectedSubAdminId);
                if ($subAdmin && $subAdmin->branch_id) {
                    $expenses->where('branch_id', $subAdmin->branch_id);
                }
            }
            // Else: no branch filter (show all data)
        } else {
            // Sub-admins see only their own branch data
            $expenses->where('branch_id', $user->id);
        }

        // Date filtering
        if ($filter) {
            $today = Carbon::today();
            switch ($filter) {
                case 'this_week':
                    $expenses->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $expenses->whereMonth('expense_date', $today->month)
                        ->whereYear('expense_date', $today->year);
                    break;
                case 'last_6_months':
                    $expenses->whereBetween('expense_date', [Carbon::now()->subMonths(6), Carbon::now()]);
                    break;
                case 'this_year':
                    $expenses->whereYear('expense_date', $today->year);
                    break;
                case 'previous_year':
                    $expenses->whereYear('expense_date', $today->subYear()->year);
                    break;
            }
        }

        // Fetch results
        $results = $expenses->select('id', 'expense_name', 'amount', 'expense_date', 'description')
            ->orderBy('expense_date', 'desc')
            ->get()
            ->map(function ($expense) use ($currencySymbol, $currencyPosition) {
                $formattedAmount = number_format($expense->amount, 2);
                $expense->formatted_amount = $currencyPosition === 'left'
                    ? $currencySymbol . ' ' . $formattedAmount
                    : $formattedAmount . ' ' . $currencySymbol;
                return $expense;
            });

        return response()->json($results);
    }
}
