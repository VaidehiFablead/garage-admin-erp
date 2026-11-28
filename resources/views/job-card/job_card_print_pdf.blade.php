<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Job Card - {{ $jobCard->job_card_number ?? '' }}</title>
    <style>
        @page {
            size: A4;
            margin: 7mm 8mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
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
            padding: 5px 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .meta-bar {
            width: 100%;
            margin: 7px 0 8px 0;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .meta-bar td {
            padding: 4px 7px;
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            font-size: 10px;
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
            font-size: 10px;
            text-transform: uppercase;
            padding: 4px 8px;
            border: 1px solid #cbd5e1;
            margin-top: 7px;
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
            font-size: 10px;
        }

        .info-subtable td {
            padding: 2px 3px;
            border-bottom: 1px dotted #e2e8f0;
        }

        .info-subtable tr:last-child td {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #475569;
            width: 37%;
        }

        .work-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            margin-bottom: 6px;
        }

        .work-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .work-table td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .checkbox-box {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1px solid #334155;
            vertical-align: middle;
            margin-right: 2px;
        }

        .checklist-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-top: none;
            margin-bottom: 6px;
        }

        .checklist-table td {
            padding: 4px 7px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
        }

        .remarks-box {
            border: 1px solid #cbd5e1;
            border-top: none;
            padding: 5px 8px;
            min-height: 36px;
            font-size: 10px;
            margin-bottom: 7px;
        }

        .summary-grid {
            width: 100%;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .summary-grid > tbody > tr > td {
            padding: 5px 8px;
            vertical-align: top;
            font-size: 10px;
        }

        .signature-table {
            width: 100%;
            margin-top: 16px;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            padding-top: 24px;
            font-size: 9.5px;
            color: #334155;
        }

        .sig-line {
            border-top: 1px solid #475569;
            width: 80%;
            margin: 0 auto 3px auto;
        }
    </style>
</head>

<body>
    @php
        $currency = $setting->currency_symbol ?? 'Rs.';
        $isQuotation = strtolower($jobCard->type ?? '') === 'quotation';
        $formattedDate = $jobCard->date ? \Carbon\Carbon::parse($jobCard->date)->format('d-m-Y') : '--';
        $checkInTime = $jobCard->created_at ? \Carbon\Carbon::parse($jobCard->created_at)->timezone('Asia/Kolkata')->format('h:i A') : '--';
        $formattedDueDate = $jobCard->next_due_date ? \Carbon\Carbon::parse($jobCard->next_due_date)->format('d-m-Y') : '--';
        $gstEnabled = ((string) ($jobCard->gst ?? '')) === '1';

        $customerName = $jobCard->customer->name ?? ($jobCard->customer_name ?: '--');
        $customerGst = $jobCard->gst_no ?: ($jobCard->customer->gst_num ?? '--');
        $customerEmail = $jobCard->customer->email ?? null;

        $labourDiscount = json_decode($jobCard->labour_discount ?? '{}', true) ?: [];
        $partsDiscount = json_decode($jobCard->parts_discount ?? '{}', true) ?: [];
        $extraDiscount = (float) ($jobCard->discount ?? 0);

        $grandTotal = (float) ($jobCard->total_subtotal_amount ?? 0);
        $remainingAmount = (float) ($jobCard->remaining_amount ?? $grandTotal);
        $paidAmount = max($grandTotal - $extraDiscount - $remainingAmount, 0);
        $paymentModeLabel = $jobCard->payment_method ? strtoupper($jobCard->payment_method) : 'PENDING';
        $paymentTypeLabel = $jobCard->payment_type ? ucfirst($jobCard->payment_type) : ($remainingAmount <= 0 && $grandTotal > 0 ? 'Full' : 'Pending');
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
                        <img src="{{ $logoSrc }}" alt="Company Logo" style="max-height: 54px; max-width: 140px; width: auto;">
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
                    <div style="font-size: 9.5px; color: #475569;">
                        <strong>Job Card #:</strong> {{ $jobCard->job_card_number ?? '--' }}<br>
                        <strong>Bill Ref #:</strong> {{ $jobCard->bill_no ?? '--' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Job Card Key Meta --}}
        <table class="meta-bar">
            <tr>
                <td><strong>Check-In Date:</strong> {{ $formattedDate }} ({{ $checkInTime }})</td>
                <td><strong>Next Due Date:</strong> {{ $formattedDueDate }}</td>
                <td><strong>Category:</strong> {{ $category_name ?? ($jobCard->category ?: '--') }}</td>
                <td><strong>Technician:</strong> {{ $technician_name ?? '--' }}</td>
                <td><strong>Tax Mode:</strong> {{ $gstEnabled ? 'With GST' : 'Without GST' }}</td>
            </tr>
        </table>

        {{-- Customer & Vehicle Information --}}
        <div class="section-title">Customer &amp; Vehicle Details</div>
        <table class="info-grid">
            <tr>
                <td style="width: 50%; border-right: 1px solid #cbd5e1;">
                    <table class="info-subtable">
                        <tr>
                            <td class="label">Customer Name</td>
                            <td><strong>{{ $customerName }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Mobile No</td>
                            <td>{{ $jobCard->mobile ?: '--' }}</td>
                        </tr>
                        @if ($customerEmail)
                            <tr>
                                <td class="label">Email</td>
                                <td>{{ $customerEmail }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="label">Address</td>
                            <td>{{ $jobCard->address ?: '--' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Customer GST No</td>
                            <td>{{ $customerGst }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table class="info-subtable">
                        <tr>
                            <td class="label">Vehicle Reg No</td>
                            <td><strong>{{ $jobCard->vehicle_no ?: '--' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Model / Make</td>
                            <td>{{ $model_name ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Chassis No</td>
                            <td>{{ $jobCard->chassis_no ?: '--' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Engine No</td>
                            <td>{{ $jobCard->engine_no ?: '--' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Odometer (KM/Hr)</td>
                            <td>{{ $jobCard->km_hours ?? '--' }} &nbsp;|&nbsp; <strong>Next Due:</strong> {{ $jobCard->due_km_hours ?? '--' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Requested Services / Labour --}}
        <div class="section-title">Requested Services / Labour Work</div>
        <table class="work-table">
            <thead>
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th style="width: 46%;">Service / Labour Description</th>
                    <th style="width: 24%;">Technician Check</th>
                    <th style="width: 24%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($labourDetails as $labour)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $labour['name'] ?? '--' }}</td>
                        <td><span class="checkbox-box"></span> Done &nbsp;&nbsp; <span class="checkbox-box"></span> Pending</td>
                        <td>{{ $labour['remark'] ?? $labour['remarks'] ?? $labour['note'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color: #64748b;">No labour services listed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Parts & Consumables --}}
        <div class="section-title">Parts &amp; Consumables Required</div>
        <table class="work-table">
            <thead>
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th style="width: 42%;">Part Name</th>
                    <th style="width: 8%;" class="text-center">Qty</th>
                    <th style="width: 20%;">Issued / Fitted</th>
                    <th style="width: 24%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($partsDetails as $part)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $part['name'] ?? '--' }}</td>
                        <td class="text-center">{{ $part['qty'] ?? 1 }}</td>
                        <td><span class="checkbox-box"></span> Issued &nbsp; <span class="checkbox-box"></span> Fitted</td>
                        <td>{{ $part['remark'] ?? $part['remarks'] ?? $part['note'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #64748b;">No parts listed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Vehicle Inspection Checklist --}}
        <div class="section-title">Vehicle Inspection &amp; Inventory Checklist (At Check-In)</div>
        <table class="checklist-table">
            <tr>
                <td style="width: 36%;">
                    <strong>Fuel Level:</strong>
                    <span class="checkbox-box"></span> E
                    <span class="checkbox-box"></span> 1/4
                    <span class="checkbox-box"></span> 1/2
                    <span class="checkbox-box"></span> 3/4
                    <span class="checkbox-box"></span> F
                </td>
                <td style="width: 16%;"><span class="checkbox-box"></span> Tool Kit</td>
                <td style="width: 16%;"><span class="checkbox-box"></span> Spare Wheel</td>
                <td style="width: 16%;"><span class="checkbox-box"></span> Jack &amp; Rod</td>
                <td style="width: 16%;"><span class="checkbox-box"></span> RC / Docs</td>
            </tr>
            <tr>
                <td colspan="5" style="height: 20px; color: #64748b;">
                    <strong>Body Condition / Dents / Scratches / Valuables:</strong>
                </td>
            </tr>
        </table>

        {{-- Customer Complaints / Remarks --}}
        <div class="section-title">Customer Complaints / Workshop Remarks</div>
        <div class="remarks-box">
            @if (!empty($jobCard->remark))
                <div><strong>Remarks:</strong> {{ $jobCard->remark }}</div>
            @endif
            @if (!empty($jobCard->comment))
                <div><strong>Notes:</strong> {{ $jobCard->comment }}</div>
            @endif
            @if (empty($jobCard->remark) && empty($jobCard->comment))
                <span style="color: #94a3b8;">No remarks recorded.</span>
            @endif
        </div>

        <div class="bottom-section">
            {{-- Cost & Payment Summary --}}
        <table class="summary-grid">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: right;">
                    <div>
                        <strong>Total Labour:</strong> {{ $currency }} {{ number_format((float) ($jobCard->total_labour ?? 0), 2) }}
                        @if (!empty($labourDiscount['amount']) && (float) $labourDiscount['amount'] > 0)
                            <span style="color:#64748b;">(Disc: {{ $labourDiscount['amount'] }}{{ ($labourDiscount['type'] ?? '') === '%' ? '%' : ' ' . $currency }})</span>
                        @endif
                    </div>
                    <div>
                        <strong>Total Parts:</strong> {{ $currency }} {{ number_format((float) ($jobCard->total_parts ?? 0), 2) }}
                        @if (!empty($partsDiscount['amount']) && (float) $partsDiscount['amount'] > 0)
                            <span style="color:#64748b;">(Disc: {{ $partsDiscount['amount'] }}{{ ($partsDiscount['type'] ?? '') === '%' ? '%' : ' ' . $currency }})</span>
                        @endif
                    </div>
                    @if ($extraDiscount > 0)
                        <div><strong>Additional Discount:</strong> - {{ $currency }} {{ number_format($extraDiscount, 2) }}</div>
                    @endif
                    <div style="font-size: 11.5px; margin-top: 3px; border-top: 1px solid #cbd5e1; padding-top: 3px;">
                        <strong>Estimated / Grand Total:</strong> {{ $currency }} {{ number_format($grandTotal, 2) }}
                    </div>
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
                    Technician Signature
                </td>
                <td>
                    <div class="sig-line"></div>
                    Authorized Signatory
                </td>
            </tr>
        </table>
        </div>
    </div>
</body>

</html>
