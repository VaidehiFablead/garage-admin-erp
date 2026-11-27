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
use App\Models\PaymentStore;
use App\Models\WhatsAppMessageTemplate;
use App\Services\PersonalWhatsAppService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $query->where('branch_id', $userId);
        } elseif ($role === 'admin') {
            $query->where('branch_id', $userId);
        } elseif ($role === 'staff') {
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
        $user = null;
        try {
            $user = Auth::guard('api')->user();
        } catch (\Throwable $e) {}
        if (!$user) {
            try {
                $user = Auth::guard('web')->user();
            } catch (\Throwable $e) {}
        }
        if (!$user) {
            $user = auth()->user();
        }

        $userId = $user->id ?? null;
        $branchId = $user->branch_id ?? null;
        $userRole = strtolower($user->role ?? '');

        $settings = DB::table('settings')->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $sub_admin_id = $request->input('sub_admin_id') ?? $request->input('selectedSubAdminId') ?? session('selectedSubAdminId');
        if ($sub_admin_id === 'null' || $sub_admin_id === 'undefined' || empty($sub_admin_id)) {
            $sub_admin_id = null;
        }

        $productsQuery = Product::where('isDeleted', 0)
            ->when($categoryId !== 'all', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });

        $branchIdToUse = null;
        if (!empty($sub_admin_id) && $sub_admin_id != 1) {
            $branchIdToUse = $sub_admin_id;
        } elseif ($userRole === 'sub-admin' || $userRole === 'sub_admin' || $userRole === 'subadmin') {
            $branchIdToUse = $branchId ?: $userId;
        } elseif ($userRole === 'staff') {
            $branchIdToUse = $branchId;
        } elseif ($userRole === 'admin') {
            $branchIdToUse = (!empty($sub_admin_id) && $sub_admin_id != 1) ? $sub_admin_id : $userId;
        } else {
            $branchIdToUse = (!empty($sub_admin_id) && $sub_admin_id != 1) ? $sub_admin_id : ($branchId ?: $userId);
        }

        if (!empty($branchIdToUse) && $branchIdToUse != 1) {
            // If branch selected or user belongs to a branch
            $products = (clone $productsQuery)->where(function ($q) use ($branchIdToUse) {
                    $q->where('branch_id', $branchIdToUse)
                      ->orWhere('branch_id', 1);
                })
                ->get();
        } elseif (!empty($branchIdToUse)) {
            $products = (clone $productsQuery)->where(function ($q) use ($branchIdToUse) {
                    $q->where('branch_id', $branchIdToUse)
                      ->orWhereNull('branch_id');
                })
                ->get();
        } else {
            $products = (clone $productsQuery)->get();
        }

        // Fallback: If no products found under this branch, fetch products for this category without branch filter
        if ($products->isEmpty()) {
            $products = (clone $productsQuery)->get();
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
                    'category_name' => optional($product->category)->name ?? 'Uncategorized',
                    'categoryId' => $product->category_id,
                ];
            })
        ]);
    }



    public function order_sale(Request $request)
    {

        // dd($request->all());
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }
        $userId = $user->id;
        $branchId = $user->branch_id;
        $role = $user->role;
        $selectedSubAdminId = $request->selectedSubAdminId;
        // Validate the request data
        $request->validate([
            'payment_method' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
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
            $TaxRateIds = TaxRate::where('status', 'active')->where('branch_id', $selectedSubAdminId)->pluck('id')->toArray();
            $taxJson = json_encode($TaxRateIds);
            // dd($request->staff_name);
            if (!is_numeric($request->staff_name) && !empty($request->staff_name)) {
                // Create new staff
                $staff = User::create([
                    'name'      => $request->staff_name,   // ✅ use staff_name, not user_id
                    'phone'     => $request->staff_phone ?? null,
                    'role'      => 'staff',
                    'branch_id' => $branchId ?? $selectedSubAdminId ?? $userId,
                    'status'    => 1,
                ]);

                // Get staff_id
                $staff_id = $staff->id;

                // Create staff details
                UserDetail::create([
                    'user_id' => $staff_id,
                    'address' => '',
                    'city'    => '',
                    'country' => '',
                ]);
                // dd($staff_id);
            } elseif (is_numeric($request->staff_name)) {
                // If staff_name is numeric, it's an existing staff ID
                $staff_id = $request->staff_name;
            } else {
                $staff_id = null; // or set default
            }
            // Create or find customer
            if (!is_numeric($request->user_id) && !empty($request->user_id)) {
                // dd($request->user_id);
                // Create new customer
                $vendor = User::create([
                    'name' => $request->user_id,
                    'phone' => $request->user_phone ?? null,
                    'role' => $request->userType ?? null,
                    'branch_id' => $branchId ?? $selectedSubAdminId ?? $userId,
                    'status' => 1,
                ]);
                $user_id = $vendor->id;

                // Create UserDetail
                UserDetail::create([
                    'user_id' => $user_id,
                    'address' => '',
                    'city' => '',
                    'country' => '',
                    // 'branch_id' => $selectedSubAdminId ?? $userId,
                ]);
            } elseif (is_numeric($request->user_id)) {
                $user_id = $request->user_id;
            } else {
                // Get default customer from users table (role = customer and name = 'Default Customer')
                $defaultCustomer = User::where('role', $request->userType)
                    ->where('name', 'Default Customer')
                    ->first();

                if (!$defaultCustomer) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Default customer not found. Please create a user with name "Default Customer".'
                    ], 400);
                }

                $user_id = $defaultCustomer->id;
            }

            if (!empty($selectedSubAdminId)) {
                $userId = $selectedSubAdminId;
            }

            if ($request->cash_partial_amount) {
                $cash_amount =  $request->cash_partial_amount ?? 0;
            } elseif ($request->cash_online_partial_cash) {
                $cash_amount =  $request->cash_online_partial_cash ?? 0;
            } elseif ($request->cash_online_cash_amount) {
                $cash_amount =  $request->cash_online_cash_amount ?? 0;
            }


            if ($request->partial_amount && $request->partial_amount != 0.00) {
                $online_amount = $request->partial_amount ?? 0;
            } elseif ($request->cash_online_partial_upi) {
                $online_amount = $request->cash_online_partial_upi ?? 0;
            } elseif ($request->cash_online_upi_amount) {
                $online_amount = $request->cash_online_upi_amount ?? 0;
            } else {
                $online_amount = 0;
            }

            if ($request->method == 'EMI') 
            {
                $status = 'pending';

            } else {
                $status = ($request->remaining_amount == 0) ? 'complete' : 'pending';
            }

            if ($request->bill_generate == 'bill_generate') {
                $remaining_amount = $request->total;
                $method = 'pending';
                $status = 'pending';
            } elseif ($request->method == 'EMI') 
            {
                $remaining_amount = $request->emi_amount - $request->calculated_emi;
                $method = $request->method;
            }else 
            {
                $remaining_amount = $request->remaining_amount;
                $method = $request->method;
            }

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user_id,
                'staff_id' => $staff_id,
                'next_warranty_date' => $request->warranty_date,
                'car_model' => $request->car_model,
                'vehicle_no' => $request->vehicle_no,
                'date' => $request->payment_date,
                'user_type' => $request->user_type ?? '',
                'payment_method' => $method ?? 'cash',
                'payment_type' => $request->type ?? '',
                'cash_amount' => $cash_amount ?? 0,
                'online_amount' => $online_amount ?? 0,
                'emi_duration' => $request->emi_duration ?? null,
                'emi_amount' => $request->calculated_emi ?? 0,
                'remaining_amount' => $remaining_amount ?? 0,
                'discount' => $request->discount ?? 0,
                'tax_id' => $taxJson,
                'total_amount' => $request->total,
                'branch_id' => $branchId ?? $userId,
                'created_by' => $userId,
                'type' => 'sales',

