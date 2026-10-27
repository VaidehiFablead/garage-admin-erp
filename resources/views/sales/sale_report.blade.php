@extends('layout.app')

@section('title', 'Sale Report')

@section('content')
<style>
    .invoice-box tr td {
        vertical-align: middle
    }
    @media screen and (max-width: 768px) {
        .page-header {
            flex-direction: row;
        }
        .page-header .page-btn {
            margin-top: 0 !important;
        }
        .card-sales-split ul {
            margin-left: 14rem;
        }
        .purchase_report_head img {
            max-width: 100px !important;
        }
        .purchase_report_head h2 {
            font-size: 17px !important;
        }
        .purchase_report_table1 tr td:first-child {
            font-size: 11px !important;
        }
        .purchase_report_table1 tr {
            display: flex;
            justify-content: space-between;
        }
        .purchase_report_table1 tr td:last-child {
            padding: 10px 5px !important;
            font-size: 11px !important;
        }
        .invoice-box {
            overflow-x: auto;
        }

        .heading td:first-child {
            padding: 10px 40px !important;

        }
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Sales Report</h4>
           
        </div>
        <div class="page-btn">
            <a href="{{ route('sales.report') }}" class="btn btn-added">
                Back
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <input type="hidden" name="selse_id" id="selse_id" value="">
            <div class="card-sales-split">
                <h2></h2>
                <ul>
                    <li>
                        <a href="javascript:void(0);" onclick="downloadPDF()" title="Download Invoice"><img
                                src="{{ env('ImagePath') .'admin/assets/img/icons/pdf.svg'}}" alt="img"></a>
                    </li>
                </ul>
            </div>
            <tr class="report-header">
                <td colspan="6">
                    <div class="row align-items-center purchase_report_head" style="padding: 10px 0;">
                        <!-- Left: Company Logo -->
                        <div class="col-4">
                            <img 
                                src="{{ $settings->logo ? env('ImagePath') . 'storage/' . $settings->logo : env('ImagePath') . 'admin/assets/img/logso.png' }}" 
                                alt="Company Logo" 
                                style="max-width: 120px;"
                            >
                        </div>

                        <!-- Center: Company Info -->
                        <div class="col-4 text-center">
                            <h4 style="margin: 0;">{{ $settings->name ?? 'Your Company Name' }}</h4>
                            <p style="margin: 0; font-size: 12px;">
                                {{ $settings->address ?? 'Company Address' }}<br>
                                Phone: {{ $settings->phone ?? '000-000-0000' }} | Email: {{ $settings->email ?? 'email@example.com' }}
                            </p>
                        </div>

                        <!-- Right: Report Title -->
                        <div class="col-4 text-end">
                            <h2 style="margin: 0;">Sales Report</h2>
                        </div>
                    </div>
                </td>
            </tr>

            <div class="download_pdf">
                <div class="invoice-box table-height"
                    style="max-width: 1600px; width:100%; margin:15px auto; padding: 0; font-size: 14px; line-height: 24px; color: #555;">
                    <table style="width: 100%; line-height: inherit; text-align: left;">
                        <tr>
                            <td colspan="12">
                                <table style="width: 100%;">
                                    <tr>
                                        

                                        <td style="padding: 10px; float: right;">
                                            <strong style="font-size:14px; color:#7367F0; font-weight:600;">Report
                                                Info</strong><br>
                                            Total Sales: {{ count($sales) }}<br>
                                            Report Date: {{ \Carbon\Carbon::now()->format('d M Y') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr class="heading" style="background: #F3F2F7;">
                            <td style="padding: 10px;"><strong>Product</strong></td>
                            <td style="padding: 10px;"><strong>Category</strong></td>
                            <td style="padding: 10px;"><strong>Original Price</strong></td>
                            <td style="padding: 10px;"><strong>Discount</strong></td>
                            <td style="padding: 10px;"><strong>Final Unit Price</strong></td>
                            <td style="padding: 10px;"><strong>Quantity</strong></td>
                            <td style="padding: 10px;"><strong>Total</strong></td>
                        </tr>

                        @php $subtotal = 0; @endphp
                        @foreach($sales as $sale)
                        @php
                        $discountPercent = $sale->invoice->discount ?? 0;
                        $originalUnitPrice = $sale->quantity ? $sale->total_amount / $sale->quantity : 0;
                        $discountPerUnit = ($originalUnitPrice * $discountPercent) / 100;
                        $finalUnitPrice = $originalUnitPrice - $discountPerUnit;
                        $finalTotal = $finalUnitPrice * $sale->quantity;

                        $subtotal += $finalTotal;

                        $images = json_decode($sale->product->images, true);
                        $imagePath = isset($images[0]) && file_exists(public_path('storage/' . $images[0]))
                        ? env('ImagePath') . 'storage/' . $images[0]
                        :  env('ImagePath') .'admin/assets/img/product/noimage.png';
                        @endphp

                        <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                            <td style="padding: 10px; white-space: normal;">
                                <a href="{{ url('product-view/' . ($sale->product->id ?? '')) }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                                    <img src="{{ $imagePath }}" alt="Product Image" style="width: 50px; height: 50px;">
                                    <div style="font-weight: 500;">{{ $sale->product->name ?? '-' }}</div>
                                </a>
                            </td>
                            <td style="padding: 10px; white-space: normal;">{{ $sale->product->category->name ?? 'N/A' }}</td>
                            <td style="padding: 10px;">
                                {{ $currencyPosition === 'left' ? $currencySymbol . number_format($originalUnitPrice, 2) : number_format($originalUnitPrice, 2) . $currencySymbol }}
                            </td>
                            <td style="padding: 10px;">{{ $discountPercent }}%</td>
                            <td style="padding: 10px;">
                                {{ $currencyPosition === 'left' ? $currencySymbol . number_format($finalUnitPrice, 2) : number_format($finalUnitPrice, 2) . $currencySymbol }}
                            </td>
                            <td style="padding: 10px;">{{ $sale->quantity }}</td>
                            <td style="padding: 10px;">
                                {{ $currencyPosition === 'left' ? $currencySymbol . number_format($finalTotal, 2) : number_format($finalTotal, 2) . $currencySymbol }}
                            </td>
                        </tr>
                        @endforeach

                    </table>

                    <div class="row mt-3">
                        <div class="col-lg-6"></div>
                        <div class="col-lg-6">
                            <div class="total-order w-100 max-widthauto m-auto mb-4">
                                <ul>
                                    <li>
                                        <h4>Subtotal</h4>
                                        <h5>
                                            {{ $currencyPosition === 'left' ? $currencySymbol . number_format($subtotal, 2) : number_format($subtotal, 2) . $currencySymbol }}
                                        </h5>
                                    </li>


                                    @foreach($taxDetails as $tax)
                                    <li>
                                        <h4>{{ $tax['name'] }} Tax</h4>
                                        <h5 style="font-family: Helvetica, Arial, sans-serif;">
                                            {{ $tax['rate'] }}%
                                            <span
                                                style="white-space: nowrap; display: inline-block; font-family: Helvetica, Arial, sans-serif;">
                                                ({{ $currencyPosition === 'left'
                                            ? $currencySymbol . number_format($tax['amount'], 2)
                                            : number_format($tax['amount'], 2) . $currencySymbol }})
                                            </span>
                                        </h5>
                                    </li>
                                    @endforeach
                                    <li class="total">
                                        <h4>Total Amount</h4>
                                        <h5>
                                            {{ $currencyPosition === 'left' ? $currencySymbol . number_format($totalAmount, 2) : number_format($totalAmount, 2) . $currencySymbol }}
                                        </h5>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
    const element = document.querySelector('.download_pdf');
    const logoUrl = "{{ $settings->logo ? env('ImagePath').'/storage/' . $settings->logo : env('ImagePath').'/admin/assets/img/logso.png' }}";

    loadImageAsBase64(logoUrl, function(logoBase64, logoWidth, logoHeight) {
        const opt = {
            margin: [50, 10, 30, 10], // top margin increased to fit header
            filename: 'Sales Report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak: { avoid: 'tr' }
        };

        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function(pdf) {
            const pageCount = pdf.internal.getNumberOfPages();
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            for (let i = 1; i <= pageCount; i++) {
                pdf.setPage(i);

                const logoTop = 10;
                const logoBottomMargin = 5;
                const headerHeight = Math.max(logoHeight, 18);
                const headerEndY = logoTop + headerHeight + logoBottomMargin;

                // === HEADER START ===
                // Logo (left)
                pdf.addImage(logoBase64, 'JPEG', 10, logoTop, logoWidth, logoHeight);

                // Company Name (center)
                pdf.setFontSize(14);
                pdf.setFont('helvetica', 'bold');
                pdf.setTextColor(0);
                pdf.text("{{ $settings->name ?? 'Company Name' }}", pageWidth / 2, logoTop + 5, { align: 'center' });

                // Address
                pdf.setFontSize(10);
                pdf.setFont('helvetica', 'normal');
                pdf.text("{{ $settings->address ?? '5001, Ascon Palaza' }}", pageWidth / 2, logoTop + 11, { align: 'center' });

                // Phone & Email
                pdf.text(
                    "Phone: {{ $settings->phone ?? '1231232123' }} | Email: {{ $settings->email ?? 'info@example.com' }}",
                    pageWidth / 2,
                    logoTop + 16,
                    { align: 'center' }
                );

                // Divider line
                pdf.setDrawColor(100);
                pdf.setLineWidth(0.5);
                pdf.line(10, headerEndY + 8, pageWidth - 10, headerEndY + 8);

                // Report Title under header
                pdf.setFontSize(12);
                pdf.setFont('helvetica', 'bold');
                pdf.text("Sales Report", pageWidth / 2, headerEndY + 15, { align: 'center' });
                // === HEADER END ===

                // === FOOTER START ===
                pdf.setFontSize(9);
                pdf.setFont('helvetica', 'normal');
                pdf.setTextColor(150);
                pdf.line(10, pageHeight - 15, pageWidth - 10, pageHeight - 15);
                pdf.text(`© {{ now()->year }} {{ $settings->name ?? 'Company Name' }} - All rights reserved.`, 10, pageHeight - 10);
                pdf.text(`Page ${i} of ${pageCount}`, pageWidth - 10, pageHeight - 10, { align: 'right' });
                // === FOOTER END ===
            }
        }).save();
    });
}

// ✅ Image to base64
function loadImageAsBase64(url, callback) {
    const img = new Image();
    img.crossOrigin = 'anonymous';

    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext('2d');
        ctx.fillStyle = "#FFFFFF";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);

        const base64 = canvas.toDataURL('image/jpeg');
        const desiredWidth = 30; // mm
        const ratio = img.height / img.width;
        const desiredHeight = desiredWidth * ratio;

        callback(base64, desiredWidth, desiredHeight);
    };

    img.onerror = function() {
        alert("Logo could not be loaded.");
        callback('', 0, 0);
    };

    img.src = url;
}

</script>
@endpush