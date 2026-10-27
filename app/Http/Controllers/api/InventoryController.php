<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\OrderItem;
use App\Models\Purchases;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;
        $userBranchId = $user->branch_id;

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');

        $categoryId = $request->query('category_id');
        $brandId = $request->query('brand_id');
        $subBranchId = $request->sub_branch_id ?? $userId;
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // ✅ OPTIMIZED: Select only needed fields and eager load relationships
        $query = Product::with([
            'category:id,name',
            'brand:id,name',
            'product_inventory:id,product_id,initial_stock,current_stock'
        ])
            ->select('id', 'name', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id')
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc');

        // ✅ Branch filter
        if ($userRole === 'admin') {
            if (!empty($subBranchId)) {
                $query->where('branch_id', $subBranchId);
            }
        } elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $userId);
        } elseif (strtolower($userRole) === 'staff') {
            $query->where('branch_id', $userBranchId);
        }

        // ✅ Category filter
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // ✅ Brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // ✅ Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . " 00:00:00",
                $endDate . " 23:59:59"
            ]);
        }

        // ✅ Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('SKU', 'like', '%' . $search . '%');
            });
        }

        $total = null;
        if ($applyPagination) {
            $total = (clone $query)->count();
        }

        if ($applyPagination) {
            $offset = ($page - 1) * $perPage;
            $products = $query->skip($offset)->take($perPage)->get();
        } else {
            $products = $query->get();
        }

        // ✅ OPTIMIZED: Use collection map for better performance
        $data = $products->map(function ($product) {
            $inventory = $product->product_inventory;
            return [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->SKU,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'current_stock' => $product->quantity,
                'price' => $product->price,
                'created_at' => $product->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $settingsBranchId = $userId;
        if ($userRole === 'admin' && !empty($subBranchId)) {
            $settingsBranchId = $subBranchId;
        } elseif ($userRole === 'sub-admin') {
            $settingsBranchId = $userId;
        } elseif (strtolower($userRole) === 'staff') {
            $settingsBranchId = $userBranchId ?? $userId;
        }

        $settings = DB::table('settings')->where('branch_id', $settingsBranchId)->first();

        $response = [
            'status' => true,
            'inventory' => $data,
            'lowStockThreshold' => $settings->low_stock ?? 0,
        ];

        if ($applyPagination) {
            $lastPage = $perPage > 0 ? (int) ceil(($total ?? 0) / $perPage) : 1;
            $response['pagination'] = [
                'current_page' => $page,
                'last_page' => max(1, $lastPage),
                'per_page' => $perPage,
                'total' => $total ?? 0,
            ];
        }

        return response()->json($response);
    }

    public function filter(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;
        $userBranchId = $user->branch_id;

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');

        $categoryId   = $request->query('category_id');
        $brandId      = $request->query('brand_id');
        $subBranchId  = $request->sub_branch_id ?? $userId;
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');

        // ✅ OPTIMIZED: Select only needed fields and eager load relationships
        $query = Product::with([
            'category:id,name',
            'brand:id,name',
            'product_inventory:id,product_id,initial_stock,current_stock'
        ])
            ->select('id', 'name', 'SKU', 'price', 'quantity', 'created_at', 'category_id', 'brand_id')
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc');

        // ✅ Branch filter
        if ($userRole === 'admin') {
            if (!empty($subBranchId)) {
                $query->where('branch_id', $subBranchId);
            }
        } elseif ($userRole === 'sub-admin') {
            $query->where('branch_id', $userId);
        } elseif (strtolower($userRole) === 'staff') {
            $query->where('branch_id', $userBranchId);
        }

        // ✅ Category filter
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // ✅ Brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        // ✅ Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                $startDate . " 00:00:00",
                $endDate . " 23:59:59"
            ]);
        }

        // ✅ Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('SKU', 'like', '%' . $search . '%');
            });
        }

        $total = null;
        if ($applyPagination) {
            $total = (clone $query)->count();
        }

        if ($applyPagination) {
            $offset = ($page - 1) * $perPage;
            $products = $query->skip($offset)->take($perPage)->get();
        } else {
            $products = $query->get();
        }

        // ✅ OPTIMIZED: Use collection map for better performance
        $data = $products->map(function ($product) {
            $inventory = $product->product_inventory;
            return [
                'id'            => $product->id,
                'product_name'  => $product->name,
                'sku'           => $product->SKU,
                'initial_stock' => $inventory->initial_stock ?? 0,
                'current_stock' => $product->quantity,
                'price'         => $product->price,
                'created_at'    => $product->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $settingsBranchId = $userId;
        if ($userRole === 'admin' && !empty($subBranchId)) {
            $settingsBranchId = $subBranchId;
        } elseif ($userRole === 'sub-admin') {
            $settingsBranchId = $userId;
        } elseif (strtolower($userRole) === 'staff') {
            $settingsBranchId = $userBranchId ?? $userId;
        }

        $settings = DB::table('settings')->where('branch_id', $settingsBranchId)->first();

        $response = [
            'status'    => true,
            'inventory' => $data,
            'lowStockThreshold' => $settings->low_stock ?? 0,
        ];

        if ($applyPagination) {
            $lastPage = $perPage > 0 ? (int) ceil(($total ?? 0) / $perPage) : 1;
            $response['pagination'] = [
                'current_page' => $page,
                'last_page' => max(1, $lastPage),
                'per_page' => $perPage,
                'total' => $total ?? 0,
            ];
        }

        return response()->json($response);
    }
    public function products_edit_inventroy(Request $request, $id)
    {
        // ✅ OPTIMIZED: Select only needed fields
        $product = Product::with('product_inventory:id,product_id,initial_stock,current_stock')
            ->select('id', 'name', 'SKU', 'price', 'quantity')
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'product' => [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->SKU,
                'price' => $product->price,
                'initial_stock' => $product->product_inventory->initial_stock ?? 0,
                'current_stock' => $product->quantity ?? 0,
            ]
        ]);
    }
    public function inventory_update(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        $userBranchId = $user->id ?? null;

        if (strtolower($user->role) === 'staff' && $user->branch_id) {
            $userBranchId = $user->branch_id;
        } elseif (!empty($request->selectedSubAdminId)) {
            $userBranchId = $request->selectedSubAdminId;
        } else {
            $userBranchId = $user->id;
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // ✅ Calculate new stock based on add/minus
        $currentStock = (int) $product->quantity; // take from DB (latest)
        $adjustment = (int) $request->stock_quantity;

        if ($request->stock_action === 'add') {
            $newStock = $currentStock + $adjustment;
        } elseif ($request->stock_action === 'minus') {
            $newStock = max(0, $currentStock - $adjustment); // prevent negative
        } else {
            $newStock = $currentStock; // fallback
        }

        // ✅ Update product table
        $product->update([
            'price' => $request->price,
            'quantity' => $newStock,
        ]);

        // ✅ Create inventory log
        ProductInventory::create([
            'product_id' => $id,
            'initial_stock' => $request->initial_stock,
            'current_stock' => $newStock,
            'branch_id' => $userBranchId,
            'type' => $request->stock_action === 'add' ? 'Stock Added' : 'Stock Minus',
            'create_by' => $user->id,
            'date' => now(),
            'quantity' => $adjustment, // optional: track how much was added/removed
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $newStock
        ]);
    }
    public function getQuantityHistory_inventory(Request $request, $productId)
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');
        // ✅ OPTIMIZED: Select only needed fields
        $product = Product::select('id', 'name')->find($productId);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ]);
        }

        // ✅ OPTIMIZED: Select only needed fields
        $query = ProductInventory::select(
            'id', 'product_id', 'type', 'initial_stock', 'current_stock',
            'create_by', 'branch_id', 'date', 'created_at'
        )
            ->where('product_id', $productId)
            ->orderBy('id', 'desc');

        $total = null;
        if ($applyPagination) {
            $total = (clone $query)->count();
            $offset = ($page - 1) * $perPage;
            $productInventories = $query->skip($offset)->take($perPage)->get();
        } else {
            $productInventories = $query->get();
        }

        // ✅ OPTIMIZED: Bulk load all users in ONE query instead of N+1
        $userIds = $productInventories->pluck('create_by')
            ->merge($productInventories->pluck('branch_id'))
            ->filter()
            ->unique()
            ->toArray();

        $users = !empty($userIds)
            ? User::select('id', 'name')
                ->whereIn('id', $userIds)
                ->get()
                ->keyBy('id')
            : collect();

        // ✅ OPTIMIZED: Use collection map for better performance
        $history = $productInventories->map(function ($inventory) use ($users) {
            $userId = $inventory->create_by ?? $inventory->branch_id;
            $user = $users->get($userId);

            $initial = $inventory->initial_stock ?? 0;
            $current = $inventory->current_stock ?? 0;
            $qty = (!empty($initial) && !empty($current)) ? abs($current - $initial) : 0;

            // Determine sign based on type
            $signedQty = match(strtolower($inventory->type ?? '')) {
                'sale' => "-" . $qty,
                'purchase', 'stock added' => "+" . $qty,
                default => ($current > $initial) ? "+" . $qty : "-" . $qty
            };

            return [
                'type'          => $inventory->type,
                'initial_stock' => $initial,
                'current_stock' => $current,
                'quantity'      => $signedQty,
                'name'          => $user->name ?? 'Unknown',
                'date'          => $inventory->date
                    ? \Carbon\Carbon::parse($inventory->date)->format('d-M-Y')
                    : ($inventory->created_at ? $inventory->created_at->format('d-M-Y') : 'N/A'),
            ];
        })->values()->toArray();

        $response = [
            'status' => true,
            'product' => $product->name,
            'history' => $history, // Already sorted by orderBy('id', 'desc') in query
        ];

        if ($applyPagination) {
            $lastPage = $perPage > 0 ? (int) ceil(($total ?? 0) / $perPage) : 1;
            $response['pagination'] = [
                'current_page' => $page,
                'last_page' => max(1, $lastPage),
                'per_page' => $perPage,
                'total' => $total ?? 0,
            ];
        }

        return response()->json($response);
    }

}
