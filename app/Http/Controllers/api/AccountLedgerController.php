<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PurchaseAutoCare;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\PaymentStore;
use App\Models\JobCard;
use App\Models\PurchaseInvoice;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
class AccountLedgerController extends Controller
{
    // public function getPaymentDetails(Request $request)
    // {
    //     try {
    //         $customerId = $request->input('customer_id');
    //         $customerName = $request->input('customer_name');
    //         $month = $request->input('month');
    //         $year = $request->input('year');
    //         $vehicleNumber = trim($request->input('vehicle_number'));
    //         $selectedSubBranchId = $request->input('selectedSubBranchId');

    //         $settings = Setting::first(['currency_symbol', 'currency_position']);

    //         // Fetch Paid Payments from PaymentStore
    //         $paidPaymentsQuery = PaymentStore::query();
    //         $paidPaymentsQuery->whereHas('jobcard', function ($query) use ($customerId, $customerName, $month, $year, $vehicleNumber, $selectedSubBranchId) {
    //             if (!empty($customerId)) {
    //                 $query->where('customer_id', $customerId);
    //             } elseif (!empty($customerName)) {
    //                 $query->where('customer_name', $customerName);
    //             }
    //             if (!empty($vehicleNumber)) {
    //                 $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
    //             }
    //             if (!empty($month) && $month !== 'all') {
    //                 $query->whereMonth('created_at', $month);
    //             }
    //             if (!empty($year)) {
    //                 $query->whereYear('created_at', $year);
    //             }
    //             if (!empty($selectedSubBranchId)) {
    //                 $query->where('subbranch_id', $selectedSubBranchId);
    //             }
    //         });

    //         $paidPayments = $paidPaymentsQuery->with(['jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no'])->get();

    //         // Fetch Pending Payments directly from JobCard
    //         $pendingPaymentsQuery = JobCard::query();
    //         if (!empty($customerId)) {
    //             $pendingPaymentsQuery->where('customer_id', $customerId);
    //         } elseif (!empty($customerName)) {
    //             $pendingPaymentsQuery->where('customer_name', $customerName);
    //         }
    //         $pendingPaymentsQuery->where('remaining_amount', '>', 0);
    //         if (!empty($vehicleNumber)) {
    //             $pendingPaymentsQuery->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
    //         }
    //         if (!empty($month) && $month !== 'all') {
    //             $pendingPaymentsQuery->whereMonth('created_at', $month);
    //         }
    //         if (!empty($year)) {
    //             $pendingPaymentsQuery->whereYear('created_at', $year);
    //         }
    //         if (!empty($selectedSubBranchId)) {
    //             $pendingPaymentsQuery->where('subbranch_id', $selectedSubBranchId);
    //         }

    //         $pendingPayments = $pendingPaymentsQuery->get([
    //             'id',
    //             'job_card_number',
    //             'total_subtotal_amount',
    //             'remaining_amount',
    //             'vehicle_no',
    //             'date',
    //             'type'
    //         ]);

