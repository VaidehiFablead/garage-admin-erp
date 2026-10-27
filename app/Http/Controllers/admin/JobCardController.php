<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\JobCard;
use App\Models\AutoCarModel;
use App\Models\AutoCarCategory;
use App\Models\User;
use App\Models\LabourService;
use App\Models\Product;
use App\Models\Setting;
use App\Models\PaymentStore;
use App\Models\UserDetail;
use App\Models\TaxRate;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobCardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id ?? null;
        $selectedSubAdminId = session('selectedSubAdminId');

        // Determine user_id to filter by
        if (strtolower($user->role) == 'staff') {
            $user_id = $user->branch_id;
        }
        $user_id = $selectedSubAdminId ?? $userId;

        // Current year
        $currentYear = now()->year;

        // Base query for job cards
        $jobCardsQuery = JobCard::whereIn('type', ['jobCard', 'quotation'])
            ->where('isDeleted', 0)
            ->with('customer', 'paymentStore')
            ->latest();

        // Apply filters if present in request, else default to current year
        if ($request->filled('year')) {
            $jobCardsQuery->whereYear('date', $request->year);
        } elseif (!$request->has('year') || $request->year != '') {
            // Default to current year if no year filter is explicitly 'All Years' (empty)
            // But wait, the previous code had: ->whereYear('date', $currentYear)
            // If the user wants "All Years", they select "", so request('year') is empty but present.
            if ($request->query('year') === null) {
                $jobCardsQuery->whereYear('date', $currentYear);
            }
        }

        if ($request->filled('month')) {
            $jobCardsQuery->whereMonth('date', $request->month);
        }

        if ($request->filled('customer')) {
            $jobCardsQuery->where('customer_id', $request->customer);
        }

        if ($request->filled('date')) {
            $jobCardsQuery->whereDate('date', Carbon::parse($request->date));
        }




        // Filter by user or subbranch
        if (strtolower($user->role) == 'staff') {
            $jobCardsQuery->where('user_id', $user_id);
        } else {
            $jobCardsQuery->where('subbranch_id', $user_id);
        }

        $jobCards = $jobCardsQuery->get();


        $uniqueCustomers = $jobCards->pluck('customer')->filter()->unique('id')->sortBy('name');
        $totalGrand = $jobCards->sum('total_subtotal_amount');

        // Calculate total labour and total parts
        $totalLabour = 0;
        $totalParts = 0;

        foreach ($jobCards as $jobCard) {
            // Decode labour_details and labour_gst
            $labourDetails = json_decode($jobCard->labour_details, true) ?? [];
            $labourGst     = []; // Labour should never have GST as per user request

            foreach ($labourDetails as $labour) {
                $lineTotal = floatval($labour['price'] ?? 0);

                // Apply GST if available
                if ($labourGst && is_array($labourGst)) {
                    foreach ($labourGst as $tax) {
                        $lineTotal += $lineTotal * (floatval($tax['rate'] ?? 0) / 100);
                    }
                }

                $totalLabour += $lineTotal;
            }

            // Sum parts total as before
            $partsDetails = json_decode($jobCard->parts_details, true) ?? [];
            foreach ($partsDetails as $part) {
                $totalParts += $part['total'] ?? 0;
            }
        }

        // Optionally, format totals
        $totalLabour = number_format($totalLabour, 2, '.', '');
        $totalParts = number_format($totalParts, 2, '.', '');

        return view('job-card.index', compact(
            'jobCards',
            'selectedSubAdminId',
            'uniqueCustomers',
            'totalGrand',
            'totalLabour',
            'totalParts'
        ));
    }


    public function getJobCards(Request $request)
    {
        $query = JobCard::where('type', 'jobCard')
            ->with('customer', 'paymentStore')
            ->where('isDeleted', 0);

        // Optional sub-admin filter (if needed)
        $selectedSubAdminId = session('selectedSubAdminId');
        if ($selectedSubAdminId) {
            $query->where('user_id', $selectedSubAdminId);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by specific date
        if ($request->filled('date')) {
            try {
                $formattedDate = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
                $query->whereDate('created_at', $formattedDate);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format.'], 400);
            }
        }

        // Filter by customer ID
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $jobCards = $query->latest()->get();

        $totalGrand = $jobCards->sum('total_subtotal_amount');

        return response()->json([
            'jobCards' => $jobCards,
            'totalGrand' => number_format($totalGrand, 2)
        ]);
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id ?? null;
        $selectedSubAdminId = session('selectedSubAdminId');

        // Determine branch filter
        if (strtolower($user->role) == 'staff') {
            $user_id = $user->branch_id;
        } else {
            $user_id = $selectedSubAdminId ?? $userId;
        }

        $query = JobCard::with(['customer', 'paymentStore', 'payments'])
            ->whereIn('type', ['jobCard', 'quotation'])
            ->where('isDeleted', 0);

        // Filter by user or subbranch
        if (strtolower($user->role) == 'staff') {
            $query->where('user_id', $user_id);
        } else {
            $query->where('subbranch_id', $user_id);
        }

        // Filter by year
        if ($request->year && $request->year != '') {
            $query->whereYear('date', $request->year);
        }

        // Filter by month
        if ($request->month) {
            $query->whereMonth('date', $request->month);
        }

        // Filter by customer
        if ($request->customer) {
            $query->where('customer_id', $request->customer);
        }

        // Filter by specific date
        if ($request->date) {
            $query->whereDate('date', Carbon::parse($request->date));
        }

        $jobCards = $query->latest()->get();

        $tableHtml = '';
        $modalHtml = '';
        $totals = [
            'labour' => 0,
            'parts' => 0,
            'grand' => 0,
        ];

        foreach ($jobCards as $card) {
            $tableHtml .= view('job-card.partials.row', compact('card'))->render();

            // Calculate totals for this card
            $totalLabour = 0;
            $labourDetails = json_decode($card->labour_details, true);
            $labourGst     = []; // Labour should never have GST as per user request

            if ($labourDetails && is_array($labourDetails)) {
                foreach ($labourDetails as $labour) {
                    $lineTotal = floatval($labour['price'] ?? 0);
                    if ($labourGst && is_array($labourGst)) {
                        foreach ($labourGst as $tax) {
                            $lineTotal += $lineTotal * (floatval($tax['rate'] ?? 0) / 100);
                        }
                    }
                    $totalLabour += $lineTotal;
                }
            }

            $totals['labour'] += floatval($totalLabour);
            $totals['parts'] += floatval($card->total_parts ?? 0);
            $totals['grand'] += floatval($card->total_subtotal_amount ?? 0);

            // Payment history modal
            $modalHtml .= '<div class="modal fade" id="paymentHistoryModal-' . $card->id . '" tabindex="-1"
                                aria-labelledby="paymentHistoryLabel-' . $card->id . '" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="paymentHistoryLabel-' . $card->id . '">
                                                Payment History - ' . ucfirst($card->customer->name ?? '-') . '
                                            </h5>
                                        </div>
                                        <div class="modal-body">';
            if ($card->payments && count($card->payments) > 0) {
                $modalHtml .= '<ul class="list-group">';
                foreach ($card->payments as $payment) {
                    $modalHtml .= '<li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span>' . \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') . '</span>
                                                            <span>
                                                                <strong>' . number_format($payment->payment_amount, 2) . '</strong>
                                                                via ' . ucfirst($payment->payment_method) . '
                                                            </span>
                                                        </li>';
                }
                $modalHtml .= '</ul>';
            } else {
                $modalHtml .= '<p class="text-muted">No payment history available.</p>';
            }
            $modalHtml .= '</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-cancel"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
        }

        // Format totals
        $totals['labour'] = number_format($totals['labour'], 2);
        $totals['parts']  = number_format($totals['parts'], 2);
        $totals['grand']  = number_format($totals['grand'], 2);

        return response()->json([
            'tableHtml' => $tableHtml,
            'modalHtml' => $modalHtml,
            'totals'    => $totals,
        ]);
    }




    public function getNextNumber(Request $request)
    {
        $prefix = $request->query('prefix', 'J'); // Default J for Without GST

        $lastJobCard = JobCard::where('job_card_number', 'LIKE', $prefix . '%')
            ->where('isDeleted', 0)
            ->orderByRaw('CAST(SUBSTRING(job_card_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastJobCard) {
            $lastNumber = (int) substr($lastJobCard->job_card_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return response()->json(['next_number' => $prefix . $nextNumber]);
    }

    public function create(Request $request)
    {
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($selectedSubAdminId) {
            $branchId = $selectedSubAdminId;
        } elseif (strtolower(Auth::user()->role) == 'staff') {
            $branchId = Auth::user()->branch_id;
        } else {
            $branchId = Auth::user()->id;
        }

        // Fetch data filtered by branch
        $carModel = AutoCarModel::where('branch_id', $branchId)->get();
        $carCategory = AutoCarCategory::where('branch_id', $branchId)->get();
        $labourService = LabourService::where('is_deleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        // $parts = Product::where('isDeleted', 0)
        //     ->where('branch_id', $branchId)
        //     ->get();
        $parts = Product::select('id', 'name', 'price', 'product_gst')
            ->where(['isDeleted' => 0, 'branch_id' => $branchId])
            ->get();
        $technicians = User::whereIn('role', ['Staff', 'staff'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();

        $customers = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->with('details') // eager load UserDetail
            ->get();

        $gst = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();




        return view('job-card/create', compact(
            'carModel',
            'carCategory',
            'technicians',
            'customers',
            'labourService',
            'parts',
            'gst'
        ));
    }

    public function edit($id)
    {
        $selectedSubAdminId = session('selectedSubAdminId');

        if ($selectedSubAdminId) {
            $branchId = $selectedSubAdminId;
        } elseif (strtolower(Auth::user()->role) == 'staff') {
            $branchId = Auth::user()->branch_id;
        } else {
            $branchId = Auth::user()->id;
        }

        // Fetch the job card to edit
        $jobCard = JobCard::where('id', $id)
            ->whereIn('type', ['jobCard', 'quotation'])
            ->where('isDeleted', 0)
            ->firstOrFail();

        // Check if user has access to this job card
        if (strtolower(Auth::user()->role) == 'staff') {
            if ($jobCard->subbranch_id != Auth::user()->branch_id) {
                abort(403, 'Unauthorized access');
            }
        } else {
            if ($jobCard->subbranch_id != $branchId) {
                abort(403, 'Unauthorized access');
            }
        }

        // Fetch data filtered by branch
        $carModel = AutoCarModel::where('branch_id', $branchId)->get();
        $carCategory = AutoCarCategory::where('branch_id', $branchId)->get();
        $labourService = LabourService::where('is_deleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        $technicians = User::whereIn('role', ['Staff', 'staff'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        $customers = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->with('details')
            ->get();
        $gst = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();
        // dd($jobCard);
        return view('job-card.edit', compact(
            'jobCard',
            'carModel',
            'carCategory',
            'technicians',
            'customers',
            'labourService',
            'gst'
        ));
    }

    public function getParts(Request $request)
    {
        $search = $request->q ?? '';
        $page = $request->page ?? 1;
        $perPage = 50;

        $selectedSubAdminId = session('selectedSubAdminId');
        $user = auth()->user();

        if ($selectedSubAdminId) {
            $branchId = $selectedSubAdminId;
        } elseif (strtolower($user->role) == 'staff') {
            $branchId = $user->branch_id;
        } else {
            $branchId = $user->id;
        }

        $query = Product::select('id', 'name', 'price', 'product_gst')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId);

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $parts = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $parts->map(function ($part) {
                return [
                    'id' => $part->id,
                    'text' => $part->name,
                    'price' => $part->price,
                    'name' => $part->name,
                    'product_gst' => $part->product_gst
                ];
            }),
            'pagination' => [
                'more' => $parts->hasMorePages()
            ]
        ]);
    }


    public function convertQuotation($id)
    {
        $jobCard = JobCard::findOrFail($id);

        $jobCard->quotation_status = 'jobCard';

        // Optional: change number from Q to J
        if (str_starts_with($jobCard->job_card_number, 'Q')) {

            $last = JobCard::where('job_card_number', 'LIKE', 'J%')
                ->orderByDesc('id')
                ->first();

            $next = $last
                ? ((int)substr($last->job_card_number, 1)) + 1
                : 1;

            $jobCard->job_card_number = 'J' . $next;
        }

        $jobCard->save();

        return response()->json([
            'status' => true,
            'message' => 'Quotation converted successfully.'
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $action = $request->input('action', 'generate_bill');

        $rules = [
            'vehicle_number' => 'required|string|max:255',
            'model_make' => 'required|string|max:255',
            'quotation' => 'nullable|string',
            'engine_no' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'km_hours' => 'nullable|numeric',
            'due_km' => 'nullable|numeric',
            'customer' => 'required|string',
            'gst_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required',
            'job_card_number' => [
                'required',
                'string',
                Rule::unique('job_cards', 'job_card_number')->where(function ($query) {
                    return $query->where('isDeleted', 0);
                }),
            ],
            'category' => 'nullable|string|max:100',
            'technician' => 'required|string|max:100',
            'date' => 'nullable|date',
            'due_date' => 'nullable',
            'labour_details' => 'nullable|array',
            'parts_details' => 'nullable|array',
            'total_labour' => 'required|numeric',
            'total_parts' => 'required|numeric',
            'grand_total' => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'remark_printable' => 'nullable|string',
            'remark_non_printable' => 'nullable|string',
            'remark' => 'nullable|string',
            'subbranch_id' => 'nullable|integer',
            'paid_by' => $action === 'pay_now'
                ? 'required|string|in:Cash,Online,Cash_online,EMI'
                : 'nullable|string|in:Cash,Online,Cash_online,EMI',
            'payment_type' => 'nullable|string|max:50',
            'online_amount' => 'nullable|numeric',
            'cash_amount' => 'nullable|numeric',
            'calculated_emi' => 'nullable|numeric',
            'order_number' => 'nullable|string|max:100',
            'discount_amount' => 'nullable|numeric',
            'record_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'customer_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:100',
            'emi_duration' => 'nullable|string|max:100',

            // Payment type options
            'type' => 'nullable|string|in:cash_Fully,cash_partially,Fully,Partially,cash_online_Fully,cash_online_Partially',

            // Cash + Online Payment Type
            'cash_online_type' => 'nullable|string|in:cash_online_Fully,cash_online_Partially',

            'cash_online_cash_amount' => 'nullable|required_if:cash_online_type,cash_online_Fully|numeric|min:0',
            'cash_online_upi_amount' => 'nullable|required_if:cash_online_type,cash_online_Fully|numeric|min:0',

            // Partially (Cash + Online)
            'cash_online_partial_cash' => 'nullable|required_if:cash_online_type,cash_online_Partially|numeric|min:0',
            'cash_online_partial_upi' => 'nullable|required_if:cash_online_type,cash_online_Partially|numeric|min:0',
            'cash_online_remaining' => 'nullable|numeric|min:0',

            // EMI fields
            'emi_amount' => 'nullable|numeric|min:0',

            // Partial cash
            'cash_partial_amount' => 'nullable|required_if:type,cash_partially|numeric|min:0',

            // Partial online
            'partial_amount' => 'nullable|required_if:type,Partially|numeric|min:0',
        ];

        $messages = [
            'vehicle_number.required' => 'Vehicle number is required.',
            'model_make.required' => 'Model make is required.',
            'engine_no.required' => 'Engine number is required.',
            'chassis_no.required' => 'Chassis number is required.',
            'customer.required' => 'Customer is required.',
            'gst_no.required' => 'GST number is required.',
            'mobile.required' => 'Mobile number is required.',
            'category.required' => 'Category is required.',
            'technician.required' => 'Technician is required.',
            'total_labour.required' => 'Total labour is required.',
            'total_parts.required' => 'Total parts is required.',
            'paid_by.required' => 'Payment method is required.',

            // Payment-specific short messages
            'cash_online_cash_amount.required_if' => 'Cash amount is required.',
            'cash_online_upi_amount.required_if' => 'UPI amount is required.',
            'cash_online_partial_cash.required_if' => 'Partial cash is required.',
            'cash_online_partial_upi.required_if' => 'Partial online is required.',
            'cash_partial_amount.required_if' => 'Partial cash is required.',
            'partial_amount.required_if' => 'Partial online amount is required.',

            // Numeric validation short messages
            'cash_online_cash_amount.numeric' => 'Enter a valid number.',
            'cash_online_upi_amount.numeric' => 'Enter a valid number.',
            'cash_online_partial_cash.numeric' => 'Enter a valid number.',
            'cash_online_partial_upi.numeric' => 'Enter a valid number.',
            'cash_partial_amount.numeric' => 'Enter a valid number.',
            'partial_amount.numeric' => 'Enter a valid number.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);


        $validator->after(function ($validator) use ($request, $action) {
            // Only run payment validations if action is pay_now
            if ($action === 'pay_now') {
                $grand = (float) ($request->grand_total ?? 0);
                $paidBy = $request->paid_by;
                $type = $request->type; // reused for cash/online

                if ($paidBy === 'Cash') {
                    if (!in_array($type, ['cash_Fully', 'cash_partially'])) {
                        $validator->errors()->add('type', 'Please select Cash payment type (Fully or Partially).');
                    }
                    if ($type === 'cash_partially') {
                        if (!is_numeric($request->cash_partial_amount) || $request->cash_partial_amount <= 0) {
                            $validator->errors()->add('cash_partial_amount', 'Partial cash amount is required and must be greater than 0.');
                        } elseif ($grand > 0 && $request->cash_partial_amount >= $grand) {
                            $validator->errors()->add('cash_partial_amount', 'Partial cash amount must be less than the grand total.');
                        }
                    }
                } elseif ($paidBy === 'Online') {
                    if (!in_array($type, ['Fully', 'Partially'])) {
                        $validator->errors()->add('type', 'Please select Online payment type (Fully or Partially).');
                    }
                    if ($type === 'Partially') {
                        if (!is_numeric($request->partial_amount) || $request->partial_amount <= 0) {
                            $validator->errors()->add('partial_amount', 'Partial online amount is required and must be greater than 0.');
                        } elseif ($grand > 0 && $request->partial_amount >= $grand) {
                            $validator->errors()->add('partial_amount', 'Partial online amount must be less than the grand total.');
                        }
                    }
                } elseif ($paidBy === 'Cash_online') {
                    $coType = $request->cash_online_type;
                    if (!in_array($coType, ['cash_online_Fully', 'cash_online_Partially'])) {
                        $validator->errors()->add('cash_online_type', 'Please select Cash + Online type (Fully or Partially).');
                    }
                    if ($coType === 'cash_online_Fully') {
                        if (!is_numeric($request->cash_online_cash_amount) || !is_numeric($request->cash_online_upi_amount)) {
                            $validator->errors()->add('cash_online_cash_amount', 'Cash and UPI amounts are required for Cash + Online (Fully).');
                        }
                    } elseif ($coType === 'cash_online_Partially') {
                        $sum = (float) ($request->cash_online_partial_cash) + (float) ($request->cash_online_partial_upi);
                        if ($grand > 0 && $sum >= $grand) {
                            $validator->errors()->add('cash_online_partial_cash', 'Sum of Cash and UPI for partial must be less than the grand total.');
                        }
                    }
                } elseif ($paidBy === 'EMI') {
                    if (!is_numeric($request->emi_duration) || (int) $request->emi_duration < 1) {
                        $validator->errors()->add('emi_duration', 'EMI duration is required and must be at least 1 month.');
                    }
                    if (!is_numeric($request->emi_amount) || $request->emi_amount <= 0) {
                        $validator->errors()->add('emi_amount', 'EMI amount is required and must be greater than 0.');
                    }
                }
            }
        });


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        // dd($request->all());
        $validated = $validator->validated();

        $isQuotation = $request->quotation === 'quotation';
        $validated['quotation_status'] = $isQuotation ? 'quotation' : 'jobCard';
        $withGST = $request->gst === 'yes';
        $prefix = $isQuotation ? 'Q' : ($withGST ? 'G' : 'J');

        $lastJobCard = JobCard::where('job_card_number', 'LIKE', $prefix . '%')
            ->where('isDeleted', 0)
            ->orderByRaw('CAST(SUBSTRING(job_card_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastJobCard) {
            $lastNumber = (int) substr($lastJobCard->job_card_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $validated['job_card_number'] = $prefix . $nextNumber;

        // Map fields as before
        $validated['vehicle_no'] = $validated['vehicle_number'];
        $validated['km_hours'] = $request->km_hours ?? null;
        $validated['due_km_hours'] = $request->due_km ?? null;

        $validated['model'] = $validated['model_make'];
        $validated['remark'] = $validated['remark_printable'];
        // $validated['payment_method'] = $validated['paid_by'] ?? 'Cash';
        if ($action === 'generate_bill') {
            $validated['payment_method'] = 'Pending';
        } else {
            $validated['payment_method'] = $validated['paid_by'] ?? 'Cash';
        }
        $validated['emi_months'] = $validated['calculated_emi'];
        $validated['discount'] = $request->input('discount_amount', 0);
        // if (!empty($validated['labour_details'])) {
        //     ksort($validated['labour_details']);
        //     $validated['labour_details'] = json_encode($validated['labour_details']);
        // } else {
        //     $validated['labour_details'] = json_encode([]);
        // }
        if (!empty($validated['labour_details'])) {
            foreach ($validated['labour_details'] as $key => $labour) {
                // if id is not numeric, treat it as a name
                if (!is_numeric($labour['id'])) {
                    $service = \App\Models\LabourService::where('name', $labour['id'])->first();

                    // if service exists → replace with its id
                    if ($service) {
                        $validated['labour_details'][$key]['id'] = $service->id;
                    } else {
                        // optional: create new record if name doesn't exist
                        $newService = \App\Models\LabourService::create([
                            'name' => $labour['id'],
                            'price' => $labour['price'] ?? 0, // fallback
                        ]);
                        $validated['labour_details'][$key]['id'] = $newService->id;
                    }
                }
            }

            // sort by key & encode
            ksort($validated['labour_details']);
            $validated['labour_details'] = json_encode($validated['labour_details']);
        } else {
            $validated['labour_details'] = json_encode([]);
        }
        // dd($validated['labour_details']);

        // Sort parts by keys (ascending)
        if (!empty($request->parts)) {
            $parts = $request->parts;
            ksort($parts);
            $validated['parts_details'] = json_encode($parts);
        } else {
            $validated['parts_details'] = json_encode([]);
        }
        $validated['total_subtotal_amount'] = $validated['grand_total'];
        $validated['remark_non_printable'] = $validated['remark_non_printable'];
        $validated['record_date'] = date('Y-m-d');
        $validated['labour_discount'] = json_encode([
            'type' => $request->labour_discount_type,
            'amount' => $request->labour_discount_amount
        ]);

        // Combine parts discount into JSON
        $validated['parts_discount'] = json_encode([
            'type' => $request->parts_discount_type,
            'amount' => $request->parts_discount_amount
        ]);

        // --- Handle due date ---
        if (!empty($validated['due_date'])) {
            $date = \DateTime::createFromFormat('d-M-Y', $validated['due_date']);
            if ($date) {
                $validated['next_due_date'] = $date->format('Y-m-d');
            } else {
                $validated['next_due_date'] = null;
            }
        }
        $user = Auth::user();
        $BranchId = $user->branch_id;
        $validated['type'] =  'jobCard';
        if (session()->has('selectedSubAdminId')) {
            $validated['user_id'] = session('selectedSubAdminId');
            $validated['subbranch_id'] = session('selectedSubAdminId');
        } else {
            $validated['user_id'] = Auth::id();
            $validated['subbranch_id'] = $BranchId ?? Auth::id();
        }

        // --- Determine grand total and payment ---
        $grand_total = $request->grand_total ?? 0.0;
        $paid_amount = 0.0;
        $remaining_amount = $grand_total;

        if ($action == 'pay_now') {
            if (!empty($request->paid_by)) {
                switch ($request->paid_by) {
                    case 'Cash':
                        if ($request->type == 'cash_Fully') {
                            $paid_amount = $request->cash_amount ?? $grand_total;
                        } elseif ($request->type == 'cash_partially') {
                            $paid_amount = $request->cash_partial_amount ?? 0;
                        }
                        break;
                    case 'Online':
                        if ($request->type == 'Fully') {
                            $paid_amount = $grand_total;
                        } elseif ($request->type == 'Partially') {
                            $paid_amount = $request->partial_amount ?? 0;
                        }
                        break;
                    case 'Cash_online':
                        if ($request->cash_online_type == 'cash_online_Fully') {
                            $paid_amount = ($request->cash_online_cash_amount ?? 0) + ($request->cash_online_upi_amount ?? 0);
                        } elseif ($request->cash_online_type == 'cash_online_Partially') {
                            $paid_amount = ($request->cash_online_partial_cash ?? 0) + ($request->cash_online_partial_upi ?? 0);
                        }
                        break;
                    case 'EMI':
                        $paid_amount = $request->calculated_emi ?? 0;
                        break;
                }
                $remaining_amount = max($grand_total - $paid_amount, 0);
            }
        }

        $validated['paid_amount'] = $paid_amount;
        $validated['remaining_amount'] = $remaining_amount;

        // --- GST and bill logic unchanged ---
        $laboursGst = [];
        $validated['labours_gst'] = json_encode($laboursGst);


        $partsGst = [];
        if (!empty($request->withservices == '1') && !empty($request->parts_details)) {
            $taxRates = TaxRate::where('status', 1)->where('branch_id', session('selectedSubAdminId') ?? Auth::id())->where('isDeleted', 0)->get();
            foreach ($taxRates as $tax) {
                $partsGst[] = [
                    'tax_id' => $tax->id,
                    'tax_name' => $tax->tax_name,
                    'rate' => (float) $tax->tax_rate,
                ];
            }
        }
        $validated['parts_gst'] = json_encode($partsGst);

        // $validated['bill_no'] = random_int(10000000, 99999999);
        $lastSequential = \DB::table('job_cards')
            ->where('bill_no', 'like', '#%')
            ->orderBy('id', 'desc') // or created_at if you prefer
            ->first();

        if ($lastSequential) {
            // Strip "#" and convert to integer
            $lastNumber = (int) ltrim($lastSequential->bill_no, '#');
            $nextNumber = $lastNumber + 1;
        } else {
            // Start fresh
            $nextNumber = 1;
        }
        $validated['gst'] = $request->withservices ?? '';
        // Format with 6 digits
        $validated['bill_no'] = '#' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        //   $parts = $request->input('parts', []);

        // DB::transaction(function () use ($parts) {
        //     foreach ($parts as $part) {
        //         if (!isset($part['id']) || !isset($part['qty'])) {
        //             continue;
        //         }

        //         $product = Product::find($part['id']);
        //         if ($product) {
        //             // Reduce available quantity
        //             $product->quantity = max(0, $product->quantity - (int) $part['qty']);
        //             $product->save();
        //         }
        //     }

        //     // ✅ Save your job card or invoice here...
        // });
        $parts_input = $request->input('parts', []);


        // --- Customer handling ---
        if (!empty($request->customer) && !is_numeric($request->customer)) {
            $vendor = User::create([
                'name' => $request->customer,
                'phone' => $request->mobile ?? '',
                'role' => 'customer',
                'status' => 1,
                'gst_num' => $request->gst_no ?? null,
                'branch_id' => $BranchId ?? $validated['user_id'],
            ]);
            $customer_id = $vendor->id;
            UserDetail::create([
                'user_id' => $customer_id,
                'address' => $request->address ?? '',
                'city' => '',
                'country' => '',
            ]);
        } elseif (is_numeric($request->customer)) {
            $customer_id = $request->customer;
        } else {
            $customer_id = null;
        }
        $validated['customer_id'] = $customer_id;

        $jobCard = JobCard::create($validated);

        if ($jobCard && !$isQuotation) {
            foreach ($parts_input as $part) {
                if (!isset($part['id']) || !isset($part['qty'])) {
                    continue;
                }

                $product = Product::find($part['id']);
                if ($product) {
                    $initialStock = $product->quantity;
                    $newQuantity = max(0, $product->quantity - (int) $part['qty']);
                    $product->quantity = $newQuantity;
                    $product->save();

                    ProductInventory::create([
                        'product_id' => $product->id,
                        'initial_stock' => $initialStock,
                        'current_stock' => $newQuantity,
                        'branch_id' => $validated['subbranch_id'],
                        'create_by' => Auth::id(),
                        'type' => 'Add Job Card',
                        'date' => now(),
                    ]);
                }
            }
        }
        $tz = 'Asia/Kolkata';
        $yesterday = Carbon::now($tz)->subDay();
        // --- PaymentStore ---
        if ($jobCard && $action == 'pay_now') {
            PaymentStore::create([
                'user_id' => $validated['user_id'],
                'jobcard_id' => $jobCard->id,
                'payment_amount' => $paid_amount,
                'remaining_amount' => $remaining_amount,
                'payment_date' => $yesterday,
                'payment_method' => $validated['payment_method'],
                'payment_type' => $request->type ?? '',
                'cash_amount' => $request->cash_amount ?? null,
                'upi_amount' => $request->upi_amount ?? null,
                'emi_month' => 1,
                'remark' => $request->remark ?? null,
                'isDeleted' => 0,
                'created_at' => $yesterday,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Job Card created successfully.',
            'job_card_id' => $jobCard->id,
            'data' => $jobCard
        ]);
    }

    public function update(Request $request, $id)
    {
        $action = $request->input('action', 'generate_bill');
        // dd($request->all());
        $rules = [
            'vehicle_number' => 'required|string|max:255',
            'model_make' => 'required|string|max:255',
            'quotation' => 'nullable|string',
            'engine_no' => 'nullable|string|max:255',
            'chassis_no' => 'nullable|string|max:255',
            'km_hours' => 'nullable|numeric',
            'due_km' => 'nullable|numeric',
            'customer' => 'required|string',
            'gst_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required',
            'job_card_number' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'technician' => 'required|string|max:100',
            'date' => 'nullable|date',
            'due_date' => 'nullable',
            'labour_details' => 'nullable|array',
            'parts_details' => 'nullable|array',
            'total_labour' => 'required|numeric',
            'total_parts' => 'required|numeric',
            'grand_total' => 'nullable|numeric',
            'remark_printable' => 'nullable|string',
            'remark_non_printable' => 'nullable|string',
        ];
        // dd($request->gst);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Get the job card to update
        $jobCard = JobCard::where('id', $id)
            ->whereIn('type', ['jobCard', 'quotation'])
            ->where('isDeleted', 0)
            ->firstOrFail();

        // Check access permissions
        $selectedSubAdminId = session('selectedSubAdminId');
        if (strtolower(Auth::user()->role) == 'staff') {
            if ($jobCard->subbranch_id != Auth::user()->branch_id) {
                abort(403, 'Unauthorized access');
            }
        } else {
            if ($jobCard->subbranch_id != ($selectedSubAdminId ?? Auth::user()->id)) {
                abort(403, 'Unauthorized access');
            }
        }
        // dd($request->remark_printable);
        // Map fields
        $validated['vehicle_no'] = $validated['vehicle_number'];
        $isQuotation = $request->quotation === 'quotation';
        $validated['quotation_status'] = $isQuotation ? 'quotation' : 'jobCard';
        $validated['type'] = $isQuotation ? 'dailyData' : 'jobCard';

        $validated['km_hours'] = $request->km_hours ?? null;
        $validated['due_km_hours'] = $request->due_km ?? null;
        $validated['model'] = $validated['model_make'];
        $validated['remark'] = $validated['remark_printable'];
        $validated['remark_printable'] = $request->remark_printable ?? '';
        $validated['remark_non_printable'] = $request->remark_non_printable ?? '';
        $validated['gst'] = $request->withservices ?? '';

        // Process labour details
        if (!empty($validated['labour_details'])) {
            foreach ($validated['labour_details'] as $key => $labour) {
                // if id is not numeric, treat it as a name
                if (!is_numeric($labour['id'])) {
                    $service = \App\Models\LabourService::where('name', $labour['id'])->first();

                    // if service exists → replace with its id
                    if ($service) {
                        $validated['labour_details'][$key]['id'] = $service->id;
                    } else {
                        // optional: create new record if name doesn't exist
                        $newService = \App\Models\LabourService::create([
                            'name' => $labour['id'],
                            'price' => $labour['price'] ?? 0, // fallback
                        ]);
                        $validated['labour_details'][$key]['id'] = $newService->id;
                    }
                }
            }

            // sort by key & encode
            ksort($validated['labour_details']);
            $validated['labour_details'] = json_encode($validated['labour_details']);
        } else {
            $validated['labour_details'] = json_encode([]);
        }

        // Process parts details
        if (!empty($request->parts)) {
            $parts = $request->parts;
            ksort($parts);
            $validated['parts_details'] = json_encode($parts);
        } else {
            $validated['parts_details'] = json_encode([]);
        }

        // Calculate totals
        $validated['total_labour'] = $request->total_labour ?? 0;
        $validated['total_parts'] = $request->total_parts ?? 0;
        $validated['total_subtotal_amount'] = $request->grand_total ?? 0;

        $laboursGst = [];
        $validated['labours_gst'] = json_encode($laboursGst);


        $partsGst = [];
        if (!empty($request->withservices == '1') && !empty($request->parts_details)) {
            $taxRates = TaxRate::where('status', 1)->where('branch_id', session('selectedSubAdminId') ?? Auth::id())->where('isDeleted', 0)->get();
            foreach ($taxRates as $tax) {
                $partsGst[] = [
                    'tax_id' => $tax->id,
                    'tax_name' => $tax->tax_name,
                    'rate' => (float) $tax->tax_rate,
                ];
            }
        }
        $validated['parts_gst'] = json_encode($partsGst);

        // Revert old parts stock if it was not a quotation
        if ($jobCard->type !== 'quotation') {
            $oldParts = json_decode($jobCard->parts_details, true) ?? [];
            foreach ($oldParts as $part) {
                if (isset($part['id']) && isset($part['qty'])) {
                    $product = Product::find($part['id']);
                    if ($product) {
                        $product->quantity += (int) $part['qty'];
                        $product->save();
                    }
                }
            }
        }

        // Adjust remaining amount if total changed: when editing a job card,
        // any increase in total_subtotal_amount should increase remaining_amount
        // (and decreases should reduce remaining, never below zero).
        $oldTotal = (float) ($jobCard->total_subtotal_amount ?? 0);
        $newTotal = (float) ($validated['total_subtotal_amount'] ?? $oldTotal);
        $diff = $newTotal - $oldTotal;

        if ($diff !== 0) {
            $currentRemaining = (float) ($jobCard->remaining_amount ?? 0);
            $updatedRemaining = $currentRemaining + $diff;
            // never allow negative remaining
            $validated['remaining_amount'] = $updatedRemaining > 0 ? $updatedRemaining : 0;
        }

        // Update the job card
        $jobCard->update($validated);

        // Subtract new parts stock if it is not a quotation
        if (!$isQuotation) {
            $parts_input = $request->parts ?? [];
            foreach ($parts_input as $part) {
                if (!isset($part['id']) || !isset($part['qty'])) {
                    continue;
                }

                $product = Product::find($part['id']);
                if ($product) {
                    $initialStock = $product->quantity;
                    $newQuantity = max(0, $product->quantity - (int) $part['qty']);
                    $product->quantity = $newQuantity;
                    $product->save();

                    ProductInventory::create([
                        'product_id' => $product->id,
                        'initial_stock' => $initialStock,
                        'current_stock' => $newQuantity,
                        'branch_id' => $jobCard->subbranch_id,
                        'create_by' => Auth::id(),
                        'type' => 'Edit Job Card',
                        'date' => now(),
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Job Card updated successfully.',
            'job_card_id' => $jobCard->id,
            'data' => $jobCard
        ]);
    }



    public function get_history(Request $request)
    {
        $vehicleNumber = $request->query('vehicle_number');

        if (!$vehicleNumber) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle number is required.',
                'data' => []
            ]);
        }

        $jobCards = JobCard::all()->where('vehicle_no', $vehicleNumber);

        if ($jobCards->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No history found for this vehicle.',
                'data' => []
            ]);
        }

        $data = $jobCards->map(function ($job) {
            return [
                'job_card_id' => $job->id,
                'job_card_number' => $job->job_card_number ?? 'N/A',
                'job_date' => $job->date ? Carbon::parse($job->date)->format('d-m-Y') : null,
                'total_amount' => $job->total_subtotal_amount ?? 0,
                'status' => $job->status ?? 'Pending',
            ];
        })->values();



        return response()->json([
            'status' => true,
            'message' => 'History fetched successfully.',
            'data' => $data
        ]);
    }

    public function viewPdf($id)
    {
        $user_id = Auth::id();
        $subAdminId = session('selectedSubAdminId') ?? $user_id;

        $setting = Setting::where('branch_id', $subAdminId) // add your branch ID variable here
            ->first();

        $jobCard = JobCard::with('customer')->findOrFail($id);

        // Labour (JSON now contains id, price, total)
        $labourDetails = json_decode($jobCard->labour_details, true) ?? [];
        $labourGst = []; // Labour should never have GST as per user request

        // Resolve labour names from table
        $labourIds = collect($labourDetails)->pluck('id')->filter()->all();
        $labourMap = LabourService::whereIn('id', $labourIds)->get()->keyBy('id');

        // Inject names into details
        foreach ($labourDetails as $key => $labour) {
            $id = $labour['id'] ?? null;
            $labourDetails[$key]['name'] = $id && isset($labourMap[$id])
                ? $labourMap[$id]->name
                : 'N/A';
        }

        // Parts (same idea)
        $partsDetails = json_decode($jobCard->parts_details, true) ?? [];
        $partsGst = json_decode($jobCard->parts_gst, true) ?? [];

        $modelId = json_decode($jobCard->model, true);
        $model = AutoCarModel::find($modelId);
        $model_name = $model ? $model->model_name : ' -- ';

        return view('job-card.view-pdf', compact(
            'jobCard',
            'labourDetails',
            'labourGst',
            'partsDetails',
            'partsGst',
            'setting',
            'model_name'
        ));
    }





    // Make sure DomPDF is installed

    // At the top

    public function downloadPdf($id)
    {
        $user_id = Auth::id();
        $subAdminId = session('selectedSubAdminId') ?? $user_id;

        $setting = Setting::where('branch_id', $subAdminId) // add your branch ID variable here
            ->first();

        $jobCard = JobCard::with('customer')->findOrFail($id);

        // Labour
        $labourIds = json_decode($jobCard->labour_details, true);

        // If you still want additional data from table (like name), fetch it
        $labourData = [];
        if ($labourIds && is_array($labourIds)) {
            $ids = array_map(fn($item) => $item['id'], $labourIds);
            $labourTableData = LabourService::whereIn('id', $ids)->get()->keyBy('id');

            foreach ($labourIds as $key => $item) {
                $labourData[$key] = [
                    'id' => $item['id'],
                    'name' => $labourTableData[$item['id']]->name ?? ' -- ', // optional
                    'price' => $item['price'],   // use price from JSON
                    'total' => $item['total'],   // use total from JSON
                ];
            }
        }

        $labourGst = []; // Labour should never have GST as per user request

        // Parts
        $partsIds = json_decode($jobCard->parts_details, true);
        $partsData = $partsIds ?? []; // already contains array of details
        $partsGst = json_decode($jobCard->parts_gst, true) ?? [];

        // Model
        $modelId = json_decode($jobCard->model, true);
        $model = AutoCarModel::find($modelId);
        $model_name = $model ? $model->model_name : ' -- ';

        $pdf = Pdf::loadView('job-card.invoice_pdf', compact(
            'jobCard',
            'labourData',
            'partsData',
            'labourGst',
            'partsGst',
            'setting',
            'model_name'
        ))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        $filename = 'Invoice_' . $jobCard->job_card_number . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadPdf1($id)
    {
        $user_id = Auth::id();
        $subAdminId = session('selectedSubAdminId') ?? $user_id;

        $setting = Setting::where('branch_id', $subAdminId) // add your branch ID variable here
            ->first();

        $jobCard = JobCard::with('customer')->findOrFail($id);

        // Labour
        $labourIds = json_decode($jobCard->labour_details, true);

        $labourData = [];
        if ($labourIds && is_array($labourIds)) {
            $ids = array_map(fn($item) => $item['id'], $labourIds);
            $labourTableData = LabourService::whereIn('id', $ids)->get()->keyBy('id');

            foreach ($labourIds as $key => $item) {
                $labourData[$key] = [
                    'id' => $item['id'],
                    'name' => $labourTableData[$item['id']]->name ?? ' -- ',
                    'price' => $item['price'],
                    'total' => $item['total'],
                ];
            }
        }

        $labourGst = []; // Labour should never have GST as per user request

        // Parts
        $partsIds = json_decode($jobCard->parts_details, true);
        $partsData = $partsIds ?? [];
        $partsGst = json_decode($jobCard->parts_gst, true) ?? [];

        // Model
        $modelId = json_decode($jobCard->model, true);
        $model = AutoCarModel::find($modelId);
        $model_name = $model ? $model->model_name : ' -- ';

        $pdf = Pdf::loadView('job-card.invoice_pdf', compact(
            'jobCard',
            'labourData',
            'partsData',
            'labourGst',
            'partsGst',
            'setting',
            'model_name'
        ))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        $filename = 'Invoice_' . $jobCard->job_card_number . '.pdf';

        // Open PDF in browser instead of downloading
        return $pdf->stream($filename);
    }



    public function destroy($id)
    {
        // Find the job card (only if not already deleted)
        $jobCard = JobCard::where('isDeleted', 0)->find($id);

        if (!$jobCard) {
            return response()->json([
                'success' => false,
                'message' => 'Job card not found or already deleted.'
            ], 404);
        }

        // Soft-delete the job card
        $jobCard->isDeleted = 1;
        $jobCard->save();

        // Soft-delete related payment_store entries
        \DB::table('payment_store')
            ->where('jobcard_id', $jobCard->id)
            ->update(['isDeleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Job card and related payments marked as deleted.',
            'data' => [
                'id' => $jobCard->id
            ]
        ], 200);
    }


    public function makePaymentSubmit(Request $request)
    {



        $user_id = Auth::id();
        $tz = 'Asia/Kolkata';
        $yesterday = \Carbon\Carbon::now($tz)->subDay();

        $request->validate([
            'order_id' => 'nullable|integer',
            'job_card_id' => 'nullable|integer',
            'payment_amount' => 'nullable|numeric',
            'payment_date' => 'nullable|date',
            'payment_type' => 'nullable|string',
            'discount_amount' => 'nullable|string',
            'emi_month' => 'nullable|integer',
            'pending_date' => 'nullable|date',
            'new_emi_value' => 'nullable',
            'emi_paid_value' => 'nullable|numeric',
        ]);

        if (!$request->filled('order_id')) {

            // Determine payment amount
            $paymentAmount = $request->emi_total_new ?? $request->emi_total ?? $request->amount ?? $request->upi_online_amount ?? 0;

            if ($request->filled('cash_amount') && $request->filled('online_amount')) {
                $paymentAmount = (float) $request->cash_amount + (float) $request->online_amount;
            } elseif ($request->filled('fully_cash_amount') && $request->filled('full_online_amount')) {
                $paymentAmount = (float) $request->fully_cash_amount + (float) $request->full_online_amount;
            } elseif ($request->cashAmount) {
                $paymentAmount = $request->cashAmount;
            } elseif ($request->upi_online_amount) {
                $paymentAmount = $request->upi_online_amount;
            } elseif ($request->emi_monthly) {
                $paymentAmount = $request->emi_monthly;
            }

            // Determine payment type
            if (
                in_array($request->paid_type, ['cash_partially']) ||
                in_array($request->online_type, ['online_partially']) ||
                in_array($request->cash_online_type, ['cash_online_partially'])
            ) {
                $type = 'partially';
            } elseif (
                in_array($request->paid_type, ['cash_fully']) ||
                in_array($request->online_type, ['online_fully']) ||
                in_array($request->cash_online_type, ['cash_online_fully']) ||
                $request->payment_type === 'fully'

            ) {
                $type = 'fully';
            } elseif (
                in_array($request->emi_type, ['emi'])
            ) {
                $type = 'emi';
            } else {
                $type = 'fully';
            }


            // Fetch JobCard if exists
            $jobCard = $request->job_card_id ? JobCard::find($request->job_card_id) : null;

            // Calculate remaining amount
            $currentRemaining = $jobCard->remaining_amount ?? 0;
            $newRemaining = max(0, $currentRemaining - $paymentAmount);

            // Create payment


            $payment = PaymentStore::create([
                'user_id' => $user_id,
                'jobcard_id' => $request->job_card_id,
                'payment_amount' => $paymentAmount,
                'remaining_amount' => $newRemaining,
                'payment_method' => $request->payment_method ?? $request->payment_type ?? '',
                'payment_date' => $yesterday,
                'payment_type' => $type,
                'cash_amount' => $request->cash_amount,
                'upi_amount' => $request->upi_amount,
                'emi_month' => $request->emi_month ?? 1,
                'remark' => $request->remark,
                'isDeleted' => 0,
                'created_at'     => $yesterday,
            ]);

            // manually set timestamps
            $payment->created_at = $yesterday;
            $payment->updated_at = $yesterday;

            // disable auto timestamps just for this save
            $payment->timestamps = false;

            // save record
            $payment->save();

            // Update JobCard
            if ($jobCard) {
                $updateData = ['remaining_amount' => $newRemaining];

                // If new EMI is being set
                if ($request->filled('new_emi_value') && $request->payment_method == 'emi' || $request->filled('emi_paid_value')) {
                    //  dd($request->all());
                    $updateData['payment_method'] = 'EMI';
                    $updateData['emi_duration'] = $request->emi_month_new ?? $request->emi_month;
                    $updateData['emi_months'] = $request->emi_total_new ?? 1;
                } else {
                    // dd($request->filled('emi_paid_value'));
                    // Otherwise, keep the actual payment method (cash, online, cash_online)
                    $method = $request->payment_method ?? $request->payment_type ?? $updateData['payment_method'] ?? 'Cash';

                    // Normalize to proper values
                    if (strtolower($method) === 'online' || strtolower($method) === 'upi') {
                        $method = 'Online';
                    } elseif (strtolower($method) === 'cash') {
                        $method = 'Cash';
                    } elseif (strtolower($method) === 'emi') {
                        $method = 'EMI';
                    } else {
                        $method = 'Cash';
                    }

                    $updateData['payment_method'] = $method;
                }

                if ($request->emi_duration) {
                    $updateData['emi_duration'] = $request->emi_duration;
                }

                // Update next pending date if provided
                if ($request->pending_date) {
                    $updateData['next_pending_date'] = $request->pending_date;
                }

                $jobCard->update($updateData);
            }
        } else {

            $paymentAmount = 0;
            $upiAmount = 0; // Store actual online payment

            // --- Full payments ---
            if ($request->filled('cash_amount') && $request->filled('online_amount')) {
                $paymentAmount = (float) $request->cash_amount + (float) $request->online_amount;
                $upiAmount = (float) $request->online_amount;
            } elseif ($request->filled('fully_cash_amount') && $request->filled('full_online_amount')) {
                $paymentAmount = (float) $request->fully_cash_amount + (float) $request->full_online_amount;
                $upiAmount = (float) $request->full_online_amount;
            } elseif ($request->filled('cashAmount')) {
                $paymentAmount = (float) $request->cashAmount;
            } elseif ($request->filled('upi_online_amount')) {
                $paymentAmount = (float) $request->upi_online_amount;
                $upiAmount = (float) $request->upi_online_amount;
            } elseif ($request->filled('amount')) {
                $paymentAmount = (float) $request->amount;
            } elseif ($request->filled('emi_total')) {
                $paymentAmount = (float) $request->emi_total;
            } elseif ($request->filled('emi_total_new')) {
                $paymentAmount = (float) $request->emi_total_new;
            }

            // --- Partial payments ---
            if ($request->payment_method === 'cash' && $request->paid_type === 'cash_partially') {
                $paymentAmount = (float) ($request->cash_partial_amount ?? $request->cash_pending_amount ?? 0);
            } elseif ($request->payment_method === 'online' && $request->online_type === 'online_partially') {
                $paymentAmount = (float) ($request->cash_partial_amount ?? $request->upi_online_amount ?? 0);
            } elseif ($request->payment_method === 'cash_online' && $request->cash_online_type === 'cash_online_partially') {
                $cashPart = (float) ($request->cash_amount ?? $request->fully_cash_amount ?? 0);
                $onlinePart = (float) ($request->online_amount ?? $request->full_online_amount ?? 0);

                $paymentAmount = $cashPart + $onlinePart;
                $upiAmount = $onlinePart;
            }



            // --- Determine payment type ---
            $type = 'emi'; // default
            if (
                in_array($request->paid_type, ['cash_partially']) ||
                in_array($request->online_type, ['online_partially']) ||
                in_array($request->cash_online_type, ['cash_online_partially'])
            ) {
                $type = 'partially';
            } elseif (
                in_array($request->paid_type, ['cash_fully']) ||
                in_array($request->online_type, ['online_fully']) ||
                in_array($request->cash_online_type, ['cash_online_fully']) ||
                $request->payment_type === 'fully'
            ) {
                $type = 'fully';
            }

            // Now $paymentAmount and $upiAmount will have correct values


            // Fetch order
            $order = Order::find($request->order_id);
            $discount = (float) ($request->discount_amount ?? 0);
            if ($discount > 0) {
                $order->discount = ($order->discount ?? 0) + $discount;
            }
            $currentRemaining = $order->remaining_amount ?? $order->total_amount;

            $newRemaining = max(0, $currentRemaining - $paymentAmount - $discount);

            if ($request->emi_type == 'emi') {
                $paymentAmount = $request->emi_monthly ?? 0.00;
            }



            if ($request->emi_type == 'fully') {

                $payment = PaymentStore::create([
                    'user_id' => $user_id,
                    'jobcard_id' => 0,
                    'order_id' => $request->order_id,
                    'payment_amount' => $request->emi_fully_amount,
                    'remaining_amount' => 0.00,
                    'payment_method' => $request->payment_method ?? $request->payment_type ?? 'Cash',
                    'payment_date' => $yesterday,
                    'payment_type' => $type,
                    'cash_amount' => $request->cash_amount,
                    'upi_amount' => $request->upi_amount,
                    'emi_month' => $request->emi_month ?? 1,
                    'discount_amount' => $discount ?? 0,
                    'remark' => $request->remark,
                    'isDeleted' => 0,
                    'created_at'     => $yesterday,
                ]);

                // manually set timestamps
                $payment->created_at = $yesterday;
                $payment->updated_at = $yesterday;

                // disable auto timestamps just for this save
                $payment->timestamps = false;

                // save record
                $payment->save();
            } else {


                // Create payment entry
                $payment = PaymentStore::create([
                    'user_id' => $user_id,
                    'jobcard_id' => 0,
                    'order_id' => $request->order_id,
                    'payment_amount' => $paymentAmount,
                    'remaining_amount' => $newRemaining,
                    'payment_method' => $request->payment_method ?? $request->payment_type ?? 'Cash',
                    'payment_date' => $yesterday,
                    'payment_type' => $type,
                    'cash_amount' => $request->cash_amount,
                    'upi_amount' => $request->upi_amount,
                    'emi_month' => $request->emi_month ?? 1,
                    'discount_amount' => $discount ?? 0,
                    'remark' => $request->remark,
                    'isDeleted' => 0,
                    'created_at'   => $yesterday,
                ]);
                $payment->created_at = $yesterday;
                $payment->updated_at = $yesterday;

                // disable auto timestamps just for this save
                $payment->timestamps = false;

                // save record
                $payment->save();;
            }

            if ($request->paid_type == 'cash_fully' || $request->online_type == 'online_fully' || $request->cash_online_type == 'cash_online_fully') {
                $paymentstatus = 'Completed';
            }





            if ($request->emi_type == 'emi' && $request->paid_type != 'cash_partially') {
                $payment_method = $request->emi_type;
            } else {
                $payment_method = $request->payment_method ?? $request->payment_type ?? 'Cash';
            }







            // $updateData=[];
            if ($order->payment_method == 'pending') {
                // dd($request->emi_duration);

                $updateData = [
                    'remaining_amount' => $newRemaining,
                    'payment_method' => $payment_method,
                    'payment_status' => $newRemaining <= 0 ? 'paid' : 'partial',
                    'emi_duration' => $request->emi_duration ?? 1,
                    'emi_amount' => $request->emi_monthly ?? 0.00,
                ];
            }


            if ($request->emi_type == 'fully') {
                $updateData = [
                    'remaining_amount' => 0.00,
                    'payment_method' => $request->payment_method ?? 'emi',
                    'payment_status' => 'Completed',
                ];
            } else {


                $updateData = [];
                if ($request->emi_type == 'emi') {
                    $updateData = [
                        'remaining_amount' => $newRemaining,
                        'payment_status' => $paymentstatus ?? 'pending',
                        'payment_method' => $request->payment_method ?? 'emi',
                        'emi_duration' => $request->emi_duration ?? 1,
                        'emi_amount' => $request->emi_monthly ?? 0.00,
                    ];
                } elseif ($request->paid_type == 'cash_partially' || $request->online_type == 'online_partially' || $request->cash_online_type == 'cash_online_partially') {

                    $updateData = [
                        'remaining_amount' => $newRemaining,
                    ];
                } else {
                    $updateData = [
                        'remaining_amount' => 0.00,
                        'payment_method' => $request->payment_method ?? 'emi',
                        'payment_status' => 'pending',
                    ];
                }
            }

            $lastId = $payment->id;
            if ($request->emi_type_fully == 'fully' && empty($request->payment_method)) {

                $payment = PaymentStore::find($lastId); // or your order_id if linked

                if ($payment) {
                    $payment->update([
                        'payment_amount' => $request->remaining_amount,
                        'remaining_amount' => 0.00,
                    ]);
                }
            }

            $order->update($updateData);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment submitted successfully.',
            'data' => $payment
        ]);
    }



    public function getHistory($job_card_id)
    {
        // $history = PaymentStore::where('order_id', $job_card_id)
        $history = PaymentStore::where('jobcard_id', $job_card_id)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }

    public function searchAndGetVehicle(Request $request)
    {
        $query = $request->input('q', '');

        $vehicles = JobCard::where('vehicle_no', 'like', "%{$query}%")
            ->select('vehicle_no', 'model', 'chassis_no', 'engine_no', 'km_hours', 'customer_id')
            ->limit(10)
            ->get();

        // dd($vehicles);

        // Find exact match (case-insensitive)
        $exactMatch = $vehicles->first(function ($vehicle) use ($query) {
            return strcasecmp($vehicle->vehicle_no, $query) === 0;
        });

        if ($exactMatch) {
            return response()->json([
                'exact' => true,
                'vehicle' => [
                    'customer_id' => $exactMatch->customer_id ?? null,
                    'vehicle_no' => $exactMatch->vehicle_no,
                    'model' => $exactMatch->model ?? null,
                    'chassis_no' => $exactMatch->chassis_no ?? null,
                    'engine_no' => $exactMatch->engine_no ?? null,
                    'km_hours' => $exactMatch->km_hours ?? null,
                ],
            ]);
        }

        // No exact match — return list of matching vehicles for dropdown
        return response()->json([
            'exact' => false,
            'vehicles' => $vehicles->map(function ($v) {
                return ['id' => $v->vehicle_no, 'text' => $v->vehicle_no];
            }),
        ]);
    }
}
