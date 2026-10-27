<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\LabourService;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LabourServiceController extends Controller
{
    // public function getAllServices(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     $userId = $user->id;
    //     $userole = $user->role;
    //     $selectedSubAdminId = $request->query('selectedSubAdminId');
    //     if ($userole == 'sub-admin' && $userId) {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     } elseif (!empty($selectedSubAdminId) && !empty($userole == 'admin')) {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $selectedSubAdminId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     } else {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     }
    //     $setting = Setting::first(); // Get currency info
    //     $currencySymbol = $setting->currency_symbol ?? '₹';
    //     $currencyPosition = $setting->currency_position ?? 'left'; // 'left' or 'right'

    //     return response()->json([
    //         'status' => true,
    //         'data' => $labourServices,
    //         'currency_symbol' => $currencySymbol,
    //         'currency_position' => $currencyPosition,
    //     ], 200);
    // }
    public function getAllServices(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;

        $selectedSubAdminId = $request->query('selectedSubAdminId');

        // Start query
        $query = LabourService::where('is_deleted', 0);

        // Apply role conditions
        if ($userRole == 'sub-admin') {
            $query->where('branch_id', $userId);

        } elseif ($userRole == 'admin' && !empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);

        } else {
            $query->where('branch_id', $userId);
        }

        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        // Optional search
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%"); // change column if needed
        }

        // Paginate instead of get()
        $labourServices = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Currency settings
        $setting = Setting::first();
        $currencySymbol = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => $labourServices->items(),
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'pagination' => [
                'current_page' => $labourServices->currentPage(),
                'last_page' => $labourServices->lastPage(),
                'per_page' => $labourServices->perPage(),
                'total' => $labourServices->total(),
                'next_page_url' => $labourServices->nextPageUrl(),
                'prev_page_url' => $labourServices->previousPageUrl(),
            ]
        ], 200);
    }

    public function addService(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $validator = Validator::make($request->all(), [
            'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('labour_services')->where(function ($query) use ($userId, $request) {
                $branchId = (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') ? $request->sub_admin_id : $userId;
                    return $query->where('branch_id', $branchId);
                })
            ],
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        $labourService = LabourService::create([
            'name' => $request->name,
            'branch_id' => $userId,
            'price' => $request->price,
        ]);

        return response()->json(['status' => true, 'labourService' => $labourService], 200);
    }

    public function deleteService($id)
    {
        $labourService = LabourService::find($id);

        if (!$labourService) {
            return response()->json(['status' => false, 'message' => 'Labour Service not found'], 404);
        }

        $labourService->is_deleted = 1;
        $labourService->save();

        return response()->json(['status' => true, 'message' => 'Labour Service deleted successfully'], 200);
    }

    public function updateService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|numeric|exists:labour_services,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('labour_services')
                    ->ignore($request->service_id)
                    ->where(function ($query) {
                        return $query->where('is_deleted', 0);
                    }),
            ],
            'price' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $labourService = LabourService::find($request->service_id);

        if (!$labourService) {
            return response()->json(['status' => false, 'message' => 'Labour Service not found'], 404);
        }

        $labourService->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json(['status' => true, 'labourService' => $labourService], 200);
    }

    public function getService($id)
    {
        try {
            $service = LabourService::where('id', $id)
                ->where('is_deleted', 0)
                ->first();

            if (!$service) {
                return response()->json([
                    'status' => false,
                    'message' => 'Labour Service not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
