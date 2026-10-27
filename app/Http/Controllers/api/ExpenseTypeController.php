<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\ExpenseType;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ExpenseTypeController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $request->validate(
            [
                'type' => 'required|string|max:255|unique:expense_types,type',
            ],
            [], // Custom error messages (optional)
            [
                'type' => 'expense type' // Custom attribute name
            ]
        );
        if (!empty($request->selectedSubAdminId)) {
            $userId = $request->selectedSubAdminId;
        }
        $expenseType = ExpenseType::create([
            'type' => $request->type,
            'isDeleted' => 0,
            'branch_id' => $userId,
        ]);

        return response()->json(['message' => 'Expense type added successfully.']);
    }

    // Example: ExpenseTypeController.php
    public function list(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userRole = $user->role ?? null;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userBranchId;

        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $query = ExpenseType::where('isDeleted', 0);

        if ($userRole === 'sub-admin') {
            // Sub-admin: show only their own branch's data
            $query->where('branch_id', $userBranchId);
        } elseif ($userRole === 'admin' && !empty($selectedSubAdminId)) {
            // Admin with filter: show selected sub-admin's branch data
            $subAdmin = User::find($selectedSubAdminId);
            if ($subAdmin) {
                $query->where('branch_id', $subAdmin->id);
            }
        } elseif ($userRole === 'admin') {
            $query->where('branch_id', $user->id);
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'LIKE', "%{$search}%");
            });
        }

        $expenseTypes = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $expenseTypes->items(),
            'pagination' => [
                'current_page' => $expenseTypes->currentPage(),
                'last_page' => $expenseTypes->lastPage(),
                'per_page' => $expenseTypes->perPage(),
                'total' => $expenseTypes->total(),
                'next_page_url' => $expenseTypes->nextPageUrl(),
                'prev_page_url' => $expenseTypes->previousPageUrl(),
            ]
        ], 200);
    }


    public function show($id)
    {
        $expenseType = ExpenseType::where('id', $id)->where('isDeleted', 0)->first();

        if (!$expenseType) {
            return response()->json([
                'message' => 'Expense type not found.'
            ], 404);
        }

        return response()->json([
            'data' => $expenseType
        ], 200);
    }

    // PUT /api/expense-types/{id} - update expense type
    public function update(Request $request, $id)
    {
        $expenseType = ExpenseType::where('id', $id)->where('isDeleted', 0)->first();

        if (!$expenseType) {
            return response()->json([
                'message' => 'Expense type not found.'
            ], 404);
        }

        // Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'type' => 'required|string|max:255|unique:expense_types,type,' . $id,
            ],
            [], // Custom error messages (optional)
            [
                'type' => 'expense type' // Custom attribute name
            ]

        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Update and save
        $expenseType->type = $request->input('type');
        $expenseType->save();

        return response()->json([
            'message' => 'Expense type updated successfully.',
            'data' => $expenseType
        ], 200);
    }
    public function destroy($id)
    {
        $expense = ExpenseType::find($id);

        if (!$expense) {
            return response()->json(['error' => 'Expense type not found.'], 404);
        }

        // Soft delete by setting isDeleted = 1
        $expense->isDeleted = 1;
        $expense->save();

        return response()->json(['success' => 'Expense type deleted successfully.']);
    }
}
