<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\JobCard;
use App\Models\Product;
use App\Models\AutoCarModel;
use App\Models\ExpenseStore;
use App\Models\PaymentStore;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class DailyDataController extends Controller
{

    // public function jobcards_get(Request $request)
    // {
    //     $user = Auth::guard('api')->user();

    //     $BranchId = null;
    //     if ($request->hasHeader('X-Selected-SubAdmin-Id')) {
    //         $BranchId = $request->header('X-Selected-SubAdmin-Id');
    //     } elseif ($request->filled('selectedSubAdminId') && $request->input('selectedSubAdminId') !== "null") {
    //         $BranchId = $request->input('selectedSubAdminId');
    //     } else {
    //         $BranchId = $user->id;
    //     }

    //     if (!empty($BranchId)) {
    //         $branchIdToFilter = $BranchId;
    //     } elseif (!empty($user->branch_id)) {
    //         $branchIdToFilter = $user->branch_id;
    //     } else {
    //         $branchIdToFilter = $user->id; // main admin case
    //     }

    //     $jobcards = JobCard::with('customer:id,name') // eager load only id + name
    //         ->select('id', 'job_card_number', 'customer_id', 'subbranch_id')
    //         ->where('remaining_amount', '>', 0)
    //         ->where('type', '=', 'jobCard')
    //         ->where('isDeleted', 0)
    //         ->where('subbranch_id', $branchIdToFilter)
    //         ->get()
    //         ->map(function ($jobcard) {
    //             return [
    //                 'id' => $jobcard->id,
    //                 'job_card_number' => $jobcard->job_card_number,
    //                 'customer_id' => $jobcard->customer_id,
    //                 'customer_name' => $jobcard->customer->name ?? 'Unknown',
    //             ];
    //         });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $jobcards
    //     ]);
    // }
    public function jobcards_get(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (strtolower($user->role) == 'staff') {

            $jobcards = JobCard::with('customer:id,name')
                ->select('id', 'job_card_number', 'customer_id', 'user_id')
                ->where('remaining_amount', '>', 0)
                ->where('type', 'jobCard')
                ->where('isDeleted', 0)
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($jobcard) {
                    return [
                        'id' => $jobcard->id,
                        'job_card_number' => $jobcard->job_card_number,
                        'customer_id' => $jobcard->customer_id,
                        'customer_name' => $jobcard->customer->name ?? 'Unknown',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $jobcards
            ]);
        }

        $BranchId = null;

        if ($request->hasHeader('X-Selected-SubAdmin-Id') && $request->header('X-Selected-SubAdmin-Id') !== "null") {
            $BranchId = $request->header('X-Selected-SubAdmin-Id');
        } elseif ($request->filled('selectedSubAdminId') && $request->input('selectedSubAdminId') !== "null") {
            $BranchId = $request->input('selectedSubAdminId');
        }

        if (!empty($BranchId)) {
            $branchIdToFilter = $BranchId;
        } elseif (!empty($user->branch_id)) {
            $branchIdToFilter = $user->branch_id;
        } else {
            $branchIdToFilter = $user->id;
        }

        $jobcards = JobCard::with('customer:id,name')
            ->select('id', 'job_card_number', 'customer_id', 'subbranch_id')
            ->where('remaining_amount', '>', 0)
            ->where('type', 'jobCard')
            ->where('isDeleted', 0)
            ->where('subbranch_id', $branchIdToFilter)
            ->get()
            ->map(function ($jobcard) {
                return [
                    'id' => $jobcard->id,
                    'job_card_number' => $jobcard->job_card_number,
                    'customer_id' => $jobcard->customer_id,
                    'customer_name' => $jobcard->customer->name ?? 'Unknown',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $jobcards
        ]);
    }

    public function jobcard_details($id)
    {
        $jobcard = JobCard::with('customer')->find($id); // Include relationships if needed

        if (!$jobcard) {
            return response()->json([
                'success' => false,
                'message' => 'Job Card not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'technician' => $jobcard->technician ?? '',
                'customer_name' => $jobcard->customer_id ?? '',
                'vehicle_no'    => $jobcard->vehicle_no ?? '',
                'model'    => $jobcard->model ?? '',
                'total_amount'  => $jobcard->total_subtotal_amount ?? 0,
                'comment'       => $jobcard->comment ?? '',
                'remaining_amount'       => $jobcard->remaining_amount ?? '',
                // add other fields as needed
            ],
        ]);
    }




    public function add_daily_data(Request $request)
    {


        $user = Auth::guard('api')->user();

        $subAdminId = session('selectedSubAdminId');

        $rules = [
            'expense_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'vehicle_no' => 'nullable|string|max:255',
            'technician_name' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:Pending,Completed',
            'name' => 'nullable|array',
            'name.*' => 'nullable|string',
            'price' => 'nullable|array',
            'price.*' => 'nullable|numeric|min:0',
            'total' => 'nullable|array',
            'total.*' => 'nullable|numeric|min:0',
            'payable_amount' => 'required|numeric|min:0',
            'paid_type' => 'required_unless:payment_mode,emi,pending',
            'payment_mode' => 'required|in:cash,online,cashonline,emi,pending',
        ];

        // Step 2: Add phone rule conditionally
        if (!is_numeric($request->customer_name)) {
            // New customer → unique phone required
            $rules['phone'] = 'nullable|string|max:15|unique:users,phone';
        } else {
            // Existing customer → phone can match their existing one
            $rules['phone'] = 'nullable|string|max:15';
        }

        $validator = Validator::make($request->all(), $rules, [
            'expense_date.required' => 'The expense date field is required.',
            'payable_amount.required' => 'The payment amount field is required.',
            'paid_type.required' => 'The payment type field is required.',
            'expense_date.date' => 'The expense date must be a valid date.',
            'customer_name.required' => 'The customer name field is required.',
            'phone.required' => 'The phone field is required.',
            'model.required' => 'The model field is required.',
            'vehicle_no.required' => 'The vehicle no. field is required.',
            'technician_name.required' => 'The technician name field is required.',
            'technician_name.exists' => 'The selected technician is invalid.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date field is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Pending or Completed.',
            'payment_mode.required' => 'The payment mode field is required.',
            'payment_mode.in' => 'The payment mode must be cash or online.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        // dd($request->all());
        //  dd($request->all());

        $BranchId = null;
        if ($request->input('selectedSubAdminId') !== "null") {
            $BranchId = $request->input('selectedSubAdminId');
        } else {
            $BranchId = $user->id;
        }




        if (!is_numeric($request->customer_name) && !empty($request->customer_name)) {

            // Create new customer
            $vendor = User::create([
                'name' => $request->customer_name,
                'phone' => $request->phone ?? null, // You had $request->customer_phone but you posted "phone"
                'role' => 'customer',
                'status' => 1,
                'branch_id' => $BranchId,
            ]);

            $customer_id = $vendor->id;

            // Create UserDetail
            UserDetail::create([
                'user_id' => $customer_id,
                'address' => '',
                'city' => '',
                'country' => '',
            ]);
        } elseif (is_numeric($request->customer_name)) {
            // Use existing customer
            $customer_id = $request->customer_name;
        } else {
            // Optional fallback if empty
            $customer_id = null;
        }



        $expenseDate = Carbon::createFromFormat('d-m-Y', $request->expense_date, 'Asia/Kolkata')
            ->setTimeFromTimeString(now('Asia/Kolkata')->format('H:i:s')) // attach current IST time
            ->format('Y-m-d H:i:s');

        $jobCardNumber = random_int(100000, 999999);

        $cashAmount = $request->input('cash_amount');
        $onlineAmount = $request->input('upi_amount');
        $discountAmount   = (float) $request->input('discount_amount') ?? 0;
        $originalPayable  = (float) $request->input('payable_amount') ?? 0;
        $effectivePayable = $originalPayable - $discountAmount;

        // If payment is FULL
        if ($request->payment_mode === 'cash' && $request->paid_type === 'full') {
            $cashAmount   = $effectivePayable;
            $onlineAmount = 0;
        } elseif ($request->payment_mode === 'online' && $request->paid_type === 'full') {
            $cashAmount   = 0;
            $onlineAmount = $effectivePayable;
        } elseif ($request->payment_mode === 'cashonline' && $request->paid_type === 'full') {
            // Use entered split (must equal effective payable, validation done before)
            $cashAmount   = $request->input('cash_amount');
            $onlineAmount = $request->input('upi_amount');
        }


        $remainingAmount = null;

        // 🔹 For EMI
        if ($request->payment_mode != 'pending') {
            if ($request->payment_mode === 'emi') {
                $monthlyAmount   = (float) $request->calculated_emi;
                $remainingAmount = $effectivePayable - $monthlyAmount; // after 1st EMI
            } else {
                $remainingAmount = $request->input('pending_amount');
            }
        } else {
            $remainingAmount = $effectivePayable;
        }

        if ($request->type_select === 'jobcard' && !empty($request->jobcard_select)) {
            // Find the existing jobcard
            $existingJobCard = JobCard::where('id', $request->jobcard_select)
                ->where('isDeleted', 0)
                ->first();

            if ($existingJobCard) {
                // Update remaining amount
                $existingJobCard->remaining_amount = $request->pending_amount;
                $existingJobCard->save();
            }
        }


        // Store customer record
        $jobcard = JobCard::create([
            'record_date' => $expenseDate,
            'date' => $expenseDate,
            'customer_id' => $customer_id,
            'mobile' => $request->phone,
            'model' => $request->model,
            'vehicle_no' => $request->vehicle_no,
            'technician' => $request->technician_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'payment_method' => $request->payment_mode,
            'payment_type' => $request->payment_mode === 'emi' ? 'partial' : ($request->paid_type ?? 'partial'),
            'cash_amount' => $cashAmount ?? $request->amount,
            'online_amount' => $onlineAmount,
            'remaining_amount' => $remainingAmount,
            'total_subtotal_amount' => $request->input('payable_amount'),
            'discount'       => $discountAmount,
            'subbranch_id' => $user->branch_id ?? $BranchId,
            'job_card_number' => $jobCardNumber,
            'type' => 'dailyData',
            'user_id' => $user->id,
            'comment' => $request->comment,
            'isDeleted' => '0',
            'extra_pay' => $request->extra_pay ?? 0,
            'old_job_card' => $request->jobcard_select,
            'emi_duration' => $request->payment_mode === 'emi' ? $request->emi_duration : null,
            'emi_months' => $request->payment_mode === 'emi' ? $request->calculated_emi : null,
        ]);


        if ($request->payment_mode != 'pending') {
            if ($request->payment_mode === 'cashonline') {
                // Insert cash entry
                PaymentStore::create([
                    'user_id' => $customer_id,
                    'jobcard_id' => $jobcard->id,
                    'payment_amount' => $cashAmount ?? 0,
                    'payment_date' => $expenseDate,
                    'payment_method' => 'cash',
                    'discount_amount' => $discountAmount,
                    'isDeleted' => '0',
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]);

                // Insert online entry
                PaymentStore::create([
                    'user_id' => $customer_id,
                    'jobcard_id' => $jobcard->id,
                    'payment_amount' => $onlineAmount ?? 0,
                    'payment_date' => $expenseDate,
                    'payment_method' => 'online',
                    'isDeleted' => '0',
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]);
            } elseif ($request->payment_mode === 'emi') {
                // ✅ Store EMI entry
                $emiDuration   = (int) $request->emi_duration;
                $monthlyAmount = (float) $request->calculated_emi;

                // 🔹 First EMI is auto-paid
                PaymentStore::create([
                    'user_id'        => $customer_id,
                    'jobcard_id'     => $jobcard->id,
                    'payment_amount' => $monthlyAmount,
                    'payment_date'   => $expenseDate,
                    'payment_method' => 'emi',
                    'discount_amount' => $discountAmount,
                    'emi_month'     => 1,              // ✅ first month paid
                    'isDeleted'      => '0',
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]);
            } else {
                // Single payment entry for cash OR online
                PaymentStore::create([
                    'user_id' => $customer_id,
                    'jobcard_id' => $jobcard->id,
                    'payment_amount' => ($cashAmount ?? 0) + ($onlineAmount ?? 0) ?: $request->amount,
                    'payment_date' => $expenseDate,
                    'payment_method' => $request->payment_mode,
                    'discount_amount' => $discountAmount,
                    'isDeleted' => '0',
                    'created_at' => $expenseDate,
                    'updated_at' => $expenseDate,
                ]);
            }
        }

        // Store item records
        $itemNames = $request->name;
        $prices = $request->price;
        $vendorNames = $request->vendor_name;
        $paymentMethods = $request->payment_mode_expense;

        if (!empty($itemNames) && is_array($itemNames) && array_filter($itemNames)) {
            foreach ($itemNames as $index => $itemName) {
                ExpenseStore::create([
                    'user_id' => $user->id,
                    'jobcard_id' => $jobcard->id,
                    'items' => $itemName,
                    'amount' => $prices[$index] ?? 0,
                    'vendor_name' => is_array($vendorNames) ? ($vendorNames[$index] ?? null) : $vendorNames,
                    'payment_method' => is_array($paymentMethods) ? ($paymentMethods[$index] ?? null) : $paymentMethods,
                    'isDeleted' => '0'
                ]);
            }
        }

        return response()->json(['status' => true, 'data' => "Daily Data Added Successfully"], 200);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'jobcard_id' => 'required|exists:job_cards,id',
            'record_date' => 'nullable|date',
            'customer_name' => 'required|string',
            'phone' => 'nullable|string',
            'vehicle_no' => 'nullable|string',
            'technician_name' => 'nullable|string',
            'status' => 'nullable|string',
            'payable_amount' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ]);

        $jobcard = JobCard::find($request->jobcard_id);

        // Store previous payable amount
        if ($request->filled('payable_amount')) {
            $oldPayableAmount = $jobcard->total_subtotal_amount ?? 0;
            $newPayableAmount = $request->payable_amount;

            $difference = $newPayableAmount - $oldPayableAmount;

            // Adjust remaining amount only if there's a difference
            if ($difference !== 0) {
                $jobcard->remaining_amount = ($jobcard->remaining_amount ?? 0) + $difference;

                // If this dailyData is linked to an original job card (old_job_card),
                // also adjust that job card's remaining amount when appropriate.
                if (!empty($jobcard->old_job_card)) {
                    $linked = JobCard::find($jobcard->old_job_card);
                    if ($linked && ($linked->type ?? '') === 'jobCard') {
                        $linked->remaining_amount = ($linked->remaining_amount ?? 0) + $difference;
                        // ensure not negative
                        if ($linked->remaining_amount < 0) $linked->remaining_amount = 0;
                        $linked->save();
                    }
                }
            }
        }

        // Now update the rest of the fields
        // $jobcard->record_date = $request->record_date;
        $jobcard->customer_name = $request->customer_name;
        $jobcard->mobile = $request->phone;
        $jobcard->vehicle_no = $request->vehicle_no;
        $jobcard->technician = $request->technician_name;
        $jobcard->status = $request->status;
        $jobcard->extra_pay = $request->extra_pay;
        if ($request->filled('payable_amount')) {
            $jobcard->total_subtotal_amount = $newPayableAmount;
        }
        $jobcard->comment = $request->comment;

        $jobcard->save();

        return response()->json(['success' => true, 'message' => 'Jobcard updated successfully.']);
    }


    public function apiIndex(Request $request)
    {
        $user = Auth::guard('api')->user();
        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');

        $query = JobCard::with([
            'autocarmodel',
            'payments' => function ($query) {
                $query->where('isDeleted', 0);
            },
            'technicianUser',
            'expenseStores' => function ($query) {
                $query->where('isDeleted', 0);
            }
        ])
            ->leftJoin('users', 'users.id', '=', 'job_cards.customer_id')
            ->where('job_cards.type', 'dailyData')
            ->where('job_cards.isDeleted', 0);

        // ✅ Apply branch/sub-admin filter if given
        if ($request->has('sub_admin_id') && !empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $query->where('job_cards.subbranch_id', $request->sub_admin_id);
        } elseif (strtolower($user->role) == 'staff') {
            $query->where('job_cards.user_id', $user->id);
        } else {
            $query->where('job_cards.subbranch_id', $user->id);
        }

        // ✅ Apply filters
        if ($request->filled('year')) {
            $query->whereYear('job_cards.record_date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('job_cards.record_date', $request->month);
        }

        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->date)->format('Y-m-d');
                $query->whereDate('job_cards.record_date', $date);
            } catch (\Exception $e) {
                // Invalid date format
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('job_cards.mobile', 'like', '%' . $search . '%')
                    ->orWhere('job_cards.vehicle_no', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('customer_id')) {
            $query->where('job_cards.customer_id', $request->customer_id);
        }

        $paymentTotals = PaymentStore::select(
            'jobcard_id',
            DB::raw('SUM(payment_amount) as total_received')
        )
            ->where('isDeleted', 0)
            ->groupBy('jobcard_id');

        $summaryRow = (clone $query)
            ->leftJoinSub($paymentTotals, 'payment_totals', function ($join) {
                $join->on('payment_totals.jobcard_id', '=', 'job_cards.id');
            })
            ->selectRaw('COALESCE(SUM(job_cards.total_subtotal_amount), 0) as total_amount')
            ->selectRaw('COALESCE(SUM(COALESCE(payment_totals.total_received, 0)), 0) as total_received')
            ->selectRaw('COALESCE(SUM(job_cards.discount), 0) as total_discount')
            ->first();

        $total = (clone $query)->count();

        $jobcardsQuery = $query->orderBy('job_cards.record_date', 'desc')
            ->select('job_cards.*', 'users.name as customer_name');

        if ($applyPagination) {
            $offset = ($page - 1) * $perPage;
            $jobcards = $jobcardsQuery->skip($offset)->take($perPage)->get();
        } else {
            $jobcards = $jobcardsQuery->get();
        }

        $totalAmount = (float) ($summaryRow->total_amount ?? 0);
        $totalReceived = (float) ($summaryRow->total_received ?? 0);
        $totalDiscount = (float) ($summaryRow->total_discount ?? 0);

        $response = [
            'status' => true,
            'data' => $jobcards,
            'summary' => [
                'total_amount' => $totalAmount,
                'total_received' => $totalReceived,
                'total_discount' => $totalDiscount,
                'total_pending' => $totalAmount - $totalReceived - $totalDiscount,
            ],
        ];

        if ($applyPagination) {
            $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
            $response['pagination'] = [
                'current_page' => $page,
                'last_page'    => max(1, $lastPage),
                'per_page'     => $perPage,
                'total'        => $total,
            ];
        }

        return response()->json($response);
    }

    public function makePayment(Request $request, $id)
    {
        // dd($request->all());
        // Validate request
        $request->validate([
            'payment_amount'   => 'required|numeric|min:1',
            'payment_mode'     => 'required|string',
            'emi_months'       => 'nullable|integer',
            'discount_amount'  => 'nullable|numeric',
            'old_job_card'     => 'nullable|integer',
            'remark'           => 'nullable|string',
        ]);

        $user = Auth::guard('api')->user();
        $paymentAmount  = (float) $request->payment_amount;
        $discountAmount = (float) $request->discount_amount;

        $tz        = 'Asia/Kolkata';
        $yesterday = Carbon::now($tz)->subDay();

        // 🔹 First, update old job card (if exists)
        if (!empty($request->old_job_card)) {
            $oldJob = JobCard::findOrFail($request->old_job_card);

            if ($discountAmount > 0) {
                $oldJob->discount = ($oldJob->discount ?? 0) + $discountAmount;
                $oldJob->remaining_amount = max($oldJob->remaining_amount - $discountAmount, 0);
            }

            $oldJob->remaining_amount = max($oldJob->remaining_amount - $paymentAmount, 0);
            $oldJob->payment_type     = ($oldJob->remaining_amount <= 0) ? 'full' : 'partial';
            $oldJob->save();
        }

        // 🔹 Update current job card
        $jobcard = JobCard::findOrFail($id);

        if ($discountAmount > 0) {
            $jobcard->discount = ($jobcard->discount ?? 0) + $discountAmount;
            $jobcard->remaining_amount = max($jobcard->remaining_amount - $discountAmount, 0);
        }

        $jobcard->remaining_amount = max($jobcard->remaining_amount - $paymentAmount, 0);
        $jobcard->payment_type     = ($jobcard->remaining_amount <= 0) ? 'full' : 'partial';
        $jobcard->save();

        // 🔹 Determine payment_type (fully / partially) from payment_mode
        $paymentMode = $request->payment_mode; // cash, online, cash_online, emi, emifully
        $paymentType = ($jobcard->remaining_amount <= 0) ? 'fully' : 'partially';

        // 🔹 Determine cash_amount and upi_amount based on payment mode
        $cashAmount = 0;
        $upiAmount  = 0;

        if ($paymentMode === 'cash') {
            $cashAmount = $paymentAmount;
            $upiAmount  = 0;
        } elseif ($paymentMode === 'online') {
            $cashAmount = 0;
            $upiAmount  = $paymentAmount;
        } elseif ($paymentMode === 'cash_online') {
            // Frontend sends fully_cash_amount / cash_amount for cash portion
            $cashAmount = (float) ($request->fully_cash_amount ?? $request->cash_amount ?? 0) ?: 0;
            $upiAmount  = (float) ($request->full_online_amount ?? $request->online_amount ?? 0) ?: 0;
        }

        // 🔹 Now create **one** PaymentStore record (only for current jobcard)
        $paymentData = [
            'user_id'          => $user->id,
            'jobcard_id'       => $jobcard->id,
            'payment_amount'   => $paymentAmount,
            'payment_date'     => $yesterday,
            'payment_method'   => $paymentMode,
            'payment_type'     => $paymentType,
            'cash_amount'      => $cashAmount,
            'upi_amount'       => $upiAmount,
            'remaining_amount' => $jobcard->remaining_amount,
            'discount_amount'  => $discountAmount,
            'remark'           => $request->remark,
            'isDeleted'        => 0,
            'created_at'       => $yesterday,
            'updated_at'       => $yesterday,
        ];

        if (in_array($paymentMode, ['emi', 'emifully'])) {
            $paymentData['emi_month']      = $request->emi_months ?? 0;
            $paymentData['payment_method'] = 'emi'; // normalize
        }

        PaymentStore::create($paymentData);

        return response()->json([
            'status'  => true,
            'message' => 'Payment recorded successfully.',
        ]);
    }

    public function getPaymentHistory($id)
    {
        $jobcard = JobCard::findOrFail($id);

        $payments = PaymentStore::where('jobcard_id', $id)
            ->where('isDeleted', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = $payments->map(function ($p) {
            return [
                'id'             => $p->id,
                'payment_amount' => $p->payment_amount,
                'payment_method' => $p->payment_method,
                'payment_date'   => Carbon::parse($p->created_at)->format('d M Y, h:i A'),
                'emi_month'      => $p->emi_month,
                'payment_type'   => $p->payment_type,
                'cash_amount'    => $p->cash_amount,
                'upi_amount'     => $p->upi_amount,
                'discount_amount'=> $p->discount_amount,
                'remark'         => $p->remark,
            ];
        });

        // Compute summary values
        $order_total = (float)$jobcard->total_subtotal_amount;
        $total_paid = (float)$payments->sum('payment_amount');
        $discount = (float)$jobcard->discount;
        $remaining = (float)$jobcard->remaining_amount;

        $summary = [
            'order_total' => $order_total,
            'total_paid'  => $total_paid,
            'discount'    => $discount,
            'remaining'   => $remaining,
        ];

        return response()->json([
            'status' => true,
            'data' => $data,
            'summary' => $summary
        ]);
    }

    public function updatePaymentHistory(Request $request, $id)
    {
        $request->validate([
            'payment_amount'   => 'required|numeric|min:0',
            'payment_method'   => 'required|string',
            'emi_months'       => 'nullable|integer',
            'discount_amount'  => 'nullable|numeric',
            'remark'           => 'nullable|string',
        ]);

        $payment = PaymentStore::findOrFail($id);
        $jobcard = JobCard::findOrFail($payment->jobcard_id);

        $oldAmount = (float)$payment->payment_amount;
        $newAmount = (float)$request->payment_amount;

        $oldDiscount = (float)$payment->discount_amount;
        $newDiscount = (float)$request->discount_amount;

        // Revert old payment and discount on JobCard first, then apply new ones
        $baseRemaining = $jobcard->remaining_amount + $oldAmount + $oldDiscount;
        $newRemaining = $baseRemaining - $newAmount - $newDiscount;
        if ($newRemaining < 0) {
            $newRemaining = 0;
        }

        $jobcard->discount = max(($jobcard->discount ?? 0) - $oldDiscount + $newDiscount, 0);
        $jobcard->remaining_amount = $newRemaining;
        $jobcard->payment_type = ($newRemaining <= 0) ? 'full' : 'partial';
        $jobcard->save();

        // Determine cash_amount and upi_amount
        $paymentMode = $request->payment_method;
        $cashAmount = 0;
        $upiAmount  = 0;

        if ($paymentMode === 'cash') {
            $cashAmount = $newAmount;
            $upiAmount  = 0;
        } elseif ($paymentMode === 'online') {
            $cashAmount = 0;
            $upiAmount  = $newAmount;
        } elseif ($paymentMode === 'cash_online') {
            $cashAmount = (float) ($request->fully_cash_amount ?? $request->cash_amount ?? 0) ?: 0;
            $upiAmount  = (float) ($request->full_online_amount ?? $request->online_amount ?? 0) ?: 0;
        }

        // Update payment record
        $payment->payment_amount = $newAmount;
        $payment->payment_method = $paymentMode;
        $payment->payment_type = ($newRemaining <= 0) ? 'fully' : 'partially';
        $payment->cash_amount = $cashAmount;
        $payment->upi_amount = $upiAmount;
        $payment->discount_amount = $newDiscount;
        $payment->remaining_amount = $newRemaining;
        $payment->remark = $request->remark;
        if ($paymentMode === 'emi') {
            $payment->emi_month = $request->emi_months ?? 0;
        }
        $payment->save();

        return response()->json([
            'status' => true,
            'message' => 'Payment updated successfully.',
            'remaining_amount' => $newRemaining,
        ]);
    }

    public function deletePaymentHistory(Request $request, $id)
    {
        $payment = PaymentStore::findOrFail($id);
        $jobcard = JobCard::findOrFail($payment->jobcard_id);

        // Mark payment as deleted
        $payment->isDeleted = 1;
        $payment->save();

        // Add both the deleted payment amount and discount amount back to the jobcard's remaining_amount
        $paymentAmount = (float)$payment->payment_amount;
        $discountAmount = (float)$payment->discount_amount;

        $newRemaining = $jobcard->remaining_amount + $paymentAmount + $discountAmount;
        
        $jobcard->remaining_amount = $newRemaining;
        $jobcard->discount = max(($jobcard->discount ?? 0) - $discountAmount, 0);
        $jobcard->payment_type = ($newRemaining <= 0) ? 'full' : 'partial';
        $jobcard->save();

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully.',
            'remaining_amount' => $newRemaining,
        ]);
    }


    public function destroy($id)
    {
        try {
            // Soft delete job_card
            $jobCard = \App\Models\JobCard::findOrFail($id);
            $jobCard->isDeleted = 1;
            $jobCard->save();

            // Soft delete related expense_store
            \App\Models\ExpenseStore::where('jobcard_id', $id)
                ->update(['isDeleted' => 1]);

            // Soft delete related payment_store
            \App\Models\PaymentStore::where('jobcard_id', $id)
                ->update(['isDeleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Job Card soft deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getTopTechnicians(Request $request)
    {
        $month = $request->month; // format: yyyy-mm
        [$year, $monthNum] = explode('-', $month);

        $branchId = $request->branch_id; // coming from filter (or logged-in user branch)

        $technicians = DB::table('job_cards as jc')
            ->join('users as u', 'u.id', '=', 'jc.technician')
            ->select(
                'jc.technician',
                'u.name as technician_name',
                DB::raw('SUM(jc.total_subtotal_amount) as total_amount')
            )
            ->whereYear('jc.created_at', $year)
            ->whereMonth('jc.created_at', $monthNum)
            ->where('u.role', 'staff') // only staff
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('u.branch_id', $branchId); // branch filter
            })
            ->groupBy('jc.technician', 'u.name')
            ->orderByDesc('total_amount')
            ->take(3)
            ->get();

        return response()->json($technicians);
    }
    public function getAllParts(Request $request)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $query = Product::where('isDeleted', 0)->where('branch_id', $branchId);

        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $parts = $query->limit(50)->get(['id', 'name', 'price']); // only return 50 at a time

        return response()->json([
            'status' => true,
            'data'   => $parts
        ]);
    }
}
