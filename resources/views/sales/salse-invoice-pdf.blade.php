<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        @page {
            size: A4;
            margin: 1mm 1mm;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            /* Set base font size here */
            margin: 0;
            padding: 0;
            background: white;
        }

        .pdf-wrapper {
            margin-top: 3mm;
            /* ensures top border is visible */
        }

        .card-body {
            width: 95%;
            min-height: 95%;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid black;
            font-size: 12px;
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
            padding: 4px 8px;
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

        .footer-section {
            width: 95%;
            page-break-inside: avoid;
        }

        .footer-fixed {
            position: fixed;
            bottom: 5%;
            left: 20px;
            right: 0px;
        }
    </style>
</head>

<body>

    <div class="card-body">
        <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 150px; vertical-align: top;">
                    @if (isset($setting->logo) && file_exists(storage_path('app/public/' . $setting->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $setting->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
                            style="height: 100px; width: auto;"> {{-- adjust height as needed --}}
                    @endif
                </td>
                <td style="vertical-align: middle; padding-left: 15px; text-align: right;">
                    <h3 style="margin: 0; text-transform: uppercase;">{{ $setting->name ?? '' }}</h3>
                    <small style="text-transform: uppercase;">
                        {{ $setting->address ?? '' }}<br>
                        Phone: {{ $setting->phone ?? '' }} |
                        Email: <span style="text-transform: none;">{{ $setting->email ?? '' }}</span>
                    </small>
                </td>
            </tr>
        </table>



        <hr style="height: 2px; background-color: #d7cdcd; border: none; margin-top: 0; margin-bottom: 20px;">


        <div class="text-center mb-1">
            <!-- <h3 class="mb-3" style="text-transform: uppercase;">Bill of Supply</h3> -->
            <div style="margin-bottom: 10px; width: 100%; text-align: center;">
                <div style="display: inline-block; width: 49%; text-align: left;">
                    <strong>INVOICE NO : {{ $sales->order_number ?? '-' }}</strong>
                </div>
                <div style="display: inline-block; width: 49%; text-align: right;">
                    <strong>GST NO : {{ $setting->gst_num ?? ' -- ' }}</strong>
                </div>
            </div>
        </div>

        <table style="width:100%; border-collapse: collapse; font-size: 12px; margin-bottom: 10px;">
            <tr>
                <td
                    style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem;">Customer
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 12px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 8px 0;">Name :</td>
                            <td style="text-align: right; padding: 0;">
                                {{ $customer['name'] ?? 'walk-in-customer' }}
                            </td>
                        </tr>
                        @if(!empty($customer['phone']))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0;">{{ $customer['phone'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($sales->car_model))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Model :</td>
                                <td style="text-align: right; padding: 0;">{{ $sales->carModel?->model_name }}</td>
                            </tr>
                        @endif
                        @if(!empty($sales->vehicle_no))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Vehicle No. :</td>
                                <td style="text-align: right; padding: 0;">{{ $sales->vehicle_no }}</td>
                            </tr>
                        @endif
                        @if(!empty($customer['email']))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Email :</td>
                                <td style="text-align: right; padding: 0;">{{ $customer['email'] }}</td>
                            </tr>
                        @endif
                        @if(!empty($customer['address']))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Address :</td>
                                <td style="text-align: right; padding: 0;">{{ $customer['address'] }}</td>
                            </tr>
                        @endif
                    </table>

                    <!-- Half vertical line -->
                    <div style="position: absolute; right: 0; top: 2%; height: 15%; border-right: 1px solid #e63509;">
                    </div>
                </td>

                <!-- Vehicle Details -->
                <td
                    style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem;">Company
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 12px; color: inherit;">
                        @if(!empty($setting->name))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Name :</td>
                                <td style="text-align: right; padding: 0;">{{ $setting->name }}</td>
                            </tr>
                        @endif

                        @if(!empty($setting->email))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Email :</td>
                                <td style="text-align: right; padding: 0;">{{ $setting->email }}</td>
                            </tr>
                        @endif

                        @if(!empty($setting->phone))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0;">{{ $setting->phone }}</td>
                            </tr>
                        @endif

                        @if(!empty($setting->address))
                            <tr>
                                <td style="padding: 0 0 8px 0;">Address :</td>
                                <td style="text-align: right; padding: 0;">{{ $setting->address }}</td>
                            </tr>
                        @endif

                        @if(!empty($setting->gst_num))
                            <tr>
                                <td style="padding: 0 0 8px 0;">GST :</td>
                                <td style="text-align: right; padding: 0;">{{ $setting->gst_num }}</td>
                            </tr>
                        @endif
                    </table>

                    <div style="position: absolute; right: 0; top: 2%; height: 15%; border-right: 1px solid #e63509;">
                    </div>
                </td>


                <!-- Invoice Details -->
                <td
                    style="width:34%; border: 0px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 1rem;">Order
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 12px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 8px 0;">Order Number :</td>
                            <td style="text-align: right; padding: 0;">{{ $sales->order_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Order Date :</td>
                            <td style="text-align: right; padding: 0;">
                                {{ !empty($sales->created_at) ? date('d M Y, h:i a', strtotime($sales->created_at)) : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Payment Status :</td>
                            <td style="text-align: right; padding: 0;">{{ ucfirst($sales->payment_status ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 8px 0;">Payment Method :</td>
                            <td style="text-align: right; padding: 0;">{{ ucfirst($sales->payment_method ?? '-') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <div class="text-center">
            <h4 style="text-transform: uppercase;">Product</h4>
        </div>

        <table class="table-bordered"
            style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 10px 0;">
            <thead>
                <tr style="background-color:#bc1e2e; color:#fff;">
                    <th style="width:10%; padding: 8px; text-align:center;">Sr No</th>
                    <th style="padding: 8px; text-align:left;">Product Name</th>
                    <th style="padding: 8px; text-align:right;">Price</th>
                    <th style="width:15%; padding: 8px; text-align:center;">QTY</th>
                    <th style="width:20%; padding: 8px; text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderItems as $item)
                    <tr>
                        <td style="text-align:center; padding:8px;">{{ $loop->iteration }}</td>
                        <td style="padding:8px; text-align:left;">
                            @php
                                $images = json_decode($item->product->images ?? '[]');
                                $firstImage = !empty($images) ? $images[0] : null;
                                $base64 = null;

                                if ($firstImage) {
                                    $imagePath = storage_path('app/public/' . $firstImage);
                                } else {
                                    $imagePath = public_path('admin/assets/img/product/product1.jpg');
                                }

                                if (file_exists($imagePath)) {
                                    $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                                    $data = file_get_contents($imagePath);
                                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                }
                            @endphp

                            <table style="border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 0; vertical-align: middle;">
                                        <img src="{{ $base64 }}" alt="img"
                                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 5px;">
                                    </td>
                                    <td style="padding: 0 0 0 5px; vertical-align: middle; white-space: nowrap;">
                                        {{ $item->product->name ?? 'Product' }}
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td style="padding:8px; text-align:right;">
                            @if($setting->currency_position === 'right')
                                {{ $item->price }}{{ $setting->currency_symbol }}
                            @else
                                {{ $setting->currency_symbol }}{{ $item->price }}
                            @endif
                        </td>

                        <td style="padding:8px; text-align:center;">
                            {{ $item->quantity }}
                        </td>

                        <td style="padding:8px; text-align:right;">
                            @if($setting->currency_position === 'right')
                                {{ $item->total_amount }}{{ $setting->currency_symbol }}
                            @else
                                {{ $setting->currency_symbol }}{{ $item->total_amount }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:8px;">No product data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>


        <table style="width: 100%; margin: 20px 0; font-size: 12px; color: #333; border-collapse: collapse;">
            <tr>
                <td style="text-align: right;">

                    @php
                        // Get subtotal from order_items
                        $subtotal = $sales->order_items->sum('total_amount');
                        $subtotal1 = $sales->order_items->sum('total_amount');

                        $discount = $sales->discount ?? 0;

                        // Convert percentage into price
                        // $discount = ($subtotal * $discountPercent) / 100;
                        $subtotal = $subtotal - $discount;

                        // Prepare values
                        $totalTaxAmount = 0;
                        $taxDetails = [];

                        if ($sales->gst_option == 'with') {

                            foreach ($taxDetails1 as $tax) {
                                $taxAmount = ($subtotal * $tax['rate']) / 100;
                                $totalTaxAmount += $taxAmount;

                                $taxDetails[] = [
                                    'name' => $tax['name'],
                                    'rate' => $tax['rate'],
                                    'formatted_amount' => number_format($taxAmount, 2)
                                ];
                            }
                        }

                        // Final total = subtotal + tax (only if with GST)
                        $finalTotal = $subtotal + $totalTaxAmount;
                    @endphp

                    <table
                        style="width: 300px; margin-left: auto; border-collapse: collapse; font-size: 12px; color: #333;">
                        <tr>
                            <td
                                style="padding: 6px 12px; text-align: left; color:#fff; background-color: #bc1e2e; border: 1px solid #e0e0e0; font-weight:bold;">
                                <strong>Subtotal</strong>
                            </td>
                            <td style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0;">
                                {{ number_format($subtotal1, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="padding: 6px 12px; text-align: left; color:#fff; background-color: #bc1e2e; border: 1px solid #e0e0e0; font-weight:bold;">
                                <strong>Discount</strong>
                            </td>
                            <td style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0;">
                                {{ number_format($discount, 2) }}
                            </td>
                        </tr>

                        {{-- Show tax only if GST option is "with" --}}
                        @if($sales->gst_option == 'with' && !empty($taxDetails))
                            @foreach($taxDetails as $index => $tax)
                                <tr>
                                    <td
                                        style="padding: 6px 12px; text-align: left; color:#fff; background-color: #bc1e2e; border: 1px solid #e0e0e0; font-weight:bold;">
                                        @php
                                            // Fallback order if name is missing
                                            $fallbackNames = ['CGST', 'SGST'];
                                            $taxName = $tax['name'] ?? ($fallbackNames[$index] ?? 'Tax');
                                        @endphp
                                        <strong>{{ $taxName }} ({{ $tax['rate'] ?? 0 }}%)</strong>
                                    </td>
                                    <td style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0;">
                                        {{ $tax['formatted_amount'] ?? '₹0.00' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif



                        <tr>
                            <td
                                style="padding: 6px 12px; text-align: left; color:#fff; background-color: #bc1e2e; border: 1px solid #e0e0e0; font-weight:bold;">
                                <strong>Total</strong>
                                @if($sales->gst_option === 'with')
                                    <span style="font-size: 10px; color: #eee;">(GST Inclusive)</span>
                                @endif
                            </td>
                            <td
                                style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0; font-weight:bold;">
                                {{ number_format($finalTotal, 2) }}
                            </td>
                        </tr>
                    </table>


                </td>
            </tr>
        </table>
@php
    $grandTotal = $finalTotal;

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
            return false;
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

    $amountInWords = ucwords(convertNumberToWords((int) $grandTotal)) . ' Rupees';
@endphp
@php
            $itemCount = count($orderItems);
        @endphp

        @if ($itemCount <= 4)
            {{-- Footer immediately after table --}}
            <div class="footer-section">
                {{-- Yaha tera pura footer ka HTML (Bank + QR + Totals + Remarks + Signature) --}}
            </div>
        @else
            {{-- Footer on a new page and stuck at bottom --}}
            <div style="page-break-before: always;"></div>
            <div class="footer-section footer-fixed">
                {{-- Yaha bhi tera pura footer ka HTML --}}
            </div>
        @endif
        <div class="footer-section footer-fixed">
            <!-- Bank Details + Totals: table layout -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 30px; font-size: 12px; color: #000;">
                <tr>
                    <!-- Bank Details -->
                    <td
                        style="width:50%; border: 1px solid #e63509; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                        <strong style="display: block; margin-bottom: 1rem; text-transform: uppercase;">Bank
                            Details:</strong>
                        <table style="width:100%; border-collapse: collapse; font-size: 12px; color: inherit;">
                            <tr>
                                <td style="padding: 0 0 5px 0;">Bank Name :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">
                                    {{ $setting->bank_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 5px 0;">Branch :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->branch ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 5px 0;">A/C No :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">{{ $setting->ac_no ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 5px 0;">IFSC Code :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">
                                    {{ $setting->ifsc_code ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                    </td>


                    <!-- QR Code -->
                    <td
                        style="width: 25%; border: 1px solid #e63509; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
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
                    <td
                        style="width: 25%; border: 1px solid #e63509; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                        <table style="width: 100%; font-size: 12px; border-collapse: collapse; color: inherit;">
                            <tr>
                                <td style="padding: 0;">Total Amount :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">
                                    {{ number_format($finalTotal ?? 0, 2) }}
                                </td>
                            </tr>
                            <!-- <tr>
                                <td style="padding: 0;">Discount :</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">
                                    {{ number_format($discount ?? 0, 2) }}
                                </td>
                            </tr> -->
                            <tr>
                                <td style="padding: 0;">Total Pending:</td>
                                <td style="text-align: right; padding: 0 0 8px 0;">

                                    {{ number_format($sales->remaining_amount ?? 0, 2) }}
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
                <tr>
                    <td style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top;">
                        <strong>{{ $amountInWords }} Only</strong>
                    </td>
                    <td
                        style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top; background-color:#bc1e2e; color:#fff;">
                        @php
                            $grandTotal = $finalTotal;
                        @endphp

                        <strong>Grand Total : {{ number_format($grandTotal, 2) }}</strong>

                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
                <tr>
                    <!-- Remarks -->
                    <td style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top;">
                        <strong>Remarks :</strong><br>
                        <span>{{ 'N/A' }}</span>
                    </td>

                    <!-- Authorized Signatory -->
                    <td
                        style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top;">
                        <p style="margin: 0;">For, {{ $setting->name ?? ' Auto Care' }}</p>
                        <br><br><br>
                        <p style="margin: 0;">(Authorized Signatory)</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>