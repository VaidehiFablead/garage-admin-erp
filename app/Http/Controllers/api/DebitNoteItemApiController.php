<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DebitNoteItem;
use App\Models\PurchaseInvoice;
use App\Models\Order;
use App\Models\PaymentStore;
use App\Models\CreditNotesType;
use App\Models\User;
use App\Models\JobCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DebitNoteItemApiController extends Controller
{
    private function resolveBranchId(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) return null;
        $role = strtolower($user->role);

        $selectedSubAdminId = $request->selectedSubAdminId;
        if ($selectedSubAdminId === 'null' || $selectedSubAdminId === 'undefined') {
            $selectedSubAdminId = null;
        }

        return match ($role) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $selectedSubAdminId ?: $user->id,
            default     => $user->id,
        };
    }

    public function index(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $user = Auth::guard('api')->user();

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);
        $applyPagination = $request->has('page') || $request->has('per_page');

        $query = DebitNoteItem::with([
            'creditNoteType',
            'order.user.userDetail',
            'purchaseInvoice.vendor.userDetail',
            'jobCard.customer'
        ])
        ->where(function($q) {
            $q->where('isDeleted', 0)->orWhereNull('isDeleted');
        })
        ->where('branch_id', $branchId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%')
                  ->orWhereHas('creditNoteType', function ($sq) use ($search) {
                      $sq->where('type_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('order.user', function ($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('purchaseInvoice.vendor', function ($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('jobCard.customer', function ($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('jobCard', function ($sq) use ($search) {
                      $sq->where('job_card_number', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('order', function ($sq) use ($search) {
                      $sq->where('order_number', 'like', '%' . $search . '%');
                  });
            });
        }

        $total = null;
        $totalSettlementAmount = 0;
        if ($applyPagination) {
            $total = (clone $query)->count();
            $totalSettlementAmount = (clone $query)->sum('settlement_amount');
        } else {
            $totalSettlementAmount = (clone $query)->sum('settlement_amount');
        }

        $query->orderBy('id', 'desc');

        if ($applyPagination) {
            $offset = ($page - 1) * $perPage;
            $items = $query->skip($offset)->take($perPage)->get();
        } else {
            $items = $query->get();
        }

        $response = [
            'status' => 'success',
            'data' => $items,
            'total_settlement_amount' => $totalSettlementAmount
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

    // public function getCreateData(Request $request)
    // {
    //     $branchId = $this->resolveBranchId($request);

    //     $creditNoteTypes = CreditNotesType::where('isdeleted', 0);
    //     $invoices = PurchaseInvoice::where('isDeleted', 0)->orderBy('id', 'desc');
    //     $orders = Order::where('isDeleted', 0)->orderBy('id', 'desc');
    //     $jobCards = JobCard::where('isDeleted', 0)
    //         ->where('type', 'jobCard')
    //         ->orderBy('id', 'desc');

    //     if ($branchId) {
    //         $creditNoteTypes->where('branch_id', $branchId);
    //         $invoices->where('branch_id', $branchId);
    //         $orders->where('branch_id', $branchId);
    //         $jobCards->where(function ($q) use ($branchId) {
    //             $q->where('subbranch_id', $branchId)
    //               ->orWhere('user_id', $branchId);
    //         });
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'creditNoteTypes' => $creditNoteTypes->get(),
    //         'invoices' => $invoices->get(),
    //         'orders' => $orders->get(),
    //         'jobCards' => $jobCards->get(['id', 'job_card_number', 'customer_id', 'total_subtotal_amount', 'remaining_amount'])
    //     ]);
    // }
    public function getCreateData(Request $request)
{
    $user = Auth::guard('api')->user();

    $UserID = $user->id;
    $role = $user->role;
    $userBranch = $user->branch_id;
    $selectedBranch = $request->selectedSubAdminId;

    if ($role == 'admin' && $selectedBranch) {
        $branchId = $selectedBranch;
    } elseif ($role == 'sub-admin') {
        $branchId = $UserID;
    } elseif ($role == 'staff') {
        $branchId = $userBranch;
    } else {
        $branchId = $UserID;
    }

    $creditNoteTypes = CreditNotesType::where('isdeleted', 0)
        ->where('branch_id', $branchId);

    if ($role == 'staff') {

        $invoices = PurchaseInvoice::where('isDeleted', 0)
            ->where('created_by', $UserID)
            ->orderBy('id', 'desc');

        $orders = Order::where('isDeleted', 0)
            ->where('created_by', $UserID)
            ->orderBy('id', 'desc');

        $jobCards = JobCard::where('isDeleted', 0)
            ->where('type', 'jobCard')
            ->where('user_id', $UserID)
            ->orderBy('id', 'desc');

    } else {

        $invoices = PurchaseInvoice::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('id', 'desc');

        $orders = Order::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->orderBy('id', 'desc');

        $jobCards = JobCard::where('isDeleted', 0)
            ->where('type', 'jobCard')
            ->where('subbranch_id', $branchId)
            ->orderBy('id', 'desc');
    }

    return response()->json([
        'status' => 'success',
        'creditNoteTypes' => $creditNoteTypes->get(),
        'invoices' => $invoices->get(),
        'orders' => $orders->get(),
        'jobCards' => $jobCards->get([
            'id',
            'job_card_number',
            'customer_id',
            'total_subtotal_amount',
            'remaining_amount'
        ])
    ]);
}
    public function getJobCardDetails(Request $request, $job_card_number)
    {
        $branchId = $this->resolveBranchId($request);

        $query = JobCard::with('customer')
            ->where('job_card_number', $job_card_number)
            ->where('isDeleted', 0);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('subbranch_id', $branchId)
                  ->orWhere('user_id', $branchId);
            });
        }

        $jobCard = $query->first();

        if (!$jobCard) {
            return response()->json(['status' => 'error', 'message' => 'Job card not found'], 404);
        }

        $totalAmount = (float) ($jobCard->total_subtotal_amount ?? 0);
        $remaining = (float) ($jobCard->remaining_amount ?? 0);
        $paidAmount = max(0, $totalAmount - $remaining);

        return response()->json([
            'status' => 'success',
            'jobcard' => [
                'id' => $jobCard->id,
                'customer_id' => $jobCard->customer_id,
                'customer_name' => $jobCard->customer->name ?? 'N/A',
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remaining,
            ]
        ]);
    }

    public function getOrderDetails(Request $request, $order_number)
    {
        $order = Order::with('user.userDetail')
            ->where('order_number', $order_number)
            ->where('isDeleted', 0)
            ->first();

        if ($order) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'customer_name' => $order->user->name ?? 'N/A',
                    'total_amount' => $order->total_amount,
                    'paid_amount' => $order->paid ?? 0,
                    'remaining_amount' => $order->remaining_amount,
                ]
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
    }

    public function getInvoiceDetails(Request $request, $invoice_number)
    {
        $invoice = PurchaseInvoice::with('vendor.userDetail')
            ->where('invoice_number', $invoice_number)
            ->where('isDeleted', 0)
            ->first();

        if ($invoice) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $invoice->id,
                    'vendor_id' => $invoice->vendor_id,
                    'vendor_name' => $invoice->vendor->name ?? 'N/A',
                    'total_amount' => $invoice->grand_total,
                    'paid_amount' => $invoice->paid ?? 0,
                    'remaining_amount' => $invoice->remaining_amount,
                ]
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invoice not found'], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:receipt,payment',
            'order_id' => 'required_if:transaction_type,receipt',
            'purchase_id' => 'required_if:transaction_type,payment',
            'credit_note_type' => 'required',
            'settlement_amount' => 'required|numeric',
            'reason' => 'required',
        ], [
            'order_id.required_if' => 'Please select an Order Number.',
            'purchase_id.required_if' => 'Please select an Invoice Number.',
            'credit_note_type.required' => 'Please select a Debit Note Type.',
            'reason.required' => 'Please provide a reason.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $branchId = $this->resolveBranchId($request);

        $debitNote = new DebitNoteItem();
        $debitNote->user_id = $user->id;
        $debitNote->branch_id = $branchId ?: $user->id;
        $debitNote->transaction_type = $request->transaction_type;
        $debitNote->order_id = $request->transaction_type === 'receipt' ? $request->order_id : null;
        $debitNote->purchase_id = $request->transaction_type === 'payment' ? $request->purchase_id : null;
        $debitNote->create_note_id = $request->credit_note_type;
        $debitNote->invoice_number = $request->invoice_number;
        $debitNote->grand_total = $request->total_amount;
        $debitNote->total_paid = $request->paid_amount;
        $debitNote->remaning_amount = $request->remaining_amount;
        $debitNote->settlement_amount = $request->settlement_amount;
        $debitNote->total = $request->final_total;
        $debitNote->reason = $request->reason;
        $debitNote->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Debit Note created successfully',
            'data' => $debitNote
        ]);
    }

    public function show(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $item = DebitNoteItem::with([
                'creditNoteType',
                'order.user.userDetail',
                'purchaseInvoice.vendor.userDetail',
                'jobCard.customer'
            ])
            ->where('branch_id', $branchId)
            ->find($id);
        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:receipt,payment',
            'order_id' => 'required_if:transaction_type,receipt',
            'purchase_id' => 'required_if:transaction_type,payment',
            'credit_note_type' => 'required',
            'settlement_amount' => 'required|numeric',
            'reason' => 'required',
        ], [
            'order_id.required_if' => 'Please select an Order Number.',
            'purchase_id.required_if' => 'Please select an Invoice Number.',
            'credit_note_type.required' => 'Please select a Debit Note Type.',
            'reason.required' => 'Please provide a reason.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $branchId = $this->resolveBranchId($request);
        $debitNote = DebitNoteItem::where('branch_id', $branchId)->find($id);
        if (!$debitNote) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $debitNote->transaction_type = $request->transaction_type;
        $debitNote->order_id = $request->transaction_type === 'receipt' ? $request->order_id : null;
        $debitNote->purchase_id = $request->transaction_type === 'payment' ? $request->purchase_id : null;
        $debitNote->create_note_id = $request->credit_note_type;
        $debitNote->invoice_number = $request->invoice_number;
        $debitNote->grand_total = $request->total_amount;
        $debitNote->total_paid = $request->paid_amount;
        $debitNote->remaning_amount = $request->remaining_amount;
        $debitNote->settlement_amount = $request->settlement_amount;
        $debitNote->total = $request->final_total;
        $debitNote->reason = $request->reason;
        $debitNote->save();

        return response()->json(['status' => 'success', 'message' => 'Debit Note updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $branchId = $this->resolveBranchId($request);
        $item = DebitNoteItem::where('branch_id', $branchId)->find($id);
        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }
        $item->isDeleted = 1;
        $item->save();
        return response()->json(['status' => 'success', 'message' => 'Debit Note deleted successfully']);
    }
}
