@extends('layout.app')

@section('title', 'Job Card View')

@section('content')

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            /* Set base font size here */
            margin: 0;
            padding: 0;
            background: white;
        }

        .card-body {
            width: 60%;
            min-height: 80%;
            padding: 15mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        @media only screen and (max-width: 768px) {
            * {
                box-sizing: border-box !important;
            }

            body {
                overflow-x: hidden;
                width: 100%;
            }

            .card-body {
                width: 98% !important;
                padding: 10px !important;
                margin: 5px auto !important;
                min-height: auto !important;
                overflow-x: hidden;
            }

            .top-header-table, .details-table, .footer-col table {
                width: 100% !important;
            }

            .top-header-table td, .details-row-td, .footer-col {
                display: block !important;
                width: 100% !important;
                /* text-align: center !important; */
                /* padding: 5px 0 !important; */
                position: static !important;
            }

            .top-header-table td img {
                margin: 0 auto 10px auto !important;
            }

            .invoice-meta-item {
                width: 100% !important;
                display: block !important;
                text-align: center !important;
                margin-bottom: 10px;
                font-size: 14px !important;
            }

            .vertical-line {
                display: none !important;
            }

            .details-row-td table td {
                text-align: left !important;
                padding: 4px 0 !important;
            }

            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
            }

            .table-responsive table {
                min-width: 500px;
                width: 100% !important;
            }
            
            .table-bordered th, .table-bordered td {
                font-size: 10px !important;
                padding: 5px 2px !important;
                word-break: break-word;
            }

            .footer-col table {
                margin-bottom: 10px;
            }
            
            .invoice-meta-container {
                text-align: center !important;
            }
        }

        table,
        table td,
        table th {
            font-size: inherit;
            /* Make sure tables inherit the font size */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .header-table {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
        }

        .header-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }

        .table-bordered thead tr {
            background-color: #e9ecf0ff;
            color: #333;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-bordered tfoot tr {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        h3,
        h4 {
            margin: 0 0 10px 0;
            color: #343a40;
        }

        .invoice-title {
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #e5e7ebff;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .logo-container {
            position: relative;
            min-height: 50px;
            margin-bottom: 10px;
        }

        .logo-container .qr-code {
            height: 60px;
            position: absolute;
            /* top: 0;
            left: 0; */
        }

        .logo-container .company-logo {
            height: 50px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .logo-container .company-details {
            text-align: center;
        }


        .signature-section img {
            height: 50px;
            margin-top: 5px;
        }

        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
    </style>


    <div class="card-body">
        <table class="top-header-table" style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <!-- Logo on the left -->
                <td style="width: 150px; vertical-align: middle;">
                    @if (isset($setting->logo) && file_exists(storage_path('app/public/' . $setting->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $setting->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
                            style="height: 100px; width: auto; max-width: 100%;">
                    @endif
                </td>

                <!-- Company info on the right -->
                <td style="vertical-align: middle; text-align: right; padding-left: 15px;">
                    <h3 style="margin: 0; text-transform: uppercase;">
                        {{ $setting->name ?? '' }}
                    </h3>
                    <small style="text-transform: uppercase;">
                        {!! preg_replace('/(Dhamdod naka,)/i', '$1<br>', $setting->address ?? '') !!}<br>
                        Phone: {{ $setting->phone ?? '' }} | 
                        Email: <span style="text-transform: none;">{{ $setting->email ?? '' }}</span>
                    </small>
                </td>
            </tr>
        </table>



        <hr style="height: 2px; background-color: #d7cdcd; border: none; margin-top: 0; margin-bottom: 20px;">


        <div class="text-center mb-1">
            <h3 class="mb-3" style="text-transform: uppercase; text-decoration: underline;">Bill of Supply</h3>
            <div class="invoice-meta-container" style="margin-bottom: 10px; width: 100%; text-align: center;">
                <div class="invoice-meta-item" style="display: inline-block; width: 49%; text-align: left; font-size: 16px;">
                    <strong>INVOICE NO : {{ $jobCard->bill_no ?? ' -- ' }}</strong>
                </div>
                <div class="invoice-meta-item" style="display: inline-block; width: 49%; text-align: right; font-size: 16px;">
                    <strong>GST NO : {{ $setting->gst_num ?? ' -- ' }}</strong>
                </div>
            </div>
        </div>

        <table class="details-table" style="width:100%; border-collapse: collapse; font-size: 12px; margin-bottom: 10px;">
            <tr>
                <!-- Customer Details -->
                <!-- Customer Details -->
                <td class="details-row-td"
                    style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem; font-size: 15px;">Customer
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 13px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 8px 0;">Name :</td>
                            <td style="text-align: right; padding: 0;">
                                {{ $jobCard->customer->name ?? ($jobCard->customer_id ?? '--') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Location :</td>
                            <td style="text-align: right; padding: 0;">
                                @php
                                    $address = $jobCard->address ?? '--';
                                    if ($address !== '--') {
                                        $words = explode(' ', $address);
                                        $chunks = array_chunk($words, 3);
                                        $formattedAddress = implode(' ', array_map(fn($chunk) => implode(' ', $chunk), $chunks));
                                        echo $formattedAddress;
                                    } else {
                                        echo $address;
                                    }
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Mobile :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->mobile ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">GST No :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->customer->gst_num ?? '--' }}</td>
                        </tr>
                    </table>

                    <!-- Half vertical line -->
                    <div class="vertical-line" style="position: absolute; right: 0; top: 10%; height: 80%; border-right: 1px solid #e63509;"></div>
                </td>

                <!-- Vehicle Details -->
                <td class="details-row-td" style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem; font-size: 15px;">Vehicle
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 13px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 8px 0;">Reg No :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->vehicle_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Model :</td>
                            <td style="text-align: right; padding: 0;">{{ $model_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Engine No :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->engine_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Chassis No :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->chassis_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">KM :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->km_hours ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Due KM :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->due_km_hours ?? '-' }}</td>
                        </tr>
                    </table>
                    <div class="vertical-line" style="position: absolute; right: 0; top: 10%; height: 80%; border-right: 1px solid #e63509;"></div>
                </td>


                <!-- Invoice Details -->
                <td class="details-row-td"
                    style="width:34%; border: 0px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem; font-size: 15px;">Invoice
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 13px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 8px 0;">Job No :</td>
                            <td style="text-align: right; padding: 0;">{{ $jobCard->job_card_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Date :</td>
                            <td style="text-align: right; padding: 0;">
                                {{ \Carbon\Carbon::parse($jobCard->date)->format('d-M-Y') ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Due Date :</td>
                            <td style="text-align: right; padding: 0;">
                                {{ \Carbon\Carbon::parse($jobCard->next_due_date)->format('d-M-Y') ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <div class="text-center">
            <h4 style="text-transform: uppercase;">Labour</h4>
        </div>
        <div class="table-responsive">
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th style="width:10%; background-color:#FF9F43; color:#fff;">Sr No</th>
                        <th class="text-center" style="background-color:#FF9F43; color:#fff;">Description</th>
                        <th class="text-center" style="width:15%; background-color:#FF9F43; color:#fff;">Rate</th>

                        {{-- Dynamic Tax Columns --}}
                        @foreach ($labourGst as $tax)
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">
                                {{ $tax['tax_name'] }} ({{ $tax['rate'] }}%)
                            </th>
                        @endforeach

                        <th class="text-center" style="width:20%; background-color:#FF9F43; color:#fff;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $labourTotal = 0; @endphp
                    @forelse($labourDetails as $index => $labour)
                        @php
                            $price = $labour['price'] ?? 0;
                            $rowTotal = $price;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $labour['name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ number_format($price, 2) }}</td>

                            {{-- Row-wise tax calculation (only if GST is available) --}}
                            @if (!empty($labourGst))
                                @foreach ($labourGst as $tax)
                                    @php
                                        $taxRate = $tax['rate'] ?? 0;
                                        $taxAmount = ($price * $taxRate) / 100;
                                        $rowTotal += $taxAmount;
                                    @endphp
                                    <td class="text-center">{{ number_format($taxAmount, 2) }}</td>
                                @endforeach
                            @endif

                            <td class="text-center">{{ number_format($rowTotal, 2) }}</td>
                        </tr>
                        @php $labourTotal += $rowTotal; @endphp
                    @empty
                        <tr>
                            <td colspan="{{ 3 + (!empty($labourGst) ? count($labourGst) : 0) + 1 }}" class="text-center">
                                No labour data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>


        <br>

        @if (count($partsDetails) > 0)
            <div class="text-center">
                <h4 style="text-transform: uppercase;">Parts</h4>
            </div>
            <div class="table-responsive">
                <table class="table-bordered">
                    <thead>
                        <tr>
                            <th style="width:10%; background-color:#FF9F43; color:#fff;">Sr No</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">Description</th>
                            <th class="text-center" style="width:10%; background-color:#FF9F43; color:#fff;">Qty</th>
                            <th class="text-center" style="width:15%; background-color:#FF9F43; color:#fff;">Rate</th>

                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">CGST (%)</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">CGST (Rs.)</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">SGST (%)</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">SGST (Rs.)</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">IGST (%)</th>
                            <th class="text-center" style="background-color:#FF9F43; color:#fff;">IGST (Rs.)</th>

                            <th class="text-center" style="width:20%; background-color:#FF9F43; color:#fff;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $partsTotal = 0; @endphp
                        @foreach ($partsDetails as $index => $part)
                            @php
                                $qty = $part['qty'] ?? 1;
                                $rate = $part['price'] ?? 0;
                                $baseAmount = $qty * $rate;
                                
                                $cgstP = $part['cgst_percent'] ?? 0;
                                $sgstP = $part['sgst_percent'] ?? 0;
                                $igstP = $part['igst_percent'] ?? 0;

                                $cgstA = ($baseAmount * $cgstP) / 100;
                                $sgstA = ($baseAmount * $sgstP) / 100;
                                $igstA = ($baseAmount * $igstP) / 100;

                                $rowTotal = $baseAmount + $cgstA + $sgstA + $igstA;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $part['name'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $qty }}</td>
                                <td class="text-center">{{ number_format($rate, 2) }}</td>

                                <td class="text-center">{{ $cgstP }}</td>
                                <td class="text-center">{{ number_format($cgstA, 2) }}</td>
                                <td class="text-center">{{ $sgstP }}</td>
                                <td class="text-center">{{ number_format($sgstA, 2) }}</td>
                                <td class="text-center">{{ $igstP }}</td>
                                <td class="text-center">{{ number_format($igstA, 2) }}</td>

                                <td class="text-center">{{ number_format($rowTotal, 2) }}</td>
                            </tr>
                            @php $partsTotal += $rowTotal; @endphp
                        @endforeach

                    </tbody>
                </table>
            </div>
        @endif

        @php

            // Decode JSON discounts
            $labourDiscount = json_decode($jobCard->labour_discount ?? '{}', true);
            $partsDiscount = json_decode($jobCard->parts_discount ?? '{}', true);

            // Compute actual discount amounts
            $labourDiscountAmount = 0;
            if (!empty($labourDiscount)) {
                if (($labourDiscount['type'] ?? '') === '%') {
                    $labourDiscountAmount = ($labourTotal ?? 0) * ($labourDiscount['amount'] ?? 0) / 100;
                } else {
                    $labourDiscountAmount = $labourDiscount['amount'] ?? 0;
                }
            }

            $partsDiscountAmount = 0;
            if (!empty($partsDiscount)) {
                if (($partsDiscount['type'] ?? '') === '%') {
                    $partsDiscountAmount = ($partsTotal ?? 0) * ($partsDiscount['amount'] ?? 0) / 100;
                } else {
                    $partsDiscountAmount = $partsDiscount['amount'] ?? 0;
                }
            }

            // Compute grand total after discount


        @endphp

      @php
        $grandTotal = ($labourTotal ?? 0) + ($partsTotal ?? 0) - $labourDiscountAmount - $partsDiscountAmount;

        $convertNumberToWords = function ($number) use (&$convertNumberToWords) {
            $hyphen      = '-';
            $conjunction = ' and ';
            $negative    = 'negative ';
            $dictionary  = [
                0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
                6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven',
                12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen',
                17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty',
                30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
                80 => 'eighty', 90 => 'ninety'
            ];

            if (!is_numeric($number)) return false;
            if ($number < 0) return $negative . $convertNumberToWords(abs($number));

            $string = '';

            if ($number < 21) {
                $string = $dictionary[$number];
            } elseif ($number < 100) {
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) $string .= $hyphen . $dictionary[$units];
            } elseif ($number < 1000) {
                $hundreds  = (int) ($number / 100);
                $remainder = $number % 100;
                $string    = $dictionary[$hundreds] . ' hundred';
                if ($remainder) $string .= $conjunction . $convertNumberToWords($remainder);
            } else {
                $baseUnits = [10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand'];
                foreach ($baseUnits as $divisor => $label) {
                    if ($number >= $divisor) {
                        $units    = (int) ($number / $divisor);
                        $remainder = $number % $divisor;
                        $string   = $convertNumberToWords($units) . ' ' . $label;
                        if ($remainder) $string .= $conjunction . $convertNumberToWords($remainder);
                        break;
                    }
                }
            }

            return $string;
        };

        $amountInWords = ucwords($convertNumberToWords((int) $grandTotal)) . ' Rupees';
        @endphp



        <!-- Bank Details + Totals: table layout -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 12px; color: #000;">
            <tr>
                <!-- Bank Details -->
                <td class="footer-col"
                    style="width:50%; border: 1px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="display: block; margin-bottom: 1rem; text-transform: uppercase; font-size: 15px;">Bank
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 13px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 5px 0;">Bank Name :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->bank_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 5px 0;">Branch :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->branch ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 5px 0;">A/C No :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->ac_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 5px 0;">IFSC Code :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->ifsc_code ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>


                <!-- QR Code -->
                <td class="footer-col"
                    style="width: 25%; border: 1px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <table style="width: 100%; font-size: 12px; border-collapse: collapse; color: inherit;">
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                @if (isset($setting->qr_code) && file_exists(storage_path('app/public/' . $setting->qr_code)))
                                    @php
                                        $imageData = base64_encode(file_get_contents(storage_path('app/public/' . $setting->qr_code)));
                                        $mimeType = mime_content_type(storage_path('app/public/' . $setting->qr_code));
                                    @endphp
                                    <img src="data:{{ $mimeType }};base64,{{ $imageData }}"
                                        style="width:80px; height:80px;">
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>


                <!-- Totals -->
                <td class="footer-col"
                    style="width: 25%; border: 1px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <table style="width: 100%; font-size: 12px; border-collapse: collapse; color: inherit; font-size: 14px;">
                        <tr>
                            <td style="padding: 0;">Total Parts :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ number_format($partsTotal ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0;">Total Labour :</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">{{ number_format($labourTotal ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0;">Labour Discount:</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">
                                {{ $labourDiscountData['type'] ?? '' }} {{ number_format($labourDiscountAmount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0;">Parts Discount:</td>
                            <td style="text-align: right; padding: 0 0 8px 0;">
                                {{ $partsDiscountData['type'] ?? '' }} {{ number_format($partsDiscountAmount, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
            <tr>
                <td class="footer-col" style="width: 50%; border: 1px solid #dee2e6; padding: 8px 12px; vertical-align: top;">
                    <strong>{{ $amountInWords }} Only</strong>
                </td>
                <td class="footer-col"
                    style="width: 50%; border: 1px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top; background-color:#FF9F43; color:#fff;">
                    @php
                        $grandTotal = ($labourTotal ?? 0) + ($partsTotal ?? 0) - $labourDiscountAmount - $partsDiscountAmount;
                    @endphp

                    <strong>Grand Total : {{ number_format($grandTotal, 2) }}</strong>

                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
            <tr>
                <!-- Remarks -->
                <td class="footer-col" style="width: 50%; border: 1px solid #dee2e6; padding: 8px 12px; vertical-align: top;">
                    <strong>Remarks :</strong><br>
                    <span>{{ $jobCard->remark ?? 'N/A' }}</span>
                </td>

                <!-- Authorized Signatory -->
                <td class="footer-col"
                    style="width: 50%; border: 1px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top;">
                    <p style="margin: 0;">For, {{ $setting->name ?? ' Auto Care' }}</p>
                    <br><br><br>
                    <p style="margin: 0;">(Authorized Signatory)</p>
                </td>
            </tr>
        </table>
    </div>

@endsection