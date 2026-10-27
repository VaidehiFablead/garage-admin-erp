<?php

namespace App\Http\Controllers\api;

use App\Models\TaxRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function getSaleDetails($invoiceNumber)
    {
        $order = Order::where('order_number', $invoiceNumber)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Fetch user manually
        $user = User::find($order->user_id);

        // Decode JSON array of tax IDs
        $taxIds = json_decode($order->tax_id ?? '[]', true);

        // Fetch multiple tax records
        $taxes = TaxRate::whereIn('id', $taxIds)->get();

        // Fetch order items and related product info
        $items = OrderItem::where('order_id', $order->id)->get()->map(function ($item) {
            $product = Product::find($item->product_id);
            $images = json_decode($product->images ?? '[]', true);
            return [
                'id' => $item->id,
                'product_id' => $item->product_id, // ✅ Add this
                'product_name' => $product->name ?? 'N/A',
                'product_image' => $images[0] ?? null,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        });

        $orderData = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'user_name' => $user->name ?? 'N/A',
            'user_phone' => $user->phone ?? '',
            'discount' => $order->discount ?? 0,
        ];

        $settings = \DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'order' => $orderData,
            'items' => $items,
            'taxes' => $taxes,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition
        ]);
    }


    public function return_sale(Request $request)
    {

        $user = Auth::guard('api')->user();
        $role = $user->role;
        $userId = $user->id;
        // dd($userBranchId);
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.order_item_id' => 'required|exists:order_items,id',
            'products.*.quantity' => 'required|numeric|min:0',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
        if (!empty($request->selectedSubAdminId))
            $userId = $request->selectedSubAdminId ?? $userId;
        try {
            DB::beginTransaction();

            $productIds = [];
            $quantities = [];
            $productMap = [];
            $order_id = null;

            // Collect product IDs, quantities, and map for re-use
            foreach ($request->products as $item) {
                $orderItem = OrderItem::findOrFail($item['order_item_id']);
                $productIds[] = $orderItem->product_id;
                $productMap[$item['order_item_id']] = $orderItem->product_id;
                $quantities[$orderItem->product_id] = $item['quantity'];
                $order_id = $orderItem->order_id; // all items belong to same order
            }

            $products = Product::whereIn('id', $productIds)->get();

            // Get old quantities
            $oldItems = OrderItem::where('order_id', $order_id)->get();
            $oldQuantities = $oldItems->pluck('quantity', 'product_id')->toArray();

            // Stock adjustment + subtotal
            $subtotal = 0;
            foreach ($products as $product) {
                $productId = $product->id;
                $newQty = $quantities[$productId] ?? 0;
                $oldQty = $oldQuantities[$productId] ?? 0;

                $subtotal += $product->price * $newQty;

                $difference = $newQty - $oldQty;
                if ($difference > 0) {
                    $product->decrement('quantity', $difference);
                } elseif ($difference < 0) {
                    $product->increment('quantity', abs($difference));
                }
            }

            // Discount and tax calculation
            $discount = $request->discount ?? 0;
            $discountAmount = $subtotal * ($discount / 100);
            $taxableAmount = $subtotal - $discountAmount;

            $taxRates = TaxRate::where('status', 'active')->get();
            $taxIds = $taxRates->pluck('id')->toArray();
            $taxJson = json_encode($taxIds);

            $totalTax = 0;
            foreach ($taxRates as $tax) {
                $totalTax += $taxableAmount * ($tax->tax_rate / 100);
            }

            $order = Order::findOrFail($order_id);

            // Update order without payment_status
            $order->update([
                'discount' => $discount,
                'discount_amount' => $discountAmount,
                'tax_ids' => $taxJson,
                'tax_amount' => $totalTax,
                'subtotal' => $subtotal,
                'total_amount' => $request->grand_total,
            ]);

            // Replace order items
            OrderItem::where('order_id', $order->id)->delete();

            foreach ($request->products as $item) {
                $productId = $productMap[$item['order_item_id']] ?? null;
                $product = $products->firstWhere('id', $productId);
                if (!$product)
                    continue;

                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id ?? null,
                    'category_id' => $product->category_id ?? null,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_amount' => $item['subtotal'],
                    'branch_id' => $userId
                ]);
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
}
