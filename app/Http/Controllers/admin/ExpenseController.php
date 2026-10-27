<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Setting;
use App\Models\ExpenseType;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function create_expense()
    {
        $expenseTypes = ExpenseType::where('isDeleted', 0)->get();
        return view('expense.createexpense', compact('expenseTypes'));
    }
    public function edit_expense()
    {

        return view('expense/editexpense');
    }
    public function expense_category()
    {

        return view('expense/expensecategory');
    }
    public function expense_list()
    {
        // Get distinct years from expense_date for dropdown
        $years = Expense::where('isDeleted', 0)
            ->selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Pass years to your view
        return view('expense.expenselist', compact('years'));
    }

    public function expense_report(Request $request)
    {
        return view('expense/expensereport');
    }
    public function expense_report_view($ids)
    {
        $idsArray = explode(',', $ids); // Convert "1,2,3" to [1,2,3]

        $expenses = Expense::whereIn('id', $idsArray)->get();

        if ($expenses->isEmpty()) {
            return redirect()->route('expense.report')->with('error', 'No expense data found.');
        }

        $settings = Setting::first();

        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        return view('expense.expense_report', compact('expenses', 'currencySymbol', 'settings', 'currencyPosition'));
    }
}
