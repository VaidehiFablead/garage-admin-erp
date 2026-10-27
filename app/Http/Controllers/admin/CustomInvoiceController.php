<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use PDF;
use App\Models\Purchases;
use App\Models\Product;
use App\Models\User;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\CustomInvoice;
use App\Models\CustomInvoiceItem;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class CustomInvoiceController extends Controller
{
    public function custom_invoice_list(Request $request)
    {
        $years = CustomInvoice::where('isDeleted', 0)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('custom-invoice/custom_invoicelist', compact('years'));
    }


    public function add_custom_invoice(Request $request)
    {
        return view('custom-invoice/addcustom_invoice');
    }
    public function edit_custom_invoice(Request $request)
    {
        return view('custom-invoice/editcustom_invoice');
    }

    public function custom_invoice_print(Request $request, $id)
    {
        // Fetch the invoice from the new table 'custom_invoices'
        $invoice = CustomInvoice::find($id);

        if (!$invoice) {
            return redirect()->route('custom-invoice.list')->with('error', 'Invoice not found.');
        }

        // Properly decode taxes (double decode if needed)
        if (!empty($invoice->taxes) && is_string($invoice->taxes)) {
            $firstDecode = json_decode($invoice->taxes, true);
            $invoice->taxes = is_string($firstDecode) ? json_decode($firstDecode, true) : $firstDecode;
        }

        // Determine the vendor or customer (vendor_id can be null)
        $userId = $invoice->vendor_id ?? $invoice->customer_id;
        $user = User::where('id', $userId)
            ->whereIn('role', ['vendor', 'customer'])
            ->first();

        // Decode products data
        $productsData = json_decode($invoice->products, true);
        $productIds = collect($productsData)->pluck('product_id')->unique()->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Populate product names and images
        foreach ($productsData as &$item) {
            $product = $products->get($item['product_id']);
            $item['product_name'] = $product ? $product->name : 'Unknown Product';

            $images = $product && $product->images ? json_decode($product->images, true) : [];
            $item['product_image'] = !empty($images)
                ? env('ImagePath').'storage/' . $images[0]
                : env('ImagePath').'/admin/assets/img/product/noimage.png';
        }

        $invoice->products = $productsData;

        $companyInfo = Setting::first();

        // Get currency settings
        $currencySymbol = $companyInfo->currency_symbol ?? '₹';
        $currencyPosition = $companyInfo->currency_position ?? 'left';

        return view('custom-invoice/download_invoice', compact(
            'invoice',
            'user',
            'companyInfo',
            'currencySymbol',
            'currencyPosition'
        ));
    }


    public function custom_invoice_view(Request $request, $id)
    {
        $invoice = CustomInvoice::findOrFail($id);

        // Properly decode taxes (double decode if needed)
        if (!empty($invoice->taxes) && is_string($invoice->taxes)) {
            $invoice->taxes = json_decode($invoice->taxes, true);
        }

        // Determine the vendor or customer (vendor_id can be null)
        $vendorId = $invoice->vendor_id ?? $invoice->customer_id;
        $vendor = User::where('id', $vendorId)
            ->whereIn('role', ['vendor', 'customer'])
            ->first();

        $productsData = json_decode($invoice->products, true);
        $productIds = collect($productsData)->pluck('product_id')->unique()->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($productsData as &$item) {
            $product = $products->get($item['product_id']);
            $item['product_name'] = $product ? $product->name : 'Unknown Product';

            $images = $product && $product->images ? json_decode($product->images, true) : [];
            $item['product_image'] = !empty($images)
                ? env('ImagePath').'storage/' . $images[0]
                : env('ImagePath').'/admin/assets/img/product/noimage.png';
        }

        $invoice->products = $productsData;
        $compenyinfo = Setting::first();

        // Get currency settings
        $currencySymbol = $compenyinfo->currency_symbol ?? '₹';
        $currencyPosition = $compenyinfo->currency_position ?? 'left';



        return view('custom-invoice/custom-invoice-details', compact('invoice', 'vendor', 'compenyinfo', 'currencySymbol', 'currencyPosition'));
    }

    public function custom_invoice_pdf($id)
    {
        $view_id = $id;
        $invoice = CustomInvoice::find($view_id);
        $setting = Setting::first();

        if (!$invoice) {
            return redirect()->route('sales.list')->with('error', 'Invoice not found.');
        }

        $vendor = $invoice->vendor_id ? User::find($invoice->vendor_id) : null;
        $customer = $invoice->customer_id ? User::find($invoice->customer_id) : null;

        $formatCurrency = function ($amount) use ($setting) {
            $amount = number_format($amount, 2);
            return $setting->currency_position === 'right'
                ? $amount . $setting->currency_symbol
                : $setting->currency_symbol . $amount;
        };

        $taxDetails = json_decode($invoice->taxes, true) ?? [];

        // Fetch invoice items with product data
        $invoiceItems = CustomInvoiceItem::where('invoice_id', $view_id)
            ->with('product')
            ->get();

        $subtotal = $invoiceItems->sum('amount_total');
        $discountPercentage = $invoice->discount ?? 0;
        $discountAmount = ($subtotal * $discountPercentage) / 100;
        $afterDiscount = $subtotal - $discountAmount;
        $shipping = $invoice->shipping ?? 0;
        $totalTaxAmount = collect($taxDetails)->sum('amount');
        $finalTotal = $afterDiscount + $totalTaxAmount + $shipping;

        $pdfData = [
            'view_id' => $view_id,
            'sales' => $invoice,
            'setting' => $setting,
            'orderItems' => $invoiceItems,
            'salesItems' => $invoiceItems,
            'customer' => $customer ? $customer->toArray() : null,
            'vendor' => $vendor ? $vendor->toArray() : null,
            'subtotal' => $formatCurrency($subtotal),
            'discountAmount' => $formatCurrency($discountAmount),
            'afterDiscount' => $formatCurrency($afterDiscount),
            'shipping' => $formatCurrency($shipping),
            'finalTotal' => $formatCurrency($finalTotal),
            'paymentStatus' => $invoiceItems->first()?->payment_status ?? 'unpaid',
            'taxDetails' => $taxDetails
        ];

        $pdf = PDF::loadView('custom-invoice.custom-invoice-pdf', $pdfData);
        return $pdf->stream('invoice_' . $view_id . '.pdf');
    }

    public function show($id)
    {
        $invoice = CustomInvoice::findOrFail($id);
        $user = $invoice->vendor_id ? User::find($invoice->vendor_id) : User::find($invoice->customer_id);
        $companyInfo = Setting::first();

        // Get currency settings
        $currencySymbol = $companyInfo->currency_symbol ?? '₹';
        $currencyPosition = $companyInfo->currency_position ?? 'left';

        return view('custom-invoice.view', compact('invoice', 'user', 'companyInfo', 'currencySymbol', 'currencyPosition'));
    }
}
