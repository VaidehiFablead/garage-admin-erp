<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;
use App\Models\User;
use App\Models\Product;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function getAllBrand(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userBranchId;

        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $query = Brand::where('isDeleted', 0);

        if ($role === 'sub-admin') {
            // Sub-admin: show only their own branch's data
            $query->where('branch_id', $userBranchId);
        } elseif ($role === 'admin' && !empty($selectedSubAdminId)) {
            // Admin with filter: show selected sub-admin's branch data
            $subAdmin = User::find($selectedSubAdminId);
            if ($subAdmin) {
                $query->where('branch_id', $subAdmin->id);
            }
        }
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        // $brand = $query->orderBy('id', 'desc')->get();
        // ✅ APPLY PAGINATION HERE
        $brand = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $brand->items(),
            'pagination' => [
                'current_page' => $brand->currentPage(),
                'last_page' => $brand->lastPage(),
                'per_page' => $brand->perPage(),
                'total' => $brand->total(),
                'next_page_url' => $brand->nextPageUrl(),
                'prev_page_url' => $brand->previousPageUrl(),
            ]
        ], 200);
        return response()->json(['status' => true, 'data' => $brand], 200);
    }
    
    public function addBrand(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userBranchId = $user->id ?? null;
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('logo')) {
            $imagePath = $request->file('logo')->store('brand', 'public');
        }

        // Create Brand
        // dd($imagePath);
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userBranchId = $request->sub_admin_id;
        }
        $brand = Brand::create([
            'name'  => $request->name,
            'logo'  => $imagePath,
            'branch_id' => $userBranchId, // Use branch_id from authenticated user
        ]);

        return response()->json(['status' => true, 'brand' => $brand], 200);
    }

    public function updateBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|numeric|exists:brands,id',
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif', // Ensure it's a single image file
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Find brand
        $brand = Brand::find($request->brand_id);
        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Brand not found'], 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            // Store new image
            $imagePath = $request->file('image')->store('img/brand', 'public');
        } else {
            $imagePath = $brand->logo; // Keep existing image
        }

        // Update brand
        $brand->update([
            'name'  => $request->name,
            'logo' => $imagePath,
        ]);
        return response()->json(['status' => true, 'category' => $brand], 200);
    }

    public function deleteBrand($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Brand not found'], 404);
        }

        // Check if any product is using this brand
        $isUsedInProducts = Product::where('brand_id', $id)->where('isDeleted', 0)->exists();

        if ($isUsedInProducts) {
            return response()->json([
                'status' => false,
                'message' => 'This brand is associated with products and cannot be deleted.'
            ], 409); // 409 Conflict
        }

        // Soft delete: set isDeleted = 1
        $brand->isDeleted = 1;
        $brand->save();

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully.'
        ], 200);
    }
}