'quotation_status' => $request->has('quotation')
    ? 'quotation'
    : 'sales',
                'payment_status' => $status,
                'shipping' => $request->shipping ?? 0,
                'misc_charges' => $request->misc_charges ?? 0,
                'remark' => $request->remark ?? $request->remarks ?? '',
                'gst_option' => $request->gst_option ?? '',


            ]);




            $lastInsertedId = $order->id;


            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $product->quantity -= $item['quantity'];
                $product->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $user_id ?? null,
                    'category_id' => $item['categoryId'] ?? null,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_amount' => $item['total'],
                    'branch_id' => $branchId ?? $userId,
                    'created_by' => $userId,
                ]);
            }

            if ($request->cash_partial_amount) {
                $grand_total = $request->cash_partial_amount;
            } elseif (!empty($request->partial_amount) && $request->partial_amount != 0.00) {
                $grand_total = $request->partial_amount;
            } elseif ($request->calculated_emi && $request->calculated_emi != 0) {
                $grand_total = $request->calculated_emi;
            } elseif ($request->cash_online_partial_cash && $request->cash_online_partial_upi && $request->cash_online_remaining) {
                $grand_total = $request->cash_online_partial_cash + $request->cash_online_partial_upi;
            } elseif ($request->cash_online_cash_amount && $request->cash_online_upi_amount) {
                $grand_total = $request->cash_online_cash_amount + $request->cash_online_upi_amount;
            } 
            else {
                $grand_total = $request->total;
            }

            if ($request->bill_generate == 'bill_generate') {
                $duration = '';
                $grand_total = 0.00;
            }else{
                $duration =1;
                $grand_total=$grand_total;
            }
        if($request->bill_generate != 'bill_generate'){

        
            $payment = PaymentStore::create([
                'user_id'          => $userId,
                'jobcard_id'       => 0,
                'order_id'       => $lastInsertedId,
                'payment_amount'   => $grand_total ?? 0.00,
                'remaining_amount' => $remaining_amount ?? 0.00,
                'payment_date'     => \Carbon\Carbon::today(),
                'payment_method'   => $method ?? '',
                'payment_type'     => $request->type ?? '',
                'cash_amount'      => $cash_amount ?? 0,
                'upi_amount'       => $online_amount ?? 0,
                'emi_month'        => $duration,
                'discount_amount'   => $request->discount,
                'isDeleted'        => 0,
                'payment_status' => $status,

            ]);
            }


            DB::commit();

            // Send WhatsApp notification (non-blocking)
            try {
                $branchIdToUse = (int) ($order->branch_id ?? $branchId ?? $userId);
                $customer = $order->user_id ? User::select('id', 'name', 'phone', 'email')->find($order->user_id) : null;
                if ($customer && !empty($customer->phone)) {
                    $waSetting = Setting::select('customer_whatsapp_message', 'name')
                        ->where('branch_id', $branchIdToUse)
                        ->first();
                    $isCustomerWhatsAppEnabled = $waSetting
                        ? !in_array((string) ($waSetting->customer_whatsapp_message ?? '1'), ['0', 'off', 'false'], true)
                        : true;

                    $phoneNumber = preg_replace('/[^0-9]/', '', (string) $customer->phone);

                    if ($isCustomerWhatsAppEnabled && $phoneNumber !== '') {
                        $invoiceMeta = $this->generateSalesInvoicePdfForWhatsApp($order, $branchIdToUse);
                        $invoiceFileUrl = (string) data_get($invoiceMeta, 'file_url', '');
                        $invoiceFileName = (string) data_get($invoiceMeta, 'file_name', 'sales_invoice.pdf');

                        $personalHandled = $this->sendPersonalWhatsAppIfSelected(
                            $branchIdToUse,
                            $customer,
                            $order,
                            $phoneNumber,
                            is_array($invoiceMeta) ? $invoiceMeta : null
                        );

                        if ($personalHandled === null) {
                            $template = $this->findWhatsAppTemplateByName($branchIdToUse, 'subscription_invoice_pdf');
                            if (!$template) {
                                $template = $this->findWhatsAppOrderTemplate($branchIdToUse, [
                                    'Order invoice PDF',
                                    'Complete order',
                                    'Subscription complete',
                                    'Pending order',
                                    'Subscription pending',
                                ]);
                            }

                            if ($template && ($template->status ?? '') === 'APPROVED' && !empty($template->name)) {
                                $templateName = trim($template->name);
                                $templateLanguage = !empty($template->language) ? $template->language : 'en';
                                $customerName = $customer->name ?? 'Customer';
                                $orderNumber = $order->order_number ?? (string) $order->id;
                                $totalAmount = number_format((float) ($order->total_amount ?? 0), 2);
                                $companyName = $waSetting->name ?? 'Company';
                                $statusForTemplate = ($status === 'complete') ? 'Paid' : 'Pending';
                                $publicInvoicePdfUrl = url('/sales/invoice/pdf/' . $order->id);

                                $templateExtraComponents = [];
                                if ($invoiceFileUrl !== '') {
                                    $templateExtraComponents[] = [
                                        'type' => 'header',
                                        'parameters' => [[
                                            'type' => 'document',
                                            'document' => [
                                                'link' => $invoiceFileUrl,
                                                'filename' => $invoiceFileName,
                                            ],
                                        ]],
                                    ];
                                }

                                $templateParams = [];
                                $components = is_string($template->components)
                                    ? json_decode($template->components, true)
                                    : $template->components;

                                if (is_array($components)) {
                                    foreach ($components as $component) {
                                        if (strtoupper((string) ($component['type'] ?? '')) === 'BODY' && isset($component['text'])) {
                                            preg_match_all('/\{\{(\d+)\}\}/', $component['text'], $m);
                                            $bodyVarNums = array_values(array_unique(array_map('intval', $m[1] ?? [])));
                                            sort($bodyVarNums, SORT_NUMERIC);
                                            foreach ($bodyVarNums as $varNum) {
                                                $templateParams[] = match ((int) $varNum) {
                                                    1 => $customerName,
                                                    2 => $orderNumber,
                                                    3 => $companyName,
                                                    4 => $orderNumber,
                                                    5 => $totalAmount,
                                                    6 => $statusForTemplate,
                                                    7 => $publicInvoicePdfUrl,
                                                    default => '',
                                                };
                                            }
                                            break;
                                        }
                                    }
                                }

                                if (empty($templateParams)) {
                                    $templateParams = [
                                        $customerName,
                                        $orderNumber,
                                        $companyName,
                                        $orderNumber,
                                        $totalAmount,
                                        $statusForTemplate,
                                        $publicInvoicePdfUrl,
                                    ];
                                }

                                WhatsAppService::sendTemplateMessage(
                                    $branchIdToUse,
                                    $phoneNumber,
                                    $templateName,
                                    $templateParams,
                                    $templateLanguage,
                                    $templateExtraComponents
                                );
                            }
                        }
                    }
                }
            } catch (\Throwable $waEx) {
                Log::warning('Order WhatsApp send failed (non-blocking): ' . $waEx->getMessage(), [
                    'order_id' => $order->id ?? null,
                ]);
            }

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
        $role   = $user->role;

        // ✅ Prefer request input over route param
        $selectedSubAdminId = $request->query('selectedSubAdminId', $selectedSubAdminId);

        $query = Order::with([
            'user:id,name,phone',
            'biller:id,name',
            'orderItems:id,order_id,created_at',
            'paymentStore'
        ])->where('isDeleted', 0)->where('type', 'Sales')->withCount([
            'paymentStore as has_payment' => function ($q) {
                $q->where('isDeleted', 0);
            }
        ]);

        if ($role === 'sub-admin') {
            $query->where('created_by', $userId);
        } elseif ($role === 'admin' && $selectedSubAdminId) {
            $query->where('created_by', $selectedSubAdminId);
        } else {
            $query->where('created_by', $userId);
        }

        $get_orders = $query->orderBy('created_at', 'desc')->get();

        $get_orders->transform(function ($order) {
            $totalAmount    = 0;
            $totalEmiMonths = 0;

            if ($order->paymentStore && $order->paymentStore->count()) 
            {
                foreach ($order->paymentStore as $payment) 
                {
                    // dd($payment->payment_amount);
                    $totalAmount    += floatval($payment->payment_amount ?? 0);
                    $totalEmiMonths += intval($payment->emi_month ?? 0);
                }
            }

            if ($order->payment_method === 'emi' || $order->payment_method === 'EMI') {
                $remainingAmount    = floatval($order->total_amount ?? 0) - $totalAmount;
                $remainingEmiMonths = intval($order->emi_duration ?? 0) - $totalEmiMonths;

            } else {
                $remainingAmount    = $order->remaining_amount;
                $remainingEmiMonths = 0;
            }

            // dd($remainingAmount );

            // ✅ Ensure no negatives
            $remainingAmount    = max($remainingAmount, 0);
            $remainingEmiMonths = max($remainingEmiMonths, 0);

            // dd( $remainingAmount);

            // ✅ Attach new fields for frontend
            $order->remaining_amount            = $remainingAmount;
            $order->remaining_emi_months        = $remainingEmiMonths;
            $order->is_payment_pending          = $remainingAmount > 0;
            $order->formatted_remaining_amount  = number_format($remainingAmount, 2, '.', '');

            return $order;
        });

        $settings         = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return response()->json([
            'status'            => true,
            'message'           => 'Orders fetched successfully',
            'currency_symbol'   => $currencySymbol,
            'currency_position' => $currencyPosition,
            'data'              => $get_orders,
        ]);
    }



    // public function get_orders(Request $request, $selectedSubAdminId = null)
    // {
    //     $user = Auth::guard('api')->user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthenticated access',
    //         ], 401);
    //     }

    //     $userId = $user->id;
    //     $role = $user->role;

    //     // Get selected sub-admin ID from route parameter or query parameter
    //     $selectedSubAdminId = $selectedSubAdminId ?: $request->query('selectedSubAdminId');

    //     $query = Order::with([
    //         'user:id,name,phone',
    //         'biller:id,name',
    //         'orderItems:id,order_id,created_at'
    //     ])->where('isDeleted', 0);

    //     if ($role === 'sub-admin') {
    //         $query->where('created_by', $userId);
    //     } elseif ($role === 'admin' && $selectedSubAdminId) {
    //         $query->where('created_by', $selectedSubAdminId);
    //     } else {
    //         $query->where('created_by', $userId);
    //     }

    //     $get_orders = $query->orderBy('created_at', 'desc')->get();

    //     $settings = DB::table('settings')->first();
    //     $currencySymbol = $settings->currency_symbol ?? '₹';
    //     $currencyPosition = $settings->currency_position ?? 'left';


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Orders fetched successfully',
    //         'currency_symbol' => $currencySymbol,
    //         'currency_position' => $currencyPosition,
    //         'data' => $get_orders,
    //     ]);
    // }


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
                'gst_option' => $sales->gst_option ?? 'without', // default without GST
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
        // if ($order->payment_status === 'pending') {
        //     return response()->json([
        //         'status' => false,
        //         'error' => 'This order cannot be deleted because its payment status is pending.',
        //     ], 400);
        // }
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
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'payment_method' => 'nullable',
            'grand_total' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        //  dd($request->all());

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

            if ($request->cash_partial_amount) {
                $cash_amount =  $request->cash_partial_amount ?? 0;
            } elseif ($request->cash_online_partial_cash) {
                $cash_amount =  $request->cash_online_partial_cash ?? 0;
            } elseif ($request->cash_online_cash_amount) {
                $cash_amount =  $request->cash_online_cash_amount ?? 0;
            }


            if ($request->partial_amount && $request->partial_amount != 0.00) {
                $online_amount = $request->partial_amount ?? 0;
            } elseif ($request->cash_online_partial_upi) {
                $online_amount = $request->cash_online_partial_upi ?? 0;
            } elseif ($request->cash_online_upi_amount) {
                $online_amount = $request->cash_online_upi_amount ?? 0;
            } else {
                $online_amount = 0;
            }



            $updateData = [
                'user_name'       => $request->customer_id,
                'user_phone'      => $request->customer_phone,
                // 'payment_method'  => $request->payment_method,
                'payment_method'  => 'pending',
                'payment_type'    => $request->type ?? '',
                'cash_amount'     => $cash_amount ?? 0,
                'online_amount'   => $online_amount ?? 0,
                'emi_duration'    => $request->emi_duration ?? null,
                'emi_amount'      => $request->calculated_emi ?? 0,
                // 'remaining_amount' => $request->remaining_amount ?? 0,
                'remaining_amount' => $request->grand_total ?? 0,
                'discount'        => $request->discount,
                // 'discount_amount' => $discountAmount,
                'tax_id'         =>  $request->tax_ids,
                'tax_amount'      => $totalTax,
                'subtotal'        => $subtotal,
                'type'              => 'sales',
                'total_amount'    => $request->grand_total,
                'car_model'    => $request->car_model,
                'vehicle_no'    => $request->vehicle_no,
                'payment_status' => ($request->remaining_amount == 0) ? 'complete' : 'pending',
                'gst_option'      => $request->gst_option ?? 'without',
            ];

            // Only allow sub-admins to update `created_by` and `branch_id`
            if ($role === 'sub-admin') {
                $updateData['created_by'] = $userId;
                $updateData['branch_id'] = $userId;
            }

            // Perform update
            $order->update($updateData);


            if ($request->cash_partial_amount) {
                $grand_total = $request->cash_partial_amount;
            } elseif (!empty($request->partial_amount) && $request->partial_amount != 0.00) {
                $grand_total = $request->partial_amount;
            } elseif ($request->calculated_emi && $request->calculated_emi != 0) {
                $grand_total = $request->calculated_emi;
            } elseif ($request->cash_online_partial_cash && $request->cash_online_partial_upi && $request->cash_online_remaining) {
                $grand_total = $request->cash_online_partial_cash + $request->cash_online_partial_upi;
            } elseif ($request->cash_online_cash_amount && $request->cash_online_upi_amount) {
                $grand_total = $request->cash_online_cash_amount + $request->cash_online_upi_amount;
            } else {
                $grand_total = $request->total;
            }

            // PaymentStore::where('jobcard_id', $request->update_id)->delete();

            // $payment = PaymentStore::create([
            //     'user_id'          => $userId,
            //     'jobcard_id'       => $request->update_id,
            //     'payment_amount'   => $grand_total ?? 0.00,
            //     'remaining_amount' => $request->remaining_amount ?? 0.00,
            //     'payment_date'     => \Carbon\Carbon::today(),
            //     'payment_method'   => $request->payment_method ?? '',
            //     'payment_type'     => $request->type ?? '',
            //     'cash_amount'      => $cash_amount ?? 0,
            //     'upi_amount'       => $online_amount ?? 0,
            //     'emi_month'        => 1,
            //     'isDeleted'        => 0,
            //     'payment_status'   => 'pending',
            // ]);



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
                } else {
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
        // dd('ssadsad');
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
            ])->where('isDeleted', 0)->where('type', 'Sales')->withCount([
            'paymentStore as has_payment' => function ($q) {
                $q->where('isDeleted', 0);
            }
        ]);;

            // Filter by date if provided
            if ($request->has('date') && !empty($request->date)) {
                $query->whereDate('created_at', $request->date);
            }

            if ($role === 'sub-admin') {
                $query->where('created_by', $userId);
            } elseif ($role === 'admin' && $selectedSubAdminId) {
                $query->where('created_by', $selectedSubAdminId);
            } else {
                $query->where('created_by', $userId);
            }
            $data = $query->orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => true,
                'data' => $data,
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

    private function formatLocalBarcodeProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'SKU' => $product->SKU,
            'hsn_code' => $product->hsn_code,
            'barcode' => $product->barcode,
            'description' => $product->description,
            'purchase_price' => $product->purchase_price,
            'price' => $product->price,
            'mrp' => $product->mrp,
            'quantity' => $product->quantity,
            'stock' => $product->quantity,
            'image' => $product->images,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'categoryId' => $product->category_id,
            'brand_id' => $product->brand_id,
            'unit_id' => $product->unit_id,
            'unit' => $product->unit ? $product->unit->unit_name : null,
            'status' => $product->status,
            'availablility' => $product->availablility,
            'gst_option' => $product->gst_option,
            'product_gst' => $product->product_gst,
        ];
    }

    public function getByBarcode(Request $request, $barcode)
    {
        $barcode = trim($barcode);
        try {
            $user = null;
            try {
                $user = Auth::guard('api')->user();
            } catch (\Throwable $e) {}
            if (!$user) {
                try {
                    $user = Auth::guard('web')->user();
                } catch (\Throwable $e) {}
            }

            $sub_admin_id = $request->input('sub_admin_id') ?? session('selectedSubAdminId');
            $branchId = $sub_admin_id ?: ($user ? ($user->branch_id ?? $user->id) : null);

            // Search product by barcode
            $productQuery = Product::with('category', 'unit', 'brand')
                ->where(function($q) use ($barcode) {
                    $q->where('barcode', $barcode)
                      ->orWhere('barcode', 'LIKE', '%' . $barcode . '%')
                      ->orWhereRaw("FIND_IN_SET(?, REPLACE(barcode, ' ', ''))", [$barcode]);
                })
                ->where('isDeleted', 0);

            if (!empty($branchId) && $branchId != 1) {
                $productQuery->where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)->orWhere('branch_id', 1);
                });
            }

            $product = $productQuery->first();
            if ($product) {
                $product->barcode = $barcode;
            }

            // Check additional barcodes in ProductBarcode table if not found
            if (!$product) {
                $additionalBarcodes = \App\Models\ProductBarcode::where(function($q) use ($barcode) {
                    $q->where('barcode', $barcode)
                      ->orWhere('barcode', 'LIKE', '%' . $barcode . '%')
                      ->orWhereRaw("FIND_IN_SET(?, REPLACE(barcode, ' ', ''))", [$barcode]);
                })->get();

                foreach ($additionalBarcodes as $additionalBarcode) {
                    $pQuery = Product::with('category', 'unit', 'brand')
                        ->where('id', $additionalBarcode->product_id)
                        ->where('isDeleted', 0);

                    if (!empty($branchId) && $branchId != 1) {
                        $pQuery->where(function($q) use ($branchId) {
                            $q->where('branch_id', $branchId)->orWhere('branch_id', 1);
                        });
                    }

                    $found = $pQuery->first();
                    if ($found) {
                        $product = $found;
                        $product->barcode = $barcode;
                        break;
                    }
                }
            }

            // Fallback without branch filter if still not found
            if (!$product) {
                $product = Product::with('category', 'unit', 'brand')
                    ->where(function($q) use ($barcode) {
                        $q->where('barcode', $barcode)
                          ->orWhere('barcode', 'LIKE', '%' . $barcode . '%')
                          ->orWhereRaw("FIND_IN_SET(?, REPLACE(barcode, ' ', ''))", [$barcode]);
                    })
                    ->where('isDeleted', 0)
                    ->first();
                if ($product) {
                    $product->barcode = $barcode;
                }
            }

            if ($product) {
                return response()->json([
                    'status' => true,
                    'product' => $this->formatLocalBarcodeProduct($product),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Product not found: ' . $barcode
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findWhatsAppOrderTemplate(int $branchId, array $useForCandidates): ?WhatsAppMessageTemplate
    {
        $templates = WhatsAppMessageTemplate::select(
                'id',
                'branch_id',
                'name',
                'components',
                'language',
                'status',
                'category',
                'use_for_template',
                'on_off',
                'isDeleted'
            )
            ->where('branch_id', $branchId)
            ->where('on_off', 'active')
            ->where('isDeleted', 0)
            ->get();

        if ($templates->isEmpty()) {
            return null;
        }

        $normalize = static function ($value): string {
            return trim(mb_strtolower((string) $value));
        };

        $candidates = collect($useForCandidates)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values();

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $normalize($candidate);

            $template = $templates->first(function ($template) use ($normalize, $normalizedCandidate) {
                return $normalize($template->use_for_template ?? '') === $normalizedCandidate;
            });

            if ($template) {
                return $template;
            }
        }

        $aliasMap = [
            'pending order' => [
                'pending order',
                'subscription pending',
                'order_pending_notification',
                'order_panding_notification',
                'order_pending',
            ],
            'subscription pending' => [
                'subscription pending',
                'pending order',
                'order_pending_notification',
                'order_panding_notification',
                'order_pending',
            ],
            'complete order' => [
                'complete order',
                'subscription complete',
                'subscription_invoice_pdf',
                'order_complete_notification',
                'order_confirmation',
                'order_conform',
            ],
            'subscription complete' => [
                'subscription complete',
                'complete order',
                'subscription_invoice_pdf',
                'order_complete_notification',
                'order_confirmation',
                'order_conform',
            ],
            'order invoice pdf' => [
                'order invoice pdf',
                'subscription_invoice_pdf',
                'complete order',
                'subscription complete',
            ],
        ];

        $aliasCandidates = [];
        foreach ($candidates as $candidate) {
            $aliasCandidates = array_merge($aliasCandidates, $aliasMap[$normalize($candidate)] ?? [$candidate]);
        }

        $aliasCandidates = array_values(array_unique(array_map($normalize, $aliasCandidates)));

        return $templates->first(function ($template) use ($normalize, $aliasCandidates) {
            return in_array($normalize($template->use_for_template ?? ''), $aliasCandidates, true)
                || in_array($normalize($template->name ?? ''), $aliasCandidates, true);
        });
    }

    private function findWhatsAppTemplateByName(int $branchId, string $templateName): ?WhatsAppMessageTemplate
    {
        $normalizedName = trim(mb_strtolower($templateName));
        if ($normalizedName === '') {
            return null;
        }

        return WhatsAppMessageTemplate::select(
                'id',
                'branch_id',
                'name',
                'components',
                'language',
                'status',
                'category',
                'use_for_template',
                'on_off',
                'isDeleted'
            )
            ->where('branch_id', $branchId)
            ->where('on_off', 'active')
            ->where('isDeleted', 0)
            ->get()
            ->first(function ($template) use ($normalizedName) {
                return trim(mb_strtolower((string) ($template->name ?? ''))) === $normalizedName;
            });
    }

    private function generateSalesInvoicePdfForWhatsApp(Order $order, int $branchId): ?array
    {
        try {
            $sales = Order::with('carModel')->find($order->id);
            if (! $sales) {
                return null;
            }

            $setting = Setting::where('branch_id', $branchId)->first()
                ?? Setting::where('branch_id', 1)->first()
                ?? Setting::first()
                ?? new Setting([
                    'currency_symbol' => '₹',
                    'currency_position' => 'left',
                ]);

            $customerModel = $sales->user_id ? User::with('userDetail')->find($sales->user_id) : null;

            $formatCurrency = function ($amount) use ($setting) {
                $amount = number_format((float) $amount, 2);
                $symbol = $setting->currency_symbol ?? '₹';
                $position = $setting->currency_position ?? 'left';

                return $position === 'right' ? $amount . $symbol : $symbol . $amount;
            };

            $orderItems = OrderItem::where('order_id', $sales->id)->get();
            $subtotal = $orderItems->sum('total_amount');

            $discountPercent = (float) ($sales->discount ?? 0);
            $discountAmount = ($discountPercent / 100) * $subtotal;
            $afterDiscount = $subtotal - $discountAmount;

            $taxDetails = [];
            if ($sales->gst_option === 'with') {
                $taxIds = json_decode($sales->tax_id, true) ?? [];
                if (!empty($taxIds)) {
                    $taxes = TaxRate::whereIn('id', $taxIds)->get();
                    foreach ($taxes as $tax) {
                        $taxAmount = ($tax->tax_rate / 100) * $afterDiscount;
                        $taxDetails[] = [
                            'name' => $tax->tax_name,
                            'rate' => $tax->tax_rate,
                            'amount' => $taxAmount,
                            'formatted_amount' => $formatCurrency($taxAmount),
                        ];
                    }
                }
            }

            $finalTotal = $afterDiscount + collect($taxDetails)->sum('amount');
            $paidAmount = (float) PaymentStore::where('order_id', $sales->id)
                ->where(function ($q) {
                    $q->whereNull('isDeleted')->orWhere('isDeleted', 0);
                })
                ->sum('payment_amount');
            $pendingAmount = max(0, $finalTotal - $paidAmount);

            $pdfData = [
                'view_id' => $sales->id,
                'sales' => $sales,
                'setting' => $setting,
                'orderItems' => $orderItems,
                'salesItems' => $orderItems,
                'customer' => [
                    'name' => $customerModel->name ?? 'walk-in-customer',
                    'email' => $customerModel->email ?? '',
                    'phone' => $customerModel->phone ?? '',
                    'address' => optional($customerModel?->userDetail)->address ?? '',
                ],
                'user' => $customerModel ? $customerModel->toArray() : null,
                'subtotal' => $formatCurrency($subtotal),
                'discountAmount' => $formatCurrency($discountAmount),
                'afterDiscount' => $formatCurrency($afterDiscount),
                'finalTotal' => $formatCurrency($finalTotal),
                'taxDetails1' => $taxDetails,
            ];

            $pdf = Pdf::loadView('sales.salse-invoice-pdf', $pdfData)->setPaper('A4', 'portrait');

            Storage::disk('public')->makeDirectory('sales-invoices');

            $fileName = 'invoice_' . $sales->id . '_' . now()->format('Ymd_His') . '.pdf';
            $relativePath = 'sales-invoices/' . $fileName;
            $pdfBytes = $pdf->output();
            Storage::disk('public')->put($relativePath, $pdfBytes);

            $waDir = public_path('wa-invoices');
            if (! is_dir($waDir)) {
                @mkdir($waDir, 0775, true);
            }
            @file_put_contents($waDir . DIRECTORY_SEPARATOR . $fileName, $pdfBytes);

            $fileUrl = asset('storage/' . $relativePath);
            try {
                $host = request() ? (string) request()->getHost() : '';
                if ($host !== '' && ! in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                    $fileUrl = request()->getSchemeAndHttpHost() . '/wa-invoices/' . rawurlencode($fileName);
                }
            } catch (\Throwable $e) {
            }

            return [
                'status' => true,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'relative_path' => $relativePath,
                'local_path' => Storage::disk('public')->path($relativePath),
                'pendingAmountNumeric' => $pendingAmount,
            ];
        } catch (\Throwable $e) {
            Log::error('Sales invoice PDF generation for WhatsApp failed', [
                'order_id' => $order->id ?? null,
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to generate sales invoice PDF: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send sale invoice via Personal WhatsApp when that channel is selected.
     * Returns true/false when personal is selected, or null to keep Business API path.
     */
    private function sendPersonalWhatsAppIfSelected(int $branchId, $customer, $order, string $phoneNumber, ?array $invoiceMeta)
    {
        if (! PersonalWhatsAppService::isPersonalSelected($branchId)) {
            return null;
        }

        $pending = null;
        if (is_array($invoiceMeta) && array_key_exists('pendingAmountNumeric', $invoiceMeta)) {
            $pending = $invoiceMeta['pendingAmountNumeric'];
        }

        $vars = PersonalWhatsAppService::saleVars($order, $customer, $branchId, [
            'pending' => $pending ?? ($order->pending_amount ?? null),
        ]);

        $result = PersonalWhatsAppService::sendSaleMessage($branchId, $phoneNumber, $vars, $invoiceMeta);
        if (! empty($result['success'])) {
            Log::info('Personal WhatsApp sale message sent', [
                'order_id' => $order->id,
                'branch_id' => $branchId,
                'phone' => $phoneNumber,
            ]);

            return true;
        }

        Log::warning('Personal WhatsApp sale message skipped or failed', [
            'order_id' => $order->id,
            'branch_id' => $branchId,
            'phone' => $phoneNumber,
            'error' => $result['message'] ?? 'unknown',
        ]);

        return false;
    }

    public function resendWhatsApp($id)
    {
        $order = Order::with('orderItems.product')->find($id);
        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Sale not found.',
            ], 404);
        }

        $user = Auth::guard('api')->user() ?: Auth::guard('web')->user();
        $branchId = (int) ($order->branch_id ?: ($user->branch_id ?? $user->id ?? 0));
        $customer = User::select('id', 'name', 'phone')->find($order->user_id);
        $phone = preg_replace('/[^0-9]/', '', (string) ($customer->phone ?? ''));

        if (! $customer || $phone === '') {
            return response()->json([
                'status' => false,
                'message' => 'Customer phone number is missing.',
            ], 422);
        }

        $invoiceMeta = $this->generateSalesInvoicePdfForWhatsApp($order, $branchId);

        if (PersonalWhatsAppService::isPersonalSelected($branchId)) {
            $pending = is_array($invoiceMeta) ? ($invoiceMeta['pendingAmountNumeric'] ?? null) : null;
            $vars = PersonalWhatsAppService::saleVars($order, $customer, $branchId, [
                'pending' => $pending ?? ($order->pending_amount ?? null),
            ]);
            $result = PersonalWhatsAppService::sendSaleMessage(
                $branchId,
                $phone,
                $vars,
                is_array($invoiceMeta) ? $invoiceMeta : null
            );
            $pdfOk = ! empty($result['pdf']) || collect($result['results'] ?? [])->contains(fn ($row) => ! empty($row['pdf']));
            if (! empty($result['success'])) {
                return response()->json([
                    'status' => true,
                    'pdf' => $pdfOk,
                    'message' => $result['message'] ?? ($pdfOk ? 'WhatsApp sent with PDF.' : 'WhatsApp sent.'),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $result['message'] ?? 'Could not send WhatsApp. Connect Personal WhatsApp and try again.',
            ], 422);
        }

        $fileUrl = (string) data_get($invoiceMeta, 'file_url', '');
        $fileName = (string) data_get($invoiceMeta, 'file_name', 'sales_invoice.pdf');
        $template = $this->findWhatsAppTemplateByName($branchId, 'subscription_invoice_pdf');
        if (! $template) {
            $template = $this->findWhatsAppOrderTemplate($branchId, [
                'Order invoice PDF',
                'Complete order',
                'Pending order',
            ]);
        }

        if (! $template || ($template->status ?? '') !== 'APPROVED' || empty($template->name)) {
            return response()->json([
                'status' => false,
                'message' => 'No WhatsApp channel is ready. Connect Personal WhatsApp or save an approved API template.',
            ], 422);
        }

        $extra = [];
        if ($fileUrl !== '') {
            $extra[] = [
                'type' => 'header',
                'parameters' => [[
                    'type' => 'document',
                    'document' => [
                        'link' => $fileUrl,
                        'filename' => $fileName,
                    ],
                ]],
            ];
        }

        $result = WhatsAppService::sendTemplateMessage(
            $branchId,
            $phone,
            trim($template->name),
            [
                $customer->name ?? 'Customer',
                $order->order_number ?? (string) $order->id,
                number_format((float) ($order->total_amount ?? 0), 2),
            ],
            $template->language ?: 'en',
            $extra
        );

        if (! empty($result['success'])) {
            return response()->json([
                'status' => true,
                'message' => 'WhatsApp sent to the customer.',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message'] ?? 'Could not send WhatsApp.',
        ], 422);
    }
}
