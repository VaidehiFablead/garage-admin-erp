<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AutoCarModel;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AutoCarModelController extends Controller
{
    public function getAllModels(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userBranchId = $user->id;

        // Default selected sub-admin
        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userBranchId;

        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

         // Base query
        $query = AutoCarModel::where('is_deleted', 0);

        if ($role == 'sub-admin') {
           $query->where('branch_id', $userBranchId);
        } elseif ($role === 'admin' && !empty($selectedSubAdminId)) {
                   $subAdmin = User::find($selectedSubAdminId);
            if ($subAdmin) {
                $query->where('branch_id', $subAdmin->id);
            }
        } 
        // Search filter (adjust column if needed)
        if (!empty($search)) {
            $query->where('model_name', 'LIKE', "%{$search}%");
        }

        // Apply pagination
        $models = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $models->items(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'last_page' => $models->lastPage(),
                'per_page' => $models->perPage(),
                'total' => $models->total(),
                'next_page_url' => $models->nextPageUrl(),
                'prev_page_url' => $models->previousPageUrl(),
            ]
        ], 200);
    }

    public function addModel(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $validator = Validator::make($request->all(), [
            'model_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auto_car_models')->where(function ($query) use ($request) {
                    return $query->where('model_brand', $request->model_brand)
                        ->where('is_deleted', 0);
                }),
            ],
            'model_brand' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        $autoCarModel = AutoCarModel::create([
            'model_name' => $request->model_name,
            'model_brand' => $request->model_brand,
            'branch_id' => $userId,
        ]);

        return response()->json(['status' => true, 'autoCarModel' => $autoCarModel], 200);
    }

    public function deleteModel($id)
    {
        $autoCarModel = AutoCarModel::find($id);

        if (!$autoCarModel) {
            return response()->json(['status' => false, 'message' => ' Model not found'], 404);
        }

        $autoCarModel->is_deleted = 1;
        $autoCarModel->save();

        return response()->json(['status' => true, 'message' => ' Model deleted successfully'], 200);
    }

    public function updateModel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_id' => 'required|numeric|exists:auto_car_models,id',
            'model_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auto_car_models')
                    ->where(function ($query) use ($request) {
                        return $query->where('model_brand', $request->model_brand)
                            ->where('is_deleted', 0);
                    })
                    ->ignore($request->model_id),
            ],
            'model_brand' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $autoCarModel = AutoCarModel::find($request->model_id);

        if (!$autoCarModel) {
            return response()->json(['status' => false, 'message' => ' Model not found'], 404);
        }

        $autoCarModel->update([
            'model_name' => $request->model_name,
            'model_brand' => $request->model_brand,
        ]);

        return response()->json(['status' => true, 'autoCarModel' => $autoCarModel], 200);
    }
}
