<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Ledger</title>
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

        /* REMOVE card-style border + shadow */
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

        /* Keep table borders only */
        .table-style {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .table-style th,
        .table-style td {
            border: 1px solid #dee2e6;
            /* only table has borders */
            padding: 6px 8px;
            text-align: center;
        }



        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-table img {
            height: 80px;
            width: auto;
        }

        .company-info {
            text-align: right;
            padding-left: 15px;
        }

        .company-info h3 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16px;
            color: #000;
        }

        .company-info small {
            font-size: 13px;
            line-height: 1.5;
            text-transform: uppercase;
        }

        hr {
            height: 2px;
            background-color: #d7cdcd;
            border: none;
            margin: 5px 0 15px;
        }

        /* INFO SECTION */
        .info-section {
            display: flex;
            justify-content: space-between;
            line-height: 1.4;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .report-info {
            text-align: right;
            font-weight: bold;
        }

        /* SECTION TITLE */
        h3.section-title {
            margin: 10px 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            color: #222;
        }

        /* TABLE STYLE
        table.table-style {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .table-style th,
        .table-style td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        } */

        .table-style th {
            background: #FF9F43;
            color: #fff;
            font-weight: 600;
        }

        .table-style tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* TOTAL BOX */
        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 6px;
        }

        .total-box table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .total-box td {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            font-weight: bold;
        }

        .total-label {
            background: #f7f7f7;
        }

        .total-amount {
            background: #f0f4ff;
            color: #222;
        }
    </style>
</head>

