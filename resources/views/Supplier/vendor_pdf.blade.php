<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendors PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm 5mm;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background: white;
            color: #333;
        }

        .pdf-wrapper {
            margin-top: 2mm;
        }

        .card-body {
            width: 100%;
            padding: 4mm;
            box-sizing: border-box;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .table-bordered thead tr th {
            background-color: #ff9f43;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        h3, h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="pdf-wrapper">
        <div class="card-body">
            <!-- Header with Logo & Shop Info -->
            <table style="width:100%; margin-bottom: 12px; border-collapse: collapse;">
                <tr>
                    <td style="width: 120px; vertical-align: middle; border: none;">
                        @if (isset($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                            @php
                                $logoPath = storage_path('app/public/' . $settings->logo);
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            @endphp
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
                                style="max-height: 50px; max-width: 120px;">
                        @endif
                    </td>
                    <td style="vertical-align: middle; text-align: right; border: none;">
                        <h3 style="margin: 0; text-transform: uppercase; font-size: 15px; color: #ff9f43;">
                            {{ $settings->name ?? 'GARAGE ERP' }}
                        </h3>
                        <div style="font-size: 11px; color: #555; margin-top: 3px;">
                            @if (!empty($settings->address)) {{ $settings->address }}<br> @endif
                            @if (!empty($settings->phone)) Phone: {{ $settings->phone }} @endif
                            @if (!empty($settings->email)) | Email: {{ $settings->email }} @endif
                            @if (!empty($settings->gst_number)) | GST: {{ $settings->gst_number }} @endif
                        </div>
                    </td>
                </tr>
            </table>

            <hr style="height: 1px; background-color: #e0e0e0; border: none; margin: 0 0 10px 0;">

            <div class="text-center" style="margin-bottom: 10px;">
                <h4 style="text-transform: uppercase; font-size: 13px; letter-spacing: 1px; margin: 0; color: #2c3e50;">All Vendors List</h4>
                <span style="font-size: 10px; color: #777;">Generated on: {{ now()->format('d-M-Y h:i A') }}</span>
            </div>

            <table class="table-bordered">
                <thead>
                    <tr>
                        <th style="width: 5%;">Sr No</th>
                        <th style="width: 18%;">Vendor Name</th>
                        <th style="width: 18%;">Email</th>
                        <th style="width: 12%;">Phone</th>
                        <th style="width: 12%;">GST Number</th>
                        <th style="width: 10%;">Country</th>
                        <th style="width: 10%;">City</th>
                        <th style="width: 15%;">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vendors as $index => $vendor)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ $vendor->name ?? 'N/A' }}</strong></td>
                            <td>{{ $vendor->email ?? 'N/A' }}</td>
                            <td class="text-center">{{ $vendor->phone ?? 'N/A' }}</td>
                            <td class="text-center">{{ $vendor->gst_number ?? 'N/A' }}</td>
                            <td class="text-center">{{ optional($vendor->details)->country ?? 'N/A' }}</td>
                            <td class="text-center">{{ optional($vendor->details)->city ?? 'N/A' }}</td>
                            <td class="text-center">{{ $vendor->created_at ? \Carbon\Carbon::parse($vendor->created_at)->format('d-M-Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No vendors found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
