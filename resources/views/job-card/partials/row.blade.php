@php
    $labourDetails = json_decode($card->labour_details, true); // associative array
    $labourGst = json_decode($card->labour_gst, true); // array of taxes

    $totalLabour = 0;

    if ($labourDetails && is_array($labourDetails)) {
        foreach ($labourDetails as $labour) {
            $lineTotal = floatval($labour['price'] ?? 0);
            // Add GST if exists
            if ($labourGst && is_array($labourGst)) {
                foreach ($labourGst as $tax) {
                    $lineTotal += $lineTotal * (floatval($tax['rate'] ?? 0) / 100);
                }
            }
            $totalLabour += $lineTotal;
        }
    }
    $totalLabourFormatted = number_format($totalLabour, 2, '.', '');
@endphp

<tr class="main-row">
    <td>{{ $card->job_card_number ?? '-' }}</td>
    <td class="mobile-show toggle-details">
        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
    </td>
    <td class="mobile-hide">{{ \Carbon\Carbon::parse($card->date)->format('d-m-Y') }}</td>
    <td class="mobile-hide" data-customer-id="{{ $card->customer->id ?? '' }}">
        {{ isset($card->customer->name) ? ucwords($card->customer->name) : '-' }}
    </td>
    <td class="mobile-hide">{{ $card->vehicle_no ?? '-' }}</td>
    <td class="mobile-hide">{{ $totalLabourFormatted ?? '0.00' }}</td>
    <td class="mobile-hide">{{ $card->total_parts ?? '0.00' }}</td>
    <td class="mobile-hide">{{ $card->total_subtotal_amount ?? '0.00' }}</td>

    <td class="text-center mobile-hide">
        @if (app('hasPermission')(16, 'view'))
            @php
                $totalAmount = 0;
                $totalEmiMonths = 0;

                if ($card->paymentStore && $card->paymentStore->count()) {
                    foreach ($card->paymentStore as $payment) {
                        $amount = floatval($payment->payment_amount ?? 0);
                        $emiMonth = intval($payment->emi_month ?? 0);
                        $totalAmount += $amount;
                        $totalEmiMonths += $emiMonth;
                    }

                    $remainingAmount = floatval($card->total_subtotal_amount ?? 0) - $totalAmount;
                    $remainingEmiMonths = intval($card->emi_duration ?? 0) - $totalEmiMonths;
                } else {
                    $remainingAmount = floatval($card->total_subtotal_amount ?? 0);
                    $remainingEmiMonths = intval($card->emi_duration ?? 0);
                }
            @endphp

            @if ($card->type == 'quotation')
                <button class="btn btn-success btn-sm convert-to-sales-btn" data-id="{{ $card->id }}">
                    Convert to JobCard
                </button>
            @endif
            {{-- Edit --}}
            @if (app('hasPermission')(16, 'edit'))
                <a class="me-3" href="{{ route('job_card.edit', $card->id) }}" title="Edit">
                    <i class="fas fa-edit" style="font-size: 20px;"></i>
                </a>
            @endif

            {{-- Download PDF --}}
            <a class="me-3" href="{{ route('job_card.download_pdf', $card->id) }}" title="Download PDF">
                <i class="fas fa-download" style="font-size: 20px;"></i>
            </a>

            {{-- View --}}
            @if (app('hasPermission')(16, 'view'))
                <a class="me-3 mt-2" href="{{ route('job_card.view_pdf', $card->id) }}" title="View">
                    <i class="fas fa-eye" style="font-size: 20px;"></i>
                </a>
            @endif
        @endif

        @if (app('hasPermission')(16, 'delete'))
            <button class="delete-jobcard-btn mt-2" data-id="{{ $card->id }}"
                style="border: none; background: none; padding: 0;">
                <i class="fas fa-trash-alt" style="font-size: 20px; color: red;"></i>
            </button>
        @endif
    </td>
</tr>

<tr class="detail-row mobile-show">
    <td colspan="2">
        <div class="p-3 border-top">
            <div class="mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($card->date)->format('d-m-Y') }}</div>
            <div class="mb-2"><strong>Customer:</strong> {{ $card->customer->name ?? '-' }}</div>
            <div class="mb-2"><strong>Vehicle:</strong> {{ $card->vehicle_no ?? '-' }}</div>
            <div class="mb-2"><strong>Labour:</strong> ₹{{ $totalLabourFormatted }}</div>
            <div class="mb-2"><strong>Parts:</strong> ₹{{ $card->total_parts ?? '0.00' }}</div>
            <div class="mb-2"><strong>Grand Total:</strong> ₹{{ $card->total_subtotal_amount ?? '0.00' }}</div>
            <div class="mt-2">
                <strong>Actions:</strong>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @if ($card->type == 'quotation')
                        <button class="btn btn-success btn-sm convert-to-sales-btn" data-id="{{ $card->id }}">
                            Convert to JobCard
                        </button>
                    @endif
                    @if (app('hasPermission')(16, 'edit'))
                        <a class="btn btn-sm " href="{{ route('job_card.edit', $card->id) }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endif

                    <a class="btn btn-sm " href="{{ route('job_card.download_pdf', $card->id) }}" title="Download PDF">
                        <i class="fas fa-download"></i>
                    </a>

                    @if (app('hasPermission')(16, 'view'))
                        <a class="btn btn-sm" href="{{ route('job_card.view_pdf', $card->id) }}" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    @endif

                    @if (app('hasPermission')(16, 'delete'))
                        <button class="btn btn-sm  delete-jobcard-btn" data-id="{{ $card->id }}">
                            <i class="fas fa-trash-alt" style=" color: red;"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </td>
</tr>
