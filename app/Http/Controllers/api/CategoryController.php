<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{


    public function getAllCategory(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userBranchId = $user->id;

        $selectedSubAdminId = $request->query('selectedSubAdminId') ?? $userBranchId;
        
        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $query = Category::where('isDeleted', 0);
        

        // if ($role === 'sub-admin') {
        //     // Sub-admin: show only their own branch's data
        //     $query->where('branch_id', $userBranchId);
        // } elseif ($role === 'admin' && !empty($selectedSubAdminId)) {
        //     // Admin with filter: show selected sub-admin's branch data
        //     $subAdmin = User::find($selectedSubAdminId);
        //     if ($subAdmin) {
        //         $query->where('branch_id', $subAdmin->id);
        //     }
        // }
        if ($role == 'staff') {

    // Staff sees categories of its main branch
    $query->where('branch_id', $user->branch_id);

} elseif ($role == 'sub-admin') {

    // Main branch
    $query->where('branch_id', $user->id);

} elseif ($role == 'admin' && !empty($selectedSubAdminId)) {

    // Selected branch
    $query->where('branch_id', $selectedSubAdminId);
}

         // ✅ Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        // Admin without filter sees all
        // $categories = $query->orderBy('id', 'desc')->get();
        // ✅ APPLY PAGINATION HERE
        $categories = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'status' => true,
            'data' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'next_page_url' => $categories->nextPageUrl(),
                'prev_page_url' => $categories->previousPageUrl(),
            ]
        ], 200);

        return response()->json(['status' => true, 'data' => $categories], 200);
    }

    public function addcategory_cat(Request $request)
    {
        // dd($request->all());
        $user = Auth::guard('api')->user();
        $role = $user->role;
        // $userBranchId = $user->id;
        // Staff should use parent branch
        $userBranchId = ($role == 'staff')
            ? $user->branch_id
            : $user->id;

        $validator = Validator::make($request->all(), [
            // 'name'  => 'required|string|max:255',
            'name'  => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp', // Ensure it's a single image file
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Store the image if it exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('img/category', 'public'); // Save in storage/app/public/img/category
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userBranchId = $request->sub_admin_id;
        }
        // Save category data
        $category = Category::create([
            'name'  => $request->name,
            'image' => $imagePath, // Store image path in DB
            'branch_id' => $userBranchId, // Store image path in DB
        ]);

        return response()->json(['status' => true, 'category' => $category], 200);
    }
    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        $productCount = Product::where('category_id', $id)
            ->where('isDeleted', 0) // Only consider active products
            ->count();

        if ($productCount > 0) {
            return response()->json([
                'status' => false,
                'message' => 'This category is associated with products and cannot be deleted.'
            ], 400); // 400 Bad Request
        }

        // Soft delete category
        $category->isDeleted = 1;
        $category->save();

        return response()->json(['status' => true, 'message' => 'Category deleted successfully'], 200);
    }

//    public function updatecategory(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'category_id' => 'required|numeric|exists:categories,id',
//             'name'  => 'required|string|max:255',
//             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
//         }

//         $category = Category::where('id', $request->category_id)
//             ->where('isDeleted', 0)
//             ->first();

//         if (!$category) {
//             return response()->json(['status' => false, 'message' => 'Category not found'], 404);
//         }

//         if ($request->hasFile('image')) {

//             if ($category->image && Storage::disk('public')->exists($category->image)) {
//                 Storage::disk('public')->delete($category->image);
//             }

//             $imagePath = $request->file('image')->store('img/category', 'public');

//         } else {
//             $imagePath = $category->image;
//         }

//         $category->update([
//             'name'  => $request->name,
//             'image' => $imagePath,
//         ]);

//         return response()->json(['status' => true, 'category' => $category], 200);
//     }
    public function updatecategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric|exists:categories,id',
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif', // Ensure it's a single image file
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Find category
        $category = Category::find($request->category_id);
        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('img/category', 'public');
        } else {
            $imagePath = $category->image; // Keep existing image
        }

        // Update category
        $category->update([
            'name'  => $request->name,
            'image' => $imagePath,
        ]);
        return response()->json(['status' => true, 'category' => $category], 200);
    }
}