<body>
    <div class="pdf-wrapper">
        <div class="card-body">
            <!-- HEADER -->
            <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
                <tr>
                    <!-- Logo -->
                    <td style="width: 150px; vertical-align: middle;">
                        @if (isset($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                            @php
                                $logoPath = storage_path('app/public/' . $settings->logo);
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            @endphp
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
                                style="height: 60px; width: auto;">
                        @endif
                    </td>

                    <!-- Company Info -->
                    <td style="vertical-align: middle; text-align: right; padding-left: 15px;">
                        <h3 style="margin: 0; text-transform: uppercase; font-size: 16px; color: #000;">
                            {{ $settings->name ?? '' }}
                        </h3>
                        <small style="text-transform: uppercase; font-size: 14px;">
                            {{ $settings->address ?? '' }}<br>
                            Phone: {{ $settings->phone ?? '' }} |
                            Email: <span style="text-transform: none;">{{ $settings->email ?? '' }}</span>
                        </small>
                    </td>
                </tr>
            </table>

            <hr>

            <!-- INFO SECTION -->
            <div class="info-section">

                <div class="report-info">
                    Customer: {{ $customerName ? ucfirst($customerName) : 'N/A' }}<br>
                    Customer GST: {{ $customerGST ?? 'N/A' }}<br>
                    Date: {{ \Carbon\Carbon::now()->format('d M Y') }}<br>
                    Months:
                    {{ !empty($months) ? implode(', ', array_map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)), $months)) : '-' }}
                </div>
            </div>

            <!-- PAID PAYMENTS -->
            <h3 class="section-title">Paid Payments</h3>
            <table class="table-style">
                <thead>
                    <tr>
                        @if ($type === 'customer')
                            <th>Job Card</th>
                            <th>Total Amount</th>
                            <th>Discount Amount</th>
                            <th>Paid Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Type</th>
                            @if (!$subAdminId)
                                <th>Vehicle No.</th>
                            @endif
                        @elseif($type === 'vendor')
                            <th>Purchase No</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Vendor</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $totalPaid = 0; @endphp
                    @forelse($paidPayments as $payment)
                        <tr>
                            @if ($type === 'customer')
                                @php $totalPaid += $payment->payment_amount; @endphp
                                <td>{{ $payment->jobcard->job_card_number ?? ($payment->order->order_number ?? '-') }}
                                </td>
                                
                                <td>{{ number_format($payment->jobcard->total_subtotal_amount ?? ($payment->order->total_amount ?? 0), 2) }}
                                </td>
                                <td>{{ number_format($payment->discount_amount ?? 0, 2) }}
                                </td>
                                <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                <td>{{ $payment->payment_date ?? '-' }}</td>
                                <td>{{ $payment->payment_method ?? '-' }}</td>
                                <td>{{ $payment->jobcard->type ?? ($payment->order->type ?? '-') }}</td>
                                @if (!$subAdminId)
                                    <td>{{ $payment->jobcard->vehicle_no ?? '-' }}</td>
                                @endif
                            @elseif($type === 'vendor')
                               {{-- @php $totalPaid += $payment->total_paid_amount; @endphp
                                <td>{{ $payment->bill_no ?? '-' }}</td>
                                <td>{{ number_format($payment->payable_amount, 2) }}</td>
                                <td>{{ number_format($payment->total_paid_amount, 2) }}</td>
                                <td>{{ $payment->created_at ?? '-' }}</td>
                                <td>{{ $payment->payment_mode ?? 'Pending' }}</td>
                                <td>{{ $payment->vendor->name ?? '-' }}</td> --}}
                                @php $totalPaid += $payment->paid_amount; @endphp
                                <td>{{ $payment->invoice_number ?? '-' }}</td>
                                <td>{{ number_format($payment->total_amount, 2) }}</td>
                                <td>{{ number_format($payment->paid_amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}</td>
                                <td>Paid</td>
                                <td>{{ $payment->vendor->name ?? '-' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type === 'customer' ? ($subAdminId ? 6 : 7) : 6 }}"
                                style="text-align:center;">No paid payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="total-box">
                <table>
                    <tr>
                        <td class="total-label">Total Paid Amount</td>
                        <td class="total-amount">{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- PENDING PAYMENTS -->
            <h3 class="section-title">Pending Payments</h3>
            <table class="table-style">
                <thead>
                    <tr>
                        @if ($type === 'customer')
                            <th>Job Card</th>
                            <th>Total Amount</th>
                            <th>Remaining Amount</th>
                            <th>Type</th>
                            @if (!$subAdminId)
                                <th>Vehicle No.</th>
                            @endif
                        @elseif($type === 'vendor')
                            <th>Purchase No</th>
                            <th>Total Amount</th>
                            <th>Remaining Amount</th>
                            <th>Vendor</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $totalPending = 0; @endphp
                    @forelse($pendingPayments as $payment)
                        <tr>
                            @if ($type === 'customer')
                                @php $totalPending += $payment->remaining_amount; @endphp
                                <td>{{ $payment->job_card_number ?? ($payment->order_number ?? '-') }}</td>
                                <td>{{ number_format($payment->total_subtotal_amount ?? ($payment->total_amount ?? 0), 2) }}
                                </td>
                                <td>{{ number_format($payment->remaining_amount, 2) }}</td>
                                <td>{{ $payment->type ?? '-' }}</td>
                                @if (!$subAdminId)
                                    <td>{{ $payment->vehicle_no ?? '-' }}</td>
                                @endif
                            @elseif($type === 'vendor')
                                @php $totalPending += $payment->remaining_amount; @endphp
                                <td>{{ $payment->invoice_number ?? ($payment->bill_no ?? '-') }}</td>
                                <td>{{ number_format($payment->total_amount ?? ($payment->payable_amount ?? 0), 2) }}</td>
                                <td>{{ number_format($payment->remaining_amount, 2) }}</td>
                                <td>{{ $payment->vendor->name ?? '-' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type === 'customer' ? ($subAdminId ? 4 : 5) : 4 }}"
                                style="text-align:center;">No pending payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="total-box">
                <table>
                    <tr>
                        <td class="total-label">Total Pending Amount</td>
                        <td class="total-amount">{{ number_format($totalPending, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
