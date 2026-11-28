<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\LabourService;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LabourServiceController extends Controller
{
    // public function getAllServices(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     $userId = $user->id;
    //     $userole = $user->role;
    //     $selectedSubAdminId = $request->query('selectedSubAdminId');
    //     if ($userole == 'sub-admin' && $userId) {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     } elseif (!empty($selectedSubAdminId) && !empty($userole == 'admin')) {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $selectedSubAdminId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     } else {
    //         $labourServices = LabourService::where('is_deleted', 0)
    //             ->where('branch_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //     }
    //     $setting = Setting::first(); // Get currency info
    //     $currencySymbol = $setting->currency_symbol ?? '₹';
    //     $currencyPosition = $setting->currency_position ?? 'left'; // 'left' or 'right'

    //     return response()->json([
    //         'status' => true,
    //         'data' => $labourServices,
    //         'currency_symbol' => $currencySymbol,
    //         'currency_position' => $currencyPosition,
    //     ], 200);
    // }
    public function getAllServices(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userRole = $user->role;

        $selectedSubAdminId = $request->query('selectedSubAdminId');

        // Start query
        $query = LabourService::where('is_deleted', 0);

        // Apply role conditions
        if ($userRole == 'sub-admin') {
            $query->where('branch_id', $userId);

        } elseif ($userRole == 'admin' && !empty($selectedSubAdminId)) {
            $query->where('branch_id', $selectedSubAdminId);

        } else {
            $query->where('branch_id', $userId);
        }

        // Pagination params
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');

        // Optional search
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%"); // change column if needed
        }

        // Paginate instead of get()
        $labourServices = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Currency settings
        $setting = Setting::first();
        $currencySymbol = $setting->currency_symbol ?? '₹';
        $currencyPosition = $setting->currency_position ?? 'left';

        return response()->json([
            'status' => true,
            'data' => $labourServices->items(),
            'currency_symbol' => $currencySymbol,
            'currency_position' => $currencyPosition,
            'pagination' => [
                'current_page' => $labourServices->currentPage(),
                'last_page' => $labourServices->lastPage(),
                'per_page' => $labourServices->perPage(),
                'total' => $labourServices->total(),
                'next_page_url' => $labourServices->nextPageUrl(),
                'prev_page_url' => $labourServices->previousPageUrl(),
            ]
        ], 200);
    }

    public function addService(Request $request)
    {
        $user = Auth::guard('api')->user();
        $userId = $user->id;
        $userole = $user->role;

        $validator = Validator::make($request->all(), [
            'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('labour_services')->where(function ($query) use ($userId, $request) {
                $branchId = (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') ? $request->sub_admin_id : $userId;
                    return $query->where('branch_id', $branchId);
                })
            ],
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        if (!empty($request->sub_admin_id) && $request->sub_admin_id !== 'null') {
            $userId = $request->sub_admin_id;
        }
        $labourService = LabourService::create([
            'name' => $request->name,
            'branch_id' => $userId,
            'price' => $request->price,
        ]);

        return response()->json(['status' => true, 'labourService' => $labourService], 200);
    }

    public function deleteService($id)
    {
        $labourService = LabourService::find($id);

        if (!$labourService) {
            return response()->json(['status' => false, 'message' => 'Labour Service not found'], 404);
        }

        $labourService->is_deleted = 1;
        $labourService->save();

        return response()->json(['status' => true, 'message' => 'Labour Service deleted successfully'], 200);
    }

    public function updateService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|numeric|exists:labour_services,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('labour_services')
                    ->ignore($request->service_id)
                    ->where(function ($query) {
                        return $query->where('is_deleted', 0);
                    }),
            ],
            'price' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $labourService = LabourService::find($request->service_id);

        if (!$labourService) {
            return response()->json(['status' => false, 'message' => 'Labour Service not found'], 404);
        }

        $labourService->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json(['status' => true, 'labourService' => $labourService], 200);
    }

    public function getService($id)
    {
        try {
            $service = LabourService::where('id', $id)
                ->where('is_deleted', 0)
                ->first();

            if (!$service) {
                return response()->json([
                    'status' => false,
                    'message' => 'Labour Service not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import Labour Services from Excel (.xlsx, .xls) or CSV (.csv, .txt)
     */
    public function importLabourServices(Request $request)
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
                // CSV or text file
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
            $priceColIdx = 1;

            $nameKeywords = ['service name', 'servicename', 'service', 'name', 'labour service', 'labour', 'item', 'item name', 'particular', 'particulars', 'title', 'description'];
            $priceKeywords = ['price', 'amount', 'rate', 'cost', 'charge', 'charges', 'service price', 'service rate', 'service_price'];

            foreach ($firstRow as $idx => $cell) {
                $cellClean = strtolower(trim((string)$cell));
                if (in_array($cellClean, $nameKeywords)) {
                    $nameColIdx = $idx;
                    $hasHeader = true;
                }
                if (in_array($cellClean, $priceKeywords)) {
                    $priceColIdx = $idx;
                    $hasHeader = true;
                }
            }

            if ($hasHeader) {
                array_shift($rows);
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                // Skip if entire row is empty
                $nonEmpty = array_filter($row, fn($c) => trim((string)$c) !== '');
                if (empty($nonEmpty)) {
                    continue;
                }

                $name = isset($row[$nameColIdx]) ? trim((string)$row[$nameColIdx]) : '';
                $priceRaw = isset($row[$priceColIdx]) ? trim((string)$row[$priceColIdx]) : '0';

                if ($name === '') {
                    $skipped++;
                    continue;
                }

                // Sanitize price: strip currency symbols, commas, whitespace
                $priceClean = preg_replace('/[^\d.]/', '', $priceRaw);
                if ($priceClean === '' || !is_numeric($priceClean)) {
                    $price = 0.00;
                } else {
                    $price = (float)$priceClean;
                }

                // Check if labour service already exists for this branch
                $existing = LabourService::where('branch_id', $branchId)
                    ->where('name', $name)
                    ->first();

                if ($existing) {
                    $existing->price = $price;
                    $existing->is_deleted = 0; // Restore if deleted
                    $existing->save();
                    $updated++;
                } else {
                    LabourService::create([
                        'name' => $name,
                        'price' => $price,
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
                    'message' => 'No valid labour service data found in the uploaded file.',
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => $skipped,
                ], 422);
            }

            $messageParts = [];
            if ($imported > 0) {
                $messageParts[] = "{$imported} new labour service(s) imported";
            }
            if ($updated > 0) {
                $messageParts[] = "{$updated} existing service(s) updated";
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
     * Export Labour Services to Excel (.xlsx)
     */
    public function export_labour_services(Request $request)
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

            $query = LabourService::where('is_deleted', 0)
                ->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereNull('branch_id');
                });

            $search = $request->query('search') ?: $request->input('search');
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            $services = $query->orderBy('created_at', 'desc')->get();

            $setting = Setting::first();
            $currencySymbol = $setting->currency_symbol ?? '₹';
            $currencyPosition = $setting->currency_position ?? 'left';

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Labour Services');

            // Headers
            $headers = [
                'A1' => 'Sr No',
                'B1' => 'Service Name',
                'C1' => 'Price',
                'D1' => 'Created At',
            ];

            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }

            // Header Styling
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);
            $sheet->getStyle('A1:D1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF2F4F7');

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(25);

            $row = 2;
            foreach ($services as $index => $service) {
                $formattedPrice = number_format((float)$service->price, 2, '.', ',');
                $priceText = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedPrice}" : "{$formattedPrice}{$currencySymbol}";
                $dateText = $service->created_at ? \Carbon\Carbon::parse($service->created_at)->format('d-M-Y, g:i a') : '-';

                $sheet->setCellValue("A{$row}", $index + 1);
                $sheet->setCellValue("B{$row}", $service->name);
                $sheet->setCellValue("C{$row}", $priceText);
                $sheet->setCellValue("D{$row}", $dateText);

                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $row++;
            }

            $fileName = 'Labour_Services_' . now()->format('Ymd_His') . '.xlsx';
            $folder = 'labour-exports';
            $relativePath = "{$folder}/{$fileName}";

            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
            $fullPath = storage_path("app/public/{$relativePath}");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($fullPath);

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            return response()->json([
                'status' => true,
                'message' => 'Labour Services Excel exported successfully.',
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
