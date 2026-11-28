<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AutoCarCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AutoCarCategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $selectedSubAdminId = $request->query('selectedSubAdminId');

        $query = AutoCarCategory::where('is_deleted', 0);
        // Role-based filtering
        if ($userole == 'sub-admin') {
             $query->where('branch_id', $userId);
        } elseif ($userole == 'admin' && !empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);
        } else {
            $query->where('branch_id', $userId);
        }
         // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Optional search (if needed)
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%"); // change column if needed
        }

        // Paginate
        $autoCarCategories = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $autoCarCategories->items(),
            'pagination' => [
                'current_page' => $autoCarCategories->currentPage(),
                'last_page' => $autoCarCategories->lastPage(),
                'per_page' => $autoCarCategories->perPage(),
                'total' => $autoCarCategories->total(),
                'next_page_url' => $autoCarCategories->nextPageUrl(),
                'prev_page_url' => $autoCarCategories->previousPageUrl(),
            ]
        ], 200);
        // return response()->json(['status' => true, 'data' => $autoCarCategories], 200);
    }

    public function addCategory(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:auto_car_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        $autoCarCategory = AutoCarCategory::create([
            'name' => $request->name,
            'branch_id' => $userId,
        ]);

        return response()->json(['status' => true, 'autoCarCategory' => $autoCarCategory], 200);
    }

    public function deleteCategory($id)
    {
        $autoCarCategory = AutoCarCategory::find($id);

        if (!$autoCarCategory) {
            return response()->json(['status' => false, 'message' => 'Service Category not found'], 404);
        }

        $autoCarCategory->is_deleted = 1;
        $autoCarCategory->save();

        return response()->json(['status' => true, 'message' => 'Service Category deleted successfully'], 200);
    }

    public function updateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric|exists:auto_car_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('auto_car_categories')->ignore($request->category_id)->where(function ($query) {
                    return $query->where('is_deleted', 0);
                }),
            ],
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $autoCarCategory = AutoCarCategory::find($request->category_id);

        if (!$autoCarCategory) {
            return response()->json(['status' => false, 'message' => 'Service Category not found'], 404);
        }

        $autoCarCategory->update([
            'name' => $request->name,
        ]);

        return response()->json(['status' => true, 'autoCarCategory' => $autoCarCategory], 200);
    }

    /**
     * Import Service Categories from Excel (.xlsx, .xls) or CSV (.csv, .txt)
     */
    public function importCategories(Request $request)
    {
        try {
            $user = null;
            try {
                $user = Auth::guard('api')->user();
            } catch (\Throwable $e) {
                $user = null;
            }
            if (!$user) {
                $user = Auth::guard('web')->user() ?? auth()->user();
            }
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            $userId = $user->id;
            $userRole = strtolower($user->role ?? '');
            $selectedSubAdminId = $request->input('selectedSubAdminId') ?: $request->input('sub_admin_id') ?: $request->query('selectedSubAdminId');

            if ($userRole === 'sub-admin') {
                $branchId = $userId;
            } elseif ($userRole === 'admin' && !empty($selectedSubAdminId) && $selectedSubAdminId !== 'null') {
                $branchId = $selectedSubAdminId;
            } else {
                $branchId = $userId;
            }

            $file = $request->file('file') ?? $request->file('csv_file') ?? $request->file('excel_file');

            if (!$file || !$file->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please select a valid CSV or Excel file to upload.'
                ], 422);
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['csv', 'txt', 'xlsx', 'xls'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid file format. Please upload a .csv, .xlsx, or .xls file.'
                ], 422);
            }

            $rows = [];
            $path = $file->getRealPath();

            if (in_array($extension, ['xlsx', 'xls'])) {
                $reader = IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($path);
                $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            } else {
                if (($handle = fopen($path, 'r')) !== false) {
                    while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                        $rows[] = $data;
                    }
                    fclose($handle);
                }
            }

            if (empty($rows)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The uploaded file is empty.'
                ], 422);
            }

            // Clean UTF-8 BOM if present on the very first cell
            if (isset($rows[0][0])) {
                $rows[0][0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$rows[0][0]);
            }

            // Detect header row dynamically
            $firstRow = $rows[0];
            $hasHeader = false;
            $nameColIdx = 0;

            $nameKeywords = ['category name', 'categoryname', 'category', 'service category', 'name', 'title'];

            foreach ($firstRow as $idx => $cell) {
                $cellClean = strtolower(trim((string)$cell));
                if (in_array($cellClean, $nameKeywords)) {
                    $nameColIdx = $idx;
                    $hasHeader = true;
                    break;
                }
            }

            if ($hasHeader) {
                array_shift($rows);
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                $nonEmpty = array_filter($row, fn($c) => trim((string)$c) !== '');
                if (empty($nonEmpty)) {
                    continue;
                }

                $name = isset($row[$nameColIdx]) ? trim((string)$row[$nameColIdx]) : '';

                if ($name === '') {
                    $skipped++;
                    continue;
                }

                // Check if category already exists for this branch
                $existing = AutoCarCategory::where('name', $name)
                    ->where(function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                          ->orWhereNull('branch_id');
                    })
                    ->first();

                if ($existing) {
                    if ($existing->is_deleted == 1) {
                        $existing->is_deleted = 0;
                        $existing->save();
                    }
                    $updated++;
                } else {
                    AutoCarCategory::create([
                        'name' => $name,
                        'branch_id' => $branchId,
                        'is_deleted' => 0,
                    ]);
                    $imported++;
                }
            }

            $totalProcessed = $imported + $updated;
            if ($totalProcessed === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No valid service category data found in the uploaded file.',
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => $skipped,
                ], 422);
            }

            $messageParts = [];
            if ($imported > 0) {
                $messageParts[] = "{$imported} new service category(s) imported";
            }
            if ($updated > 0) {
                $messageParts[] = "{$updated} existing category(s) updated/verified";
            }
            if ($skipped > 0) {
                $messageParts[] = "{$skipped} blank row(s) skipped";
            }
            $message = implode(', ', $messageParts) . ' successfully.';

            return response()->json([
                'status' => true,
                'message' => $message,
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error importing file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Service Categories to Excel (.xlsx)
     */
    public function export_categories(Request $request)
    {
        try {
            $user = null;
            try {
                $user = Auth::guard('api')->user();
            } catch (\Throwable $e) {
                $user = null;
            }
            if (!$user) {
                $user = Auth::guard('web')->user() ?? auth()->user();
            }

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            $userId = $user->id;
            $userRole = strtolower($user->role ?? '');
            $selectedSubAdminId = $request->query('selectedSubAdminId') ?: $request->input('selectedSubAdminId');

            if ($userRole === 'sub-admin') {
                $branchId = $userId;
            } elseif ($userRole === 'admin' && !empty($selectedSubAdminId) && $selectedSubAdminId !== 'null') {
                $branchId = $selectedSubAdminId;
            } else {
                $branchId = $userId;
            }

            $query = AutoCarCategory::where('is_deleted', 0)
                ->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereNull('branch_id');
                });

            $search = $request->query('search') ?: $request->input('search');
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            $categories = $query->orderBy('created_at', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Service Categories');

            // Headers
            $headers = [
                'A1' => 'Sr No',
                'B1' => 'Category Name',
                'C1' => 'Created At',
            ];

            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }

            // Header Styling
            $sheet->getStyle('A1:C1')->getFont()->setBold(true);
            $sheet->getStyle('A1:C1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF2F4F7');

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(25);

            $row = 2;
            foreach ($categories as $index => $category) {
                $dateText = $category->created_at ? Carbon::parse($category->created_at)->format('d-M-Y, g:i a') : '-';

                $sheet->setCellValue("A{$row}", $index + 1);
                $sheet->setCellValue("B{$row}", $category->name);
                $sheet->setCellValue("C{$row}", $dateText);

                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $row++;
            }

            $fileName = 'Service_Categories_' . now()->format('Ymd_His') . '.xlsx';
            $folder = 'autocar-category-exports';
            $relativePath = "{$folder}/{$fileName}";

            Storage::disk('public')->makeDirectory($folder);
            $fullPath = storage_path("app/public/{$relativePath}");

            $writer = new Xlsx($spreadsheet);
            $writer->save($fullPath);

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            return response()->json([
                'status' => true,
                'message' => 'Service Categories Excel exported successfully.',
                'file_url' => $fileUrl,
                'file_name' => $fileName,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error exporting Excel: ' . $e->getMessage()
            ], 500);
        }
    }
}
