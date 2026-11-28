<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $jobCard->bill_no ?? $jobCard->job_card_number ?? '' }}</title>
    <style>
        @page {
            size: A4;
            margin: 7mm 8mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .jobcard-box {
            border: 1.5px solid #1f2937;
            padding: 9px 11px;
            position: relative;
            height: 272mm;
            box-sizing: border-box;
        }

        .bottom-section {
            position: absolute;
            bottom: 10px;
            left: 11px;
            right: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .badge-title {
            display: inline-block;
            background: #1f2937;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 0.8px;
            padding: 4px 10px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .meta-bar {
            width: 100%;
            margin: 6px 0 7px 0;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .meta-bar td {
            padding: 4px 6px;
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9.5px;
        }

        .meta-bar tr td:last-child {
            border-right: none;
        }

        .meta-bar tr:last-child td {
            border-bottom: none;
        }

        .section-title {
            background: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 3px 7px;
            border: 1px solid #cbd5e1;
            margin-top: 6px;
        }

        .info-grid {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-top: none;
            margin-bottom: 6px;
        }

        .info-grid > tbody > tr > td {
            padding: 4px 7px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .info-subtable td {
            padding: 2px 3px;
            border-bottom: 1px dotted #e2e8f0;
            font-size: 9.5px;
        }

        .info-subtable tr:last-child td {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #475569;
            width: 35%;
        }

        .work-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            margin-bottom: 6px;
        }

        .work-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 9px;
            text-transform: uppercase;
            padding: 4px 5px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .work-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right, .text-end {
            text-align: right;
        }

        .text-left, .text-start {
            text-align: left;
        }

        .summary-grid {
            width: 100%;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            margin-top: 6px;
        }

        .summary-grid > tbody > tr > td {
            padding: 5px 8px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .signature-table {
            width: 100%;
            margin-top: 14px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            padding-top: 20px;
            font-size: 9.5px;
            color: #334155;
        }

        .sig-line {
            border-top: 1px solid #475569;
            width: 70%;
            margin: 0 auto 3px auto;
        }
    </style>
</head>

<body>
    @php
        $currency = $setting->currency_symbol ?? 'Rs.';
        $isQuotation = strtolower($jobCard->type ?? '') === 'quotation';
        $formattedDate = $jobCard->date ? \Carbon\Carbon::parse($jobCard->date)->format('d-m-Y') : '--';
        $formattedDueDate = $jobCard->next_due_date ? \Carbon\Carbon::parse($jobCard->next_due_date)->format('d-m-Y') : '';
        $gstEnabled = ((string) ($jobCard->gst ?? '')) === '1';

        // Value check helper
        $hasVal = function($val) {
            if ($val === null) return false;
            $v = trim((string) $val);
            return $v !== '' && $v !== '--' && $v !== '-' && strtolower($v) !== 'n/a' && strtolower($v) !== 'null';
        };

        $customerName = $jobCard->customer->name ?? ($jobCard->customer_name ?: ($jobCard->customer_id ?? ''));
        $customerGst = $jobCard->gst_no ?: ($jobCard->customer->gst_num ?? '');
        $customerEmail = $jobCard->customer->email ?? '';

        // Check which GST columns exist with values in parts
        $hasCgst = false;
        $hasSgst = false;
        $hasIgst = false;

        foreach ($partsData as $part) {
            if ((float) ($part['cgst_percent'] ?? 0) > 0 || (float) ($part['cgst_amount'] ?? 0) > 0) {
                $hasCgst = true;
            }
            if ((float) ($part['sgst_percent'] ?? 0) > 0 || (float) ($part['sgst_amount'] ?? 0) > 0) {
                $hasSgst = true;
            }
            if ((float) ($part['igst_percent'] ?? 0) > 0 || (float) ($part['igst_amount'] ?? 0) > 0) {
                $hasIgst = true;
            }
        }

        // Decode JSON discounts
        $labourDiscount = json_decode($jobCard->labour_discount ?? '{}', true) ?: [];
        $partsDiscount = json_decode($jobCard->parts_discount ?? '{}', true) ?: [];

        // Compute actual discount amounts
        $labourDiscountAmount = 0;
        if (!empty($labourDiscount)) {
            if (($labourDiscount['type'] ?? '') === '%') {
                $labourDiscountAmount = ($labourTotal ?? 0) * (float) ($labourDiscount['amount'] ?? 0) / 100;
            } else {
                $labourDiscountAmount = (float) ($labourDiscount['amount'] ?? 0);
            }
        }

        $partsDiscountAmount = 0;
        if (!empty($partsDiscount)) {
            if (($partsDiscount['type'] ?? '') === '%') {
                $partsDiscountAmount = ($partsTotal ?? 0) * (float) ($partsDiscount['amount'] ?? 0) / 100;
            } else {
                $partsDiscountAmount = (float) ($partsDiscount['amount'] ?? 0);
            }
        }

        $extraDiscount = (float) ($jobCard->discount ?? 0);
        $grandTotal = (float) ($jobCard->total_subtotal_amount ?? (($labourTotal ?? 0) + ($partsTotal ?? 0) - $labourDiscountAmount - $partsDiscountAmount - $extraDiscount));
        $remainingAmount = (float) ($jobCard->remaining_amount ?? $grandTotal);
        $paidAmount = max($grandTotal - $extraDiscount - $remainingAmount, 0);

        if (!function_exists('convertNumberToWords')) {
            function convertNumberToWords($number)
            {
                $hyphen = '-';
                $conjunction = ' and ';
                $negative = 'negative ';
                $dictionary = [
                    0 => 'zero',
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four',
                    5 => 'five',
                    6 => 'six',
                    7 => 'seven',
                    8 => 'eight',
                    9 => 'nine',
                    10 => 'ten',
                    11 => 'eleven',
                    12 => 'twelve',
                    13 => 'thirteen',
                    14 => 'fourteen',
                    15 => 'fifteen',
                    16 => 'sixteen',
                    17 => 'seventeen',
                    18 => 'eighteen',
                    19 => 'nineteen',
                    20 => 'twenty',
                    30 => 'thirty',
                    40 => 'forty',
                    50 => 'fifty',
                    60 => 'sixty',
                    70 => 'seventy',
                    80 => 'eighty',
                    90 => 'ninety',
                ];

                if (!is_numeric($number)) {
                    return '';
                }

                if ($number < 0) {
                    return $negative . convertNumberToWords(abs($number));
                }

                $string = '';
                if ($number < 21) {
                    $string = $dictionary[$number];
                } elseif ($number < 100) {
                    $tens = ((int) ($number / 10)) * 10;
                    $units = $number % 10;
                    $string = $dictionary[$tens];
                    if ($units) {
                        $string .= $hyphen . $dictionary[$units];
                    }
                } elseif ($number < 1000) {
                    $hundreds = (int) ($number / 100);
                    $remainder = $number % 100;
                    $string = $dictionary[$hundreds] . ' hundred';
                    if ($remainder) {
                        $string .= $conjunction . convertNumberToWords($remainder);
                    }
                } else {
                    $baseUnits = [10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand'];
                    foreach ($baseUnits as $divisor => $label) {
                        if ($number >= $divisor) {
                            $units = (int) ($number / $divisor);
                            $remainder = $number % $divisor;
                            $string = convertNumberToWords($units) . ' ' . $label;
                            if ($remainder) {
                                $string .= $conjunction . convertNumberToWords($remainder);
                            }
                            break;
                        }
                    }
                }
                return $string;
            }
        }

        $amountInWords = ucwords(convertNumberToWords((int) round($grandTotal))) . ' Rupees';
    @endphp

    <div class="jobcard-box">
        {{-- Garage Header --}}
        <table class="header-table">
            <tr>
                <td style="width: 22%;">
                    @php
                        $logoSrc = null;
                        $candidatePaths = [];
                        if (!empty($setting->logo)) {
                            $candidatePaths[] = storage_path('app/public/' . $setting->logo);
                            $candidatePaths[] = public_path('storage/' . $setting->logo);
                        }
                        $candidatePaths[] = public_path('admin/assets/img/logo.png');
                        $candidatePaths[] = public_path('admin/assets/img/logo3.png');

                        foreach ($candidatePaths as $path) {
                            if ($path && file_exists($path) && is_file($path)) {
                                $mime = mime_content_type($path);
                                if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                                    $img = @imagecreatefromwebp($path);
                                    if ($img) {
                                        ob_start();
                                        imagepng($img);
                                        $pngData = ob_get_clean();
                                        imagedestroy($img);
                                        if ($pngData) {
                                            $logoSrc = 'data:image/png;base64,' . base64_encode($pngData);
                                            break;
                                        }
                                    }
                                } elseif ($mime !== 'image/webp') {
                                    $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Company Logo" style="max-height: 52px; max-width: 140px; width: auto;">
                    @endif
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">
                        {{ $setting->name ?? 'Garage Workshop' }}
                    </div>
                    <div style="font-size: 9px; color: #475569; margin-top: 2px;">
                        {{ $setting->address ?? '' }}<br>
                        @if(!empty($setting->phone)) Ph: {{ $setting->phone }} @endif
                        @if(!empty($setting->alt_phone)) / {{ $setting->alt_phone }} @endif
                        @if(!empty($setting->email)) | {{ $setting->email }} @endif
                        @if(!empty($setting->gst_num)) <br>GSTIN: {{ $setting->gst_num }} @endif
                    </div>
                </td>
                <td style="width: 28%; text-align: right;">
                    <div class="badge-title">{{ $isQuotation ? 'QUOTATION' : 'TAX INVOICE' }}</div>
                    <div style="font-size: 9.5px; color: #475569; margin-top: 2px;">
                        <strong>Invoice #:</strong> {{ $jobCard->bill_no ?: ($jobCard->job_card_number ?? '--') }}<br>
                        <strong>Job Card #:</strong> {{ $jobCard->job_card_number ?? '--' }}<br>
                        <strong>Date:</strong> {{ $formattedDate }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Invoice Key Meta Bar --}}
        <table class="meta-bar">
            <tr>
                <td><strong>Invoice Date:</strong> {{ $formattedDate }}</td>
                @if ($hasVal($formattedDueDate))
                    <td><strong>Next Due Date:</strong> {{ $formattedDueDate }}</td>
                @endif
                <td><strong>Type:</strong> {{ $jobCard->type ?? ($isQuotation ? 'Quotation' : 'Invoice') }}</td>
                <td><strong>Payment Status:</strong> {{ ucfirst($jobCard->payment_status ?? ($remainingAmount <= 0 && $grandTotal > 0 ? 'Completed' : 'Pending')) }}</td>
                <td><strong>GST Mode:</strong> {{ ($hasCgst || $hasSgst || $hasIgst || !empty($labourGst) || $gstEnabled) ? 'With GST' : 'Without GST' }}</td>
            </tr>
        </table>

        {{-- Customer & Vehicle Details --}}
        <div class="section-title">Customer &amp; Vehicle Details</div>
        <table class="info-grid">
            <tr>
                <td style="width: 50%; border-right: 1px solid #cbd5e1;">
                    <table class="info-subtable">
                        @if ($hasVal($customerName))
                            <tr>
                                <td class="label">Customer Name</td>
                                <td><strong>{{ $customerName }}</strong></td>
                            </tr>
                        @endif
                        @if ($hasVal($jobCard->mobile))
                            <tr>
                                <td class="label">Mobile No</td>
                                <td>{{ $jobCard->mobile }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($customerEmail))
                            <tr>
                                <td class="label">Email</td>
                                <td>{{ $customerEmail }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($jobCard->address))
                            <tr>
                                <td class="label">Address</td>
                                <td>{{ $jobCard->address }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($customerGst))
                            <tr>
                                <td class="label">Customer GST No</td>
                                <td>{{ $customerGst }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td style="width: 50%;">
                    <table class="info-subtable">
                        @if ($hasVal($jobCard->vehicle_no))
                            <tr>
                                <td class="label">Vehicle Reg No</td>
                                <td><strong>{{ $jobCard->vehicle_no }}</strong></td>
                            </tr>
                        @endif
                        @if ($hasVal($model_name))
                            <tr>
                                <td class="label">Model / Make</td>
                                <td>{{ $model_name }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($jobCard->chassis_no))
                            <tr>
                                <td class="label">Chassis No</td>
                                <td>{{ $jobCard->chassis_no }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($jobCard->engine_no))
                            <tr>
                                <td class="label">Engine No</td>
                                <td>{{ $jobCard->engine_no }}</td>
                            </tr>
                        @endif
                        @if ($hasVal($jobCard->km_hours) || $hasVal($jobCard->due_km_hours))
                            <tr>
                                <td class="label">Odometer (KM/Hr)</td>
                                <td>
                                    @if ($hasVal($jobCard->km_hours))
                                        {{ $jobCard->km_hours }}
                                    @endif
                                    @if ($hasVal($jobCard->km_hours) && $hasVal($jobCard->due_km_hours))
                                        &nbsp;|&nbsp;
                                    @endif
                                    @if ($hasVal($jobCard->due_km_hours))
                                        <strong>Next Due:</strong> {{ $jobCard->due_km_hours }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        {{-- Labour / Services Table --}}
        <div class="section-title">Labour &amp; Services</div>
        <table class="work-table">
            <thead>
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th>Service / Labour Description</th>
                    <th style="width: 14%;" class="text-right">Rate ({{ $currency }})</th>
                    @if (!empty($labourGst))
                        @foreach ($labourGst as $tax)
                            <th style="width: 12%;" class="text-center">
                                {{ $tax['tax_name'] }} ({{ $tax['rate'] }}%)
                            </th>
                        @endforeach
                    @endif
                    <th style="width: 15%;" class="text-right">Total ({{ $currency }})</th>
                </tr>
            </thead>
            <tbody>
                @php $labourTotal = 0; @endphp
                @forelse($labourData as $index => $labour)
                    @php
                        $price = (float) ($labour['price'] ?? 0);
                        $rowTotal = $price;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $labour['name'] ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($price, 2) }}</td>
                        @if (!empty($labourGst))
                            @foreach ($labourGst as $tax)
                                @php
                                    $taxRate = (float) ($tax['rate'] ?? 0);
                                    $taxAmount = ($price * $taxRate) / 100;
                                    $rowTotal += $taxAmount;
                                @endphp
                                <td class="text-center">{{ number_format($taxAmount, 2) }}</td>
                            @endforeach
                        @endif
                        <td class="text-right">{{ number_format($rowTotal, 2) }}</td>
                    </tr>
                    @php $labourTotal += $rowTotal; @endphp
                @empty
                    <tr>
                        <td colspan="{{ 3 + (!empty($labourGst) ? count($labourGst) : 0) }}" class="text-center" style="color: #64748b;">
                            No labour data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Parts & Materials Table --}}
        @if (count($partsData) > 0)
            <div class="section-title">Parts &amp; Consumables</div>
            <table class="work-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th>Part Description</th>
                        <th style="width: 6%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Rate</th>
                        @if ($hasCgst)
                            <th style="width: 8%;" class="text-center">CGST (%)</th>
                            <th style="width: 9%;" class="text-right">CGST</th>
                        @endif
                        @if ($hasSgst)
                            <th style="width: 8%;" class="text-center">SGST (%)</th>
                            <th style="width: 9%;" class="text-right">SGST</th>
                        @endif
                        @if ($hasIgst)
                            <th style="width: 8%;" class="text-center">IGST (%)</th>
                            <th style="width: 9%;" class="text-right">IGST</th>
                        @endif
                        <th style="width: 14%;" class="text-right">Total ({{ $currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @php $partsTotal = 0; @endphp
                    @foreach ($partsData as $index => $part)
                        @php
                            $qty = (float) ($part['qty'] ?? 1);
                            $rate = (float) ($part['price'] ?? 0);
                            $baseAmount = $qty * $rate;

                            $cgstP = (float) ($part['cgst_percent'] ?? 0);
                            $sgstP = (float) ($part['sgst_percent'] ?? 0);
                            $igstP = (float) ($part['igst_percent'] ?? 0);

                            $cgstA = ($baseAmount * $cgstP) / 100;
                            $sgstA = ($baseAmount * $sgstP) / 100;
                            $igstA = ($baseAmount * $igstP) / 100;

                            $rowTotal = $baseAmount + $cgstA + $sgstA + $igstA;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $part['name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $qty }}</td>
                            <td class="text-right">{{ number_format($rate, 2) }}</td>
                            @if ($hasCgst)
                                <td class="text-center">{{ $cgstP > 0 ? $cgstP . '%' : '-' }}</td>
                                <td class="text-right">{{ number_format($cgstA, 2) }}</td>
                            @endif
                            @if ($hasSgst)
                                <td class="text-center">{{ $sgstP > 0 ? $sgstP . '%' : '-' }}</td>
                                <td class="text-right">{{ number_format($sgstA, 2) }}</td>
                            @endif
                            @if ($hasIgst)
                                <td class="text-center">{{ $igstP > 0 ? $igstP . '%' : '-' }}</td>
                                <td class="text-right">{{ number_format($igstA, 2) }}</td>
                            @endif
                            <td class="text-right">{{ number_format($rowTotal, 2) }}</td>
                        </tr>
                        @php $partsTotal += $rowTotal; @endphp
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="bottom-section">
            {{-- Bank Details & Totals Summary --}}
            <table class="summary-grid">
                <tr>
                    {{-- Left: Bank Details, QR & Remarks --}}
                    <td style="width: 54%; border-right: 1px solid #cbd5e1;">
                        <div style="font-weight: bold; text-transform: uppercase; margin-bottom: 3px; font-size: 9.5px; color: #0f172a;">
                            Bank Details:
                        </div>
                        <table style="width: 100%; font-size: 9px; margin-bottom: 5px;">
                            <tr>
                                <td style="width: 65%; vertical-align: top;">
                                    <div><strong>Bank:</strong> {{ $setting->bank_name ?? 'N/A' }}</div>
                                    <div><strong>Branch:</strong> {{ $setting->branch ?? 'N/A' }}</div>
                                    <div><strong>A/C No:</strong> {{ $setting->ac_no ?? 'N/A' }}</div>
                                    <div><strong>IFSC:</strong> {{ $setting->ifsc_code ?? 'N/A' }}</div>
                                </td>
                                <td style="width: 35%; text-align: center; vertical-align: middle;">
                                    @if (isset($setting->qr_code) && file_exists(storage_path('app/public/' . $setting->qr_code)))
                                        @php
                                            $qrPath = storage_path('app/public/' . $setting->qr_code);
                                            $qrData = base64_encode(file_get_contents($qrPath));
                                            $qrMime = mime_content_type($qrPath);
                                        @endphp
                                        <img src="data:{{ $qrMime }};base64,{{ $qrData }}" style="width: 58px; height: 58px; border: 1px solid #cbd5e1;">
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div style="border-top: 1px dotted #cbd5e1; padding-top: 3px; margin-top: 3px;">
                            <strong>Amount In Words:</strong><br>
                            <span style="font-size: 9px; color: #334155;">{{ $amountInWords }} Only</span>
                        </div>

                        @if ($hasVal($jobCard->remark))
                            <div style="margin-top: 3px; font-size: 9px;">
                                <strong>Remarks:</strong> {{ $jobCard->remark }}
                            </div>
                        @endif
                    </td>

                    {{-- Right: Totals Breakdown --}}
                    <td style="width: 46%; text-align: right;">
                        <table style="width: 100%; font-size: 9.5px;">
                            <tr>
                                <td class="text-left" style="color: #475569;">Total Labour:</td>
                                <td class="text-right"><strong>{{ $currency }} {{ number_format((float) ($labourTotal ?? 0), 2) }}</strong></td>
                            </tr>
                            @if ($labourDiscountAmount > 0)
                                <tr>
                                    <td class="text-left" style="color: #dc2626;">Labour Discount:</td>
                                    <td class="text-right" style="color: #dc2626;">- {{ $currency }} {{ number_format($labourDiscountAmount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-left" style="color: #475569;">Total Parts:</td>
                                <td class="text-right"><strong>{{ $currency }} {{ number_format((float) ($partsTotal ?? 0), 2) }}</strong></td>
                            </tr>
                            @if ($partsDiscountAmount > 0)
                                <tr>
                                    <td class="text-left" style="color: #dc2626;">Parts Discount:</td>
                                    <td class="text-right" style="color: #dc2626;">- {{ $currency }} {{ number_format($partsDiscountAmount, 2) }}</td>
                                </tr>
                            @endif
                            @if ($extraDiscount > 0)
                                <tr>
                                    <td class="text-left" style="color: #dc2626;">Additional Discount:</td>
                                    <td class="text-right" style="color: #dc2626;">- {{ $currency }} {{ number_format($extraDiscount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-left" style="border-top: 1px solid #cbd5e1; padding-top: 3px; font-weight: bold; color: #0f172a; font-size: 11px;">Grand Total:</td>
                                <td class="text-right" style="border-top: 1px solid #cbd5e1; padding-top: 3px; font-weight: bold; color: #0f172a; font-size: 11px;">
                                    {{ $currency }} {{ number_format($grandTotal, 2) }}
                                </td>
                            </tr>
                            @if ($paidAmount > 0)
                                <tr>
                                    <td class="text-left" style="color: #16a34a; font-size: 9px;">Paid Amount:</td>
                                    <td class="text-right" style="color: #16a34a; font-size: 9px;">{{ $currency }} {{ number_format($paidAmount, 2) }}</td>
                                </tr>
                            @endif
                            @if ($remainingAmount > 0 && $remainingAmount < $grandTotal)
                                <tr>
                                    <td class="text-left" style="color: #ea580c; font-size: 9px;">Remaining Balance:</td>
                                    <td class="text-right" style="color: #ea580c; font-size: 9px;">{{ $currency }} {{ number_format($remainingAmount, 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Signatures --}}
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="sig-line"></div>
                        Customer Signature
                    </td>
                    <td>
                        <div class="sig-line"></div>
                        For, {{ $setting->name ?? 'Auto Care' }}<br>(Authorized Signatory)
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
