<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AutoCarCategory;

class AutoCarCategoryController extends Controller
{
    public function autoCategoryList(Request $request)
    {
        return view('autocar_category.list');
    }

    public function addAutoCategory(Request $request)
    {
        return view('autocar_category.add');
    }

    public function editAutoCategory(Request $request, $id)
    {
        $autoCarCategory = AutoCarCategory::findOrFail($id);
        return view('autocar_category.edit', compact('autoCarCategory'));
    }

    public function importCategoryView(Request $request)
    {
        return view('autocar_category.import');
    }

    public function downloadSampleCsv()
    {
        $csv = "Category Name\n"
             . "COATING SERVICE\n"
             . "PPF\n"
             . "MACHINE TEST\n"
             . "CAR DETAILING\n"
             . "WASHING & CLEANING\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ServiceCategoriesSample.csv"',
        ]);
    }

    public function downloadSampleExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Service Categories');
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $rows = [
            ['COATING SERVICE'],
            ['PPF'],
            ['MACHINE TEST'],
            ['CAR DETAILING'],
            ['WASHING & CLEANING'],
        ];

        $rowNum = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $rowNum++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'ServiceCategoriesSample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
