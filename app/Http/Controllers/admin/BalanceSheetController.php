<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{

    public function index(Request $request)
    {
        $branchId = session('selectedSubAdminId') ?? (auth()->check() ? (auth()->user()->branch_id ?? auth()->user()->id) : null);
        $startDate = $request->start_date ?? \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? \Carbon\Carbon::now()->endOfMonth()->toDateString();

        // ======================
        // 🏷️ Currency Settings & Company Info
        // ======================
        $settings = DB::table('settings')->first();
        $sym      = $settings->currency_symbol ?? ($settings->currencySymbol ?? '₹');
        $pos      = $settings->currency_position ?? ($settings->currencyPosition ?? 'left');
        
        $companyName = $settings->name ?? 'Company Name';
        $companyAddress = $settings->address ?? 'Address Line';
        $companyPhone = $settings->phone ?? 'Phone';

        // ======================
        // 🧾 1️⃣ ASSETS
        // ======================

        // Cash (sum of all recorded cash payments)
        
        $cash = DB::table('payment_store')
            ->join('orders', 'orders.id', '=', 'payment_store.order_id')
            ->where('payment_store.isDeleted', 0)
            ->whereIn('payment_store.payment_method', ['cash', 'Cash'])
            ->where(function ($q) {
                $q->whereNotNull('payment_store.order_id')
                    ->where('payment_store.order_id', '<>', '')
                    ->where('payment_store.order_id', '>', 0);
            })
            ->where(function ($q) {
                $q->whereNull('payment_store.purchase_id')
                    ->orWhere('payment_store.purchase_id', '=', 0)
                    ->orWhere('payment_store.purchase_id', '=', '');
            })
            ->where(function ($q) {
                $q->whereNull('payment_store.custom_invoice_id')
                    ->orWhere('payment_store.custom_invoice_id', '=', 0)
                    ->orWhere('payment_store.custom_invoice_id', '=', '');
            })
            ->when($branchId, fn($q) => $q->where('orders.branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('payment_store.payment_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('payment_store.payment_date', '<=', $endDate))
            ->sum('payment_store.payment_amount');

            // Bank (UPI / Bank / Online)
        $bank = DB::table('payment_store')
            ->join('orders', 'orders.id', '=', 'payment_store.order_id')
            ->where('payment_store.isDeleted', 0)
            ->whereIn('payment_store.payment_method', ['upi', 'bank', 'online','Online', 'debit card', 'Debit Card', 'Debit card', 'scan', 'Scan'])
            ->where(function ($q) {
                $q->whereNotNull('payment_store.order_id')
                    ->where('payment_store.order_id', '<>', '')
                    ->where('payment_store.order_id', '>', 0);
            })
            ->where(function ($q) {
                $q->whereNull('payment_store.purchase_id')
                    ->orWhere('payment_store.purchase_id', '=', 0)
                    ->orWhere('payment_store.purchase_id', '=', '');
            })
            ->where(function ($q) {
                $q->whereNull('payment_store.custom_invoice_id')
                    ->orWhere('payment_store.custom_invoice_id', '=', 0)
                    ->orWhere('payment_store.custom_invoice_id', '=', '');
            })
            ->when($branchId, fn($q) => $q->where('orders.branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('payment_store.payment_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('payment_store.payment_date', '<=', $endDate))
            ->sum('payment_store.payment_amount');

        // Inventory value (sum of product quantity * price)
        $inventory = DB::table('products')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum(DB::raw('quantity * price'));

        $assetGroups = [
            'Fixed Assets' => [
                // Add fixed assets queries here when available
            ],
            'Investments' => [
                // Add investments queries here when available
            ],
            'Current Assets' => [
                'Closing Stock' => $inventory,
                'Cash-in-Hand' => $cash,
                'Bank Accounts' => $bank,
            ],
        ];

        // Total Assets
        $totalAssets = $cash + $bank + $inventory;


        // ======================
        // 💸 2️⃣ LIABILITIES
        // ======================

        // Accounts Payable (Unpaid purchase invoices)
        $accountsPayable = DB::table('purchase_invoice')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('remaining_amount');

        // GST Payable (GST from Orders marked as with GST)
        $gstPayable = DB::table('orders')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('gst_option', 1)
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum(DB::raw('(total_amount * 18) / 100')); // change 18 if dynamic tax_id system

        $totalSales = DB::table('orders')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('total_amount');

        $totalPurchases = DB::table('purchase_invoice')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('grand_total');

        $totalExpenses = DB::table('expenses')
            ->where('isDeleted', 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($startDate, fn($q) => $q->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('expense_date', '<=', $endDate))
            ->sum('amount');

        $totalLiabilities = $accountsPayable + $gstPayable;

        // Retained Earnings = Total Assets - Total Liabilities
        $retainedEarnings = $totalAssets - $totalLiabilities;

        $liabilityGroups = [
            'Capital Account' => [
                // Add capital accounts queries here when available
            ],
            'Loans (Liability)' => [
                // Add loans queries here when available
            ],
            'Current Liabilities' => [
                'Accounts Payable' => $accountsPayable,
            ],
        ];
        
        if ($gstPayable > 0) {
            $liabilityGroups['Current Liabilities']['Duties & Taxes (GST)'] = $gstPayable;
        }

        $liabilityGroups['Profit & Loss A/c'] = [
            'Current Period' => $retainedEarnings,
        ];

        // ======================
        // 📘 4️⃣ TOTALS
        // ======================
        $totals = [
            'assets'             => $totalAssets,
            'liabilities_equity' => $totalLiabilities + $retainedEarnings,
        ];

        // ======================
        // 📄 5️⃣ RETURN VIEW OR EXPORT
        // ======================
        $data = compact(
            'assetGroups',
            'liabilityGroups',
            'totals',
            'sym',
            'pos',
            'companyName',
            'companyAddress',
            'companyPhone',
            'startDate',
            'endDate',
            'settings'
        );

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounting.balance-sheet-print', $data);
            return $pdf->download('Balance_Sheet_'.$startDate.'_to_'.$endDate.'.pdf');
        }

        if ($request->export === 'excel') {
            return $this->downloadBalanceSheetExcel($data, $startDate, $endDate);
        }

        return view('accounting.balance-sheet', $data);
    }

    private function downloadBalanceSheetExcel(array $data, string $startDate, string $endDate)
    {
        $sym     = $data['sym'] ?? '₹';
        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-M-Y') : '';
        $fmtAmt  = fn($v) => number_format((float) $v, 2);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Balance Sheet');

        // ── Styles helpers ──────────────────────────────────────────────
        $headerBg  = 'FF1B2850'; // dark navy
        $groupBg   = 'FFF3F4F6'; // light grey
        $totalBg   = 'FFFFF3CD'; // light yellow
        $white     = 'FFFFFFFF';

        $applyStyle = function (string $range, array $style) use ($sheet) {
            $sheet->getStyle($range)->applyFromArray($style);
        };

        // ── Company header ───────────────────────────────────────────────
        $row = 1;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", $data['companyName'] ?? '');
        $applyStyle("A{$row}:D{$row}", [
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", $data['companyAddress'] ?? '');
        $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'Contact: ' . ($data['companyPhone'] ?? ''));
        $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'Balance Sheet');
        $applyStyle("A{$row}:D{$row}", [
            'font'      => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'From ' . $fmtDate($startDate) . ' to ' . $fmtDate($endDate));
        $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;  // blank

        // ── Column headers ───────────────────────────────────────────────
        $row++;
        $sheet->setCellValue("A{$row}", 'ASSETS');
        $sheet->setCellValue("C{$row}", 'LIABILITIES & EQUITY');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:D{$row}");
        $applyStyle("A{$row}:D{$row}", [
            'font'      => ['bold' => true, 'color' => ['argb' => $white]],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBg]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $row++;
        $sheet->setCellValue("A{$row}", 'Particulars');
        $sheet->setCellValue("B{$row}", 'Amount (' . $sym . ')');
        $sheet->setCellValue("C{$row}", 'Particulars');
        $sheet->setCellValue("D{$row}", 'Amount (' . $sym . ')');
        $applyStyle("A{$row}:D{$row}", [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $groupBg]],
        ]);

        // ── Flatten groups into rows ─────────────────────────────────────
        $flattenGroups = function (array $groups) use ($fmtAmt, $sym): array {
            $lines = [];
            foreach ($groups as $groupName => $items) {
                $lines[] = [$groupName, '', true];  // [label, amount, isGroupHeader]
                foreach ($items as $label => $value) {
                    $lines[] = ['  ' . $label, $sym . $fmtAmt($value), false];
                }
                $lines[] = ['', '', false];  // spacer
            }
            return $lines;
        };

        $assetLines     = $flattenGroups($data['assetGroups'] ?? []);
        $liabilityLines = $flattenGroups($data['liabilityGroups'] ?? []);
        $maxRows        = max(count($assetLines), count($liabilityLines));

        $dataStartRow = $row + 1;
        for ($i = 0; $i < $maxRows; $i++) {
            $row++;
            $aLine = $assetLines[$i]     ?? ['', '', false];
            $lLine = $liabilityLines[$i] ?? ['', '', false];

            $sheet->setCellValue("A{$row}", $aLine[0]);
            $sheet->setCellValue("B{$row}", $aLine[1]);
            $sheet->setCellValue("C{$row}", $lLine[0]);
            $sheet->setCellValue("D{$row}", $lLine[1]);

            // Group heading style
            if ($aLine[2] || $lLine[2]) {
                $applyStyle("A{$row}:D{$row}", [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $groupBg]],
                ]);
            }

            // Right-align amount columns
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        // ── Totals row ───────────────────────────────────────────────────
        $row++;
        $totals = $data['totals'] ?? [];
        $sheet->setCellValue("A{$row}", 'Total Assets');
        $sheet->setCellValue("B{$row}", $sym . $fmtAmt($totals['assets'] ?? 0));
        $sheet->setCellValue("C{$row}", 'Total Liabilities & Equity');
        $sheet->setCellValue("D{$row}", $sym . $fmtAmt($totals['liabilities_equity'] ?? 0));
        $applyStyle("A{$row}:D{$row}", [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $totalBg]],
        ]);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // ── Column widths ────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(32);
        $sheet->getColumnDimension('D')->setWidth(20);

        // ── Stream response ──────────────────────────────────────────────
        $filename = 'Balance_Sheet_' . $startDate . '_to_' . $endDate . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