    //         return response()->json([
    //             'paidPayments' => $paidPayments,
    //             'pendingPayments' => $pendingPayments,
    //             'settings' => $settings,
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error($e->getMessage());
    //         return response()->json(['error' => 'An error occurred while fetching payment details.'], 500);
    //     }
    // }
    public function getPaymentDetails(Request $request)
    {
        // dd($request->all());

        try {
            $type = $request->input('type', 'customer'); // default to customer
            $months = $request->input('month'); // array
            $year = $request->input('year');
            $vehicleNumber = trim($request->input('vehicle_number'));
            $selectedSubAdminId = $request->input('selectedSubAdminId1');

            $settings = Setting::first(['currency_symbol', 'currency_position']);
            $applyMonthFilter = !empty($months) && !(is_array($months) && in_array('all', $months));
            // dd($months);

            if ($type === 'customer') {
                $customerId = $request->input('id');
                $customerName = $request->input('name');
                if ($customerId === 'all') $customerId = null;

                $user = Auth::guard('api')->user();
                $branchId = !empty($selectedSubAdminId) ? $selectedSubAdminId : ($user->role === 'sub-admin' ? $user->id : 1);
                $userId = $user->id;
                $userRole = $user->role;
                // dd($branchId);

                // ---------------- PAID PAYMENTS ----------------
                $paidPaymentsQuery = PaymentStore::query();

                $paidPaymentsQuery->where(function ($q) use ($customerId, $months, $year, $branchId, $applyMonthFilter, $vehicleNumber, $user, $userId, $userRole) {
                    // Via Orders
                    $q->whereHas('order', function ($query) use ($customerId, $months, $year, $branchId, $applyMonthFilter, $user, $userId, $userRole) {
                        $query->where('isDeleted', 0);
                        if (!empty($customerId)) $query->where('user_id', $customerId);
                        if ($applyMonthFilter) $query->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                        if (!empty($year)) $query->whereYear('created_at', $year);
                        if (!empty($branchId)) $query->where('branch_id', $branchId);
                        if ($userRole == 'staff') {
                            $query->where('created_by', $userId);
                        }
                        else {
                            if (!empty($branchId)) {
                                $query->where('branch_id', $branchId);
                            }
                        }
                    })
                        // Via Job Cards
                        ->orWhereHas('jobcard', function ($query) use ($customerId, $months, $year, $branchId, $applyMonthFilter, $vehicleNumber, $user) {
                            $query->where('isDeleted', 0);
                            if (!empty($customerId)) $query->where('customer_id', $customerId);
                            if (!empty($vehicleNumber)) $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                            if ($applyMonthFilter) $query->whereIn(\DB::raw('MONTH(date)'), (array) $months);
                            if (!empty($year)) $query->whereYear('date', $year);
                            // if (!empty($branchId)) $query->where('subbranch_id', $branchId);
                            if ($user->role == 'staff') {
                                $query->where('user_id', $user->id);
                            } else {
                                if (!empty($branchId)) {
                                    $query->where('subbranch_id', $branchId);
                                }
                            }
                        });
                });

                $paidPayments = $paidPaymentsQuery
                    ->select('id', 'order_id', 'jobcard_id', 'payment_amount', 'payment_date', 'payment_method', 'discount_amount')
                    ->with([
                        'order:id,order_number,total_amount,date,type',
                        'jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no'
                    ])
                    ->get();

                // ---------------- PENDING PAYMENTS ----------------
                // Orders Pending
                $pendingOrdersQuery = Order::query()->where('isDeleted', 0)->where('remaining_amount', '>', 0);
                if (!empty($customerId)) $pendingOrdersQuery->where('user_id', $customerId);
                if ($applyMonthFilter) $pendingOrdersQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year)) $pendingOrdersQuery->whereYear('created_at', $year);
                // if (!empty($branchId)) $pendingOrdersQuery->where(function ($q) use ($branchId) {
                // $q->where('branch_id', $branchId)
                // ->orWhereNull('branch_id');
                // if ($user->role == 'staff') {
                //     $pendingOrdersQuery->where('created_by', $user->id);
                // } else {
                //     if (!empty($branchId)) {
                //         $pendingOrdersQuery->where(function ($q) use ($branchId) {
                //             $q->where('branch_id', $branchId)
                //             ->orWhereNull('branch_id');
                //         });
                // }
                if ($user->role == 'staff') {

    $pendingOrdersQuery->where('created_by', $user->id);

} else {

    if (!empty($branchId)) {

        $pendingOrdersQuery->where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhereNull('branch_id');
        });

    }

}
            

                $pendingOrders = $pendingOrdersQuery->get([
                    'id',
                    'order_number as job_card_number',
                    'total_amount as total_subtotal_amount',
                    'remaining_amount',
                    'date',
                    'type'
                ]);
                $convertedJobCardNumbers = JobCard::where('type', 'dailyData')
                    ->pluck('job_card_number')
                    ->toArray();
                // dd($convertedJobCardNumbers);
                // JobCards Pending
                // $pendingJobCardsQuery = JobCard::query()->where('isDeleted', 0)->where('remaining_amount', '>', 0)->whereNull('old_job_card');
                // if (!empty($customerId)) $pendingJobCardsQuery->where('customer_id', $customerId);
                // if (!empty($vehicleNumber)) $pendingJobCardsQuery->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                // if ($applyMonthFilter) $pendingJobCardsQuery->whereIn(\DB::raw('MONTH(date)'), (array) $months);
                // if (!empty($year)) $pendingJobCardsQuery->whereYear('date', $year);
                // if (!empty($branchId)) $pendingJobCardsQuery->where('subbranch_id', $branchId);
                $pendingJobCardsQuery = JobCard::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->where('type', 'jobCard')
                    ->whereNotIn('job_card_number', $convertedJobCardNumbers);

                if (!empty($customerId)) {
                    $pendingJobCardsQuery->where('customer_id', $customerId);
                }

                if (!empty($vehicleNumber)) {
                    $pendingJobCardsQuery->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                }

                if ($applyMonthFilter) {
                    $pendingJobCardsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                }

                if (!empty($year)) {
                    $pendingJobCardsQuery->whereYear('created_at', $year);
                }

                // if (!empty($branchId)) {
                //     $pendingJobCardsQuery->where('subbranch_id', $branchId);
                // }
                // if (!empty($branchId)) {
                // $pendingJobCardsQuery->where(function ($q) use ($branchId) {
                //     $q->where('subbranch_id', $branchId)
                //     ->orWhereNull('subbranch_id');
                // });
                if ($user->role == 'staff') {

                    $pendingJobCardsQuery->where('user_id', $user->id);

                } else {

                    if (!empty($branchId)) {

                        $pendingJobCardsQuery->where(function ($q) use ($branchId) {
                            $q->where('subbranch_id', $branchId)
                            ->orWhereNull('subbranch_id');
                        });

                    }

                }
        

                $pendingJobCards = $pendingJobCardsQuery->get([
                    'id',
                    'job_card_number',
                    'total_subtotal_amount',
                    'remaining_amount',
                    'vehicle_no',
                    'date',
                    'type'
                ]);

                $pendingPayments = $pendingOrders->concat($pendingJobCards);
            }
            // ---------------- VENDOR LOGIC ----------------
            else if ($type === 'vendor') {
                $vendorId = $request->input('id'); // sent from frontend
                $vendorName = $request->input('name');
                if ($vendorId === 'all') $vendorId = null;

                $user = Auth::guard('api')->user();
                $branchId = !empty($selectedSubAdminId) ? $selectedSubAdminId : ($user->role === 'sub-admin' ? $user->id : 1);

                // Paid payments: only purchases with at least one payment
                // $paidPaymentsQuery = PurchaseAutoCare::query()
                //     ->where('isDeleted', 0)
                //     ->with([
                //         'vendor',
                //         'payments' => function ($q) {
                //             $q->where('isDeleted', 0); // only active payments
                //         }
                //     ])
                //     ->whereHas('payments', function ($q) {
                //         $q->where('isDeleted', 0); // ensure there is at least one payment
                //     });
                $paidPaymentsQuery = PurchaseInvoice::query()
                ->where('isDeleted', 0)
                ->where('paid', '>', 0) // ✅ only paid invoices
                ->with('vendor');

                if (!empty($vendorId))
                    $paidPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $paidPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $paidPaymentsQuery->whereYear('created_at', $year);
                // if (!empty($branchId))
                //     $paidPaymentsQuery->where('branch_id', $branchId);
                if ($user->role == 'staff') {
                    $paidPaymentsQuery->where('created_by', $user->id);
                } else {
                    if (!empty($branchId)) {
                        $paidPaymentsQuery->where('branch_id', $branchId);
                    }
                }

                // $paidPayments = $paidPaymentsQuery->get()->map(function ($purchase) {
                //     // Calculate total paid for this purchase
                //     $purchase->total_paid_amount = $purchase->payments->sum('payment_amount');
                //     return $purchase;
                // });
                $paidPayments = $paidPaymentsQuery->get()->map(function ($invoice) {
                    return [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'vendor_id' => $invoice->vendor_id,
                        'vendor_name' => $invoice->vendor ? $invoice->vendor->name : '-',
                        'total_amount' => $invoice->total_amount,
                        'paid_amount' => $invoice->paid,
                        'remaining_amount' => $invoice->remaining_amount,
                        'date' => $invoice->created_at,
                        'type' => 'PurchaseInvoice'
                    ];
                });


                // Pending payments: purchases with remaining_amount > 0
                // $pendingPaymentsQuery = PurchaseAutoCare::query()
                //     ->where('isDeleted', 0)
                //     ->where('remaining_amount', '>', 0)
                //     ->with('vendor');
                $pendingPaymentsQuery = PurchaseInvoice::query()
                ->where('isDeleted', 0)
                ->where('remaining_amount', '>', 0)
                ->with('vendor');


                if (!empty($vendorId))
                    $pendingPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $pendingPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $pendingPaymentsQuery->whereYear('created_at', $year);
                // if (!empty($branchId))
                //     $pendingPaymentsQuery->where('branch_id', $branchId);
                if ($user->role == 'staff') {
                    $pendingPaymentsQuery->where('created_by', $user->id);
                } else {
                    if (!empty($branchId)) {
                        $pendingPaymentsQuery->where('branch_id', $branchId);
                    }
                }

                $pendingPayments = $pendingPaymentsQuery->get();
            }


        // dd($paidPayments);
            return response()->json([
                'type' => $type,
                'paidPayments' => $paidPayments,
                'pendingPayments' => $pendingPayments,
                'settings' => $settings,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paid_payment_all(Request $request)
    {
        //  dd($request->all());
        try {
            $user = Auth::guard('api')->user();
            $type = $request->input('type', 'customer'); // default to customer
            $months = $request->input('month'); // array
            $year = $request->input('year');
            $vehicleNumber = trim($request->input('vehicle_number'));
            $selectedSubAdminId = $request->input('selectedSubAdminId1') ?? $user->id;
            $dateFilter = $request->input('date_filter', null);

            $settings = Setting::first(['currency_symbol', 'currency_position']);
            $applyMonthFilter = !empty($months) && !(is_array($months) && in_array('all', $months));

            // Helper to apply date_filter
            $applyDateFilter = function ($query, $column = 'created_at') use ($dateFilter) {
                if ($dateFilter === 'today') {
                    $query->whereDate($column, now()->toDateString());
                } elseif ($dateFilter === 'this_week') {
                    $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateFilter === 'this_month') {
                    $query->whereMonth($column, now()->month)
                        ->whereYear($column, now()->year);
                }
            };
            

            if ($type === 'customer') {
                $customerId = $request->input('id');
                if ($customerId === 'all')
                    $customerId = null;

                if (!empty($selectedSubAdminId) && $selectedSubAdminId != 1) {
                    // Paid payments via Orders
                    $paidPaymentsQuery = PaymentStore::query()
                        ->whereHas('order', function ($query) use ($customerId, $months, $year, $selectedSubAdminId, $applyMonthFilter, $applyDateFilter) {
                            $query->where('isDeleted', 0);
                            if (!empty($customerId))
                                $query->where('user_id', $customerId);
                            if ($applyMonthFilter)
                                $query->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                            if (!empty($year))
                                $query->whereYear('created_at', $year);
                            if (!empty($selectedSubAdminId))
                                $query->where('branch_id', $selectedSubAdminId);

                            $applyDateFilter($query, 'created_at'); // apply date filter
                        });

                    $paidPayments = $paidPaymentsQuery->with(['order:id,order_number,total_amount,date,type'])->get();

                    $pendingPaymentsQuery = Order::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0);
                    if (!empty($customerId))
                        $pendingPaymentsQuery->where('user_id', $customerId);
                    if ($applyMonthFilter)
                        $pendingPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                    if (!empty($year))
                        $pendingPaymentsQuery->whereYear('created_at', $year);
                    if (!empty($selectedSubAdminId))
                        $pendingPaymentsQuery->where('branch_id', $selectedSubAdminId);

                    $applyDateFilter($pendingPaymentsQuery, 'created_at'); // apply date filter
                    $pendingPayments = $pendingPaymentsQuery->get([
                        'id',
                        'order_number as job_card_number',
                        'total_amount as total_subtotal_amount',
                        'remaining_amount',
                        'date',
                        'type'
                    ]);
                    // dd($pendingPayments, $paidPayments);
                    
                } else {
                    // Paid payments via JobCards
                    // Paid payments via JobCards
                    $paidPaymentsQuery = PaymentStore::query()
                        ->whereHas('jobcard', function ($query) use ($customerId, $vehicleNumber, $selectedSubAdminId, $applyDateFilter) {
                            $query->where('isDeleted', 0)
                                ->where('type', 'jobCard'); // only type 'jobCard'
    
                            if (!empty($customerId))
                                $query->where('customer_id', $customerId);
                            if (!empty($vehicleNumber))
                                $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                            if (!empty($selectedSubAdminId))
                                $query->where('subbranch_id', $selectedSubAdminId);

                            // Apply date filter
                            $applyDateFilter($query, 'date'); // or 'created_at' if you want PaymentStore created_at
                        });

                    $paidPayments = $paidPaymentsQuery
                        ->with(['jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no,old_job_card'])
                        ->get();

                    // DailyData payments
                    $dailyDataQuery = PaymentStore::query()
                        ->whereHas('jobcard', function ($query) use ($customerId, $vehicleNumber, $selectedSubAdminId, $applyDateFilter) {
                            $query->where('isDeleted', 0)
                                ->where('type', 'dailyData');

                            if (!empty($customerId))
                                $query->where('customer_id', $customerId);
                            if (!empty($vehicleNumber))
                                $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                            if (!empty($selectedSubAdminId))
                                $query->where('subbranch_id', $selectedSubAdminId);

                            // Apply the date filter on jobcard 'date' column
                            $applyDateFilter($query, 'date');
                        })
                        ->with(['jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no,old_job_card']);

                    $dailyDataPayments = $dailyDataQuery->get();

                    // Convert dailyData with old_job_card to actual JobCard info
                    $dailyDataAsJobCard = $dailyDataPayments->filter(function ($item) {
                        return !is_null($item->jobcard->old_job_card);
                    })->map(function ($item) {
                        if ($item->jobcard && $item->jobcard->old_job_card) {
                            // Fetch the original JobCard from job_cards table
                            $originalJobCard = \App\Models\JobCard::select('id', 'job_card_number', 'total_subtotal_amount', 'date', 'vehicle_no')
                                ->find($item->jobcard->old_job_card);

                            if ($originalJobCard) {
                                $item->jobcard->job_card_number = $originalJobCard->job_card_number;
                                $item->jobcard->total_subtotal_amount = $originalJobCard->total_subtotal_amount;
                                $item->jobcard->date = $originalJobCard->date;
                                $item->jobcard->vehicle_no = $originalJobCard->vehicle_no;
                                $item->jobcard->type = 'jobCard';
                            }
                        }
                        return $item;
                    });

                    // Remaining dailyData stay as pendingPayments
                    $pendingPayments = $dailyDataPayments->diff($dailyDataAsJobCard);

                    // Merge converted dailyData into paidPayments
                    $paidPayments = $paidPayments->merge($dailyDataAsJobCard);

                    // dd results



                    // dd($paidPayments, $pendingPayments);
                }
            }
            // ---------------- VENDOR LOGIC ----------------
            else if ($type === 'vendor') {
                $vendorId = $request->input('id');
                if ($vendorId === 'all')
                    $vendorId = null;

                $paidPaymentsQuery = PurchaseAutoCare::query()
                    ->where('isDeleted', 0)
                    ->with([
                        'vendor',
                        'payments' => function ($q) {
                            $q->where('isDeleted', 0);
                        }
                    ])
                    ->whereHas('payments', function ($q) {
                        $q->where('isDeleted', 0);
                    });

                if (!empty($vendorId))
                    $paidPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $paidPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $paidPaymentsQuery->whereYear('created_at', $year);
                if (!empty($selectedSubAdminId))
                    $paidPaymentsQuery->where('sub_admin_id', $selectedSubAdminId);

                $applyDateFilter($paidPaymentsQuery, 'created_at'); // apply date filter
                $paidPayments = $paidPaymentsQuery->get()->map(function ($purchase) {
                    $purchase->total_paid_amount = $purchase->payments->sum('payment_amount');
                    return $purchase;
                });

                $pendingPaymentsQuery = PurchaseAutoCare::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->with('vendor');

                if (!empty($vendorId))
                    $pendingPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $pendingPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $pendingPaymentsQuery->whereYear('created_at', $year);
                if (!empty($selectedSubAdminId))
                    $pendingPaymentsQuery->where('sub_admin_id', $selectedSubAdminId);

                $applyDateFilter($pendingPaymentsQuery, 'created_at'); // apply date filter
                $pendingPayments = $pendingPaymentsQuery->get();
            }


            return response()->json([
                'type' => $type,
                'paidPayments' => $paidPayments,
                'pendingPayments' => $pendingPayments,
                'settings' => $settings,
            ]);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPendingPaymentDetails(Request $request)
    {
        // dd($request->all());
        try {
            $user = Auth::guard('api')->user();
            $type = $request->input('type', 'customer'); // default customer
            $months = $request->input('month');
            $year = $request->input('year');
            $vehicleNumber = trim($request->input('vehicle_number'));
            $selectedSubAdminId = $request->input('selectedSubAdminId1') ?? $user->id;
            $filter = $request->input('filter', 'today'); // <-- new filter
            $settings = Setting::first(['currency_symbol', 'currency_position']);
            $applyMonthFilter = !empty($months) && !(is_array($months) && in_array('all', $months));

            // 📌 Date filter handling
            $today = now()->toDateString();
            $weekStart = now()->startOfWeek()->toDateString();
            $weekEnd = now()->endOfWeek()->toDateString();
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();

            $applyDateFilter = function ($query, $column) use ($filter, $today, $weekStart, $weekEnd, $monthStart, $monthEnd, $applyMonthFilter, $months, $year) {
                if ($filter === 'today') {
                    $query->whereDate($column, $today);
                } elseif ($filter === 'week') {
                    $query->whereBetween($column, [$weekStart, $weekEnd]);
                } elseif ($filter === 'month') {
                    if ($applyMonthFilter) {
                        $query->whereIn(\DB::raw('MONTH(' . $column . ')'), (array) $months);
                    } else {
                        $query->whereBetween($column, [$monthStart, $monthEnd]);
                    }
                    if (!empty($year)) {
                        $query->whereYear($column, $year);
                    }
                }
            };

            // ---------------- CUSTOMER ----------------
            if ($type === 'customer') {
                $customerId = $request->input('id');
                $customerName = $request->input('name');

                if ($customerId === 'all') {
                    $customerId = null;
                }

                if (!empty($selectedSubAdminId) && $selectedSubAdminId != 1) {
                    // ---------------- ORDERS ----------------
                    $paidPaymentsQuery = Order::query()->where('isDeleted', 0)->where('remaining_amount', '>', 0);
                    if (!empty($customerId))
                        $paidPaymentsQuery->where('user_id', $customerId);
                    if ($applyMonthFilter)
                        $paidPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                    if (!empty($year))
                        $paidPaymentsQuery->whereYear('created_at', $year);
                    if (!empty($selectedSubAdminId))
                        $paidPaymentsQuery->where('branch_id', $selectedSubAdminId);

                    $paidPayments = $paidPaymentsQuery->get([
                        'id',
                        'order_number as job_card_number',
                        'total_amount as total_subtotal_amount',
                        'remaining_amount',
                        'date',
                        'type'
                    ]);
                    
                    $pendingPaymentsQuery = Order::query()->where('isDeleted', 0)->where('remaining_amount', '>', 0);
                    if (!empty($customerId))
                        $pendingPaymentsQuery->where('user_id', $customerId);
                    if ($applyMonthFilter)
                        $pendingPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                    if (!empty($year))
                        $pendingPaymentsQuery->whereYear('created_at', $year);
                    if (!empty($selectedSubAdminId))
                        $pendingPaymentsQuery->where('branch_id', $selectedSubAdminId);

                    $pendingPayments = $pendingPaymentsQuery->get([
                        'id',
                        'order_number as job_card_number',
                        'total_amount as total_subtotal_amount',
                        'remaining_amount',
                        'date',
                        'type'
                    ]);
                } else {
                    // ---------------- JOBCARDS ----------------
                    

                    // ------------------- PAID PAYMENTS (JobCard) -------------------
                    $paidPaymentsQuery = JobCard::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0)
                        ->where('type', 'jobCard');

                    if (!empty($customerId))
                        $paidPaymentsQuery->where('customer_id', $customerId);
                    if (!empty($vehicleNumber))
                        $paidPaymentsQuery->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                    if (!empty($selectedSubAdminId))
                        $paidPaymentsQuery->where('subbranch_id', $selectedSubAdminId);

                    // ✅ Apply filter by date
                    $applyDateFilter($paidPaymentsQuery, 'date');

                    $paidPayments = $paidPaymentsQuery->get([
                        'id',
                        'job_card_number',
                        'total_subtotal_amount',
                        'remaining_amount',
                        'vehicle_no',
                        'date',
                        'type'
                    ]);

                    // ------------------- PENDING PAYMENTS (DailyData) -------------------
                    $pendingPaymentsQuery = JobCard::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0)
                        ->where('type', 'dailyData')->whereNull('old_job_card');

                    if (!empty($customerId))
                        $pendingPaymentsQuery->where('customer_id', $customerId);
                    if (!empty($vehicleNumber))
                        $pendingPaymentsQuery->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                    if (!empty($selectedSubAdminId))
                        $pendingPaymentsQuery->where('subbranch_id', $selectedSubAdminId);

                    // ✅ Apply filter by date
                    $applyDateFilter($pendingPaymentsQuery, 'date');

                    $pendingPayments = $pendingPaymentsQuery->get([
                        'id',
                        'job_card_number',
                        'total_subtotal_amount',
                        'remaining_amount',
                        'vehicle_no',
                        'date',
                        'type'
                    ]);
                    // dd($pendingPayments, $paidPayments);
                }
            }
            // ---------------- VENDOR LOGIC ----------------
            else if ($type === 'vendor') {
                $vendorId = $request->input('id'); // sent from frontend
                $vendorName = $request->input('name');

                if ($vendorId === 'all') {
                    $vendorId = null;
                }

                // Paid payments: only purchases with at least one payment
                $paidPaymentsQuery = PurchaseAutoCare::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->with('vendor');

                if (!empty($vendorId))
                    $paidPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $paidPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $paidPaymentsQuery->whereYear('created_at', $year);
                if (!empty($selectedSubAdminId))
                    $paidPaymentsQuery->where('sub_admin_id', $selectedSubAdminId);

                $paidPayments = $paidPaymentsQuery->get();

                // Pending payments: purchases with remaining_amount > 0
                $pendingPaymentsQuery = PurchaseAutoCare::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->with('vendor');

                if (!empty($vendorId))
                    $pendingPaymentsQuery->where('vendor_id', $vendorId);
                if ($applyMonthFilter)
                    $pendingPaymentsQuery->whereIn(\DB::raw('MONTH(created_at)'), (array) $months);
                if (!empty($year))
                    $pendingPaymentsQuery->whereYear('created_at', $year);
                if (!empty($selectedSubAdminId))
                    $pendingPaymentsQuery->where('sub_admin_id', $selectedSubAdminId);

                $pendingPayments = $pendingPaymentsQuery->get();
                // dd($selectedSubAdminId);
            }

            return response()->json([
                'type' => $type,
                'paidPayments'   => $paidPayments,
                'pendingPayments' => $pendingPayments,

                'settings' => $settings,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportAccountLedgerExcel(Request $request)
    {
        // dd($request->all());
        try {
            $type = $request->input('type', 'customer'); // 'customer' or 'vendor'
            $customerId = $request->input('id');
            $customerName = $request->input('name');
            $vendorId = $request->input('id');
            $vendorName = $request->input('name');
            $monthsInput = $request->input('month', []);
            $year = $request->input('year');
            $vehicleNumber = trim($request->input('vehicle_number'));
            $user = Auth::guard('api')->user();
            $selectedSubAdminId = $request->input('selectedSubAdminId');

            $branchId = !empty($selectedSubAdminId)
                ? $selectedSubAdminId
                : ($user->role === 'sub-admin' ? $user->id : 1);

            // === Normalize months ===
            $allMonthsSelected = false;
            $months = [];

            if (!empty($monthsInput)) {
                if ((is_array($monthsInput) && in_array('all', $monthsInput)) || $monthsInput === 'all') {
                    $allMonthsSelected = true;
                    $months = range(1, 12); // all months
                } else {
                    $months = array_map(fn($m) => (int) $m, (array) $monthsInput); // ensure integers
                }
            }

            // === Fetch Payments ===
            // if ($type === 'customer') {
            //     if ($branchId) {
            //         // ---------------- ORDERS ----------------
            //         $paidPayments = PaymentStore::where('isDeleted', 0)
            //             ->whereHas('order', function ($query) use ($customerId, $months, $year, $branchId, $allMonthsSelected) {
            //                 $query->where('isDeleted', 0)
            //                     ->when($customerId && $customerId !== 'all', fn($q) => $q->where('user_id', $customerId))
            //                     ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //                         $q->where(function ($q2) use ($months) {
            //                             foreach ($months as $month) {
            //                                 $q2->orWhereMonth('created_at', $month);
            //                             }
            //                         });
            //                     })
            //                     ->when($year, fn($q) => $q->whereYear('created_at', $year))
            //                     ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
            //             })
            //             ->with('order')
            //             ->get();

            //         $pendingPayments = Order::where('isDeleted', 0)
            //             ->where('remaining_amount', '>', 0)
            //             ->when($customerId && $customerId !== 'all', fn($q) => $q->where('user_id', $customerId))
            //             ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //                 $q->where(function ($q2) use ($months) {
            //                     foreach ($months as $month) {
            //                         $q2->orWhereMonth('created_at', $month);
            //                     }
            //                 });
            //             })
            //             ->when($year, fn($q) => $q->whereYear('created_at', $year))
            //             ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            //             ->get();
            //     } else {

            //         // ---------------- JOBCARDS ----------------
            //         $paidPayments = PaymentStore::where('isDeleted', 0)
            //             ->whereHas('jobcard', function ($query) use ($customerId, $customerName, $vehicleNumber, $months, $year, $allMonthsSelected) {
            //                 $query->where('isDeleted', 0)
            //                     ->when($customerId && $customerId !== 'all', fn($q) => $q->where('customer_id', $customerId))
            //                     ->when(!$customerId && $customerName, fn($q) => $q->where('customer_name', $customerName))
            //                     ->when($vehicleNumber, fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
            //                     ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //                         $q->where(function ($q2) use ($months) {
            //                             foreach ($months as $month) {
            //                                 $q2->orWhereMonth('date', $month);
            //                             }
            //                         });
            //                     })
            //                     ->when($year, fn($q) => $q->whereYear('date', $year));
            //             })
            //             ->with('jobcard')
            //             ->get();

            //         $pendingPayments = JobCard::where('isDeleted', 0)
            //             ->where('remaining_amount', '>', 0)->whereNull('old_job_card')
            //             ->when($customerId && $customerId !== 'all', fn($q) => $q->where('customer_id', $customerId))
            //             ->when(!$customerId && $customerName, fn($q) => $q->where('customer_name', $customerName))
            //             ->when($vehicleNumber, fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
            //             ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //                 $q->where(function ($q2) use ($months) {
            //                     foreach ($months as $month) {
            //                         $q2->orWhereMonth('date', $month);
            //                     }
            //                 });
            //             })
            //             ->when($year, fn($q) => $q->whereYear('date', $year))
            //             ->get();
            //     }
            // } 
            // =========================================================
            // ================= CUSTOMER LOGIC =========================
            // =========================================================

            if ($type === 'customer') {

                // ---------------- PAID ----------------
                $paidPayments = PaymentStore::query()
                    ->where(function ($q) use ($customerId, $months, $year, $branchId, $allMonthsSelected, $vehicleNumber) {

                        // ORDERS
                        $q->whereHas('order', function ($query) use ($customerId, $months, $year, $branchId, $allMonthsSelected) {
                            $query->where('isDeleted', 0);

                            if (!empty($customerId) && $customerId !== 'all') {
                                $query->where('user_id', $customerId);
                            }

                            if (!$allMonthsSelected && !empty($months)) {
                                $query->whereIn(\DB::raw('MONTH(created_at)'), $months);
                            }

                            if (!empty($year)) {
                                $query->whereYear('created_at', $year);
                            }

                            if (!empty($branchId)) {
                                $query->where('branch_id', $branchId);
                            }
                        })

                        // JOBCARDS
                        ->orWhereHas('jobcard', function ($query) use ($customerId, $months, $year, $branchId, $allMonthsSelected, $vehicleNumber) {
                            $query->where('isDeleted', 0);

                            if (!empty($customerId) && $customerId !== 'all') {
                                $query->where('customer_id', $customerId);
                            }

                            if (!empty($vehicleNumber)) {
                                $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                            }

                            if (!$allMonthsSelected && !empty($months)) {
                                $query->whereIn(\DB::raw('MONTH(date)'), $months);
                            }

                            if (!empty($year)) {
                                $query->whereYear('date', $year);
                            }

                            if (!empty($branchId)) {
                                $query->where('subbranch_id', $branchId);
                            }
                        });
                    })
                    ->with([
                        'order:id,order_number,total_amount,date,type',
                        'jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no'
                    ])
                    ->get();

                // ---------------- PENDING ORDERS ----------------
                $pendingOrders = Order::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('user_id', $customerId))
                    ->when(!$allMonthsSelected && !empty($months), fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $months))
                    ->when($allMonthsSelected === false && empty($months) && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->get([
                        'id',
                        'order_number as job_card_number',
                        'total_amount as total_subtotal_amount',
                        'remaining_amount',
                        'date',
                        'type'
                    ]);

                // ---------------- PENDING JOBCARDS ----------------
                $convertedJobCardNumbers = JobCard::where('type', 'dailyData')
                    ->pluck('job_card_number')
                    ->toArray();

                $pendingJobCards = JobCard::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->where('type', 'jobCard')
                    ->whereNotIn('job_card_number', $convertedJobCardNumbers)
                    ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('customer_id', $customerId))
                    ->when(!empty($vehicleNumber), fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
                    ->when(!$allMonthsSelected && !empty($months), fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $months))
                    ->when($allMonthsSelected === false && empty($months) && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                    ->when($branchId, fn($q) => $q->where('subbranch_id', $branchId))
                    ->get([
                        'id',
                        'job_card_number',
                        'total_subtotal_amount',
                        'remaining_amount',
                        'vehicle_no',
                        'date',
                        'type'
                    ]);

                $pendingPayments = $pendingOrders->concat($pendingJobCards);
            }
            // else {
            //     // ---------------- VENDOR ----------------
            //     if ($vendorId === 'all')
            //         $vendorId = null;


            //     $paidPayments = PurchaseAutoCare::where('isDeleted', 0)
            //         ->with(['vendor', 'payments' => fn($q) => $q->where('isDeleted', 0)])
            //         ->whereHas('payments', fn($q) => $q->where('isDeleted', 0))
            //         ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            //         ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //             $q->where(function ($q2) use ($months) {
            //                 foreach ($months as $month) {
            //                     $q2->orWhereMonth('created_at', $month);
            //                 }
            //             });
            //         })
            //         ->when($year, fn($q) => $q->whereYear('created_at', $year))
            //         ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            //         ->get()
            //         ->map(function ($purchase) {
            //             // Add a new field 'payment_amount' that sums all related payments
            //             $purchase->payment_amount = $purchase->payments->sum('payment_amount') ?: 0;
            //             return $purchase; // return full object with new field
            //         });

            //     $pendingPayments = PurchaseAutoCare::where('isDeleted', 0)
            //         ->where('remaining_amount', '>', 0)
            //         ->with('vendor')
            //         ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            //         ->when(!$allMonthsSelected && !empty($months), function ($q) use ($months) {
            //             $q->where(function ($q2) use ($months) {
            //                 foreach ($months as $month) {
            //                     $q2->orWhereMonth('created_at', $month);
            //                 }
            //             });
            //         })
            //         ->when($year, fn($q) => $q->whereYear('created_at', $year))
            //         ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            //         ->get();
            // }
            else {
                if ($vendorId === 'all') {
                    $vendorId = null;
                }

                $paidPayments = PurchaseInvoice::query()
                    ->where('isDeleted', 0)
                    ->where('paid', '>', 0)
                    ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
                    ->when(!$allMonthsSelected && !empty($months), fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $months))
                    ->when($allMonthsSelected === false && empty($months) && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->with(['vendor', 'purchases'])
                    ->get()
                    ->map(function ($invoice) {
                        $paymentStatus = optional($invoice->purchases->first())->payment_status;
                        return (object)[
                            'invoice_number' => $invoice->invoice_number,
                            'total_amount' => $invoice->total_amount,
                            'paid_amount' => $invoice->paid,
                            'remaining_amount' => $invoice->remaining_amount,
                            'date' => $invoice->created_at,
                            'vendor' => $invoice->vendor,
                            'payment_method' => $paymentStatus ?? '-',
                        ];
                    });

                $pendingPayments = PurchaseInvoice::query()
                    ->where('isDeleted', 0)
                    ->where('remaining_amount', '>', 0)
                    ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
                    ->when(!$allMonthsSelected && !empty($months), fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $months))
                    ->when($allMonthsSelected === false && empty($months) && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->with(['vendor', 'purchases'])
                    ->get()
                    ->map(function ($invoice) {
                        return (object)[
                            'invoice_number' => $invoice->invoice_number,
                            'total_amount' => $invoice->total_amount,
                            'remaining_amount' => $invoice->remaining_amount,
                            'vendor' => $invoice->vendor
                        ];
                    });
            }

            // dd($paidPayments);

            // === Excel Generation ===
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            // === Title ===
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->setCellValue("A{$row}", 'Account Ledger Report');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 2;

            // === Customer/Vendor Info ===
            $sheet->setCellValue("A{$row}", $type === 'customer' ? 'Customer Name' : 'Vendor Name')
                ->setCellValue("B{$row}", $customerName ?? $vendorName ?? '-');
            $row++;

            $monthNames = $allMonthsSelected
                ? 'All Months'
                : implode(', ', array_map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)), $months));

            $sheet->setCellValue("A{$row}", 'Months')->setCellValue("B{$row}", $monthNames);
            $row++;
            $sheet->setCellValue("A{$row}", 'Year')->setCellValue("B{$row}", $year ?: '-');
            $row++;
            $sheet->setCellValue("A{$row}", 'Vehicle Number')->setCellValue("B{$row}", $vehicleNumber ?: '-');
            $row += 2;

            // === Paid Section ===
            $sheet->setCellValue("A{$row}", '--- PAID PAYMENTS ---');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB('1F4E78');
            $row++;

            $headers = $type === 'customer'
                ? ['Job Card #', 'Total Amount', 'Discount Amount', 'Paid Amount', 'Payment Date', 'Payment Method', 'Vehicle No.', 'Type']
                : ['Purchase #', 'Total Amount', 'Paid Amount', 'Payment Date', 'Payment Method', 'Vendor Name'];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}{$row}", $header);
                $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("{$col}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
                $sheet->getStyle("{$col}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $row++;

            // === Paid Data ===
            $totalPaid = 0;
            foreach ($paidPayments as $payment) {
                if ($type === 'customer') {
                    if (!empty($payment->jobcard)) {
                        $jobcard = $payment->jobcard;
                    } elseif (!empty($payment->order)) {
                        $jobcard = $payment->order;
                    } else {
                        $jobcard = null;
                    }
                } else {
                    $jobcard = $payment;
                }
                if ($type === 'vendor') {

                $sheet->setCellValue("A{$row}", $payment->invoice_number ?? '-');
                $sheet->setCellValue("B{$row}", $payment->total_amount ?? '-');
                $sheet->setCellValue("C{$row}", $payment->paid_amount ?? '-');
                $sheet->setCellValue("D{$row}", $payment->date ?? '-');
                $sheet->setCellValue("E{$row}", $payment->payment_method ?? '-');
                $sheet->setCellValue("F{$row}", $payment->vendor->name ?? '-');
                // $sheet->setCellValue("G{$row}", '-');

            } else {
                $sheet->setCellValue("A{$row}", $jobcard->job_card_number ?? ($jobcard->order_number ?? ($jobcard->bill_no ?? '-')));
                // $sheet->setCellValue("B{$row}", $jobcard->total_subtotal_amount ?? ($jobcard->total_amount ?? ($jobcard->payable_amount ?? '-')));
                $sheet->setCellValue("B{$row}",
                    $jobcard->total_subtotal_amount
                    ?? $jobcard->total_amount
                    ?? '-'
                );
                $sheet->setCellValue("C{$row}", $payment->discount_amount ?? '-');
                $sheet->setCellValue("D{$row}", $payment->payment_amount);
                $sheet->setCellValue("E{$row}", $payment->payment_date ?? ($jobcard->created_at ?? '-'));
                $sheet->setCellValue("F{$row}", $payment->payment_method ?? ($jobcard->payment_mode ?? '-'));
                $sheet->setCellValue("G{$row}",
                    isset($payment->jobcard) ? ($payment->jobcard->vehicle_no ?? '-') : '-'
                );
                $sheet->setCellValue("H{$row}", $jobcard->type ?? '-');
            }

                // foreach (range('A', 'H') as $colLetter) {
                //     $sheet->getStyle("{$colLetter}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                // }
                $lastColumn = $type === 'vendor' ? 'F' : 'H';

                foreach (range('A', $lastColumn) as $colLetter) {
                    $sheet->getStyle("{$colLetter}{$row}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }
                // $totalPaid += floatval($payment->payment_amount);
                $totalPaid += floatval(
        $type === 'vendor'
            ? ($payment->paid_amount ?? 0)
            : ($payment->payment_amount ?? 0)
        );
                $row++;
            }

            // === Total Paid ===
            $sheet->setCellValue("A{$row}", 'Total Paid Amount');
            $sheet->setCellValue("D{$row}", number_format($totalPaid, 2));
            $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
            $row += 2;

            // === Pending Section ===
            $sheet->setCellValue("A{$row}", '--- PENDING PAYMENTS ---');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB('9C0006');
            $row++;

            $headers = $type === 'customer'
                ? ['Job Card #', 'Total Amount', 'Remaining Amount', 'Type', 'Vehicle No.']
                : ['Purchase #', 'Total Amount', 'Remaining Amount', 'Vendor Name'];

            $col = 'A';

            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}{$row}", $header);
                $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("{$col}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC');
                $sheet->getStyle("{$col}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $row++;

            $totalPending = 0;
            foreach ($pendingPayments as $pending) {
                // dd($pending);
                // $sheet->setCellValue("A{$row}", $pending->job_card_number ?? ($pending->order_number ?? ($pending->bill_no ?? '-')));
                $sheet->setCellValue("A{$row}",
                    $type === 'vendor'
                        ? ($pending->invoice_number ?? '-')
                        : ($pending->job_card_number ?? ($pending->order_number ?? '-'))
                );
                $sheet->setCellValue("B{$row}", $pending->total_subtotal_amount ?? ($pending->total_amount ?? ($pending->payable_amount ?? '-')));
                $sheet->setCellValue("C{$row}", $pending->remaining_amount);
                // $sheet->setCellValue("D{$row}", $pending->type ?? '-');
                // $sheet->setCellValue("E{$row}", $type === 'customer'
                //     ? ($pending->vehicle_no ?? '-')
                //     : ($pending->vendor->name ?? '-'));
                if ($type === 'customer') {
                    $sheet->setCellValue("D{$row}", $pending->type ?? '-');
                    $sheet->setCellValue("E{$row}", $pending->vehicle_no ?? '-');
                } else {
                    $sheet->setCellValue("D{$row}", $pending->vendor->name ?? '-');
                }
                // foreach (range('A', 'E') as $colLetter) {
                //     $sheet->getStyle("{$colLetter}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                // }
                $lastColumn = $type === 'vendor' ? 'D' : 'E';

                foreach (range('A', $lastColumn) as $colLetter) {
                    $sheet->getStyle("{$colLetter}{$row}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }
                $totalPending += floatval($pending->remaining_amount);
                $row++;
            }

            $sheet->setCellValue("A{$row}", 'Total Pending Amount');
            $sheet->setCellValue("C{$row}", number_format($totalPending, 2));
            $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);

            foreach (range('A', 'G') as $colLetter) {
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            $filename = 'account_ledger_' . preg_replace('/[^a-zA-Z0-9]/', '_', $customerName ?? $vendorName ?? 'account') . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            return new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Error generating Excel file.'], 500);
        }
    }

    // public function downloadAccountLedgerPDF(Request $request)
    // {

    //     try {
    //         $type = $request->input('type', 'customer'); // 'customer' or 'vendor'
    //         $customerId = $request->input('id');
    //         $customerName = $request->input('name');
    //         $vendorId = $request->input('id'); // for vendor
    //         $vendorName = $request->input('name'); // for vendor
    //         $months = $request->input('month', []); // can be array or 'all'
    //         $year = $request->input('year');
    //         $vehicleNumber = trim($request->input('vehicle_number'));
    //         $branchId = $request->input('selectedSubAdminId1');

    //         // Handle month filtering logic
    //         if ($months === 'all' || (is_array($months) && in_array('all', $months))) {
    //             $applyMonthFilter = false;
    //             $monthsForQuery = range(1, 12); // All months (1-12)
    //         } else {
    //             $applyMonthFilter = !empty($months);
    //             $monthsForQuery = (array) $months;
    //         }


    //         // Convert for view (to avoid mktime() error)
    //         $monthsForView = $applyMonthFilter ? $monthsForQuery : range(1, 12);

    //         // Fetch customer GST if needed
    //         $customerGST = null;
    //         if (!empty($customerId) && $customerId !== 'all') {
    //             $customerGST = User::where('id', $customerId)->value('gst_num');
    //         } elseif (!empty($customerName)) {
    //             $customerGST = User::where('name', $customerName)->value('gst_num');
    //         }

    //         $settings = Setting::where('branch_id', $branchId)->first() ?? Setting::first();

    //         if ($type === 'customer') {

    //             // if ($branchId) {
    //             //     // ---------------- ORDERS ----------------
    //             //     $paidPayments = PaymentStore::where('isDeleted', 0)
    //             //         ->whereHas('order', function ($query) use ($customerId, $applyMonthFilter, $monthsForQuery, $year, $branchId) {
    //             //             $query->where('isDeleted', 0);
    //             //             if ($customerId !== 'all' && !empty($customerId)) {
    //             //                 $query->where('user_id', $customerId);
    //             //             }
    //             //             if ($applyMonthFilter) {
    //             //                 $query->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery);
    //             //             }

    //             //             if (!empty($year)) {
    //             //                 $query->whereYear('created_at', $year);
    //             //             }
    //             //             $query->where('branch_id', $branchId);
    //             //         })
    //             //         ->with('order')
    //             //         ->get();

    //             //     $pendingPayments = Order::query()
    //             //         ->where('isDeleted', 0)
    //             //         ->where('remaining_amount', '>', 0)
    //             //         ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('user_id', $customerId))
    //             //         ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //             //         ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //             //         ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
    //             //         ->get();
    //             // } 
    //             // else 
    //             // {

    //             //     // ---------------- JOBCARDS ----------------

    //             //     $paidPayments = PaymentStore::where('isDeleted', 0)
    //             //         ->whereHas('jobcard', function ($query) use ($customerId, $customerName, $vehicleNumber, $applyMonthFilter, $monthsForQuery, $year) {
    //             //             $query->where('isDeleted', 0);
    //             //             if ($customerId !== 'all' && !empty($customerId)) {
    //             //                 $query->where('customer_id', $customerId);
    //             //             } elseif (!empty($customerName)) {
    //             //                 $query->where('customer_name', $customerName);
    //             //             }
    //             //             if (!empty($vehicleNumber)) {
    //             //                 $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
    //             //             }
    //             //             if ($applyMonthFilter) {
    //             //                 $query->whereIn(\DB::raw('MONTH(date)'), $monthsForQuery);
    //             //             }
    //             //             if (!empty($year)) {
    //             //                 $query->whereYear('date', $year);
    //             //             }
    //             //         })
    //             //         ->with('jobcard')
    //             //         ->get();

    //             //     // dd($paidPayments);
    //             //     $pendingPayments = JobCard::query()
    //             //         ->where('isDeleted', 0)->whereNull('old_job_card')
    //             //         ->where('remaining_amount', '>', 0)
    //             //         ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('customer_id', $customerId))
    //             //         ->when($customerId === 'all' && !empty($customerName), fn($q) => $q->where('customer_name', $customerName))
    //             //         ->when(!empty($vehicleNumber), fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
    //             //         ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //             //         ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //             //         ->get();
    //             // }
    //             // ---------------- PENDING ORDERS ----------------
    //             // ---------------- PAID PAYMENTS ----------------
    //             $paidPaymentsQuery = PaymentStore::query();

    //             $paidPaymentsQuery->where(function ($q) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter, $vehicleNumber) {

    //                 // Orders
    //                 $q->whereHas('order', function ($query) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter) {
    //                     $query->where('isDeleted', 0);

    //                     if (!empty($customerId) && $customerId !== 'all') {
    //                         $query->where('user_id', $customerId);
    //                     }

    //                     if ($applyMonthFilter) {
    //                         $query->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery);
    //                     }

    //                     if (!empty($year)) {
    //                         $query->whereYear('created_at', $year);
    //                     }

    //                     if (!empty($branchId)) {
    //                         $query->where(function ($q) use ($branchId) {
    //                             $q->where('branch_id', $branchId)
    //                             ->orWhereNull('branch_id');
    //                         });
    //                     }
    //                 })

    //                 // JobCards
    //                 ->orWhereHas('jobcard', function ($query) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter, $vehicleNumber) {
    //                     $query->where('isDeleted', 0);

    //                     if (!empty($customerId) && $customerId !== 'all') {
    //                         $query->where('customer_id', $customerId);
    //                     }

    //                     if (!empty($vehicleNumber)) {
    //                         $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
    //                     }

    //                     if ($applyMonthFilter) {
    //                         $query->whereIn(\DB::raw('MONTH(date)'), $monthsForQuery);
    //                     }

    //                     if (!empty($year)) {
    //                         $query->whereYear('date', $year);
    //                     }

    //                     if (!empty($branchId)) {
    //                         $query->where('subbranch_id', $branchId);
    //                     }
    //                 });
    //             });

    //             $paidPayments = $paidPaymentsQuery
    //                 ->with([
    //                     'order:id,order_number,total_amount,date,type',
    //                     'jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no'
    //                 ])
    //                 ->get();
    //                                 $pendingOrders = Order::query()
    //                     ->where('isDeleted', 0)
    //                     ->where('remaining_amount', '>', 0)
    //                     ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('user_id', $customerId))
    //                     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //                     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //                     ->when($branchId, fn($q) => $q->where(function ($q2) use ($branchId) {
    //                         $q2->where('branch_id', $branchId)->orWhereNull('branch_id');
    //                     }))
    //                     ->get([
    //                         'id',
    //                         'order_number as job_card_number',
    //                         'total_amount as total_subtotal_amount',
    //                         'remaining_amount',
    //                         'date',
    //                         'type'
    //                     ]);


    //                 // ---------------- PENDING JOBCARDS ----------------
    //                 $pendingJobCards = JobCard::query()
    //                     ->where('isDeleted', 0)
    //                     ->where('remaining_amount', '>', 0)
    //                     ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('customer_id', $customerId))
    //                     ->when(!empty($vehicleNumber), fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
    //                     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //                     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //                     ->when($branchId, fn($q) => $q->where(function ($q2) use ($branchId) {
    //                         $q2->where('subbranch_id', $branchId)->orWhereNull('subbranch_id');
    //                     }))
    //                     ->get([
    //                         'id',
    //                         'job_card_number',
    //                         'total_subtotal_amount',
    //                         'remaining_amount',
    //                         'vehicle_no',
    //                         'date',
    //                         'type'
    //                     ]);


    //                 // ✅ FINAL MERGE (IMPORTANT)
    //                 $pendingPayments = $pendingOrders->concat($pendingJobCards);
    //             } else {
    //             if ($vendorId === 'all')
    //                 $vendorId = null;

    //             // Paid payments: purchases with at least one payment
    //             // $paidPayments = PurchaseAutoCare::where('isDeleted', 0)
    //             //     ->with([
    //             //         'vendor',
    //             //         'payments' => function ($q) {
    //             //             $q->where('isDeleted', 0);
    //             //         }
    //             //     ])
    //             //     ->whereHas('payments', function ($q) {
    //             //         $q->where('isDeleted', 0);
    //             //     })
    //             //     ->when(!empty($vendorId), fn($q) => $q->where('vendor_id', $vendorId))
    //             //     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //             //     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //             //     ->when(!empty($branchId), fn($q) => $q->where('sub_admin_id', $branchId))
    //             //     ->get()
    //             //     ->map(function ($purchase) {
    //             //         $purchase->total_paid_amount = $purchase->payments->sum('payment_amount');
    //             //         return $purchase;
    //             //     });

    //             // // Pending payments: purchases with remaining_amount > 0
    //             // $pendingPayments = PurchaseAutoCare::where('isDeleted', 0)
    //             //     ->where('remaining_amount', '>', 0)
    //             //     ->with('vendor')
    //             //     ->when(!empty($vendorId), fn($q) => $q->where('vendor_id', $vendorId))
    //             //     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //             //     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //             //     ->when(!empty($branchId), fn($q) => $q->where('sub_admin_id', $branchId))
    //             //     ->get();
    //             // ---------------- VENDOR PAID ----------------
    //                 $paidPayments = PurchaseInvoice::query()
    //                     ->where('isDeleted', 0)
    //                     ->where('paid', '>', 0)
    //                     ->with('vendor')
    //                     ->when(!empty($vendorId), fn($q) => $q->where('vendor_id', $vendorId))
    //                     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //                     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //                     ->when(!empty($branchId), function ($q) use ($branchId) {
    //                         $q->where(function ($q2) use ($branchId) {
    //                             $q2->where('branch_id', $branchId)
    //                             ->orWhereNull('branch_id');
    //                         });
    //                     })
    //                     ->get()
    //                     ->map(function ($invoice) {
    //                         return (object)[
    //                             'invoice_number' => $invoice->invoice_number,
    //                             'total_amount' => $invoice->total_amount,
    //                             'paid_amount' => $invoice->paid,
    //                             'remaining_amount' => $invoice->remaining_amount,
    //                             'date' => $invoice->created_at,
    //                             'vendor' => $invoice->vendor
    //                         ];
    //                     });


    //                 // ---------------- VENDOR PENDING ----------------
    //                 $pendingPayments = PurchaseInvoice::query()
    //                     ->where('isDeleted', 0)
    //                     ->where('remaining_amount', '>', 0)
    //                     ->with('vendor')
    //                     ->when(!empty($vendorId), fn($q) => $q->where('vendor_id', $vendorId))
    //                     ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
    //                     ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
    //                     ->when(!empty($branchId), function ($q) use ($branchId) {
    //                         $q->where(function ($q2) use ($branchId) {
    //                             $q2->where('branch_id', $branchId)
    //                             ->orWhereNull('branch_id');
    //                         });
    //                     })
    //                     ->get();
    //         }
    //         $paidCount = $paidPayments->count();
    //         $pendingCount = $pendingPayments->count();
    //         // dd($paidCount, $pendingCount);
    //         $totalCount = $paidCount + $pendingCount;
    //         if ($totalCount > 1000) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Total records (paid + pending) exceed 1000. Please refine your filter.'
    //             ], 400);
    //         }

    //         $pdf = Pdf::loadView('account_ledger.pdf', [
    //             'type' => $type,
    //             'paidPayments' => $paidPayments,
    //             'pendingPayments' => $pendingPayments,
    //             'subAdminId' => $branchId,
    //             'settings' => $settings,
    //             'customerName' => $customerName ?? $vendorName,
    //             'months' => $monthsForView, // Use the converted months for view
    //             'year' => $year,
    //             'vehicleNumber' => $vehicleNumber,
    //             'customerGST' => $customerGST
    //         ]);

    //         $filename = 'account_ledger_' . preg_replace('/[^a-zA-Z0-9]/', '_', $customerName ?? $vendorName ?? 'account') . '.pdf';
    //         return $pdf->download($filename);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile()
    //         ], 500);
    //     }
    // }
        public function downloadAccountLedgerPDF(Request $request)
        {
            try {
                $type = $request->input('type', 'customer');
                $customerId = $request->input('id');
                $customerName = $request->input('name');
                $vendorId = $request->input('id');
                $vendorName = $request->input('name');
                $months = $request->input('month', []);
                $year = $request->input('year');
                $vehicleNumber = trim($request->input('vehicle_number'));
                $user = Auth::guard('api')->user();
                $selectedSubAdminId = $request->input('selectedSubAdminId');
                $branchId = !empty($selectedSubAdminId) ? $selectedSubAdminId : ($user->role === 'sub-admin' ? $user->id : 1);
                // dd( $selectedSubAdminId,$branchId);
                // ✅ Month logic
                if ($months === 'all' || (is_array($months) && in_array('all', $months))) {
                    $applyMonthFilter = false;
                    $monthsForQuery = range(1, 12);
                } else {
                    $applyMonthFilter = !empty($months);
                    $monthsForQuery = (array) $months;
                }

                $monthsForView = $applyMonthFilter ? $monthsForQuery : range(1, 12);

                // ✅ Settings
                $settings = Setting::where('branch_id', $branchId)->first() ?? Setting::first();

                // =========================================================
                // ================= CUSTOMER LOGIC =========================
                // =========================================================
                if ($type === 'customer') {

                    // ---------------- PAID ----------------
                    $paidPayments = PaymentStore::query()
                        ->where(function ($q) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter, $vehicleNumber) {

                            // ORDERS
                            $q->whereHas('order', function ($query) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter) {
                                $query->where('isDeleted', 0);

                                if (!empty($customerId) && $customerId !== 'all') {
                                    $query->where('user_id', $customerId);
                                }

                                if ($applyMonthFilter) {
                                    $query->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery);
                                }

                                if (!empty($year)) {
                                    $query->whereYear('created_at', $year);
                                }

                                // ✅ STRICT BRANCH FILTER
                                if (!empty($branchId)) {
                                    $query->where('branch_id', $branchId);
                                }
                            })

                            // JOBCARDS
                            ->orWhereHas('jobcard', function ($query) use ($customerId, $monthsForQuery, $year, $branchId, $applyMonthFilter, $vehicleNumber) {
                                $query->where('isDeleted', 0);

                                if (!empty($customerId) && $customerId !== 'all') {
                                    $query->where('customer_id', $customerId);
                                }

                                if (!empty($vehicleNumber)) {
                                    $query->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]);
                                }

                                if ($applyMonthFilter) {
                                    $query->whereIn(\DB::raw('MONTH(date)'), $monthsForQuery);
                                }

                                if (!empty($year)) {
                                    $query->whereYear('date', $year);
                                }

                                // ✅ STRICT BRANCH FILTER
                                if (!empty($branchId)) {
                                    $query->where('subbranch_id', $branchId);
                                }
                            });
                        })
                        ->with([
                            'order:id,order_number,total_amount,date,type',
                            'jobcard:id,job_card_number,total_subtotal_amount,date,type,vehicle_no'
                        ])
                        ->get();


                    // ---------------- PENDING ORDERS ----------------
                    $pendingOrders = Order::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0)
                        ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('user_id', $customerId))
                        ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
                        ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                        ->when($branchId, function ($q) use ($branchId) {
                            return $q->where(function ($sub) use ($branchId) {
                                $sub->where('branch_id', $branchId)
                                    ->orWhereNull('branch_id');
                            });
                        })
                        ->get([
                            'id',
                            'order_number as job_card_number',
                            'total_amount as total_subtotal_amount',
                            'remaining_amount',
                            'date',
                            'type'
                        ]);

                    // ---------------- PENDING JOBCARDS ----------------
                    $convertedJobCardNumbers = JobCard::where('type', 'dailyData')
                        ->pluck('job_card_number')
                        ->toArray();

                    $pendingJobCards = JobCard::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0)
                        ->where('type', 'jobCard')
                        ->whereNotIn('job_card_number', $convertedJobCardNumbers)
                        ->when($customerId !== 'all' && $customerId, fn($q) => $q->where('customer_id', $customerId))
                        ->when(!empty($vehicleNumber), fn($q) => $q->whereRaw('LOWER(vehicle_no) = ?', [strtolower($vehicleNumber)]))
                        ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
                        ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                        ->when($branchId, function ($q) use ($branchId) {
                            return $q->where(function ($sub) use ($branchId) {
                                $sub->where('subbranch_id', $branchId)
                                    ->orWhereNull('subbranch_id');
                            });
                        })
                        ->get([
                            'id',
                            'job_card_number',
                            'total_subtotal_amount',
                            'remaining_amount',
                            'vehicle_no',
                            'date',
                            'type'
                        ]);

                    $pendingPayments = $pendingOrders->concat($pendingJobCards);
                }

                // =========================================================
                // ================= VENDOR LOGIC ===========================
                // =========================================================
                else {

                    if ($vendorId === 'all') {
                        $vendorId = null;
                    }

                    // ---------------- PAID ----------------
                    $paidPayments = PurchaseInvoice::query()
                        ->where('isDeleted', 0)
                        ->where('paid', '>', 0)
                        ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
                        ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
                        ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId)) // ✅ FIXED
                        ->with('vendor')
                        ->get()
                        ->map(function ($invoice) {
                            return (object)[
                                'invoice_number' => $invoice->invoice_number,
                                'total_amount' => $invoice->total_amount,
                                'paid_amount' => $invoice->paid,
                                'remaining_amount' => $invoice->remaining_amount,
                                'date' => $invoice->created_at,
                                'vendor' => $invoice->vendor
                            ];
                        });

                    // ---------------- PENDING ----------------
                    $pendingPayments = PurchaseInvoice::query()
                        ->where('isDeleted', 0)
                        ->where('remaining_amount', '>', 0)
                        ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
                        ->when($applyMonthFilter, fn($q) => $q->whereIn(\DB::raw('MONTH(created_at)'), $monthsForQuery))
                        ->when(!$applyMonthFilter && !empty($year), fn($q) => $q->whereYear('created_at', $year))
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId)) // ✅ FIXED
                        ->with('vendor')
                        ->get();
                }

                // ---------------- LIMIT CHECK ----------------
                if (($paidPayments->count() + $pendingPayments->count()) > 1000) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many records. Please filter.'
                    ], 400);
                }

                // ---------------- PDF ----------------
                $pdf = Pdf::loadView('account_ledger.pdf', [
                    'type' => $type,
                    'paidPayments' => $paidPayments,
                    'pendingPayments' => $pendingPayments,
                    'subAdminId' => $branchId,
                    'settings' => $settings,
                    'customerName' => $customerName ?? $vendorName,
                    'months' => $monthsForView,
                    'year' => $year,
                    'vehicleNumber' => $vehicleNumber
                ]);

                return $pdf->download('account_ledger.pdf');

            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }
