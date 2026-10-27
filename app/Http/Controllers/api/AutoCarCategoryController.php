<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AutoCarCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AutoCarCategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $selectedSubAdminId = $request->query('selectedSubAdminId');

        $query = AutoCarCategory::where('is_deleted', 0);
        // Role-based filtering
        if ($userole == 'sub-admin') {
             $query->where('branch_id', $userId);
        } elseif ($userole == 'admin' && !empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);
        } else {
            $query->where('branch_id', $userId);
        }
         // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Optional search (if needed)
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%"); // change column if needed
        }

        // Paginate
        $autoCarCategories = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $autoCarCategories->items(),
            'pagination' => [
                'current_page' => $autoCarCategories->currentPage(),
                'last_page' => $autoCarCategories->lastPage(),
                'per_page' => $autoCarCategories->perPage(),
                'total' => $autoCarCategories->total(),
                'next_page_url' => $autoCarCategories->nextPageUrl(),
                'prev_page_url' => $autoCarCategories->previousPageUrl(),
            ]
        ], 200);
        // return response()->json(['status' => true, 'data' => $autoCarCategories], 200);
    }

    public function addCategory(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:auto_car_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        $autoCarCategory = AutoCarCategory::create([
            'name' => $request->name,
            'branch_id' => $userId,
        ]);

        return response()->json(['status' => true, 'autoCarCategory' => $autoCarCategory], 200);
    }

    public function deleteCategory($id)
    {
        $autoCarCategory = AutoCarCategory::find($id);

        if (!$autoCarCategory) {
            return response()->json(['status' => false, 'message' => 'Service Category not found'], 404);
        }

        $autoCarCategory->is_deleted = 1;
        $autoCarCategory->save();

        return response()->json(['status' => true, 'message' => 'Service Category deleted successfully'], 200);
    }

    public function updateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric|exists:auto_car_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auto_car_categories')->ignore($request->category_id)->where(function ($query) {
                    return $query->where('is_deleted', 0);
                }),
            ],
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $autoCarCategory = AutoCarCategory::find($request->category_id);

        if (!$autoCarCategory) {
            return response()->json(['status' => false, 'message' => 'Service Category not found'], 404);
        }

        $autoCarCategory->update([
            'name' => $request->name,
        ]);

        return response()->json(['status' => true, 'autoCarCategory' => $autoCarCategory], 200);
    }
}
