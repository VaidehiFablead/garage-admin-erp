<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Purchases;
use App\Models\TaxRate;
use App\Models\JobCard;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // public function getAllProduct(Request $request)
    // {
    //     $user = Auth::guard('api')->user();

    //     // ✅ Load settings once
    //     $settings = DB::table('settings')
    //         ->select('low_stock', 'currency_symbol', 'currency_position')
    //         ->first();

    //     // ✅ Resolve branch id based on role
    //     $branchId = match ($user->role) {
    //         'admin'     => $request->sub_branch_id,
    //         'sub-admin' => $user->id,
    //         'Staff'     => $user->branch_id,
    //         default     => null,
    //     };

    //     // ✅ Build query
    //     $products = Product::query()
    //         ->select([
    //             'id',
    //             'name',
    //             'SKU',
    //             'price',
    //             'quantity',
    //             'unit_id',
    //             'category_id',
    //             'brand_id',
    //             'branch_id',
    //             'images',
    //         ])
    //         ->with([
    //             'category:id,name',
    //             'brand:id,name',
    //         ])
    //         ->with(['unit:id,unit_name'])
    //         ->where('isDeleted', 0)
    //         ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
    //         ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
    //         ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
    //         ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
    //         ->latest('id')
    //         ->get();

    //         // dd($products);
    //     return response()->json([
    //         'status'            => true,
    //         'data'              => $products,
    //         'currencySymbol'    => $settings->currency_symbol ?? '₹',
    //         'currencyPosition'  => $settings->currency_position ?? 'left',
    //         'lowStockThreshold' => $settings->low_stock ?? 0,
    //     ]);
    // }
    public function getAllProduct(Request $request)
{
    $user = Auth::guard('api')->user();

    // ✅ Load settings once
    $settings = DB::table('settings')
        ->select('low_stock', 'currency_symbol', 'currency_position')
        ->first();

    // ✅ Resolve branch id strictly
    $branchId = match (strtolower($user->role)) {
        'admin'     => $user->id,
        'sub-admin' => $user->branch_id ?? $user->id,
        'staff'     => $user->branch_id,
        default     => $user->id,
    };

    // ✅ Pagination params
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    // ✅ Build query
    $query = Product::query()
        ->select([
            'id',
            'name',
            'SKU',
            'price',
            'quantity',
            'unit_id',
            'category_id',
            'brand_id',
            'branch_id',
            'images',
        ])
        ->with([
            'category:id,name',
            'brand:id,name',
            'unit:id,unit_name'
        ])
        ->where('isDeleted', 0)
        ->where('branch_id', $branchId)
        ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
        ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
        ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id));

    // ✅ Optional search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('SKU', 'LIKE', "%{$search}%");
        });
    }

    // ✅ Paginate instead of get()
    $products = $query->latest('id')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'status' => true,
        'data' => $products->items(),
        'currencySymbol' => $settings->currency_symbol ?? '₹',
        'currencyPosition' => $settings->currency_position ?? 'left',
        'lowStockThreshold' => $settings->low_stock ?? 0,

        // ✅ Pagination response
        'pagination' => [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ]
    ]);
}

    public function export_product(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1️⃣ Resolve Branch ID (single, clean logic)
     -------------------------------------------------*/
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        /* -------------------------------------------------
     | 2️⃣ Fetch Products
     -------------------------------------------------*/
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units','products.unit_id', '=', 'units.id')
            ->select([
                'products.name as product_name',
                'products.SKU',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.description',
                'products.created_at',
                'categories.name as category_name',
                'brands.name as brand_name',
                'units.unit_name'
            ])
            ->where('products.isDeleted', 0)
            ->where('products.branch_id', $branchId)
            ->when(
                $request->category_id,
                fn($q) =>
                $q->where('products.category_id', $request->category_id)
            )
            ->when(
                $request->brand_id,
                fn($q) =>
                $q->where('products.brand_id', $request->brand_id)
            )
            ->when(
                $request->unit_id,
                fn($q) =>
                $q->where('products.unit_id', $request->unit_id)
            )
            ->latest('products.id')
            ->get();

        /* -------------------------------------------------
     | 3️⃣ Currency Settings
     -------------------------------------------------*/
        $settings = DB::table('settings')
            ->where('branch_id', $branchId)
            ->first();

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? '₹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
        $currencyPosition = $settings->currency_position ?? 'left';

        /* -------------------------------------------------
     | 4️⃣ Create Excel
     -------------------------------------------------*/
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Product Name',
            'B1' => 'SKU',
            'C1' => 'Barcode',
            'D1' => 'Category',
            'E1' => 'Brand',
            'F1' => 'Quantity',
            'G1' => 'Unit',
            'H1' => 'Price',
            'I1' => 'Description',
            'J1' => 'Created At',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // Data
        $row = 2;
        foreach ($products as $product) {

            $price = $currencyPosition === 'left'
                ? "{$currencySymbol} {$product->price}"
                : "{$product->price} {$currencySymbol}";

            $sheet->fromArray([
                $product->product_name,
                $product->SKU,
                $product->barcode,
                $product->category_name ?? '-',
                $product->brand_name ?? '-',
                $product->quantity ?? '-',
                $product->unit_name ?? '-',
                $price,
                $product->description ?? '-',
                \Carbon\Carbon::parse($product->created_at)->format('Y-m-d'),
            ], null, "A{$row}");

            $row++;
        }

        /* -------------------------------------------------
     | 5️⃣ Save File
     -------------------------------------------------*/
        $fileName     = 'Products_' . now()->format('Ymd_His') . '.xlsx';
        $folder       = 'product-exports';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path("app/public/{$relativePath}"));

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        /* -------------------------------------------------
     | 6️⃣ Response
     -------------------------------------------------*/
        return response()->json([
            'status'    => true,
            'message'   => 'Product Excel exported successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function export_product_pdf(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1️⃣ Resolve Branch ID (single source of truth)
     -------------------------------------------------*/
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        /* -------------------------------------------------
     | 2️⃣ Build Product Query
     -------------------------------------------------*/
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units','products.unit_id', '=', 'units.id')
            ->select([
                'products.id',
                'products.name as product_name',
                'products.SKU',
                'products.barcode',
                'products.price',
                'products.quantity',
                'products.description',
                'products.created_at',
                'categories.name as category_name',
                'brands.name as brand_name',
                'units.unit_name'
            ])
            ->where('products.isDeleted', 0)
            ->where('products.branch_id', $branchId)
            ->when(
                $request->category_id,
                fn($q) =>
                $q->where('products.category_id', $request->category_id)
            )
            ->when(
                $request->brand_id,
                fn($q) =>
                $q->where('products.brand_id', $request->brand_id)
            )
            ->latest('products.id')
            ->get();
        // dd($products);
            /* -------------------------------------------------
        | 3️⃣ Settings (currency, GST, branding)
        -------------------------------------------------*/
            $settings = DB::table('settings')
                ->where('branch_id', $branchId)
                ->first();

            $currencySymbol = trim(
                html_entity_decode($settings->currency_symbol ?? '₹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
            );

            /* -------------------------------------------------
        | 4️⃣ PDF Generation
        -------------------------------------------------*/
            $pdf = Pdf::loadView('product.product_pdf', [
                'products'         => $products,
                'currencySymbol'   => $currencySymbol,
                'currencyPosition' => $settings->currency_position ?? 'left',
                'settings'         => $settings,
                'userName'         => $user->name ?? 'N/A',
                'gstNum'           => $user->gst_number ?? 'N/A',
            ])->setPaper('A4', 'portrait');

            /* -------------------------------------------------
        | 5️⃣ Store PDF
        -------------------------------------------------*/
            $fileName     = 'Products_' . now()->format('Ymd_His') . '.pdf';
            $folder       = 'all-products';
            $relativePath = "{$folder}/{$fileName}";

            Storage::disk('public')->makeDirectory($folder);
            Storage::disk('public')->put($relativePath, $pdf->output());

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            /* -------------------------------------------------
        | 6️⃣ Response
        -------------------------------------------------*/
            return response()->json([
                'status'    => true,
                'message'   => 'Product PDF generated successfully.',
                'file_url'  => $fileUrl,
                'file_name' => $fileName,
            ]);
    }

    public function getProductById($id)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::with(['category', 'brand'])->find($id);
        if (!$product) {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }

        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'error' => 'Unauthorized access to this product.'], 403);
        }

        return response()->json(['status' => true, 'product' => $product], 200);
    }

    public function createProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1️⃣ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $rules = [
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric',
            'SKU' => ['required', 'string', 'max:255', Rule::unique('products', 'SKU')->where(fn($q) => $q->where('branch_id', $branchId)),],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId)),],
            'hsn_code' => 'nullable',
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->where(fn($q) => $q->where('branch_id', $branchId)),],
            'quantity'      => 'required|integer|min:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp',
            'branch_id'     => 'nullable|numeric',
            'sub_admin_id'  => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'          => 'unit',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->gst_option === 'with_gst') {
                $productGst = $request->product_gst;
                if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
                    $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2️⃣ Upload Images
     -------------------------------------------------*/
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('img/product', 'public');
            }
        }

        /* -------------------------------------------------
     | 3️⃣ Resolve Branch ID (clean & safe)
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        /* -------------------------------------------------
     | 4️⃣ Availability Logic
     -------------------------------------------------*/
        $availability = $validated['quantity'] == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 5️⃣ Create Product
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        $product = Product::create([
            'vendor_id'     => $validated['vendor_id'] ?? 1,
            'category_id'   => $validated['category_id'],
            'brand_id'      => $validated['brand_id'] ?? null,
            'branch_id'     => $branchId,
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'SKU'           => $validated['SKU'],
            'hsn_code'      => $validated['hsn_code'] ?? null,
            'barcode'       => $validated['barcode'] ?? null,
            'quantity'      => $validated['quantity'],
            'unit_id'       => $validated['unit_id'],
            'status'        => $validated['status'] ?? 'active',
            'availablility' => $availability,
            'gst_option'    => $validated['gst_option'] ?? 'without_gst',
            'product_gst'   => !empty($gstData) ? json_encode($gstData) : null,
            'images'        => json_encode($imagePaths),
        ]);

        /* -------------------------------------------------
     | 6️⃣ Inventory Log
     -------------------------------------------------*/
        ProductInventory::create([
            'product_id'    => $product->id,
            'initial_stock' => $validated['quantity'],
            'current_stock' => $validated['quantity'],
            'branch_id'     => $branchId,
            'create_by'     => $user->id,
            'type'          => 'Create',
            'date'          => now(),
        ]);

        /* -------------------------------------------------
     | 7️⃣ Response
     -------------------------------------------------*/
        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'product' => $product->fresh(),
        ], 200);
    }

    public function updateProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1️⃣ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::findOrFail($request->product_id);

        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'error' => 'Unauthorized access to this product.'], 403);
        }

        $rules = [
            'product_id'    => 'required|exists:products,id',
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric',
            'SKU' => ['required', 'string', 'max:255', Rule::unique('products', 'SKU')->where(fn($q) => $q->where('branch_id', $branchId))->ignore($request->product_id),],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId))->ignore($request->product_id),],
            'hsn_code' => 'nullable',
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->where(fn($q) => $q->where('branch_id', $branchId))->ignore($request->product_id),],
            'quantity'      => 'required|integer|min:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp',
            'capacity'      => 'nullable|string',
            'voltage'       => 'nullable|string',
            'warranty'      => 'nullable|string',
            'expiry_date'   => 'nullable|date',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'       => 'unit',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->gst_option === 'with_gst') {
                $productGst = $request->product_gst;
                if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
                    $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2️⃣ Fetch Product
     -------------------------------------------------*/
        // $product = Product::findOrFail($validated['product_id']);

        /* -------------------------------------------------
     | 3️⃣ Handle Images
     -------------------------------------------------*/
        $existingImages = json_decode($product->images, true) ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $existingImages[] = $image->store('img/product', 'public');
            }
        }

        /* -------------------------------------------------
     | 4️⃣ Resolve Branch ID
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();

        // Checked product ownership above

        /* -------------------------------------------------
     | 5️⃣ Availability Auto-Logic
     -------------------------------------------------*/
        $availability = $validated['quantity'] == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 6️⃣ Prepare GST Data
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        /* -------------------------------------------------
     | 7️⃣ Update Product
     -------------------------------------------------*/
        $product->update([
            'vendor_id'     => $validated['vendor_id'] ?? 1,
            'category_id'   => $validated['category_id'],
            'brand_id'      => $validated['brand_id'],
            'branch_id'     => $branchId,
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'SKU'           => $validated['SKU'],
            'hsn_code'      => $validated['hsn_code'],
            'barcode'       => $validated['barcode'] ?? null,
            'quantity'      => $validated['quantity'],
            'unit_id'       => $validated['unit_id'],
            'status'        => $validated['status'] ?? $product->status,
            'availablility' => $availability,
            'gst_option'    => $validated['gst_option'] ?? 'without_gst',
            'product_gst'   => !empty($gstData) ? json_encode($gstData) : null,
            'images'        => json_encode($existingImages),
            'capacity'      => $validated['capacity'] ?? null,
            'voltage'       => $validated['voltage'] ?? null,
            'warranty'      => $validated['warranty'] ?? null,
            'expiry_date'   => $validated['expiry_date'] ?? null,
        ]);

        /* -------------------------------------------------
     | 8️⃣ Inventory Log
     -------------------------------------------------*/
        ProductInventory::create([
            'product_id'    => $product->id,
            'initial_stock' => $validated['quantity'],
            'current_stock' => $validated['quantity'],
            'branch_id'     => $branchId,
            'create_by'     => $user->id,
            'type'          => 'Edit',
            'date'          => now(),
        ]);

        /* -------------------------------------------------
     | 9️⃣ Response
     -------------------------------------------------*/
        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'product' => $product->fresh(),
        ], 200);
    }

    public function deleteProduct($id)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::find($id);

        if (! $product) {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }

        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'error' => 'Unauthorized access to this product.'], 403);
        }

        $orderItemExists = OrderItem::where('product_id', $id)->exists();
        $purchaseExists  = Purchases::where('item', $id)->exists();

        if ($orderItemExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing orders and cannot be deleted.',
            ], 409);
        }

        if ($purchaseExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing purchases and cannot be deleted.',
            ], 409);
        }

        // Soft delete: mark isDeleted as 1
        $product->isDeleted = 1;
        $product->save();

        return response()->json(['status' => true, 'message' => 'Product deleted successfully'], 200);
    }

    public function removeProductImage(Request $request)
    {
        $product = Product::find($request->product_id);

        if (! $product) {
            return response()->json(["success" => false, "message" => "Product not found"]);
        }

        $images = json_decode($product->images, true);

        // Check if image exists
        if (($key = array_search($request->image, $images)) !== false) {
            // Remove image from storage
            Storage::delete("storage/img/product" . $request->image);

            // Remove image from array and update database
            unset($images[$key]);
            $product->images = json_encode(array_values($images)); // Re-index array
            $product->save();

            return response()->json(["success" => true, "message" => "Image deleted"]);
        }

        return response()->json(["success" => false, "message" => "Image not found"]);
    }

    public function importProducts(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $user->id; // User's branch ID

        $request->validate([
            'csv_file' => 'required|max:4096',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $fileExtension = strtolower($file->getClientOriginalExtension());
        
        $data = [];
        $hasHeader = false;

        if ($fileExtension === 'sql') {
            $content = file_get_contents($path);
            if (preg_match('/INSERT INTO `products` \([^\)]+\) VALUES\s*(.*?);/is', $content, $matches)) {
                $valuesBlock = $matches[1];
                // Clean single line value blocks and split by rows (usually ending with ), )
                $rows = preg_split('/\),\s*(?:\r?\n|\()/', trim($valuesBlock, "() \r\n\t"));
                foreach ($rows as $rowLine) {
                    $rowLine = trim($rowLine);
                    if ($rowLine === '') continue;
                    // Trim trailing parenthesis if single row
                    $rowLine = rtrim($rowLine, ')');
                    $parsedRow = str_getcsv($rowLine, ',', "'");
                    $parsedRow = array_map(function($val) {
                        $trimmed = trim($val);
                        // Clean leading/trailing quotes if parsed incorrectly
                        $trimmed = trim($trimmed, "'\"");
                        return (strtoupper($trimmed) === 'NULL' || $trimmed === '') ? null : $trimmed;
                    }, $parsedRow);
                    $data[] = $parsedRow;
                }
            }
            $hasHeader = false;
        } else {
            $data = array_map('str_getcsv', file($path));

            if (count($data) > 0) {
                $firstRow = $data[0];
                
                // Check if the first row is a header row
                foreach ($firstRow as $col) {
                    $colLower = strtolower(trim($col));
                    if ($colLower === 'name' || $colLower === 'product name' || $colLower === 'sku' || $colLower === 'price' || $colLower === 'quantity' || $colLower === 'category name') {
                        $hasHeader = true;
                        break;
                    }
                }
            }
        }

        if (count($data) > 0) {
            if ($hasHeader) {
                // Remove the header row from data
                $header = array_map('trim', array_shift($data));

                // Map header names to their column indexes dynamically
                $getColIndex = function($keywords, $defaultIndex = -1) use ($header) {
                    foreach ($header as $idx => $colName) {
                        foreach ($keywords as $kw) {
                            if (stripos(trim($colName), $kw) !== false) {
                                return $idx;
                            }
                        }
                    }
                    return $defaultIndex;
                };

                $nameIdx         = $getColIndex(['product name', 'name'], 0);
                $categoryIdx     = $getColIndex(['category name', 'category'], 1);
                $brandIdx        = $getColIndex(['brand name', 'brand'], 2);
                $skuIdx          = $getColIndex(['sku'], 3);
                $qtyIdx          = $getColIndex(['quantity', 'qty'], 4);
                $unitIdx         = $getColIndex(['unit'], 5);
                $priceIdx        = $getColIndex(['price', 'rate'], 6);
                $statusIdx       = $getColIndex(['status'], 7);
                $availIdx        = $getColIndex(['availability', 'stock'], 8);
                $descIdx         = $getColIndex(['description', 'desc'], 9);
            } else {
                // If there's no header row, map directly to the database dump column indexes!
                $nameIdx         = 6;   // name
                $categoryIdx     = 4;   // category_id
                $brandIdx        = 5;   // brand_id
                $skuIdx          = 7;   // SKU
                $qtyIdx          = 11;  // quantity
                $unitIdx         = 13;  // unit_id
                $priceIdx        = 10;  // price
                $statusIdx       = 19;  // status
                $availIdx        = 18;  // availablility
                $descIdx         = 9;   // description
            }

            $insertedCount = 0;
            $updatedSKUs   = [];
            $invalidSKUs   = [];

            foreach ($data as $row) {
                if (empty($row) || count($row) < 2) continue; // Skip empty rows

                $name         = isset($row[$nameIdx]) ? strtolower(trim($row[$nameIdx])) : '';
                $categoryName = isset($row[$categoryIdx]) ? strtolower(trim($row[$categoryIdx])) : '';
                $brandName    = isset($row[$brandIdx]) && trim($row[$brandIdx]) !== '' ? strtolower(trim($row[$brandIdx])) : null;
                $sku          = isset($row[$skuIdx]) ? trim($row[$skuIdx]) : '';
                $quantity     = isset($row[$qtyIdx]) ? (int)trim($row[$qtyIdx]) : 0;
                $unitName     = isset($row[$unitIdx]) && trim($row[$unitIdx]) !== '' ? strtolower(trim($row[$unitIdx])) : null;
                $price        = isset($row[$priceIdx]) ? (float)trim($row[$priceIdx]) : 0.0;
                
                $status       = isset($row[$statusIdx]) ? strtolower(trim($row[$statusIdx])) : 'inactive';
                if ($status !== 'active' && $status !== 'inactive') {
                    if ($status === '1' || $status === 'true' || $status === 'yes' || strpos($status, 'act') !== false) {
                        $status = 'active';
                    } else {
                        $status = 'inactive';
                    }
                }

                $availability = isset($row[$availIdx]) ? strtolower(trim($row[$availIdx])) : 'out_stock';
                if ($availability !== 'in_stock' && $availability !== 'out_stock') {
                    if (strpos($availability, 'in') !== false || $availability === '1' || $availability === 'true') {
                        $availability = 'in_stock';
                    } else {
                        $availability = 'out_stock';
                    }
                }

                $description  = isset($row[$descIdx]) ? strtolower(trim($row[$descIdx])) : null;

                // ✅ Allow any non-empty SKU format (since database table SKU is varchar)
                if (empty($sku)) {
                    $invalidSKUs[] = 'EMPTY_SKU';
                    continue;
                }

                // ✅ Create or fetch category (support numeric ID or Name)
                $category = null;
                if (is_numeric($categoryName)) {
                    $category = Category::find((int)$categoryName);
                }
                if (!$category) {
                    $category = Category::firstOrCreate(
                        ['name' => $categoryName, 'branch_id' => $branchId],
                        ['isDeleted' => 0]
                    );
                }

                // ✅ Create or fetch brand (support numeric ID or Name)
                $brand = null;
                if ($brandName) {
                    if (is_numeric($brandName)) {
                        $brand = Brand::find((int)$brandName);
                    }
                    if (!$brand) {
                        $brand = Brand::firstOrCreate(
                            ['name' => $brandName, 'branch_id' => $branchId],
                            ['isDeleted' => 0]
                        );
                    }
                }

                // ✅ Create or fetch unit (support numeric ID or Name)
                $unit = null;
                if ($unitName) {
                    if (is_numeric($unitName)) {
                        $unit = Unit::find((int)$unitName);
                    }
                    if (!$unit) {
                        $unit = Unit::firstOrCreate(
                            ['unit_name' => $unitName, 'created_by' => $branchId],
                            ['is_delete' => 0]
                        );
                    }
                }

                // ✅ Check if product with same SKU exists in the same branch
                $product = Product::where('SKU', $sku)
                    ->where('branch_id', $branchId)
                    ->first();

                if ($product) {
                    $product->quantity += $quantity;
                    $product->save();
                    $updatedSKUs[] = $sku;
                } else {
                    Product::create([
                        'name'          => $name,
                        'category_id'   => $category->id,
                        'brand_id'      => $brand?->id,
                        'SKU'           => $sku,
                        'quantity'      => $quantity,
                        'unit_id'       => $unit?->id,
                        'price'         => $price,
                        'status'        => $status,
                        'availablility' => $availability,
                        'description'   => $description,
                        'branch_id'     => $branchId,
                    ]);
                    $insertedCount++;
                }
            }

            // ✅ Return response
            if ($insertedCount > 0 || count($updatedSKUs) > 0) {
                return response()->json([
                    "status"       => true,
                    "message"      => $insertedCount > 0
                        ? "$insertedCount product(s) imported successfully."
                        : "Existing product(s) updated with additional quantity.",
                    "updated_skus" => $updatedSKUs,
                    "invalid_skus" => $invalidSKUs,
                ]);
            } elseif (! empty($invalidSKUs)) {
                return response()->json([
                    "status"       => false,
                    "message"      => "No new products imported.",
                    "invalid_skus" => $invalidSKUs,
                ]);
            } else {
                return response()->json([
                    "status"  => false,
                    "message" => "CSV file is empty or contains invalid data.",
                ]);
            }
        }

        return response()->json([
            "status"  => false,
            "message" => "CSV file is empty!",
        ]);
    }

    public function getProductDetails($id)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::with(['category', 'brand', 'unit'])->findOrFail($id);

        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'error' => 'Unauthorized access to this product.'], 403);
        }

        // Fetch currency settings
        $settings         = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $barcodeGenerator = new DNS1D();
        $barcodeHtml      = null;

        if (! empty($product->barcode)) {
            $barcodeHtml = $barcodeGenerator->getBarcodeHTML($product->barcode, 'C128');
        }

        return response()->json([
            'product'          => $product,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'barcode_html'     => $barcodeHtml,
        ]);
    }

    public function getQuantityHistory($productId)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::find($productId);
        if (! $product) {
            return response()->json(['status' => false, 'message' => 'Product not found.']);
        }

        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'message' => 'Unauthorized access to this product.'], 403);
        }

        $history = [];

        // Initial stock (e.g., when product added)
        $history[] = [
            'type'     => 'Added',
            'quantity' => $product->quantity,
            'note'     => 'Initial stock on product creation',
            'date'     => $product->created_at->format('d-M-Y h:i A'),
        ];

        // Sales data (reduce quantity)
        $orderItems = OrderItem::with('order')->where('product_id', $productId)->get();
        foreach ($orderItems as $item) {
            $orderNumber = $item->order ? $item->order->order_number : 'N/A';

            $history[] = [
                'type'     => 'Sold',
                'quantity' => $item->quantity,
                'note'     => 'Sold in order #' . $orderNumber,
                'date'     => $item->created_at->format('d-M-Y h:i A'),
            ];
        }

        return response()->json([
            'status'  => true,
            'product' => $product->name,
            'history' => $history,
        ]);
    }

    public function getCategory(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Agar request me sub_branch_id aaya hai to use karo, warna logged-in user ka branch_id lo
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };
        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        // dd($categories);

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ], 200);
    }

    public function getBrand(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Branch ID decide karna (agar sub_branch_id aaya hai to use karo, otherwise user ka branch_id)
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };
        $Brands = Brand::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $Brands,
        ], 200);
    }

    public function getTaxRates(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $taxRates = TaxRate::where('isDeleted', 0)
            ->where('status', 'active')
            ->where('branch_id', $branchId)
            ->get(['id', 'tax_name', 'tax_rate']);

        return response()->json([
            'status' => true,
            'data'   => $taxRates,
        ], 200);
    }

    public function getUnits(Request $request)
    {
        $user = Auth::guard('api')->user();

        // dd($user);

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        // dd($branchId);

        $units = Unit::where('is_delete', 0)
            ->where('created_by', $branchId)
            ->get(['id', 'unit_name']);

            // dd($units);

        return response()->json([
            'status' => true,
            'data'   => $units,
        ], 200);
    }

    public function edit_product(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->branch_id ?? $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $product = Product::findOrFail($id);
        if ($product->branch_id != $branchId) {
            return response()->json(['status' => false, 'error' => 'Unauthorized access to this product.'], 403);
        }

        return response()->json([
            'status'  => true,
            'product' => $product,
        ]);
    }
    public function convertQuotationToJobCard($id)
    {
        DB::beginTransaction();
 
        try {
 
            $jobCard = JobCard::findOrFail($id);
 
            if ($jobCard->type != 'quotation') {
                return response()->json([
                    'status' => false,
                    'message' => 'This record is not a quotation'
                ]);
            }
 
            $parts = json_decode($jobCard->parts_details, true);
 
            if ($parts) {
 
                foreach ($parts as $part) {
                    if (!isset($part['id']) || !isset($part['qty'])) {
                        continue;
                    }
 
                    $product = Product::find($part['id']);
 
                    if ($product) {
                        if ($product->quantity < (int)$part['qty']) {
                            DB::rollback();
                            return response()->json([
                                'status' => false,
                                'message' => 'Insufficient stock for ' . $product->name
                            ]);
                        }
 
                        $initialStock = $product->quantity;
                        $newQuantity = max(0, $product->quantity - (int)$part['qty']);
                        $product->quantity = $newQuantity;
                        if ($newQuantity == 0) {
                            $product->availablility = 'out_stock';
                        }
                        $product->save();
 
                        ProductInventory::create([
                            'product_id' => $product->id,
                            'initial_stock' => $initialStock,
                            'current_stock' => $newQuantity,
                            'branch_id' => $jobCard->subbranch_id,
                            'create_by' => Auth::guard('api')->id(),
                            'type' => 'Job Card',
                            'date' => now(),
                        ]);
                    }
                }
            }
 
            $jobCard->type = 'jobCard';
            $jobCard->save();
 
            DB::commit();
 
            return response()->json([
                'status' => true,
                'message' => 'Quotation converted and inventory updated successfully'
            ]);
        } catch (\Exception $e) {
 
            DB::rollback();
 
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
