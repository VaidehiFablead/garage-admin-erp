<?php

namespace App\Http\Controllers\admin;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\JobCard;
use App\Models\AutoCarModel;
use App\Models\User;
use App\Models\PaymentStore;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PDF;

class DailyDataController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if(strtolower($user->role) == 'staff'){
            $subAdminId = $user->branch_id;
            $UserId = $user->id;
        }else{
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        }
        $month = $request->query('month');
        $year = $request->query('year');    
        $date = $request->query('date');

        // 1️⃣ Build base query with eager loading of related data
        $jobCardsQuery = JobCard::with([
            'expenseStores' => function ($query) {
                $query->where('isDeleted', 0);
            },
            'autocarmodel',
            'technicianUser',
            'payments' => function ($query) {
                $query->where('isDeleted', 0);
            }
        ])
            ->leftJoin('users', 'users.id', '=', 'job_cards.customer_id') // 👈 join users table
            ->where('job_cards.isDeleted', 0)
            ->where('job_cards.type', 'dailyData')
            ->select('job_cards.*', 'users.name as customer_name');


            // dd($user->role);
        if (strtolower($user->role) == 'staff') {
            $jobCardsQuery->where('user_id', $UserId);
            // $jobCardsQuery->where('payment_type', 'partial');
        }
        if (!empty($subAdminId)) {
            $jobCardsQuery->where('subbranch_id', $subAdminId);
        }

        // 2️⃣ Apply filters
        if ($date) {
            $jobCardsQuery->whereDate('record_date', \Carbon\Carbon::parse($date)->format('Y-m-d'));
        } elseif ($month || $year) {
            if ($month) {
                $jobCardsQuery->whereMonth('record_date', $month);
            }
            if ($year) {
                $jobCardsQuery->whereYear('record_date', $year);
            }
        }

        // 3️⃣ Fetch and group job cards by date
        $jobCards = $jobCardsQuery->orderBy('record_date', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->record_date)->format('d-m-Y');
            });

        // 4️⃣ Flatten all job cards for total calculations
        $allJobCards = $jobCards->flatten();

        // 5️⃣ Totals across filtered records
        $cashTotal = $allJobCards
            ->where('payment_method', 'cash')
            ->flatMap
            ->payments
            ->sum('payment_amount');

        $onlineTotal = $allJobCards
            ->where('payment_method', 'online')
            ->flatMap
            ->payments
            ->sum('payment_amount');

        // ✅ Get expenses directly from expenses table
        $expenseTotal = Expense::whereIn('expense_date', $allJobCards->pluck('record_date')->unique())
            ->where('branch_id', $subAdminId)
            ->sum('amount');

        $combined = $cashTotal + $onlineTotal;
        $total = $cashTotal - $expenseTotal;

        $dailySummaries = [];

        // ----- Per Day Totals -----
        foreach ($jobCards as $dateKey => $cards) {

        // Collect all payments for these jobcards on this date
        $paymentIds = $cards->pluck('id'); // Assuming 'id' is jobcard id or key to payments

        $cashTotal = PaymentStore::whereIn('jobcard_id', $paymentIds)
            ->where('payment_method', 'cash')
            ->sum('payment_amount');

        $onlineTotal = PaymentStore::whereIn('jobcard_id', $paymentIds)
            ->where('payment_method', 'online')
            ->sum('payment_amount');

        // Get expenses for this specific date from expenses table
        

        $expenseTotal = Expense::whereDate(
            'expense_date',
            \Carbon\Carbon::createFromFormat('d-m-Y', $dateKey)->format('Y-m-d')
        )
        ->where('branch_id', $subAdminId)->sum('amount');
 

 

       

        $combined = $cashTotal + $onlineTotal;
        $total = $cashTotal - $expenseTotal;

        $dailySummaries[$dateKey] = [
            'cash' => $cashTotal,
            'online' => $onlineTotal,
            'expenses' => $expenseTotal,
            'total' => $total,
            'combined' => $combined,
        ];
        }

        $years = JobCard::where('isDeleted', 0)
            ->pluck('record_date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y'))
            ->unique()
            ->sort()
            ->values();

        $technicians = User::whereIn('role', ['Staff', 'staff'])
            ->when($subAdminId, function ($query, $subAdminId) {
                // If subAdminId exists, filter by branch_id
                return $query->where('branch_id', $subAdminId);
            })
            ->get();

            $customers = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->when($subAdminId, function ($query, $subAdminId) {
                return $query->where('branch_id', $subAdminId);
            })
            ->get();

        
        // dd($user->branch_id);
             
        if(strtolower($user->role) == 'staff'){
        $companyData = Setting::where('branch_id', $user->branch_id) // add your branch ID variable here
                    ->first();
    }else{
            $companyData = Setting::where('branch_id', $subAdminId) // add your branch ID variable here
                            ->first();
        }
        // 8️⃣ Return view
        return view('daily-data.index', compact(
            'jobCards',
            'cashTotal',
            'onlineTotal',
            'expenseTotal',
            'total',
            'combined',
            'allJobCards',
            'dailySummaries',
            'technicians',
            'years',
            'customers',
            'companyData'
        ));
    }



    public function create(Request $request)
    {
        $user = Auth::user();
        if(strtolower($user->role) == 'staff'){
            $subAdminId = $user->branch_id;
        }else{
        $subAdminId = session('selectedSubAdminId') ?? $user->id;
        }
        $technicians = User::whereIn('role', ['Staff', 'staff'])
            ->where('isDeleted', 0)
            ->when($subAdminId, function ($query, $subAdminId) {
                return $query->where('branch_id', $subAdminId);
            })
            ->get();

        $auto_car_model = AutoCarModel::when($subAdminId, function ($query, $subAdminId) {
                return $query->where('branch_id', $subAdminId);
            })
            ->get();

        $customers = User::where('role', 'customer')
            ->where('isDeleted', 0)
            ->when($subAdminId, function ($query, $subAdminId) {
                return $query->where('branch_id', $subAdminId);
            })
            ->get();

        return view('daily-data.add-daily-data', compact('technicians', 'auto_car_model', 'customers'));
    }




    public function downloadCSV(Request $request)
    {
        $inputDate = $request->query('date');
        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $inputDate)->format('Y-m-d');

        if (!$date) {
            abort(400, 'Date parameter is required.');
        }

        $jobcards = JobCard::with(['technicianUser', 'payments', 'autocarmodel', 'expenseStores', 'autoCustomer'])
            ->where('isDeleted', 0)
            ->where('type', 'dailyData')
            ->whereDate('record_date', $date)
            ->get();

        if ($jobcards->isEmpty()) {
            abort(404, 'No records found for this date.');
        }

        $expenses = \App\Models\Expense::whereDate('expense_date', $date)
            ->where('isDeleted', 0)
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', "Daily Data for " . date('d-m-Y', strtotime($date)));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $headers = [
            'Sr No',
            'Date',
            'Customer Name',
            'Mobile No',
            'Vehicle',
            'Vehicle No',
            'Amount',
            'Recive Amount',
            'Advance Paid',
            'Discount Amount',
            'Job Done By',
            'Amount Due/Recive',
            'Start Date',
            'End Date',
            'Payment Mode',
            'Status'
        ];

        $colIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colIndex . '2', value: $header);
            $sheet->getStyle($colIndex . '2')->getFont()->setBold(true);
            $colIndex++;
        }

        $rowIndex = 3;
        $srNo = 1;
        $totalCash = 0;
        $totalOnline = 0;
        $totalExpense = $expenses->sum('amount');
        $overallTotal = 0;
        $discountAmount = 0;
        foreach ($jobcards as $card) {
            $receivedAmount = $card->payments->sum('payment_amount');
            $discountAmount += $card->discount;
            $technician = optional($card->technicianUser)->name;
            $balanceStatus = ($card->payment_type === 'full') ? 'Received' : 'Due';

            // Track payment methods text and totals separately
            $methodLabels = [];
            foreach ($card->payments as $payment) {
                $method = strtolower($payment->payment_method);
                $amount = $payment->payment_amount;

                if ($method === 'cash') {
                    $totalCash += $amount;
                    $methodLabels[] = 'Cash';
                } elseif ($method === 'gpay' || $method === 'online') {
                    $totalOnline += $amount;
                    $methodLabels[] = 'Online';
                } elseif ($method === 'cashonline') {
                    // If saved directly as "cashonline"
                    // You might want to decide how to split this
                    $half = $amount / 2; 
                    $totalCash += $half;
                    $totalOnline += $half;
                    $methodLabels[] = 'Cash + Online';
                } else {
                    $methodLabels[] = ucfirst($method);
                }
            }

            // Remove duplicates and join for display
            $paymentMethods = implode(' + ', array_unique($methodLabels));

            // Fill Excel sheet
            $sheet->setCellValue("A$rowIndex", $srNo);
            $sheet->setCellValue("B$rowIndex", $card->record_date);
            $sheet->setCellValue("C$rowIndex", $card->autoCustomer->name);
            $sheet->setCellValue("D$rowIndex", $card->mobile);
            $sheet->setCellValue("E$rowIndex", $card->autocarmodel->model_name ?? 'N/A');
            $sheet->setCellValue("F$rowIndex", $card->vehicle_no);
            $sheet->setCellValue("G$rowIndex", $card->total_subtotal_amount);
            $sheet->setCellValue("H$rowIndex", $receivedAmount);
            $sheet->setCellValue("I$rowIndex", $card->extra_pay);
            $sheet->setCellValue("J$rowIndex", $card->discount);
            $sheet->setCellValue("K$rowIndex", $technician);
            $sheet->setCellValue("L$rowIndex", $balanceStatus);
            $sheet->setCellValue("M$rowIndex", $card->start_date);
            $sheet->setCellValue("N$rowIndex", $card->end_date);
            $sheet->setCellValue("O$rowIndex", $paymentMethods);
            $sheet->setCellValue("P$rowIndex", $card->status);

            $srNo++;
            $rowIndex++;
        }


        $overallTotal = $totalCash - $totalExpense;
        $summaryRowStart = $rowIndex + 2;

        
        $sheet->setCellValue("B$summaryRowStart", 'Total Discount');
        $sheet->setCellValue("C$summaryRowStart", $discountAmount);

        $sheet->setCellValue("B" . ($summaryRowStart + 1), 'Total Cash');
        $sheet->setCellValue("C" . ($summaryRowStart + 1), $totalCash);

        $sheet->setCellValue("B" . ($summaryRowStart + 2), 'Total Online');
        $sheet->setCellValue("C" . ($summaryRowStart + 2), $totalOnline);

        $sheet->setCellValue("B" . ($summaryRowStart + 3), 'Total Expenses');
        $sheet->setCellValue("C" . ($summaryRowStart + 3), $totalExpense);

        $sheet->setCellValue("B" . ($summaryRowStart + 4), 'Work Total Cash');
        $sheet->setCellValue("C" . ($summaryRowStart + 4), $overallTotal);

        $sheet->setCellValue("B" . ($summaryRowStart + 5), 'Cash + Online');
        $sheet->setCellValue("C" . ($summaryRowStart + 5), $totalCash + $totalOnline);

        for ($i = 0; $i < 6; $i++) {
            $sheet->getStyle("B" . ($summaryRowStart + $i))->getFont()->setBold(true);
            $sheet->getStyle("C" . ($summaryRowStart + $i))->getFont()->setBold(true);

            // ✅ Add border to both columns B and C
            $sheet->getStyle("B" . ($summaryRowStart + $i) . ":C" . ($summaryRowStart + $i))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $dataRange = "A2:P" . ($rowIndex - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // ------------------------ EXPENSE TABLE SECTION ------------------------
        $expenseTableStartCol = 'E'; // Start column for expense table
        $expenseTableStartRow = $summaryRowStart; // Same row as totals section

        // Title row for expense table
        $sheet->mergeCells("{$expenseTableStartCol}{$expenseTableStartRow}:" . chr(ord($expenseTableStartCol) + 4) . "{$expenseTableStartRow}");
        $sheet->setCellValue("{$expenseTableStartCol}{$expenseTableStartRow}", "Expenses for " . date('d-m-Y', strtotime($date)));
        $sheet->getStyle("{$expenseTableStartCol}{$expenseTableStartRow}")->getFont()->setBold(true)->setSize(12);

        // Headers
        $expenseHeaders = ['Sr No', 'Expense Name', 'Amount', 'Description', 'Payment Method'];
        $colIndex = $expenseTableStartCol;
        foreach ($expenseHeaders as $header) {
            $sheet->setCellValue($colIndex . ($expenseTableStartRow + 1), $header);
            $sheet->getStyle($colIndex . ($expenseTableStartRow + 1))->getFont()->setBold(true);
            $colIndex++;
        }

        // Data
        $expenseRowIndex = $expenseTableStartRow + 2;
        $expenseSr = 1;

        foreach ($expenses as $expense) {
            $sheet->setCellValue("{$expenseTableStartCol}{$expenseRowIndex}", $expenseSr);
            $sheet->setCellValue(chr(ord($expenseTableStartCol) + 1) . "{$expenseRowIndex}", $expense->expense_name);
            $sheet->setCellValue(chr(ord($expenseTableStartCol) + 2) . "{$expenseRowIndex}", $expense->amount);
            $sheet->setCellValue(chr(ord($expenseTableStartCol) + 3) . "{$expenseRowIndex}", $expense->description);
            $sheet->setCellValue(chr(ord($expenseTableStartCol) + 4) . "{$expenseRowIndex}", ucfirst($expense->payment_method));

            $expenseSr++;
            $expenseRowIndex++;
        }

        // Total row for expenses
        $sheet->setCellValue(chr(ord($expenseTableStartCol) + 1) . "{$expenseRowIndex}", 'Total Expense');
        $sheet->setCellValue(chr(ord($expenseTableStartCol) + 2) . "{$expenseRowIndex}", $totalExpense);
        $sheet->getStyle(chr(ord($expenseTableStartCol) + 1) . "{$expenseRowIndex}:" . chr(ord($expenseTableStartCol) + 2) . "{$expenseRowIndex}")
            ->getFont()->setBold(true);

        // Border around expense table
        $sheet->getStyle(
            "{$expenseTableStartCol}" . ($expenseTableStartRow + 1) . ":" .
            chr(ord($expenseTableStartCol) + 4) . $expenseRowIndex
        )->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        // ----------------------------------------------------------------------

        $fileName = "daily_data_$date.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloaddailydataReport(Request $request)
    {
        $date = $request->input('date'); // Optional filter

        // --- Fetch Job Cards ---
        // $jobCardQuery = JobCard::with(['technicianUser', 'payments', 'autoCustomer']);

        $jobCardQuery = JobCard::with([
            'technicianUser',
            'payments' => function ($query) {
                $query->where('isDeleted', 0);
            },
            'autocarmodel'
        ])
            ->leftJoin('users', 'users.id', '=', 'job_cards.customer_id') // join users table
            ->where('job_cards.isDeleted', 0)
            ->where('job_cards.type', 'dailyData')
            ->select('job_cards.*', 'users.name as customer_name');


        if ($date) {
            $parsedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            $jobCardQuery->whereDate('record_date', $parsedDate);
        }

        $jobCards = $jobCardQuery->get();

        // --- Fetch Expenses ---
        $expenseQuery = Expense::query();
        if ($date) {
            $expenseQuery->whereDate('expense_date', $parsedDate);
        }
        $expenses = $expenseQuery->get();

        $jobCardIds = $jobCards->pluck('id'); // all job card IDs in your filtered set

        $cashTotal = \DB::table('payment_store')
            ->whereIn('jobcard_id', $jobCardIds)
            ->where('payment_method', 'cash')
            ->sum('payment_amount');

        $onlineTotal = \DB::table('payment_store')
            ->whereIn('jobcard_id', $jobCardIds)
            ->where('payment_method', 'online')
            ->sum('payment_amount');

        $expenseTotal = $expenses->sum('amount');

        $discountTotal = \DB::table('job_cards')
        ->whereIn('id', $jobCardIds)
        ->sum('discount');

        $companyData = \DB::table('settings')->first();

        $user_id = Auth::id();
        $subAdminId = session('selectedSubAdminId') ?? $user_id;

        $companyData = Setting::where('branch_id', $subAdminId) // add your branch ID variable here
                        ->first();
       

        $total = $cashTotal + $onlineTotal;
        $combined = $cashTotal - $expenseTotal;

        // --- Prepare Data for PDF ---
        $pdf = PDF::loadView('daily-data.daily-data-report', [
            'jobCards' => $jobCards,
            'expenses' => $expenses,
            'cashTotal' => $cashTotal,
            'onlineTotal' => $onlineTotal,
            'expenseTotal' => $expenseTotal,
            'discountTotal' => $discountTotal,
            'total' => $total,
            'combined' => $combined,
            'companyData' => $companyData,
            'downloadDate' => Carbon::parse($date)->format('d-m-Y')
        ])->setPaper('a4', 'portrait');

        return $pdf->download('daily-data-report.pdf');
    }






}
