<?php

namespace App\Http\Controllers\api;

use App\Models\TaxRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchases;
use App\Models\CustomInvoice;
use App\Models\CustomInvoiceItem;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomInvoiceController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $userId = $user->id;
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable',
            'vendor_id' => 'nullable',
            'status' => 'required',
            'discount' => 'nullable|numeric',
            'shipping' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required',
            'products.*.category_id' => 'required',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.total' => 'required|numeric|min:0',
            'taxes' => 'nullable|array',
            'taxes.*.id' => 'required|numeric',
            'taxes.*.name' => 'required|string',
            'taxes.*.rate' => 'required|numeric',
            'taxes.*.amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ✅ Generate unique invoice number
            do {
                $invoice_number = 'INV-' . mt_rand(10000000, 99999999);
            } while (CustomInvoice::where('invoice_number', $invoice_number)->exists());
            if (!empty($request->selectedSubAdminId)) {
                $userId = $request->selectedSubAdminId;
            }
            $vendor_id = null;
            $customer_id = null;

            // ✅ Check vendor
            if (!empty($request->vendor_id)) {
                if (!is_numeric($request->vendor_id)) {
                    $existingVendor = User::where('id', $request->vendor_id)->first();
                    if ($existingVendor) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A vendor with this phone number already exists.',
                        ], 409);
                    }
                    $vendor = User::create([
                        'name' => $request->vendor_id,
                        'phone' => null,
                        'role' => 'vendor',
                        'status' => $request->status,
                    ]);
                    $vendor_id = $vendor->id;

                    UserDetail::create([
                        'user_id' => $vendor_id,
                        'address' => '',
                        'city' => '',
                        'state' => '',
                        'country' => '',
                        'pincode' => '',
                        'branch_id' => $userId,
                    ]);
                } else {
                    $vendor_id = $request->vendor_id;
                }
            }

            // ✅ Check customer
            if (!empty($request->customer_id)) {
                if (!is_numeric($request->customer_id)) {
                    $existingCustomer = User::where('id', $request->customer_id)->first();
                    if ($existingCustomer) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A vendor with this phone number already exists.', // Adjust message if needed
                        ], 409);
                    }
                    $customer = User::create([
                        'name' => $request->customer_id,
                        'phone' => null,
                        'role' => 'customer', // Change to 'customer' if needed
                        'status' => $request->status,
                        'branch_id' => $userId,
                    ]);
                    $customer_id = $customer->id;

                    UserDetail::create([
                        'user_id' => $customer_id,
                        'address' => '',
                        'city' => '',
                        'state' => '',
                        'country' => '',
                        'pincode' => '',
                        'branch_id' => $userId,

                    ]);
                } else {
                    $customer_id = $request->customer_id;
                }
            }



            // ✅ Process Products
            $processedProducts = [];
            foreach ($request->products as $product) {
                if (!is_numeric($product['category_id'])) {
                    $category = Category::create(['name' => $product['category_id']]);
                    $category_id = $category->id;
                } else {
                    $category_id = $product['category_id'];
                }

                if (!is_numeric($product['id'])) {
                    do {
                        $sku = mt_rand(10000000, 99999999);
                    } while (Product::where('SKU', $sku)->exists());

                    $newProduct = Product::create([
                        'name' => $product['id'],
                        'category_id' => $category_id,
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                        'vendor_id' => $vendor_id ?? $customer_id,
                        'availablility' => 'in_stock',
                        'status' => 'active',
                        'SKU' => $sku,
                        'branch_id' => $userId,
                    ]);
                    $product_id = $newProduct->id;
                } else {
                    $existingProduct = Product::find($product['id']);
                    if ($existingProduct) {
                        $existingProduct->update([
                            'price' => $product['price'],
                            // 'quantity' => $existingProduct->quantity + $product['quantity'],
                            'category_id' => $category_id,
                        ]);
                        $product_id = $existingProduct->id;
                    }
                }

                $processedProducts[] = [
                    'product_id' => $product_id,
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'total' => $product['total'],
                ];
            }

            // ✅ Store CustomInvoice
            $customInvoice = CustomInvoice::create([
                'invoice_number' => $invoice_number,
                'vendor_id' => $vendor_id ?? null,
                'customer_id' => $customer_id ?? null,
                'products' => json_encode($processedProducts),
                'total_amount' => collect($processedProducts)->sum('total'),
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping,
                'grand_total' => $request->grand_total,
                'status' => $request->status,
                'taxes' => json_encode($request->taxes),
                'branch_id' => $userId,
            ]);



            // ✅ Store CustomInvoiceItems
            foreach ($processedProducts as $item) {
                CustomInvoiceItem::create([
                    'invoice_id' => $customInvoice->id,
                    'item' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount_total' => $item['total'],
                    'vendor_id' => $vendor_id,
                    'customer_id' => $customer_id,
                    'purchase_status' => $request->status,
                    'payment_status' => 'unpaid',
                ]);
            }


            DB::commit();

            // $user = null;
            // if ($customInvoice->vendor_id) {
            //     $user = User::find($customInvoice->vendor_id);
            // } elseif ($customInvoice->customer_id) {

            //     $user = User::find($customInvoice->customer_id);
            // }

            // // ✅ Send email if user exists and has email set
            // if ($user && !empty($user->email)) {

            //     \Mail::to($user->email)->send(new \App\Mail\CustomInvoiceCreated($customInvoice, $user));
            // }

            return response()->json([
                'success' => true,
                'message' => 'Custom invoice and invoice items created successfully!',
                'invoice_number' => $invoice_number,
                'invoice_id' => $customInvoice->id, // Add this line
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $user = Auth::guard('api')->user();

        $query = DB::table('custom_invoice')
            ->join('custom_invoice_item', function ($join) {
                $join->on('custom_invoice.id', '=', 'custom_invoice_item.invoice_id')
                    ->where('custom_invoice_item.isDeleted', '=', 0);
            })
            ->leftJoin('users as vendors', 'custom_invoice_item.vendor_id', '=', 'vendors.id')
            ->leftJoin('users as customers', 'custom_invoice_item.customer_id', '=', 'customers.id')
            ->join('products', 'custom_invoice_item.item', '=', 'products.id')
            ->select(
                'custom_invoice.id',
                DB::raw("COALESCE(vendors.name, customers.name) as vendor_name"),
                'custom_invoice.invoice_number',
                'custom_invoice.grand_total',
                'custom_invoice_item.purchase_status',
                'custom_invoice_item.payment_status',
                'custom_invoice_item.created_at as date',
                DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as product_names"),
                DB::raw("GROUP_CONCAT(custom_invoice_item.price SEPARATOR ', ') as product_prices"),
                DB::raw("GROUP_CONCAT(custom_invoice_item.quantity SEPARATOR ', ') as product_quantities")
            )
            ->where('custom_invoice.isDeleted', '=', 0);

        if ($user->role === 'admin' && $request->selectedSubAdminId) {
            if ($request->filled('selectedSubAdminId')) {
                $subAdmin = DB::table('users')
                    ->where('id', $request->selectedSubAdminId)
                    ->where('role', 'sub-admin')
                    ->first();

                if ($subAdmin) {
                    $query->where('custom_invoice.branch_id', $subAdmin->id);
                } else {
                    // If invalid sub-admin id given, return empty result
                    return response()->json([
                        'success' => true,
                        'currency_symbol' => '₹', // default or fetch settings if needed
                        'currency_position' => 'left',
                        'data' => []
                    ]);
                }
            } else {
                // No sub-admin selected: return empty or all data? 
                // If you want empty:
                return response()->json([
                    'success' => true,
                    'currency_symbol' => '₹',
                    'currency_position' => 'left',
                    'data' => []
                ]);

                // Or if you want all data for admin when no selectedSubAdminId, comment above and uncomment below:
                // do nothing here (i.e., no branch_id filter)
            }
        } else if ($user->role === 'sub-admin') {
            $query->where('custom_invoice.branch_id', $user->id);
        } else if ($user->role === 'admin') {
            // For other roles, restrict to their own branch too
            $query->where('custom_invoice.branch_id', $user->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('custom_invoice_item.created_at', $request->date);
        }

        $purchases = $query
            ->groupBy(
                'custom_invoice.id',
                'vendors.name',
                'customers.name',
                'custom_invoice.invoice_number',
                'custom_invoice.grand_total',
                'custom_invoice_item.purchase_status',
                'custom_invoice_item.payment_status',
                'custom_invoice_item.created_at'
            )
            ->orderBy('custom_invoice_item.created_at', 'desc')
            ->get();

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'success' => true,
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data' => $purchases
        ]);
    }





    public function delete(Request $request)
    {
        try {
            DB::beginTransaction(); // Start transaction

            // Find the custom invoice
            $invoice = CustomInvoice::find($request->id);
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
            }

            // Check if all related custom_invoice_items have status 'completed'
            $nonCompletedCount = CustomInvoiceItem::where('invoice_id', $request->id)
                ->where('purchase_status', '!=', 'completed')
                ->where('isDeleted', 0)
                ->count();

            if ($nonCompletedCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete. These purchases are not completed.'
                ]);
            }

            // Soft delete related items
            CustomInvoiceItem::where('invoice_id', $request->id)
                ->where('purchase_status', 'completed')
                ->update(['isDeleted' => 1]);

            // Soft delete the invoice
            $invoice->isDeleted = 1;
            $invoice->save();

            DB::commit(); // Commit transaction

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get($id)
    {
        try {
            $invoiceData = DB::table('custom_invoice_item')
                ->join('products', 'custom_invoice_item.item', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('custom_invoice', 'custom_invoice_item.invoice_id', '=', 'custom_invoice.id')
                ->leftJoin('users as vendors', 'custom_invoice.vendor_id', '=', 'vendors.id')
                ->leftJoin('users as customers', 'custom_invoice.customer_id', '=', 'customers.id')
                ->where('custom_invoice.id', $id)
                ->select(
                    'custom_invoice.vendor_id',
                    'custom_invoice.customer_id',
                    'custom_invoice.status',
                    'custom_invoice.shipping',
                    'custom_invoice.taxes',
                    'custom_invoice.discount',
                    'custom_invoice.paid',
                    'vendors.phone as vendor_phone',
                    'vendors.name as vendor_name',
                    'customers.phone as customer_phone',
                    'customers.name as customer_name',

                    // ✅ Only 1 status based on presence of unpaid
                    DB::raw("IF(SUM(custom_invoice_item.payment_status = 'unpaid') > 0, 'unpaid', 'paid') as payment_status"),

                    DB::raw("GROUP_CONCAT(products.id ORDER BY products.id SEPARATOR ', ') as product_ids"),
                    DB::raw("GROUP_CONCAT(products.name ORDER BY products.id SEPARATOR ', ') as product_names"),
                    DB::raw("GROUP_CONCAT(products.price ORDER BY products.id SEPARATOR ', ') as product_prices"),
                    DB::raw("GROUP_CONCAT(custom_invoice_item.quantity ORDER BY products.id SEPARATOR ', ') as product_quantities"),
                    DB::raw("GROUP_CONCAT(categories.id ORDER BY products.id SEPARATOR ', ') as category_ids"),
                    DB::raw("GROUP_CONCAT(categories.name ORDER BY products.id SEPARATOR ', ') as category_names"),
                    DB::raw("SUM(custom_invoice_item.price * custom_invoice_item.quantity) as total_amount"),
                    DB::raw("(SUM(custom_invoice_item.price * custom_invoice_item.quantity) + custom_invoice.shipping - custom_invoice.discount) as grand_total")
                )
                ->groupBy(
                    'custom_invoice.vendor_id',
                    'custom_invoice.customer_id',
                    'custom_invoice.status',
                    'custom_invoice.shipping',
                    'custom_invoice.taxes',
                    'custom_invoice.discount',
                    'custom_invoice.paid',
                    'vendors.phone',
                    'vendors.name',
                    'customers.phone',
                    'customers.name'
                )
                ->first();

            if (!$invoiceData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found',
                ], 404);
            }

            // Decode taxes from JSON format
            $taxes = json_decode($invoiceData->taxes, true) ?? [];

            return response()->json([
                'success' => true,
                'data' => $invoiceData,
                'taxes' => $taxes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable',
            'vendor_id' => 'nullable',
            'status' => 'required',
            'payment_status' => 'required',
            'discount' => 'nullable|numeric',
            'shipping' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required',
            'products.*.category_id' => 'required',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.total' => 'required|numeric|min:0',
            'taxes' => 'nullable|array',
            'taxes.*.id' => 'required|numeric',
            'taxes.*.name' => 'required|string',
            'taxes.*.rate' => 'required|numeric',
            'taxes.*.amount' => 'required|numeric',
        ]);



        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customInvoice = CustomInvoice::find($id);
            if (!$customInvoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found.',
                ], 404);
            }

            $vendor_id = null;
            $customer_id = null;

            // ✅ Vendor Handling
            if (!empty($request->vendor_id)) {
                if (!is_numeric($request->vendor_id)) {
                    $existingVendor = User::where('id', $request->vendor_id)->first();
                    if ($existingVendor) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A vendor with this phone number already exists.',
                        ], 409);
                    }
                    $vendor = User::create([
                        'name' => $request->vendor_id,
                        'phone' => null,
                        'role' => 'vendor',
                        'status' => $request->status,
                    ]);
                    $vendor_id = $vendor->id;

                    UserDetail::create([
                        'user_id' => $vendor_id,
                        'address' => '',
                        'city' => '',
                        'state' => '',
                        'country' => '',
                        'pincode' => '',
                    ]);
                } else {
                    $vendor_id = $request->vendor_id;
                }
            }

            // ✅ Customer Handling
            if (!empty($request->customer_id)) {
                if (!is_numeric($request->customer_id)) {
                    $existingCustomer = User::where('id', $request->customer_id)->first();
                    if ($existingCustomer) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A customer with this phone number already exists.',
                        ], 409);
                    }
                    $customer = User::create([
                        'name' => $request->customer_id,
                        'phone' => null,
                        'role' => 'customer', // updated to 'customer'
                        'status' => $request->status,
                    ]);
                    $customer_id = $customer->id;

                    UserDetail::create([
                        'user_id' => $customer_id,
                        'address' => '',
                        'city' => '',
                        'state' => '',
                        'country' => '',
                        'pincode' => '',
                    ]);
                } else {
                    $customer_id = $request->customer_id;
                }
            }

            // ✅ Process Products
            $processedProducts = [];
            foreach ($request->products as $product) {
                if (!is_numeric($product['category_id'])) {
                    $category = Category::create(['name' => $product['category_id']]);
                    $category_id = $category->id;
                } else {
                    $category_id = $product['category_id'];
                }

                if (!is_numeric($product['id'])) {
                    do {
                        $sku = mt_rand(10000000, 99999999);
                    } while (Product::where('SKU', $sku)->exists());

                    $newProduct = Product::create([
                        'name' => $product['id'],
                        'category_id' => $category_id,
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                        'vendor_id' => $vendor_id ?? $customer_id,
                        'availablility' => 'in_stock',
                        'status' => 'active',
                        'SKU' => $sku,
                    ]);
                    $product_id = $newProduct->id;
                } else {
                    $existingProduct = Product::find($product['id']);
                    if ($existingProduct) {
                        $existingProduct->update([
                            'price' => $product['price'],
                            // 'quantity' => $existingProduct->quantity + $product['quantity'],
                            'category_id' => $category_id,
                        ]);
                        $product_id = $existingProduct->id;
                    }
                }

                $processedProducts[] = [
                    'product_id' => $product_id,
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'total' => $product['total'],
                ];
            }

            // ✅ Update CustomInvoice
            $customInvoice->update([
                'vendor_id' => $vendor_id ?? null,
                'customer_id' => $customer_id ?? null,
                'products' => json_encode($processedProducts),
                'total_amount' => collect($processedProducts)->sum('total'),
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping,
                'grand_total' => $request->grand_total,
                'status' => $request->status,
                'taxes' => json_encode($request->taxes),
            ]);

            // ✅ Update CustomInvoiceItems
            // First delete existing items
            CustomInvoiceItem::where('invoice_id', $customInvoice->id)->delete();

            // Then re-create them
            foreach ($processedProducts as $item) {
                CustomInvoiceItem::create([
                    'invoice_id' => $customInvoice->id,
                    'item' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount_total' => $item['total'],
                    'vendor_id' => $vendor_id,
                    'customer_id' => $customer_id,
                    'purchase_status' => $request->status,
                    'payment_status' => $request->payment_status,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully!',
                'invoice_id' => $customInvoice->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
