<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DebitNoteItem;
use App\Models\CustomInvoice;
use App\Models\Order;
use App\Models\CreditNotesType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebitNoteItemController extends Controller
{
    public function index(Request $request)
    {
        return view('debit_notes_items.index');
    }

    public function create()
    {
        return view('debit_notes_items.create');
    }

    public function edit($id)
    {
        $debitNoteItem = DebitNoteItem::findOrFail($id);

        $user           = auth()->user();
        $UserID         = $user->id;
        $role           = $user->role;
        $userBranch     = $user->branch_id;
        $selectedBranch = session('selectedSubAdminId');

        // Decide branch correctly
        if ($role == 'admin' && $selectedBranch) {
            $branchId = $selectedBranch;
        } elseif ($role == 'sub-admin') {
            $branchId = $UserID;
        } elseif ($role == 'staff') {
            $branchId = $userBranch;
        } else {
            $branchId = $UserID;
        }

        if ($debitNoteItem->branch_id != $branchId) {
            abort(403, 'Unauthorized action.');
        }

        return view('debit_notes_items.edit', compact('id'));
    }

    public function show($id)
    {
        $debitNoteItem = DebitNoteItem::findOrFail($id);

        $user           = auth()->user();
        $UserID         = $user->id;
        $role           = $user->role;
        $userBranch     = $user->branch_id;
        $selectedBranch = session('selectedSubAdminId');

        // Decide branch correctly
        if ($role == 'admin' && $selectedBranch) {
            $branchId = $selectedBranch;
        } elseif ($role == 'sub-admin') {
            $branchId = $UserID;
        } elseif ($role == 'staff') {
            $branchId = $userBranch;
        } else {
            $branchId = $UserID;
        }

        if ($debitNoteItem->branch_id != $branchId) {
            abort(403, 'Unauthorized action.');
        }

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();

        return view('debit_notes_items.show', [
            'id' => $id,
            'currencySymbol'   => $settings->currency_symbol ?? '₹',
            'currencyPosition' => $settings->currency_position ?? 'left',
            'compenyinfo' => $settings
        ]);
    }
}
