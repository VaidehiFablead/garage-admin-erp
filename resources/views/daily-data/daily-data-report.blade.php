<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Data Report</title>
    <style>
        @page {
            size: A4;
            margin: 1mm 1mm;
        }

        body {
            font-family: sans-serif;
            font-size: 8px;
        }

        .card-body {
            width: 95%;
            min-height: 95%;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid black;
            font-size: 12px;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #d8dadd;
        }

        ;

        th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 6px;
        }

        td {
            padding: 6px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0 0 10px 0;
        }

        .totals {
            margin-top: 20px;
        }

        .totals table {
            width: auto;
            margin-left: auto;
        }

        .totals td {
            font-weight: bold;
            padding: 5px 10px;
        }

        .card-body table {
            width: 100%;
            table-layout: fixed;
            /* makes cells shrink instead of overflowing */
            word-wrap: break-word;
            /* wrap long text */
        }
    </style>
</head>

<body>
    <div class="card-body">
        <table style="width: 100%; border: none; margin-bottom: 10px;">
            <tr>
                <!-- Company Logo -->
                
                <td style="width:25%; border:none; vertical-align:middle;">
                    @if (isset($companyData->logo) && file_exists(storage_path('app/public/' . $companyData->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $companyData->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp

                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" style="height:60px; width:auto;">
                    @endif
                </td>

                <!-- Company Info (Right-aligned) -->
                <td style="text-align: right; border: none; vertical-align: middle;">
                    <h1 style="margin: 0; font-size: 16px; text-transform: uppercase;">
                        {{ $companyData->name ?? 'Your Company Name' }}
                    </h1>
                    <p style="margin: 2px 0; font-size: 13px; text-transform: uppercase;">
                        {{ $companyData->address ?? '123 Main Street, Your City, Country' }}
                    </p>
                    <p style="margin: 2px 0; font-size: 13px;">
                        Phone: {{ $companyData->phone ?? '+91 98765 43210' }} |
                        Email: <span style="text-transform: none;">
                            {{ $companyData->email ?? 'info@company.com' }}
                        </span>
                    </p>
                </td>
            </tr>
        </table>


        <hr style="margin: 5px 0;">
        <h2>Daily Data Report</h2>
        <h2>Date - {{ $downloadDate }}</h2>

        {{-- Job Cards Table --}}
        <h3>Job Cards</h3>
        @if ($jobCards->count() > 0)
            <table style="font-size: 10px !important;">
                <thead>
                    <tr>
                        <th style="background-color:#FF9F43; color:#fff;">Date</th>
                        <th style="background-color:#FF9F43; color:#fff;">Name</th>
                        <th style="background-color:#FF9F43; color:#fff;">Mobile</th>
                        <th style="background-color:#FF9F43; color:#fff;">Bike</th>
                        <th style="background-color:#FF9F43; color:#fff;">Bike No.</th>
                        <th style="background-color:#FF9F43; color:#fff;">Work Amount</th>
                        <th style="background-color:#FF9F43; color:#fff;">Received</th>
                        <th style="background-color:#FF9F43; color:#fff;">Advance Paid</th>
                        <th style="background-color:#FF9F43; color:#fff;">Discount</th>
                        <th style="background-color:#FF9F43; color:#fff;">Technician</th>
                        <th style="background-color:#FF9F43; color:#fff;">Due/Received</th>
                        <th style="background-color:#FF9F43; color:#fff;">Start Date</th>
                        <th style="background-color:#FF9F43; color:#fff;">End Date</th>
                        <th style="background-color:#FF9F43; color:#fff;">Payment Mode</th>
                        <th style="background-color:#FF9F43; color:#fff;">Work Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobCards as $jobCard)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($jobCard->date)->format('d-m-Y') }}</td>

                            <td>{{ $jobCard->customer_name ?? '-' }}</td>
                            <td>{{ $jobCard->mobile ?? '-' }}</td>
                            <td>{{ $jobCard->autocarmodel->model_name ?? '-' }}</td>
                            <td>{{ $jobCard->vehicle_no ?? '-' }}</td>
                            <td>{{ number_format($jobCard->total_subtotal_amount, 2) }}</td>
                            <td>{{ number_format($jobCard->payments->sum('payment_amount'), 2) }}</td>
                            <td>{{ number_format($jobCard->extra_pay, 2) }}</td>
                            <td>{{ number_format($jobCard->discount, 2) }}</td>
                            <td>{{ $jobCard->technicianUser->name ?? '-' }}</td>
                            <td>
                                @if ($jobCard->payment_type === 'partial')
                                    Due
                                @else
                                    Received
                                @endif
                            </td>
                            <td>{{ $jobCard->start_date ?? '-' }}</td>
                            <td>{{ $jobCard->end_date ?? '-' }}</td>
                            <td>
                                @if ($jobCard->payment_method === 'cashonline')
                                    Cash + Online
                                @else
                                    {{ $jobCard->payment_method }}
                                @endif
                            </td>
                            <td>{{ $jobCard->status ? $jobCard->status : 'No Status' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center;">No job cards found for the selected date.</p>
        @endif

        {{-- Expenses Table --}}
        <h3>Expenses</h3>
        @if ($expenses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="background-color:#FF9F43; color:#fff;">Date</th>
                        <th style="background-color:#FF9F43; color:#fff;">Expense Name</th>
                        <th style="background-color:#FF9F43; color:#fff;">Description</th>
                        <th style="background-color:#FF9F43; color:#fff;">Payment Mode</th>
                        <th style="background-color:#FF9F43; color:#fff;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}</td>
                            <td>{{ $expense->expense_name }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ ucfirst($expense->payment_method) }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center;">No expenses found for the selected date.</p>
        @endif

        {{-- Totals --}}
        <div class="totals">
            <table>
                <tr>
                    <td>Total Discount:</td>
                    <td>{{ number_format($discountTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Cash:</td>
                    <td>{{ number_format($cashTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Online:</td>
                    <td>{{ number_format($onlineTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Expenses:</td>
                    <td>{{ number_format($expenseTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Amount(Cash - Expenses):</td>
                    <td>{{ number_format($combined, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Cash + Online Amount:</td>
                    <td>{{ number_format($cashTotal + $onlineTotal, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
