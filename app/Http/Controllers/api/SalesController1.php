<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TaxRate;
use App\Models\Setting;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function getAllSales()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        // Build query
        $query = Order::with(['user:id,name,phone'])
            ->where('isDeleted', 0);

        // Filter if role is sub-admin - show only orders created by this sub-admin
        if ($role === 'sub-admin') {
            $query->where('created_by', $userId);
        }elseif($role === 'admin'){
            $query->where('created_by', $userId);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        if ($sales->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No sales found'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $sales,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
        ], 200);
    }


    // In your controller
    public function getProductsByCategory(Request $request, $categoryId)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id ?? null;
        $userRole = $user->role ?? null;

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $sub_admin_id = $request->input('sub_admin_id');

        if (!empty($sub_admin_id)) {
            // ✅ If sub_admin_id is given
            $products = Product::where('category_id', $categoryId)
                ->where('isDeleted', 0)
                ->where('branch_id', $sub_admin_id)
                ->get();
        } elseif (!empty($userId) && !empty($userRole == 'sub-admin')) {
            // ✅ Otherwise, use logged-in user's branch_id
            $products = Product::where('category_id', $categoryId)
                ->where('isDeleted', 0)
                ->where('branch_id', $userId)
                ->get();
        } else {
            // ✅ No branch filter
            $products = Product::where('category_id', $categoryId)
                ->where('isDeleted', 0)
                ->where('branch_id', $userId)
                ->get();
        }

        return response()->json([
            'status' => true,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'image' => $product->images,
                    'category_name' => $product->category->name,
                    'categoryId' => $product->category_id,
                ];
            })
        ]);
    }



    public function order_sale(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }
        $userId = $user->id;
        $role = $user->role;
        $selectedSubAdminId = $request->selectedSubAdminId;
        // Validate the request data
        $request->validate([
            'customer_id' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|array',
            'tax.*.tax_name' => 'nullable|string',
            'tax.*.rate' => 'nullable|numeric|min:0',
            'tax.*.amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.categoryId' => 'nullable|integer|exists:categories,id',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Check if all items have enough stock before proceeding
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->quantity < $item['quantity']) {
                    return response()->json([
                        'status' => false,
                        'message' => "Insufficient stock for product ID: {$item['product_id']}",
                    ], 400);
                }
            }

            // Generate unique order number
            $orderNumber = now()->format('Ymd') . str_pad((Order::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT);

            // Store active tax IDs as JSON (optional)
            $TaxRateIds = TaxRate::where('status', 'active')->pluck('id')->toArray();
            $taxJson = json_encode($TaxRateIds);

            // Create or find customer
            if (!is_numeric($request->customer_id) && !empty($request->customer_id)) {
                // Create new customer
                $vendor = User::create([
                    'name' => $request->customer_id,
                    'phone' => $request->customer_phone ?? null,
                    'role' => 'customer',
                    'branch_id' => $selectedSubAdminId ?? $userId,
                    'status' => 1,
                ]);
                $customer_id = $vendor->id;

                // Create UserDetail
                UserDetail::create([
                    'user_id' => $customer_id,
                    'address' => '',
                    'city' => '',
                    'country' => '',
                    // 'branch_id' => $selectedSubAdminId ?? $userId,
                ]);
            } elseif (is_numeric($request->customer_id)) {
                // Use existing customer
                $customer_id = $request->customer_id;
            } else {
                // Get default customer from users table (role = customer and name = 'Default Customer')
                $defaultCustomer = User::where('role', 'customer')
                    ->where('name', 'Default Customer')
                    ->first();

                if (!$defaultCustomer) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Default customer not found. Please create a user with name "Default Customer".'
                    ], 400);
                }

                $customer_id = $defaultCustomer->id;
            }

            if (!empty($selectedSubAdminId)) {
                $userId = $selectedSubAdminId;
            }
            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $customer_id,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
                'tax_id' => $taxJson,
                'total_amount' => $request->total,
                'branch_id' => $userId,
                'created_by' => $userId,
                'payment_status' => 'completed'
            ]);

            // Insert order items and reduce product stock
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $product->quantity -= $item['quantity'];
                $product->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $customer_id ?? null,
                    'category_id' => $item['categoryId'] ?? null,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_amount' => $item['total'],
                    'branch_id' => $userId,
                    'created_by' => $userId,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to place order. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_orders(Request $request, $selectedSubAdminId = null)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // Get selected sub-admin ID from route parameter or query parameter
        $selectedSubAdminId = $selectedSubAdminId ?: $request->query('selectedSubAdminId');

        $query = Order::with([
            'user:id,name,phone',
            'biller:id,name',
            'orderItems:id,order_id,created_at'
        ])->where('isDeleted', 0);

        if ($role === 'sub-admin') {
            $query->where('created_by', $userId);
        } elseif ($role === 'admin' && $selectedSubAdminId) {
            $query->where('created_by', $selectedSubAdminId);
        }else{
            $query->where('created_by', $userId);
        }

        $get_orders = $query->orderBy('created_at', 'desc')->get();

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully',
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data' => $get_orders,
        ]);
    }


    public function get_salse_detail(Request $request) {}

    public function getsalseById($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        $sales = Order::with(['order_items.product', 'user'])->find($id);
        $companyInfo = Setting::first();

        if (!$sales) {
            return response()->json(['status' => false, 'error' => 'Sale not found'], 404);
        }

        // Check if sub-admin is trying to access an order they didn't create
        if ($role === 'sub-admin' && $sales->created_by !== $userId) {
            return response()->json([
                'status' => false,
                'error' => 'You can only view orders that you created.',
            ], 403);
        }

        // Fetch order items with product & include date
        $orderItems = OrderItem::where('order_id', $id)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'date' => $item->created_at, // or your own date column
                    // Add more fields as needed
                ];
            });

        return response()->json([
            'status' => true,
            'sales' => [
                ...$sales->toArray(),
                'user_name' => $sales->user->name ?? 'walk-in-customer',
                'user_phone' => $sales->user->phone ?? 'N/A',
            ],
            'order_items' => $orderItems,
            'company_info' => $companyInfo,
            'currency_symbol' => $companyInfo->currency_symbol ?? '₹',
            'currency_position' => $companyInfo->currency_position ?? 'left',
        ], 200);
    }

    public function delete($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // Find the order
        $order = Order::find($id);

        // If order not found, return error
        if (!$order) {
            return response()->json([
                'status' => false,
                'error' => 'Order not found',
            ], 404);
        }

        // Check if sub-admin is trying to delete an order they didn't create
        if ($role === 'sub-admin' && $order->created_by !== $userId) {
            return response()->json([
                'status' => false,
                'error' => 'You can only delete orders that you created.',
            ], 403);
        }

        // ❌ Prevent deletion if payment is pending
        if ($order->payment_status === 'pending') {
            return response()->json([
                'status' => false,
                'error' => 'This order cannot be deleted because its payment status is pending.',
            ], 400);
        }
        // Soft delete: set isDeleted = 1
        $order->isDeleted = 1;
        $order->save();

        // Soft delete related order items
        OrderItem::where('order_id', $order->id)->update(['isDeleted' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }
    public function update_sale(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        $validator = Validator::make($request->all(), [
            'update_id' => 'required|exists:orders,id',
            'customer_id' => 'nullable',
            'customer_phone' => 'nullable|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:0',
            'discount' => 'numeric|min:0|max:100',
            'payment_method' => 'required|in:cash,debit,scan',
            'status' => 'required|in:completed,pending,failed',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->update_id);

            // Check if sub-admin is trying to update an order they didn't create
            if ($role === 'sub-admin' && $order->created_by !== $userId) {
                return response()->json([
                    'status' => false,
                    'error' => 'You can only update orders that you created.',
                ], 403);
            }
            $products = Product::whereIn('id', $request->product_ids)->get();

            // Fetch old order items
            $oldItems = OrderItem::where('order_id', $order->id)->get();
            $oldQuantities = $oldItems->pluck('quantity', 'product_id')->toArray();

            $subtotal = 0;
            foreach ($request->product_ids as $productId) {
                $product = $products->firstWhere('id', $productId);
                if ($product) {
                    $quantity = $request->quantities[$productId] ?? 0;
                    $oldQty = $oldQuantities[$productId] ?? 0;

                    $difference = $quantity - $oldQty;

                    // Check if stock is available for increment
                    if ($difference > 0 && $product->quantity < $difference) {
                        // Abort early with error
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stock Quantity Exceeded. Only {$product->quantity} quantity are available for '{$product->name}'."
                        ], 422);
                    }

                    // Stock adjustment
                    if ($difference > 0) {
                        $product->decrement('quantity', $difference);
                    } elseif ($difference < 0) {
                        $product->increment('quantity', abs($difference));
                    }

                    $subtotal += $product->price * $quantity;
                }
            }

            // Tax & discount calculations
            $discountAmount = $subtotal * ($request->discount / 100);
            $taxableAmount = $subtotal - $discountAmount;

            $taxRates = TaxRate::where('status', 'active')->get();
            $taxIds = $taxRates->pluck('id')->toArray();
            $taxJson = json_encode($taxIds);

            $totalTax = 0;
            foreach ($taxRates as $tax) {
                $totalTax += $taxableAmount * ($tax->tax_rate / 100);
            }

            // Update Order
            // $order->update([
            //     'user_name' => $request->customer_id,
            //     'user_phone' => $request->customer_phone,
            //     'payment_method' => $request->payment_method,
            //     'discount' => $request->discount,
            //     'discount_amount' => $discountAmount,
            //     'tax_ids' => $taxJson,
            //     'tax_amount' => $totalTax,
            //     'subtotal' => $subtotal,
            //     'total_amount' => $request->grand_total,
            //     'payment_status' => $request->status,
            //     'created_by'      => $userId,
            //     'branch_id'       => $userId, // ✅ If user is tied to a branch
            // ]);
            // Prepare update data
            $updateData = [
                'user_name'       => $request->customer_id,
                'user_phone'      => $request->customer_phone,
                'payment_method'  => $request->payment_method,
                'discount'        => $request->discount,
                'discount_amount' => $discountAmount,
                'tax_ids'         => $taxJson,
                'tax_amount'      => $totalTax,
                'subtotal'        => $subtotal,
                'total_amount'    => $request->grand_total,
                'payment_status'  => $request->status,
            ];

            // Only allow sub-admins to update `created_by` and `branch_id`
            if ($role === 'sub-admin') {
                $updateData['created_by'] = $userId;
                $updateData['branch_id'] = $userId;
            }

            // Perform update
            $order->update($updateData);

            // Remove old order items
            OrderItem::where('order_id', $order->id)->delete();

            // Reinsert updated order items
            foreach ($request->product_ids as $productId) {
                $product = $products->firstWhere('id', $productId);
                if ($product) {
                    $quantity = $request->quantities[$productId] ?? 0;
                    $price = $product->price;
                    $total = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id ?? null,
                        'category_id' => $product->category_id ?? null,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_amount' => $total,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function orderReport(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // Get selected sub-admin ID from request
        $selectedSubAdminId = $request->query('selectedSubAdminId');

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $filter = $request->query('filter');

        $orderItems = OrderItem::with(['product.category', 'product.brand', 'order'])
            ->whereHas('order', function ($query) use ($filter, $role, $userId, $selectedSubAdminId) {
                $query->where('payment_status', 'completed')
                    ->where('isDeleted', 0);

                // Filter based on role and selected sub-admin
                if ($role === 'sub-admin') {
                    // Sub-admin can only see their own orders
                    $query->where('created_by', $userId);
                } elseif ($role === 'admin' && $selectedSubAdminId) {
                    // Admin can filter by specific sub-admin if selected
                    $query->where('created_by', $selectedSubAdminId);
                }else{
                    $query->where('created_by', $userId);
                }
                // If admin and no sub-admin selected, show all orders

                if ($filter) {
                    $today = Carbon::today();
                    switch ($filter) {
                        case 'this_week':
                            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $query->whereMonth('created_at', $today->month)
                                ->whereYear('created_at', $today->year);
                            break;
                        case 'last_6_months':
                            $query->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()]);
                            break;
                        case 'this_year':
                            $query->whereYear('created_at', $today->year);
                            break;
                        case 'previous_year':
                            $query->whereYear('created_at', $today->subYear()->year);
                            break;
                    }
                }
            })
            ->select('id', 'product_id', 'quantity', 'price', 'order_id')
            ->get()
            ->map(function ($orderItem) use ($currencySymbol, $currencyPosition) {
                $product = $orderItem->product;

                $soldQty = $orderItem->quantity;
                $soldAmount = $orderItem->price * $soldQty;
                $created_at = optional($orderItem->order)->created_at;

                $imageList = json_decode($product->images);
                $imagePath = $imageList[0] ?? null;
                $imageUrl = $imagePath ? 'storage/' . $imagePath : 'admin/assets/img/product/noimage.png';

                $formattedAmount = number_format($soldAmount, 2);
                $soldAmountFormatted = $currencyPosition === 'left'
                    ? $currencySymbol . $formattedAmount
                    : $formattedAmount . $currencySymbol;

                return [
                    'id' => $orderItem->id,
                    'product_id' => $product->id,         // <<<<< add this
                    'name' => $product->name,
                    'SKU' => $product->SKU,
                    'category' => $product->category->name ?? 'N/A',
                    'brand' => $product->brand->name ?? 'N/D',
                    'sold_amount' => $soldAmountFormatted,
                    'sold_qty' => $soldQty,
                    'instock_qty' => $product->quantity,
                    'image' => $imageUrl,
                    'created_at' => $created_at
                ];
            });

        return response()->json($orderItems);
    }
    public function getFilteredOrders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access',
            ], 401);
        }

        $userId = $user->id;
        $role = $user->role;

        // Get selected sub-admin ID from request
        $selectedSubAdminId = $request->query('selectedSubAdminId');

        // Get currency settings
        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        // dd($request->date);
        try {
            // Build query
            $query = Order::with([
                'user:id,name,phone',
                'biller:id,name',
                'orderItems:id,order_id,created_at'
            ])->where('isDeleted', 0);

            // Filter by date if provided
            if ($request->has('date') && !empty($request->date)) {
                $query->whereDate('created_at', $request->date);
            }

            if ($role === 'sub-admin') {
                $query->where('created_by', $userId);
            } elseif ($role === 'admin' && $selectedSubAdminId) {
                $query->where('created_by', $selectedSubAdminId);
            }else{
                 $query->where('created_by', $userId);
            }
            $orders = $query->orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => true,
                'data' => $orders,
                'currency_symbol' => $currencySymbol,
                'currency_position' => $currencyPosition,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'currency_symbol' => $currencySymbol,
                'currency_position' => $currencyPosition,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
