<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CreditNoteItem;
use App\Models\JobCard;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreditNoteItemApiController extends Controller
{
    private function resolveBranchId(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role);

        return match ($role) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $request->selectedSubAdminId ?: $user->id,
            default     => $user->id,
        };
    }


    public function index(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $query = CreditNoteItem::with(['order.user.userDetail', 'purchaseInvoice.vendor.userDetail', 'creditNote'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId);

        // ✅ Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($o) use ($search) {
                    $o->where('order_number', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('purchaseInvoice', function ($pi) use ($search) {
                    $pi->where('invoice_number', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('order.user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('purchaseInvoice.vendor', function ($v) use ($search) {
                    $v->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

         // Calculate total settlement for the filtered result (before pagination)
        $totalSettlement = (float) $query->sum('settlement_amount');

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);

        $itemsPaginated = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // $items = $query->orderBy('created_at', 'desc')->get();

       // Attach jobcard info when type is receipt but order is missing
        $itemsPaginated->each(function ($item) use ($branchId, $user) {
            if (!in_array($item->type_id, ['receipt', 1, '1'], true)) {
                return;
            }
            if ($item->order || empty($item->order_id)) {
                return;
            }
            $jobcardQuery = JobCard::with('customer:id,name')
                ->where('isDeleted', 0)
                ->where('id', $item->order_id);

            if ($user && strtolower($user->role) === 'staff') {
                // $jobcardQuery->where('user_id', $user->branch_id);
                $jobcardQuery->where('user_id', $user->id);
            } else {
                $jobcardQuery->where('subbranch_id', $branchId);
            }

            $jobcard = $jobcardQuery->first();
            if ($jobcard) {
                $item->setAttribute('jobcard', $jobcard);
            }
        });

        // return response()->json([
        //     'data' => $items
        // ]);
        return response()->json([
            'data' => $itemsPaginated->items(),
            'pagination' => [
                'current_page' => $itemsPaginated->currentPage(),
                'last_page'    => $itemsPaginated->lastPage(),
                'per_page'     => $itemsPaginated->perPage(),
                'total'        => $itemsPaginated->total(),
            ],
            'total_settlement' => number_format($totalSettlement, 2, '.', '')
        ]);
    }

    public function getJobCards(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        // dd($branchId);
        $user = Auth::guard('api')->user();
        // dd($user);

        $query = JobCard::with('customer:id,name')
            ->select('id', 'job_card_number', 'customer_id', 'customer_name')
            ->where('isDeleted', 0)
            ->where('type', 'jobCard');

        if ($user && strtolower($user->role) === 'staff') {
            // $query->where('user_id', $user->branch_id);
            $query->where('user_id', $user->id);
        } else {
            $query->where('subbranch_id', $branchId);
        }

        $jobcards = $query->orderBy('job_card_number', 'desc')
            ->get()
            ->map(function ($jobcard) {
                return [
                    'id' => $jobcard->id,
                    'job_card_number' => $jobcard->job_card_number,
                    'customer_id' => $jobcard->customer_id,
                    'customer_name' => $jobcard->customer_name ?? ($jobcard->customer->name ?? 'Unknown'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $jobcards
        ]);
    }

    public function getJobCardDetails(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $query = JobCard::with('customer.userDetail')
            ->where('isDeleted', 0)
            ->where('id', $id);

        if ($user && strtolower($user->role) === 'staff') {
            // $query->where('user_id', $user->branch_id);
            $query->where('user_id', $user->id);
        } else {
            $query->where('subbranch_id', $branchId);
        }

        $jobcard = $query->first();

        if (!$jobcard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        $total = (float) ($jobcard->total_subtotal_amount ?? 0);
        $remaining = (float) ($jobcard->remaining_amount ?? 0);
        $paid = max($total - $remaining, 0);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jobcard->id,
                'job_card_number' => $jobcard->job_card_number,
                'customer_id' => $jobcard->customer_id,
                'customer_name' => $jobcard->customer_name ?? ($jobcard->customer->name ?? 'N/A'),
                'customer_phone' => $jobcard->customer->phone ?? $jobcard->mobile ?? '',
                'customer_address' => $jobcard->customer->userDetail->address ?? $jobcard->address ?? '',
                'total_amount' => $total,
                'remaining_amount' => $remaining,
                'paid_amount' => $paid,
            ]
        ]);
    }

    public function getPurchaseDetails(Request $request, $invoiceNumber)
    {
        $branchId = $this->resolveBranchId($request);
        
        $invoice = PurchaseInvoice::where('invoice_number', $invoiceNumber)
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->first();

        if (!$invoice) {
            return response()->json(['error' => 'Purchase Invoice not found'], 404);
        }

        $vendor = User::find($invoice->vendor_id);

        $purchaseData = [
            'id'               => $invoice->id,
            'vendor_id'        => $invoice->vendor_id,
            'invoice_number'   => $invoice->invoice_number,
            'vendor_name'      => $vendor->name ?? 'N/A',
            'total_amount'     => $invoice->grand_total ?? 0,
            'remaining_amount' => $invoice->remaining_amount ?? 0,
            'paid_amount'      => $invoice->paid ?? 0,
        ];

        return response()->json([
            'purchase' => $purchaseData
        ]);
    }


    public function store(Request $request)
    {
        $authUser = Auth::guard('api')->user();
        $userId   = $authUser->id;

        // 🔹 Decide branch ID properly
        if ($authUser->role == 'staff' && $authUser->branch_id) {
            $userBranchId = $authUser->branch_id; // staff uses their branch_id
        } elseif ($authUser->role == 'sub-admin') {
            $userBranchId = $authUser->id; // sub-admin uses own id
        } elseif ($authUser->role == 'admin' && ! empty($request->selectedSubAdminId)) {
            $userBranchId = (int) $request->selectedSubAdminId; // admin chooses sub-admin
        } else {
            $userBranchId = $authUser->id; // fallback to logged in user's id
        }
        // dd($userBranchId);
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:receipt,payment',
            'order_id' => 'required_if:transaction_type,receipt',
            'purchase_id' => 'required_if:transaction_type,payment',
            'user_id' => 'required',
            'credite_note_id' => 'required|exists:credit_notes_type,id',
            'total_amt' => 'required|numeric',
            'remaining_amt' => 'required|numeric',
            'total_paid' => 'required|numeric',
            'settlement_amount' => 'required|numeric',
            'total' => 'required|numeric',
            'reason' => 'required|string',
        ], [
            'order_id.required_if' => 'Please select a Job-card Number.',
            'purchase_id.required_if' => 'Please select an Invoice Number.',
            'credite_note_id.required' => 'Please select a Credit Note Type.',
            'reason.required' => 'Please provide a reason.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $creditNoteItem = CreditNoteItem::create([
                'type_id' => $request->transaction_type,
                'order_id' => $request->transaction_type === 'receipt' ? $request->order_id : null,
                'purchase_id' => $request->transaction_type === 'payment' ? $request->purchase_id : null,
                'user_id' => $request->user_id,
                'credite_note_id' => $request->credite_note_id,
                'branch_id'          => $userBranchId ?? $userId, // ✅ REQUIRED
                'total_amt' => $request->total_amt,
                'remaining_amt' => $request->remaining_amt,
                'total_paid' => $request->total_paid,
                'settlement_amount' => $request->settlement_amount,
                'total' => $request->total,
                'reason' => $request->reason,
                'isDeleted' => 0,
            ]);


            return response()->json([
                'status' => true,
                'message' => 'Credit Note Item created successfully!',
                'data' => $creditNoteItem
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $creditNoteItem = CreditNoteItem::with(['order.user.userDetail', 'purchaseInvoice.vendor.userDetail', 'creditNote'])
            ->where('branch_id', $branchId)
            ->find($id);

        if (!$creditNoteItem) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        // Attach jobcard info when type is receipt but order is missing
        if (in_array($creditNoteItem->type_id, ['receipt', 1, '1'], true) && !$creditNoteItem->order && !empty($creditNoteItem->order_id)) {
            $jobcardQuery = JobCard::with('customer.userDetail')
                ->where('isDeleted', 0)
                ->where('id', $creditNoteItem->order_id);

            if ($user && strtolower($user->role) === 'staff') {
                $jobcardQuery->where('user_id', $user->branch_id);
            } else {
                $jobcardQuery->where('subbranch_id', $branchId);
            }

            $jobcard = $jobcardQuery->first();
            if ($jobcard) {
                $creditNoteItem->setAttribute('jobcard', $jobcard);
            }
        }

        return response()->json(['status' => true, 'data' => $creditNoteItem], 200);
    }

    public function update(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $creditNoteItem = CreditNoteItem::where('branch_id', $branchId)->find($id);

        if (!$creditNoteItem) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:receipt,payment',
            'order_id' => 'required_if:transaction_type,receipt',
            'purchase_id' => 'required_if:transaction_type,payment',
            'user_id' => 'required',
            'credite_note_id' => 'required|exists:credit_notes_type,id',
            'total_amt' => 'required|numeric',
            'remaining_amt' => 'required|numeric',
            'total_paid' => 'required|numeric',
            'settlement_amount' => 'required|numeric',
            'total' => 'required|numeric',
            'reason' => 'required|string',
        ], [
            'order_id.required_if' => 'Please select a Job-card Number.',
            'purchase_id.required_if' => 'Please select an Invoice Number.',
            'credite_note_id.required' => 'Please select a Credit Note Type.',
            'reason.required' => 'Please provide a reason.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $creditNoteItem->update([
                'type_id' => $request->transaction_type,
                'order_id' => $request->transaction_type === 'receipt' ? $request->order_id : null,
                'purchase_id' => $request->transaction_type === 'payment' ? $request->purchase_id : null,
                'user_id' => $request->user_id,
                'credite_note_id' => $request->credite_note_id,
                'total_amt' => $request->total_amt,
                'remaining_amt' => $request->remaining_amt,
                'total_paid' => $request->total_paid,
                'settlement_amount' => $request->settlement_amount,
                'total' => $request->total,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Credit Note Item updated successfully!',
                'data' => $creditNoteItem
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $creditNoteItem = CreditNoteItem::where('branch_id', $branchId)->find($id);

        if (!$creditNoteItem) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        try {
            $creditNoteItem->update(['isDeleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Credit Note Item deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
