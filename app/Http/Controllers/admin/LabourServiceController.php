<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Import base Controller class
use App\Models\LabourService;

class LabourServiceController extends Controller
{
    public function labour_list(Request $request)
    {
        return view('labour_service/labour_list');
    }
    public function add_labour(Request $request)
    {
        return view('labour_service/add_service');
    }
    public function edit_labour(Request $request, $id)
    {
        $labourService = LabourService::findOrFail($id);

        return view('labour_service/edit_service', compact('labourService'));
    }

    public function import_labour(Request $request)
    {
        return view('labour_service/import_labour');
    }

    public function downloadSampleCsv()
    {
        $csv = "Service Name,Price\n"
             . "Full Engine Service,800.00\n"
             . "Full Body Service,100.00\n"
             . "Full Service,10000.00\n"
             . "Car Wash,200.00\n"
             . "Wheel Alignment,350.00\n"
             . "Brake Pad Replacement,450.00\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="LabourServicesSample.csv"',
        ]);
    }

    public function downloadSampleExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Labour Services');
        $sheet->setCellValue('A1', 'Service Name');
        $sheet->setCellValue('B1', 'Price');

        // Style header row
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $rows = [
            ['Full Engine Service', 800.00],
            ['Full Body Service', 100.00],
            ['Full Service', 10000.00],
            ['Car Wash', 200.00],
            ['Wheel Alignment', 350.00],
            ['Brake Pad Replacement', 450.00],
        ];

        $rowNum = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $rowNum++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'LabourServicesSample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
