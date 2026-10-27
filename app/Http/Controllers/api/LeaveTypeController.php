<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\LeaveTypeModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token missing or invalid.',
            ], 401);
        }

        $perPage = max(1, (int) $request->input('per_page', 10));
        $page = max(1, (int) $request->input('page', 1));
        $search = trim((string) $request->input('search', ''));

        $query = LeaveTypeModel::query();

        if ($search !== '') {
            $query->where('leave_type', 'LIKE', "%{$search}%");
        }

        $leaveTypes = $query->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $leaveTypes->items(),
            'pagination' => [
                'current_page' => $leaveTypes->currentPage(),
                'last_page' => $leaveTypes->lastPage(),
                'per_page' => $leaveTypes->perPage(),
                'total' => $leaveTypes->total(),
                'next_page_url' => $leaveTypes->nextPageUrl(),
                'prev_page_url' => $leaveTypes->previousPageUrl(),
            ],
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token missing or invalid.',
            ], 401);
        }

        $leaveType = LeaveTypeModel::find($id);
        if (!$leaveType) {
            return response()->json([
                'status' => false,
                'message' => 'Leave type not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $leaveType,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token missing or invalid.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|string|max:255|unique:leave_type,leave_type',
            'number_of_leaves' => 'required|integer|min:0',
            'allow_half_day' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $leaveType = LeaveTypeModel::create([
            'leave_type' => trim((string) $request->input('leave_type')),
            'number_of_leaves' => (int) $request->input('number_of_leaves'),
            'allow_half_day' => (bool) $request->input('allow_half_day', false),
            'requires_approval' => (bool) $request->input('requires_approval', true),
            'created_by' => (int) $authUser->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave type added successfully.',
            'leave_type' => array_merge($leaveType->toArray(), ['leave_id' => $leaveType->id]),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token missing or invalid.',
            ], 401);
        }

        $leaveType = LeaveTypeModel::find($id);
        if (!$leaveType) {
            return response()->json([
                'status' => false,
                'message' => 'Leave type not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|string|max:255|unique:leave_type,leave_type,' . $id,
            'number_of_leaves' => 'required|integer|min:0',
            'allow_half_day' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $leaveType->update([
            'leave_type' => trim((string) $request->input('leave_type')),
            'number_of_leaves' => (int) $request->input('number_of_leaves'),
            'allow_half_day' => (bool) $request->input('allow_half_day', $leaveType->allow_half_day),
            'requires_approval' => (bool) $request->input('requires_approval', $leaveType->requires_approval),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave type updated successfully.',
            'data' => $leaveType,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $authUser = Auth::guard('api')->user();
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token missing or invalid.',
            ], 401);
        }

        $leaveType = LeaveTypeModel::find($id);
        if (!$leaveType) {
            return response()->json([
                'status' => false,
                'message' => 'Leave type not found.',
            ], 404);
        }

        $leaveType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave type deleted successfully.',
        ], 200);
    }
}
