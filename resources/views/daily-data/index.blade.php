@extends('layout.app')

@section('title', 'All Daily Data')

@section('content')
    <style>
        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 16px;
        }

        .table-scroll-top div {
            height: 1px;
        }

        .accordion-arrow {
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .accordion-arrow.open {
            transform: rotate(90deg);
            /* Makes it point down */
        }

        tr[aria-expanded="true"] .accordion-arrow {
            transform: rotate(90deg);
        }

        /* Add spacing between nested tables */
        .collapse>td>div>table {
            margin-top: 1rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Give nested tables a subtle background and border */
        .collapse>td>div>table.table-bordered {
            background-color: #fafafa;
            border: 1px solid #dee2e6;
        }

        /* Different background for summary table */
        .collapse>td>div>table:last-of-type {
            background-color: #f1f3f5;
        }

        /* Header styles for clarity */
        .collapse>td>div>table thead th {
            background-color: #1b2850;
            color: white;
            font-weight: 600;
        }

        /* Extra padding around the div container */
        .collapse>td>div {
            padding: 1rem;
        }

        /* Optional: add rounded corners */
        .collapse>td>div>table {
            border-radius: 4px;
            overflow: hidden;
        }

        /* Space between the modal trigger button and other actions */
        .table td .btn {
            margin-right: 0.25rem;
        }

        @media (min-width: 769px) {
            .table td.action-cell {
                text-align: right;
                white-space: nowrap;
            }

            .table td.action-cell .btn {
                margin-left: 0.25rem;
                margin-right: 0;
            }
        }

        /* Smaller font for expense table */
        .collapse>td>div>table:nth-of-type(2) td {
            font-size: 0.9rem;
        }

        /* Add a subtle shadow to separate the nested content visually */
        .collapse>td>div {
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.05);
        }

        .capitalize td {
            text-transform: capitalize;
        }

        .mobile-show {
            display: none;
        }

        /* ── Search Daily Data table: mobile collapse ── */
        .jc-toggle-btn {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.2s;
            line-height: 1;
        }

        .jc-toggle-btn.minus {
            background: #dc3545;
        }

        .jc-toggle-btn:hover {
            background: #e68a35;
        }

        .jc-toggle-btn.minus:hover {
            background: #c82333;
        }

        .jc-detail-row {
            display: none;
        }

        .jc-detail-row.show {
            display: table-row;
        }

        .jc-detail-content {
            padding: 12px 16px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .jc-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
        }

        .jc-detail-item:last-child {
            border-bottom: none;
        }

        .jc-detail-label {
            font-weight: 600;
            color: #595b5d;
        }

        .jc-detail-value {
            color: #1b2850;
            text-align: right;
        }

        /* Mobile: hide all columns except Customer (1st) and Details (2nd) */
        @media (max-width: 768px) {

            #jobcards-table th:nth-child(n+3),
            #jobcards-table td:nth-child(n+3) {
                display: none !important;
            }

            #jobcards-table th:nth-child(2),
            #jobcards-table td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                width: 56px;
            }

            #jobcards-table {
                width: 100% !important;
                table-layout: fixed;
            }

            #jobcards-table th:nth-child(1),
            #jobcards-table td:nth-child(1) {
                width: calc(100% - 56px);
                white-space: normal;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            #jobcards-table .jc-detail-row>td {
                display: table-cell !important;
                width: 100% !important;
            }

            #jobcards-table .jc-detail-row .jc-detail-content {
                width: 100%;
            }
        }

        /* Desktop: hide the Details toggle column */
        @media (min-width: 769px) {

            #jobcards-table th:nth-child(2),
            #jobcards-table td:nth-child(2) {
                display: none !important;
            }

            .jc-detail-row {
                display: none !important;
            }
        }

        .mobile-toggle-column {
            display: none;
        }

        .toggle-details {
            cursor: pointer;
            color: #ff9f43;
            font-size: 1.1rem;
            vertical-align: middle;
            text-align: center;
            width: 30px;
            border: none;
            background: transparent;
            line-height: 1;
        }

        .toggle-details.active {
            color: #dc3545;
        }

        .mobile-section-toggle {
            display: none;
        }

        .mobile-section-toggle-btn {
            cursor: pointer;
            color: #ff9f43;
            font-size: 1.1rem;
            line-height: 1;
            border: none;
            background: transparent;
            width: 30px;
            text-align: center;
        }

        .mobile-section-toggle-btn.active {
            color: #dc3545;
        }

        #filter-name {
            max-width: 200px;
            font-size: 0.85rem;
        }

        tfoot tr {
            border-top: 2px solid #000;
            /* strong top border */
            font-weight: bold;
            /* bold text */
            background-color: #f8f9fa;
            /* light background for brightness */
        }

        tfoot th {
            border: 1px solid #000;
            /* border around each cell */
            padding: 8px;
            /* some spacing */
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .page-header .page-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-right: auto;
            min-width: 0;
            flex: 1 1 auto;
        }

        .page-header .page-title h4 {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .page-header .page-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-left: auto;
            justify-content: flex-end;
        }

        .page-header .page-btn .btn {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .page-header .page-btn .btn-added {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .page-header .page-btn .btn-added img {
            flex-shrink: 0;
            margin-right: 0;
        }

        /* Tablet view: keep all header buttons in one row */
        @media (min-width: 769px) and (max-width: 1024px) {
            .page-header .page-btn {
                flex-wrap: nowrap !important;
                overflow: visible;
            }

            .page-header .page-btn .header-action-btn {
                flex: 0 0 auto;
                white-space: nowrap;
                font-size: 0.78rem;
                padding: 0.25rem 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-hide {
                display: none !important;
            }

            .mobile-show {
                display: table-cell !important;
            }

            .mobile-toggle-column {
                display: table-cell !important;
                width: 56px;
                min-width: 56px;
                max-width: 56px;
                text-align: center;
            }

            .table-responsive {
                border: none;
            }

            table.table {
                min-width: 100% !important;
            }

            .group-row .toggle-cell {
                text-align: center;
                vertical-align: middle;
            }

            .group-row .data-portion .accordion-arrow {
                display: none;
            }

            .page-header {
                align-items: flex-start;
            }

            .page-header .page-title {
                flex-wrap: nowrap;
            }

            .page-header .page-title h4 {
                margin-right: auto;
            }

            .page-header .page-btn {
                width: 100%;
                flex-wrap: wrap;
                overflow: visible;
                margin-left: 0;
                justify-content: flex-start;
            }

            .page-header .page-btn .header-action-btn {
                flex: 0 0 calc(50% - 0.25rem);
                max-width: calc(50% - 0.25rem);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 34px;
                white-space: nowrap;
                text-align: center;
                line-height: 1;
                font-size: 0.75rem;
            }

            .d-flex.flex-wrap.gap-3 {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .d-flex.flex-wrap.align-items-center.gap-3 {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex.flex-wrap.align-items-center.gap-3 span {
                width: 100%;
            }

            .mobile-section-toggle {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 0.5rem;
                gap: 0.5rem;
            }

            .mobile-section-toggle .mobile-section-title,
            .mobile-section-toggle h5 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 700;
            }

            .mobile-section-body.mobile-collapsed {
                display: none;
            }

            /* Ensure tables inside mobile sections wrap and do not force horizontal scroll */
            .mobile-section-body table {
                table-layout: auto;
                width: 100%;
                overflow: visible;
            }

            .mobile-section-body table th,
            .mobile-section-body table td {
                white-space: normal;
                word-break: break-word;
                padding: .35rem .5rem;
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Daily Data</h4>
                <!-- <h6>Manage your purchases</h6> -->
            </div>
            <div class="page-btn d-flex flex-wrap gap-2">
                @if (app('hasPermission')(24, 'add'))
                    <a href="{{ route('daily_data.add-daily-data') }}" class="btn btn-sm btn-added header-action-btn">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">
                        New Daily Data
                    </a>
                @endif

                <!-- Top Performers Button -->
                <button id="top-technicians-btn" class="btn btn-primary btn-sm header-action-btn">
                    Top 3 Performers
                </button>

                <!-- Modal -->
                <div class="modal fade" id="topTechniciansModal" tabindex="-1" aria-labelledby="topTechniciansLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg rounded-3 border-0">

                            <!-- Header -->
                            <div class="modal-header bg-light border-bottom-0 py-2 px-3 flex-wrap">
                                <h6 class="modal-title fw-bold me-auto">🏆 Top 3 Performers (Staff)</h6>
                                <div class="d-flex align-items-center mt-2 mt-md-0">
                                    <input type="month" id="filter-month123" class="form-control form-control-sm"
                                        style="width:130px;">
                                    <button type="button" class="btn btn-primary btn-sm ms-2"
                                        data-bs-dismiss="modal">Back</button>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="modal-body p-3">
                                <div id="technicians-result">
                                    <p class="text-muted small">Loading...</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Expense Button -->
                @if (app('hasPermission')(5, 'view'))
                    <a href="{{ route('expense.list') }}" class="btn btn-sm btn-added header-action-btn">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/expense1.svg' }}" alt="img">
                        &nbsp;Expenses
                    </a>
                @endif


            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="all-records-tab" href="#">All Records</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="search-daily-tab" href="#">Search Daily Data</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-records-content">
                <div class="card">
                    <div class="table-top mt-3">
                        <div class="row g-2 align-items-end flex-lg-nowrap mx-3 filter-group">
                            @php
                                $selectedMonth = request('month');
                                $selectedYear = request('year');
                                $selectedDate = request('date');
                            @endphp

                            <div class="col-md-6 col-4">
                                <label for="filter-month" class="form-label mb-1">Month</label>
                                <select id="filter-month" class="form-control form-control-sm">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-6 col-4">
                                <label for="filter-year" class="form-label mb-1">Year</label>
                                <select id="filter-year" class="form-control form-control-sm">
                                    <option value="">All Years</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 col-4">
                                <label for="filter-date" class="form-label mb-1">Date</label>
                                <input type="date" name="filter-date" id="filter-date"
                                    class="form-control form-control-sm" value="{{ $selectedDate }}">
                            </div>
                        </div>
                        @php
                            $user = Auth::user();
                            $grandTotalProfit = 0;
                            $grandTotalDiscount = 0;
                            $grandTotalExpense = 0;
                            $grandTotalReceived = 0;
                            $grandTotalPending = 0;
                            $grandTotalEarned = 0;

                            $summaryColspan = 4;
                            if (in_array(auth()->user()->role, ['admin', 'sub-admin'])) {
                                $summaryColspan++;
                            }
                            if ($user->role != 'Staff') {
                                $summaryColspan += 3;
                            }
                            $summaryColspan++;
                        @endphp

                    </div>


                    <div class="table-scroll-top">
                        <div></div>
                    </div>

                    <div class="table-responsive mt-3" style="overflow-x: auto;">
                        <table class="table" id="order-table" style="max-width: 2000px;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="mobile-show text-center">Details</th>
                                    <th class="mobile-hide">Work Amount</th>
                                    @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                        <th class="mobile-hide">Expense Amount</th>
                                    @endif
                                    @if ($user->role != 'Staff')
                                        <th class="mobile-hide">Received Amount</th>
                                        <th class="mobile-hide">Discount Amount</th>
                                    @endif
                                    <th class="mobile-hide">Pending Amount</th>
                                    @if ($user->role != 'Staff')
                                        <th class="mobile-hide">Earned Amount</th>
                                    @endif
                                    <th class="text-center mobile-hide">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($jobCards) > 0)


                                    @foreach ($jobCards as $date => $cards)
                                        @php

                                            $subAdminId = session('selectedSubAdminId') ?? $user->id;

                                            $totalProfit = $cards->sum('total_subtotal_amount');
                                            $totalDiscount = $cards->sum('discount');

                                            // Convert '08-08-2025' → '2025-08-08' for DB
                                            $dbDate = \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

                                            // Fetch total expense for this date from expenses table
                                            $totalExpense = \App\Models\Expense::whereDate('expense_date', $dbDate)
                                                ->where('branch_id', $subAdminId)
                                                ->sum('amount');

                                            $totalReceived = $cards->sum(
                                                fn($card) => $card->payments->sum('payment_amount'),
                                            );
                                            $totalPending = $totalProfit - $totalReceived - $totalDiscount;
                                            $totalEarned = $totalReceived - $totalExpense;
                                            $dateSlug = \Illuminate\Support\Str::slug($date);
                                        @endphp
                                        {{-- Parent Row --}}
                                        <tr class="group-row" data-collapse-id="{{ $dateSlug }}"
                                            style="cursor: pointer;" data-total-profit="{{ $totalProfit }}"
                                            data-total-expense="{{ $totalExpense }}"
                                            data-total-received="{{ $totalReceived }}"
                                            data-total-discount="{{ $totalDiscount }}"
                                            data-total-pending="{{ $totalPending }}"
                                            data-total-earned="{{ $totalEarned }}"
                                            onclick="handleRowClick(event, '{{ $dateSlug }}', this)">
                                            <td class="data-portion">
                                                <span class="accordion-arrow me-2">&#9660;</span>
                                                {{ $date }}
                                            </td>
                                            <td class="toggle-cell mobile-show">
                                                <button type="button" class="toggle-details" aria-label="Toggle details"
                                                    onclick="handleRowClick(event, '{{ $dateSlug }}', this.closest('tr'))"><i
                                                        class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                            </td>
                                            <td class="data-portion mobile-hide">{{ number_format($totalProfit, 2) }}</td>
                                            @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                                <td class="data-portion mobile-hide">{{ number_format($totalExpense, 2) }}
                                                </td>
                                            @endif
                                            @if ($user->role != 'Staff')
                                                <td class="data-portion mobile-hide">
                                                    {{ number_format($totalReceived, 2) }}</td>
                                                <td class="data-portion mobile-hide">
                                                    {{ number_format($totalDiscount, 2) }}</td>
                                            @endif
                                            <td class="data-portion mobile-hide">{{ number_format($totalPending, 2) }}
                                            </td>
                                            @if ($user->role != 'Staff')
                                                <td class="data-portion mobile-hide">{{ number_format($totalEarned, 2) }}
                                                </td>
                                            @endif
                                            <td class="text-end align-middle action-cell mobile-hide">
                                                @if ($date === \Carbon\Carbon::now()->format('d-m-Y'))
                                                    <!-- <a href="{{ route('daily_data.add-daily-data') }}" class="btn btn-sm btn-warning"
                                                            style="color: white; background-color: #ff9f43;">
                                                            <i class="fa fa-plus" aria-hidden="true"></i> Add New Daily Data
                                                        </a> -->
                                                @endif
                                                {{-- Download CSV Button --}}
                                                @if ($user->role != 'Staff')
                                                    <a href="{{ route('jobcards.export', ['date' => $date]) }}"
                                                        class="btn btn-sm btn-success" style="color: white;">
                                                        <i class="fas fa-file-excel"></i> Export Excel
                                                    </a>
                                                    <a href="{{ route('daily-data.downloadReport', ['date' => $date]) }}"
                                                        class="btn btn-sm btn-danger" style="color: white;">
                                                        <i class="fas fa-file-pdf"></i> Download PDF
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- Collapsible Detail Row --}}
                                        <tr class="collapse" id="collapse-{{ $dateSlug }}">
                                            <td colspan="{{ $summaryColspan }}" class="p-0">
                                                <div class="p-2">
                                                    <div class="mobile-section-wrap mt-1">
                                                        <div class="mobile-section-toggle">
                                                            <span class="mobile-section-title">Job Card Details</span>
                                                            <button type="button" class="mobile-section-toggle-btn"
                                                                data-mobile-toggle-target="jobcards-{{ $dateSlug }}"
                                                                aria-expanded="false"><i class="fa fa-plus-circle"
                                                                    aria-hidden="true"></i></button>
                                                        </div>
                                                        <div id="jobcards-{{ $dateSlug }}"
                                                            class="mobile-section-body mobile-collapsed">
                                                            <div class="table-responsive"
                                                                style="max-height: 400px; overflow-y: auto;">
                                                                {{-- Job Cards Table --}}
                                                                <table class="table table-bordered mb-0 capitalize">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="mobile-hide">Customer</th>
                                                                            <th>Cash</th>
                                                                            <th class="mobile-toggle-column text-center">
                                                                                Action</th>
                                                                            <th class="mobile-hide">Mobile No.</th>
                                                                            <th class="mobile-hide">Vehicle</th>
                                                                            <th class="mobile-hide">Vehicle No.</th>
                                                                            @if ($user->role != 'Staff')
                                                                                <th class="mobile-hide">Received</th>
                                                                            @endif
                                                                            <th class="mobile-hide">Advance Paid</th>
                                                                            <th class="mobile-hide">Job By</th>
                                                                            <th class="mobile-hide">Due/Received</th>
                                                                            <th class="mobile-hide">Discount</th>
                                                                            <!-- <th class="mobile-hide">End Date</th> -->
                                                                            <th class="mobile-hide">Pay Mode</th>
                                                                            <th class="mobile-hide">Work Status</th>
                                                                            <th class="text-center mobile-hide">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($cards as $card)
                                                                            <tr>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->customer_name }}</td>
                                                                                <td>{{ number_format($card->total_subtotal_amount, 2) }}
                                                                                </td>
                                                                                <td
                                                                                    class="mobile-toggle-column text-center">
                                                                                    <button class="toggle-details"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#card-details-{{ $card->id }}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="card-details-{{ $card->id }}">
                                                                                        <i class="fa fa-plus-circle"></i>
                                                                                    </button>
                                                                                </td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->mobile }}</td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->autocarmodel->model_name ?? '-' }}
                                                                                </td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->vehicle_no ?? '-' }}</td>
                                                                                @if ($user->role != 'Staff')
                                                                                    <td class="mobile-hide">
                                                                                        {{ number_format($card->payments->sum('payment_amount'), 2) }}
                                                                                    </td>
                                                                                @endif
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->extra_pay ?? '0.00' }}</td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->technicianUser->name ?? '-' }}
                                                                                </td>
                                                                                <td class="mobile-hide">
                                                                                    @if ($card->payment_type === 'partial')
                                                                                        Due
                                                                                    @else
                                                                                        Received
                                                                                    @endif
                                                                                </td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->discount ?? '0.00' }}</td>
                                                                                <td class="mobile-hide">
                                                                                    @if ($card->payment_method === 'cashonline')
                                                                                        Cash + Online
                                                                                    @else
                                                                                        {{ $card->payment_method }}
                                                                                    @endif
                                                                                </td>
                                                                                <td class="mobile-hide">
                                                                                    {{ $card->status ?? 'N/A' }}</td>
                                                                                <td class="text-center mobile-hide">
                                                                                    @if ($card->payment_type === 'partial' && $card->remaining_amount > 0)
                                                                                        @php
                                                                                            $emiPaidMonthsBtn = 0;
                                                                                            if (
                                                                                                $card->payments &&
                                                                                                count($card->payments) >
                                                                                                    0
                                                                                            ) {
                                                                                                foreach (
                                                                                                    $card->payments
                                                                                                    as $pmt
                                                                                                ) {
                                                                                                    if (
                                                                                                        $pmt->payment_method ===
                                                                                                        'emi'
                                                                                                    ) {
                                                                                                        $emiPaidMonthsBtn +=
                                                                                                            $pmt->emi_month;
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        @endphp
                                                                                        <button
                                                                                            class="btn btn-sm btn-success dd-make-payment-btn"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#ddMakePaymentModal"
                                                                                            data-id="{{ $card->id }}"
                                                                                            data-total-amount="{{ $card->total_subtotal_amount }}"
                                                                                            data-remaining-amount="{{ $card->remaining_amount }}"
                                                                                            data-is-emi="{{ $card->payment_method === 'emi' ? 1 : 0 }}"
                                                                                            data-emi-duration="{{ $card->emi_duration ?? 12 }}"
                                                                                            data-emi-paid="{{ $emiPaidMonthsBtn }}">
                                                                                            <i
                                                                                                class="fas fa-money-bill-wave"></i>
                                                                                            Payment
                                                                                        </button>
                                                                                    @endif
                                                                                    @if ($user->role != 'Staff')
                                                                                        <button class="btn dd-open-history"
                                                                                            data-id="{{ $card->id }}"
                                                                                            title="Payment History">
                                                                                            <i class="fas fa-history"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                    @if (app('hasPermission')(24, 'edit'))
                                                                                        <button
                                                                                            class="btn btn-sm edit-jobcard mobile-hide"
                                                                                            data-id="{{ $card->id }}"
                                                                                            data-record_date="{{ \Carbon\Carbon::parse($card->record_date)->format('Y-m-d') }}"
                                                                                            data-customer_name="{{ $card->customer_name }}"
                                                                                            data-phone="{{ $card->mobile }}"
                                                                                            data-vehicle_model="{{ $card->model }}"
                                                                                            data-vehicle_no="{{ $card->vehicle_no }}"
                                                                                            data-technician_name="{{ $card->technician }}"
                                                                                            data-start_date="{{ $card->start_date }}"
                                                                                            data-end_date="{{ $card->end_date }}"
                                                                                            data-status="{{ $card->status }}"
                                                                                            data-extra_pay="{{ $card->extra_pay }}"
                                                                                            data-payment_type="{{ $card->payment_type }}"
                                                                                            data-payable_amount="{{ $card->total_subtotal_amount }}"
                                                                                            data-remaining_amount="{{ $card->remaining_amount }}"
                                                                                            data-payment_mode="{{ $card->payment_method }}"
                                                                                            data-comment="{{ $card->comment }}"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#editJobcardModal">
                                                                                            <svg width="16"
                                                                                                height="20"
                                                                                                viewBox="0 0 16 20"
                                                                                                fill="none"
                                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                                <path
                                                                                                    d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z"
                                                                                                    fill="#092C4C" />
                                                                                            </svg>
                                                                                        </button>
                                                                                    @endif
                                                                                    @if (app('hasPermission')(24, 'delete'))
                                                                                        <button
                                                                                            class="btn btn-sm delete-jobcard mobile-hide"
                                                                                            data-id="{{ $card->id }}">
                                                                                            <svg width="24"
                                                                                                height="24"
                                                                                                viewBox="0 0 24 24"
                                                                                                fill="none"
                                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                                <path
                                                                                                    d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z"
                                                                                                    fill="#092C4C" />
                                                                                                <path
                                                                                                    d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z"
                                                                                                    fill="#092C4C" />
                                                                                            </svg>
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="collapse mobile-collapse-row"
                                                                                id="card-details-{{ $card->id }}">
                                                                                <td colspan="{{ $user->role != 'Staff' ? 14 : 13 }}"
                                                                                    class="p-0">
                                                                                    <div class="p-2 bg-white">
                                                                                        <div class="row gx-2">
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Mobile No.:</strong>
                                                                                                {{ $card->mobile }}</div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Vehicle:</strong>
                                                                                                {{ $card->autocarmodel->model_name ?? '-' }}
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Vehicle
                                                                                                    No.:</strong>
                                                                                                {{ $card->vehicle_no ?? '-' }}
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Amount:</strong>
                                                                                                {{ $card->total_subtotal_amount }}
                                                                                            </div>
                                                                                            @if ($user->role != 'Staff')
                                                                                                <div class="col-12 mb-2">
                                                                                                    <strong>Received:</strong>
                                                                                                    {{ number_format($card->payments->sum('payment_amount'), 2) }}
                                                                                                </div>
                                                                                            @endif
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Advance
                                                                                                    Paid:</strong>
                                                                                                {{ $card->extra_pay ?? '0.00' }}
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Job By:</strong>
                                                                                                {{ $card->technicianUser->name ?? '-' }}
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Due/Received:</strong>
                                                                                                @if ($card->payment_type === 'partial')
                                                                                                    Due
                                                                                                @else
                                                                                                    Received
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Discount:</strong>
                                                                                                {{ $card->discount ?? '0.00' }}
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Pay Mode:</strong>
                                                                                                @if ($card->payment_method === 'cashonline')
                                                                                                    Cash + Online
                                                                                                @else
                                                                                                    {{ $card->payment_method }}
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="col-12 mb-2">
                                                                                                <strong>Work
                                                                                                    Status:</strong>
                                                                                                {{ $card->status ?? 'N/A' }}
                                                                                            </div>
                                                                                            <div class="col-12">
                                                                                                @if ($card->payment_type === 'partial' && $card->remaining_amount > 0)
                                                                                                    @php
                                                                                                        $emiPaidMonthsMob = 0;
                                                                                                        if (
                                                                                                            $card->payments &&
                                                                                                            count(
                                                                                                                $card->payments,
                                                                                                            ) > 0
                                                                                                        ) {
                                                                                                            foreach (
                                                                                                                $card->payments
                                                                                                                as $pmt
                                                                                                            ) {
                                                                                                                if (
                                                                                                                    $pmt->payment_method ===
                                                                                                                    'emi'
                                                                                                                ) {
                                                                                                                    $emiPaidMonthsMob +=
                                                                                                                        $pmt->emi_month;
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    @endphp
                                                                                                    <button
                                                                                                        class="btn btn-sm btn-success me-1 dd-make-payment-btn"
                                                                                                        data-bs-toggle="modal"
                                                                                                        data-bs-target="#ddMakePaymentModal"
                                                                                                        data-id="{{ $card->id }}"
                                                                                                        data-total-amount="{{ $card->total_subtotal_amount }}"
                                                                                                        data-remaining-amount="{{ $card->remaining_amount }}"
                                                                                                        data-is-emi="{{ $card->payment_method === 'emi' ? 1 : 0 }}"
                                                                                                        data-emi-duration="{{ $card->emi_duration ?? 12 }}"
                                                                                                        data-emi-paid="{{ $emiPaidMonthsMob }}">
                                                                                                        <i
                                                                                                            class="fas fa-money-bill-wave"></i>
                                                                                                        Payment
                                                                                                    </button>
                                                                                                @endif
                                                                                                @if ($user->role != 'Staff')
                                                                                                    <button
                                                                                                        class="btn btn-sm btn-secondary dd-open-history"
                                                                                                        data-id="{{ $card->id }}"
                                                                                                        title="Payment History">
                                                                                                        <i
                                                                                                            class="fas fa-history"></i>
                                                                                                    </button>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Expenses --}}
                                                    @php
                                                        $hasExpenses = false;
                                                        foreach ($cards as $card) {
                                                            if (
                                                                $card->expenseStores
                                                                    ->where('isDeleted', 0)
                                                                    ->isNotEmpty()
                                                            ) {
                                                                $hasExpenses = true;
                                                                break;
                                                            }
                                                        }
                                                    @endphp

                                                    {{-- Summary --}}
                                                    @php
                                                        $summary = $dailySummaries[$date] ?? null;
                                                    @endphp
                                                    @if ($summary)
                                                        <div class="mobile-section-wrap mt-3">
                                                            <div class="mobile-section-toggle">
                                                                <h5 class="fw-bold mb-1 mt-3">Overall Total for
                                                                    {{ $date }}
                                                                </h5>
                                                                <button type="button" class="mobile-section-toggle-btn"
                                                                    data-mobile-toggle-target="summary-{{ $dateSlug }}"
                                                                    aria-expanded="false"><i class="fa fa-plus-circle"
                                                                        aria-hidden="true"></i></button>
                                                            </div>
                                                            <div id="summary-{{ $dateSlug }}"
                                                                class="mobile-section-body mobile-collapsed">
                                                                <table class="table table-bordered mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Cash</th>
                                                                            <th>Online</th>
                                                                            @if ($user->role != 'Staff')
                                                                                <th>Expenses</th>
                                                                                <th>Total (Cash - Expenses)</th>
                                                                            @endif
                                                                            <th>Cash + Online</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ number_format($summary['cash'], 2) }}
                                                                            </td>
                                                                            <td>{{ number_format($summary['online'], 2) }}
                                                                            </td>
                                                                            @if ($user->role != 'Staff')
                                                                                <td>{{ number_format($summary['expenses'], 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($summary['total'], 2) }}
                                                                                </td>
                                                                            @endif
                                                                            <td>{{ number_format($summary['combined'], 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            $grandTotalProfit += $totalProfit;
                                            $grandTotalExpense += $totalExpense;
                                            $grandTotalReceived += $totalReceived;
                                            $grandTotalDiscount += $totalDiscount;
                                            $grandTotalPending += $totalPending;
                                            $grandTotalEarned += $totalEarned;
                                        @endphp
                                    @endforeach


                                @endif
                            </tbody>
                        </table>

                        @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                            <div id="grand-total-box"
                                style="
                                                                        border: 1px solid #ccc; 
                                                                        padding: 8px 12px; 
                                                                        display: inline-block; 
                                                                        border-radius: 4px;
                                                                        font-weight: bold;
                                                                    ">

                                <span style="color: black;">
                                    Total Work Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalProfit, 2) }}</span>
                                </span>
                                &nbsp;&nbsp;
                                <span style="color: black;">
                                    Total Expense Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalExpense, 2) }}</span>
                                </span>
                                &nbsp;&nbsp;
                                <span style="color: black;">
                                    Total Received Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalReceived, 2) }}</span>
                                </span>
                                &nbsp;&nbsp;
                                <span style="color: black;">
                                    Total Discount Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalProfit, 2) }}</span>
                                </span>
                                &nbsp;&nbsp;
                                <span style="color: black;">
                                    Total Pending Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalPending, 2) }}</span>
                                </span>
                                &nbsp;&nbsp;
                                <span style="color: black;">
                                    Total Earned Amount:
                                    <span style="color: orange;">{{ number_format($grandTotalEarned, 2) }}</span>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade" id="search-daily-content">
                <div class="card">


                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start my-3 mx-3 gap-3">
                            <!-- Filter Section -->
                            <div style="display: flex; flex-direction: column; min-width: 220px;">
                                <label for="filter-name" class="form-label mb-1">Search by Customers</label>
                                <select id="filter-name" class="form-select form-select-sm select2">
                                    <option value="">All Customers</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Totals + Buttons -->
                            <div class="d-flex flex-wrap align-items-center gap-3 p-2 border rounded"
                                style="font-weight: 600;">
                                @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                    <span style="color: #1b2850" class="amount_dis">Total Work Amount: <span
                                            id="total-amount" style="color: #ff9f43">0</span></span>
                                    <span style="color: #1b2850" class="amount_dis">Total Received: <span
                                            id="total-received" style="color: #ff9f43">0</span></span>
                                    <span style="color: #1b2850" class="amount_dis">Total Discount Amount: <span
                                            id="total-discount" style="color: #ff9f43">0</span></span>
                                    <span style="color: #1b2850" class="amount_dis">Total Pending: <span
                                            id="total-pending" style="color: #ff9f43">0</span></span>
                                @endif
                                <div style="display: flex;">
                                    <button id="download-csv" class="btn btn-sm btn-success">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                    <button id="download-pdf" class="btn btn-sm btn-danger" style="margin-left: 1rem;">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-end align-items-center my-3 mx-3 gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="jobcards-search-input" class="mb-0">Search:</label>
                                <input type="search" id="jobcards-search-input" class="form-control form-control-sm"
                                    placeholder="Search daily data">
                            </div>
                        </div>

                        <div class="table-responsive mt-3" style="overflow-x: auto;">
                            <table id="jobcards-table" class="table capitalize">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Details</th>
                                        <th>Record Date</th>
                                        <th>Vehicle</th>
                                        <th>Vehicle No.</th>
                                        <th>Job By</th>
                                        <th>Due/Received</th>
                                        <th>Pay Mode</th>
                                        <th>Amount</th>
                                        <th>Received</th>
                                        <th>Discount</th>
                                        <th>Pending</th>
                                        <th>Advance Paid</th>
                                    </tr>
                                </thead>
                                <tbody id="jobcards-body">

                                </tbody>

                            </table>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center px-3 pb-3 gap-3"
                            id="jobcards-pagination-controls" style="display: none;">
                            <div class="d-flex align-items-center gap-2">
                                <label for="jobcards-per-page-select" class="mb-0 text-muted" style="font-size: 14px;">Show per page :</label>
                                <select id="jobcards-per-page-select" class="form-select form-select-sm"
                                    style="width: auto; min-width: 70px;">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <div class="small text-muted ms-3">
                                    <span id="jobcards-pagination-from">0</span> - <span id="jobcards-pagination-to">0</span>
                                    of <span id="jobcards-pagination-total">0</span> items
                                </div>
                            </div>
                            <nav aria-label="Daily data pagination">
                                <ul class="pagination pagination-sm mb-0" id="jobcards-pagination-numbers"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="editJobcardModal" tabindex="-1" aria-labelledby="editJobcardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJobcardModalLabel">Edit Daily Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editJobcardForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <label>Record Date</label>
                                <input type="date" name="record_date" class="form-control" id="edit_record_date"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Customer Name</label>

                                <select id="edit_customer_name" name="customer_name" style="z-index:1;"
                                    class="form-control select2">
                                    <option value="">Select Customer</option>

                                    @foreach ($customers as $username)
                                        <option value="{{ $username->id }}" data-phone="{{ $username->phone }}">
                                            {{ $username->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" id="edit_phone">
                            </div>


                            <div class="col-md-3 col-6">
                                <label>Vehicle No.</label>
                                <input type="text" name="vehicle_no" class="form-control" id="edit_vehicle_no">
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="edit_technician_name">Technician Name</label>
                                <select name="technician_name" class="form-control" id="edit_technician_name">
                                    <option value="">Select Technician</option>
                                    @foreach ($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="edit_status">Work Status</label>
                                <select name="status" class="form-control" id="edit_status">
                                    <option value="">Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>

                                </select>
                            </div>


                            <div class="col-md-3 col-6">
                                <label>Payable Amount</label>
                                <input type="number" name="payable_amount" class="form-control"
                                    id="edit_payable_amount" readonly>
                            </div>
                            <div class="col-md-3 col-6">
                                <label>Remaining Amount</label>
                                <input type="number" class="form-control" id="edit_remaining_amount" readonly>
                            </div>
                            <div class="col-md-3 col-6">
                                <label>Advance / Extra Amount</label>
                                <input type="number" name="extra_pay" id="extra_pay" class="form-control">
                                <div class="text-danger error-extra_pay"></div>
                            </div>
                            <div class="col-md-3">
                                <label>Comment</label>
                                <textarea name="comment" class="form-control" id="edit_comment"></textarea>
                            </div>
                        </div>



                        <input type="hidden" name="jobcard_id" id="edit_jobcard_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SHARED PAYMENT MODAL  (Purchase-List style)               --}}
    {{-- ============================================================ --}}
    <form id="ddMakePaymentForm">
        <div class="modal fade" id="ddMakePaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">

                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-sm btn-cancel text-white" id="ddViewHistoryBtn">View
                                Payment History</button>
                        </div>

                        <!-- Payment History Container -->
                        <div id="ddPaymentHistoryBox" class="border p-2 rounded bg-white d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Payment History</h6>
                                <button type="button" class="btn btn-sm btn-danger"
                                    id="ddCloseHistoryBtn">&times;</button>
                            </div>
                            <ul id="ddPaymentHistoryList" class="list-unstyled mb-0"
                                style="max-height: 200px; overflow-y: auto;"></ul>
                        </div>

                        <div class="border p-2 rounded bg-light">
                            <strong>Total Amount:</strong> ₹<span id="ddEmiTotal"></span><br>
                            <strong>Remaining Amount:</strong> ₹<span id="ddRemainingAmountDisplay">0.00</span>
                        </div>

                        <br>

                        <!-- Payment Method -->
                        <div class="mb-3" id="ddPaymentMethodDiv">
                            <label for="ddPaymentMethodSelect" class="form-label">Select Payment Method</label>
                            <select class="form-select" id="ddPaymentMethodSelect" name="payment_method">
                                <option value="" selected disabled>Select</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cash_online">Cash + Online</option>
                            </select>
                            <div class="text-danger" id="ddPaymentMethodError"></div>
                        </div>

                        <div class="mb-3 d-none" id="ddCashOnlineTypeDiv">
                            <label for="ddCashOnlineTypeSelect" class="form-label">Select Cash + Online Type</label>
                            <select class="form-select" id="ddCashOnlineTypeSelect" name="cash_online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_online_fully">Cash + Online Fully</option>
                                <option value="cash_online_partially">Cash + Online Partially</option>
                            </select>
                            <div class="text-danger" id="ddCashOnlineTypeError"></div>
                        </div>

                        <!-- Fully Cash + Online -->
                        <div class="mb-3 d-none" id="ddFullyCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="text" id="ddCashOnlineFullAmount" name="fully_cash_amount"
                                class="form-control">
                            <div class="text-danger" id="ddCashOnlineFullAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="text" id="ddUpiOnlineFullAmount" name="full_online_amount"
                                class="form-control" readonly>
                            <div class="text-danger" id="ddUpiOnlineFullAmountError"></div>
                        </div>

                        <!-- Partial Cash + Online -->
                        <div class="mb-3 d-none" id="ddPartialCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="text" id="ddCashOnlinePartialAmount" name="cash_amount"
                                class="form-control">
                            <div class="text-danger" id="ddCashOnlinePartialAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="text" id="ddUpiOnlinePartialAmount" name="online_amount"
                                class="form-control">
                            <div class="text-danger" id="ddUpiOnlinePartialAmountError"></div>
                            <label class="mt-2">Remaining Amount</label>
                            <input type="text" id="ddRemainingCashOnlineAmount" name="remaining_amount"
                                class="form-control" readonly>
                        </div>

                        <div class="mb-3 d-none" id="ddOnlineTypeDiv">
                            <label for="ddOnlineTypeSelect" class="form-label">Select Online Type</label>
                            <select class="form-select" id="ddOnlineTypeSelect" name="online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="online_fully">Online Fully</option>
                                <option value="online_partially">Online Partially</option>
                            </select>
                            <div class="text-danger" id="ddOnlineTypeError"></div>
                        </div>

                        <!-- Paid Type Dropdown -->
                        <div class="mb-3 d-none" id="ddPaidTypeDiv">
                            <label for="ddPaidTypeSelect" class="form-label">Paid Type</label>
                            <select class="form-select" id="ddPaidTypeSelect" name="paid_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_partially">Cash Partially</option>
                                <option value="cash_fully">Cash Fully</option>
                            </select>
                            <div class="text-danger" id="ddPaidTypeError"></div>
                        </div>

                        <!-- UPI Amount Input -->
                        <div class="mb-3 d-none" id="ddUpiAmountDiv">
                            <label for="ddUpiAmountInput" class="form-label">Online Amount</label>
                            <input type="text" class="form-control" id="ddUpiAmountInput" name="upi_online_amount"
                                readonly>
                            <div class="text-danger" id="ddUpiAmountError"></div>
                        </div>

                        <!-- Partially Paid Fields -->
                        <div class="mb-3 d-none" id="ddPartialPaidFields">
                            <label for="ddPartialAmount" class="form-label">Enter Amount</label>
                            <input type="text" class="form-control mb-2" id="ddPartialAmount" name="amount">
                            <div class="text-danger" id="ddPartialAmountError"></div>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                <div style="flex: 1;">
                                    <label for="ddPendingAmount" class="form-label">Pending Amount</label>
                                    <input type="text" class="form-control" id="ddPendingAmount"
                                        name="cash_pending_amount" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Fully Paid Fields -->
                        <div class="mb-3 d-none" id="ddFullyPaidFields">
                            <label class="form-label">Cash Amount</label>
                            <input type="text" class="form-control" id="ddCashAmount" name="cashAmount">
                            <div class="text-danger" id="ddCashAmountError"></div>
                        </div>

                        <!-- EMI section (only for EMI payment method cards) -->
                        <div class="mb-3 d-none" id="ddEmiSection">
                            <div class="mb-3">
                                <label for="ddPaymentTypeSelect" class="form-label">Payment Type</label>
                                <select class="form-select" id="ddPaymentTypeSelect" name="emi_payment_type">
                                    <option value="" selected disabled>Select Payment Type</option>
                                    <option value="emi">EMI</option>
                                    <option value="fully">Fully</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="ddEmiMonthDiv">
                                <label for="ddEmiMonthInput" class="form-label">Enter EMI Month</label>
                                <input type="number" class="form-control" id="ddEmiMonthInput" name="emi_month_new"
                                    min="1" value="1">
                                <small class="text-danger d-none" id="ddEmiMonthError">Month exceeds allowed EMI
                                    duration.</small>
                            </div>
                            <div class="mb-3 d-none" id="ddEmiTotalDiv">
                                <label for="ddEmiTotalCalculated" class="form-label">EMI Amount</label>
                                <input type="number" class="form-control" id="ddEmiTotalCalculated"
                                    name="emi_total_new" readonly>
                            </div>
                        </div>

                        <!-- Discount field -->
                        <div class="mb-3" id="ddDiscountDiv">
                            <label for="ddDiscountAmount" class="form-label">Discount (₹)</label>
                            <input type="number" class="form-control" id="ddDiscountAmount" name="discount_amount"
                                step="0.01" min="0">
                        </div>
                        <!-- Remark -->
<div class="mb-3">
    <label for="ddPaymentRemark" class="form-label">Remark</label>
    <textarea
        class="form-control"
        id="ddPaymentRemark"
        name="remark"
        rows="3"
        placeholder="Add any remarks about this payment..."></textarea>
</div>

                        <!-- Hidden inputs -->
                        <input type="hidden" id="ddJobCardId" name="jobcard_id">
                        <input type="hidden" id="ddEditPaymentId" name="edit_payment_id">
                        <input type="hidden" id="ddRemainingAmountHidden" name="remaining_amount">
                        <input type="hidden" id="ddIsEmi" name="is_emi" value="0">
                        <input type="hidden" id="ddEmiDuration" name="emi_duration" value="0">
                        <input type="hidden" id="ddEmiPaid" name="emi_paid" value="0">
                        <input type="hidden" id="ddTotalAmount" name="total_amount" value="0">

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit text-white"
                                style="background-color: #ff9f43;">Submit Payment</button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Shared Payment History Modal -->
    <div class="modal fade" id="ddPaymentHistoryModal" tabindex="-1" aria-labelledby="ddPaymentHistoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ddPaymentHistoryLabel">Payment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="ddGlobalPaymentHistoryList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        setTimeout(() => {
            const topScroll = document.querySelector('.table-scroll-top');
            const tableResponsive = document.querySelector('.table-responsive');
            const orderTable = document.getElementById('order-table');

            if (topScroll && tableResponsive && orderTable) {
                const topInnerDiv = topScroll.querySelector('div');

                if (topInnerDiv) {
                    topInnerDiv.style.width = orderTable.scrollWidth + 'px';

                    // Sync scrollbars
                    topScroll.addEventListener('scroll', () => {
                        tableResponsive.scrollLeft = topScroll.scrollLeft;
                    });
                    tableResponsive.addEventListener('scroll', () => {
                        topScroll.scrollLeft = tableResponsive.scrollLeft;
                    });
                }
            }
        }, 200);
        $(document).ready(function() {

            $(".select2").select2({
                tags: true,
                width: '120px'
            });

            const authToken = localStorage.getItem("authToken");



            $('.edit-jobcard').on('click', function() {
                const btn = $(this);


                $('#edit_jobcard_id').val(btn.data('id'));
                $('#edit_record_date').val(btn.data('record_date'));
                // $('#edit_customer_name').val(btn.data('customer_name'));
                $('#edit_phone').val(btn.data('phone'));
                $('#edit_vehicle_model').val(btn.data('vehicle_model'));
                $('#edit_vehicle_no').val(btn.data('vehicle_no'));
                $('#edit_technician_name').val(btn.data('technician_name'));
                $('#edit_start_date').val(btn.data('start_date'));
                $('#edit_end_date').val(btn.data('end_date'));
                $('#edit_status').val(btn.data('status'));
                $('#edit_vendor_name').val(btn.data('vendor_name'));
                $('#edit_item_name').val(btn.data('item_name'));
                $('#edit_item_price').val(btn.data('item_price'));
                $('#edit_total_expense_amount').val(btn.data('total_expense_amount'));
                $('#edit_payable_amount').val(btn.data('payable_amount'));
                $('#edit_remaining_amount').val(btn.data('remaining_amount'));
                $('#edit_payment_mode').val(btn.data('payment_mode'));
                $('#edit_comment').val(btn.data('comment'));
                $('#extra_pay').val(btn.data('extra_pay'));
                const paymentType = btn.data('payment_type'); // get payment type
                let customerName = btn.data('customer_name');

                $('#edit_customer_name option').each(function() {
                    if ($(this).text().trim() === customerName) {
                        $('#edit_customer_name').val($(this).val()).trigger('change');
                    }
                });


                $('#edit_payment_type').val(paymentType); // set value

                if (paymentType === 'full') {
                    $('#edit_payable_amount').prop('disabled', true); // disable input
                } else {
                    $('#edit_payable_amount').prop('disabled', false); // enable input
                }
            });

            $('#editJobcardForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/api/jobcards/update', // adjust your route
                    method: 'POST',
                    data: formData,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(res) {
                        location.reload();
                    },
                    error: function(err) {
                        alert('Error saving changes.');
                    }
                });
            });
        });
        $(document).ready(function() {
            return;
            let dataTable;
            const authToken = localStorage.getItem("authToken");



            function getPaymentTotal(payments) {
                return payments.reduce((sum, p) => sum + parseFloat(p.payment_amount || 0), 0);
            }


            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function loadJobCards() {
                $.ajax({
                    url: '/api/daily-data/ajax',
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        sub_admin_id: selectedSubAdminId
                    },
                    success: function(res) {
                        if (res.status && Array.isArray(res.data)) {
                            let rows = '';
                            const customerSet = new Set();

                            res.data.forEach(card => {
                                const paymentTotal = getPaymentTotal(card.payments || []);
                                const totalAmount = parseFloat(card.total_subtotal_amount) || 0;
                                const pendingAmount = totalAmount - paymentTotal - parseFloat(
                                    card.discount) || 0;

                                if (card.customer_name) {
                                    customerSet.add(card.customer_name.trim());
                                }

                                rows += `
                                                            <tr>
                                                                <td>${card.customer_name || '-'}</td>
                                                                <td>${card.record_date || '-'}</td>
                                                                <td>${card.autocarmodel?.model_name || '-'}</td>
                                                                <td>${card.vehicle_no || '-'}</td>
                                                                <td>${card.technician_user?.name || '-'}</td>
                                                                <td>${card.payment_type === 'partial' ? 'Due' : 'Received'}</td>
                                                                <td>${card.payment_method === 'cashonline' ? 'Cash + Online' : card.payment_method || '-'}</td>
                                                                <td class="amount">${formatAmount(totalAmount)}</td>
                                                                <td class="received">${formatAmount(paymentTotal)}</td>
                                                                <td class="discount">${formatAmount(card.discount)}</td>
                                                                <td class="pending">${formatAmount(pendingAmount)}</td> <!-- NEW -->
                                                                <td class="extra_pay">${formatAmount(card.extra_pay)}</td> <!-- NEW -->
                                                            </tr>
                                                        `;
                            });

                            let options = '<option value="">All Customers</option>';
                            Array.from(customerSet).sort().forEach(name => {
                                options += `<option value="${name}">${name}</option>`;
                            });
                            $('#filter-name').html(options);

                            $('#jobcards-body').html(rows);

                            if ($.fn.DataTable.isDataTable('#jobcards-table')) {
                                dataTable.clear().destroy();
                            }

                            dataTable = $('#jobcards-table').DataTable();

                            dataTable.on('draw', function() {
                                calculateVisibleTotals();
                            });

                            dataTable.one('init', function() {
                                calculateVisibleTotals
                                    (); // Run totals after initialization finished
                            });

                            $('#filter-name').trigger('change');
                        } else {
                            $('#jobcards-body').html('<tr><td colspan="9">No data available</td></tr>');
                        }
                    },
                    error: function() {
                        alert('Failed to load jobcard data.');
                    }
                });
            }

            function calculateVisibleTotals() {
                let totalAmount = 0;
                let totalReceived = 0;
                let totalPending = 0;
                let totalDiscount = 0;

                // Use DataTables API to get filtered/visible rows
                const rows = dataTable.rows({
                    filter: 'applied'
                }).nodes();

                $(rows).each(function() {
                    const amountText = $(this).find('td.amount').text().replace(/,/g, '') || '0';
                    const receivedText = $(this).find('td.received').text().replace(/,/g, '') || '0';
                    const discountText = $(this).find('td.discount').text().replace(/,/g, '') || '0';
                    const pendingText = $(this).find('td.pending').text().replace(/,/g, '') || '0';

                    const amount = parseFloat(amountText) || 0;
                    const received = parseFloat(receivedText) || 0;
                    const discount = parseFloat(discountText) || 0;
                    const pending = parseFloat(pendingText) || 0;

                    totalAmount += amount;
                    totalReceived += received;
                    totalDiscount += discount;
                    totalPending += pending;
                });

                $('#total-amount').text(totalAmount.toFixed(2));
                $('#total-received').text(totalReceived.toFixed(2));
                $('#total-discount').text(totalDiscount.toFixed(2));
                $('#total-pending').text(totalPending.toFixed(2));
            }


            function formatAmount(value) {
                if (!value) return '0.00';
                return parseFloat(value).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // 🔁 Initial load
            loadJobCards();

            // ⛳ Custom filter by customer name
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const selectedName = $('#filter-name').val().toLowerCase().trim();
                const customerName = data[0].toLowerCase().trim(); // 1st column

                return !selectedName || customerName === selectedName;
            });

            // 🔁 Apply customer filter on change
            $('#filter-name').on('change', function() {
                if (dataTable) {
                    dataTable.draw(); // will also trigger totals
                }
            });

            // ⬇ Download filtered CSV
            $('#download-csv').on('click', function() {
                if (!dataTable) return;

                const wb = XLSX.utils.book_new(); // Create a new workbook
                const ws_data = [];

                // Headers
                const headers = [];
                $('#jobcards-table thead th').each(function() {
                    headers.push($(this).text().trim());
                });
                ws_data.push(headers);

                // Rows
                dataTable.rows({
                    search: 'applied'
                }).every(function() {
                    const row = this.node();
                    const rowData = [];

                    $(row).find('td').each(function() {
                        rowData.push($(this).text().trim());
                    });

                    ws_data.push(rowData);
                });

                const ws = XLSX.utils.aoa_to_sheet(ws_data); // Convert array to worksheet
                XLSX.utils.book_append_sheet(wb, ws, "Daily Data"); // Add sheet to workbook

                XLSX.writeFile(wb, "filtered_daily_data.xlsx"); // Download file
            });

            $('#download-pdf').on('click', function() {
                if (!dataTable) return;

                const headers = [];
                $('#jobcards-table thead th').each(function() {
                    headers.push($(this).text().trim());
                });

                const rows = [];
                dataTable.rows({
                    search: 'applied'
                }).every(function() {
                    const row = this.node();
                    const rowData = [];
                    $(row).find('td').each(function() {
                        rowData.push($(this).text().trim());
                    });
                    rows.push(rowData);
                });

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                const logoUrl =
                    '{{ !empty($companyData) && !empty($companyData->logo) ? env('ImagePath') . '/storage/' . $companyData->logo : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}';

                const img = new Image();
                img.crossOrigin = "anonymous";
                img.src = logoUrl;

                img.onload = function() {
                    const maxWidth = 25; // Bigger logo size
                    const aspectRatio = img.height / img.width;
                    const imgWidth = maxWidth;
                    const imgHeight = imgWidth * aspectRatio;

                    // Draw logo on left
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    const imgData = canvas.toDataURL('image/png');
                    doc.addImage(imgData, 'PNG', 14, 10, imgWidth, imgHeight);

                    // Company info on right side
                    const pageWidth = doc.internal.pageSize.getWidth();
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(14);
                    doc.text('{{ $companyData->name ?? 'Your Company Name' }}', pageWidth - 14, 15, {
                        align: 'right'
                    });

                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(10);
                    doc.text('{{ $companyData->address ?? '123 Main Street, Your City, Country' }}',
                        pageWidth - 14, 20, {
                            align: 'right'
                        });

                    doc.text(
                        'Phone: {{ $companyData->phone ?? '+91 98765 43210' }} | Email: {{ $companyData->email ?? 'info@company.com' }}',
                        pageWidth - 14,
                        25, {
                            align: 'right'
                        }
                    );

                    // Horizontal line
                    doc.setLineWidth(0.5);
                    doc.line(10, 35, pageWidth - 10, 35);

                    // Title
                    doc.setFontSize(13);
                    doc.setFont("helvetica", "bold");
                    doc.text("Daily Data Report", 14, 45);

                    // Table with red header
                    doc.autoTable({
                        head: [headers],
                        body: rows,
                        startY: 50,
                        styles: {
                            fontSize: 8
                        },
                        headStyles: {
                            fillColor: [220, 53, 69], // Bootstrap Red (#dc3545)
                            textColor: [255, 255, 255],
                            halign: 'center'
                        },
                    });
                    const pageCount = doc.internal.getNumberOfPages();
                    for (let i = 1; i <= pageCount; i++) {
                        doc.setPage(i);
                        doc.setDrawColor(0); // Black border
                        doc.setLineWidth(0.5); // Border thickness
                        doc.rect(5, 5, doc.internal.pageSize.getWidth() - 10, doc.internal.pageSize
                            .getHeight() - 10, 'S');
                        // (x, y, width, height, style)
                    }

                    doc.save('filtered_daily_data.pdf');
                };

                img.onerror = function() {
                    console.error('Logo image failed to load.');
                    doc.text('{{ $companyData->name ?? 'Your Company Name' }}', 55, 15);
                    doc.save('filtered_daily_data.pdf');
                };
            });




        });

        $(document).ready(function() {
            window.__dailyDataServerPaginationInitialized = true;

            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let currentPage = 1;
            let lastPage = 1;
            let perPage = parseInt($('#jobcards-per-page-select').val(), 10) || 10;
            let searchQuery = '';
            let searchTimer = null;

            function getPaymentTotal(payments) {
                return (payments || []).reduce((sum, payment) => {
                    return sum + parseFloat(payment.payment_amount || 0);
                }, 0);
            }

            function formatAmount(value) {
                return parseFloat(value || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function getFilterPayload(includePagination = true) {
                const payload = {
                    sub_admin_id: selectedSubAdminId,
                    customer_id: $('#filter-name').val() || '',
                    search: searchQuery,
                    month: $('#filter-month').val() || '',
                    year: $('#filter-year').val() || '',
                    date: $('#filter-date').val() || ''
                };

                if (includePagination) {
                    payload.page = currentPage;
                    payload.per_page = perPage;
                }

                return payload;
            }

            function renderJobCardRows(cards) {
                if (!cards || cards.length === 0) {
                    $('#jobcards-body').html(
                    '<tr><td colspan="13" class="text-center">No data available</td></tr>');
                    return;
                }

                let rows = '';

                cards.forEach((card, index) => {
                    const paymentTotal = getPaymentTotal(card.payments);
                    const totalAmount = parseFloat(card.total_subtotal_amount || 0);
                    const discountAmount = parseFloat(card.discount || 0);
                    const pendingAmount = totalAmount - paymentTotal - discountAmount;
                    const rowId = 'jc-detail-' + index + '-' + (card.id || index);
                    const payMode = card.payment_method === 'cashonline' ? 'Cash + Online' : (card
                        .payment_method || '-');
                    const dueReceived = card.payment_type === 'partial' ? 'Due' : 'Received';

                    rows += `
                        <tr>
                            <td>${card.customer_name || '-'}</td>
                            <td class="text-center" style="width:56px;">
                                <button type="button" class="jc-toggle-btn" data-target="${rowId}" aria-label="Toggle details">
                                    <span class="toggle-icon">+</span>
                                </button>
                            </td>
                            <td>${card.record_date || '-'}</td>
                            <td>${card.autocarmodel?.model_name || '-'}</td>
                            <td>${card.vehicle_no || '-'}</td>
                            <td>${card.technician_user?.name || '-'}</td>
                            <td>${dueReceived}</td>
                            <td>${payMode}</td>
                            <td>${formatAmount(totalAmount)}</td>
                            <td>${formatAmount(paymentTotal)}</td>
                            <td>${formatAmount(discountAmount)}</td>
                            <td>${formatAmount(pendingAmount)}</td>
                            <td>${formatAmount(card.extra_pay)}</td>
                        </tr>
                        <tr class="jc-detail-row" id="${rowId}">
                            <td colspan="2" class="p-0">
                                <div class="jc-detail-content">
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Record Date</span>
                                        <span class="jc-detail-value">${card.record_date || '-'}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Vehicle</span>
                                        <span class="jc-detail-value">${card.autocarmodel?.model_name || '-'}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Vehicle No.</span>
                                        <span class="jc-detail-value">${card.vehicle_no || '-'}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Job By</span>
                                        <span class="jc-detail-value">${card.technician_user?.name || '-'}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Due/Received</span>
                                        <span class="jc-detail-value">${dueReceived}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Pay Mode</span>
                                        <span class="jc-detail-value">${payMode}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Amount</span>
                                        <span class="jc-detail-value" style="color:#ff9f43;font-weight:bold;">${formatAmount(totalAmount)}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Received</span>
                                        <span class="jc-detail-value">${formatAmount(paymentTotal)}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Discount</span>
                                        <span class="jc-detail-value">${formatAmount(discountAmount)}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Pending</span>
                                        <span class="jc-detail-value" style="color:#dc3545;font-weight:bold;">${formatAmount(pendingAmount)}</span>
                                    </div>
                                    <div class="jc-detail-item">
                                        <span class="jc-detail-label">Advance Paid</span>
                                        <span class="jc-detail-value">${formatAmount(card.extra_pay)}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#jobcards-body').html(rows);

                // Bind toggle buttons
                $('#jobcards-body').off('click', '.jc-toggle-btn').on('click', '.jc-toggle-btn', function() {
                    const targetId = $(this).data('target');
                    const detailRow = $('#' + targetId);
                    const icon = $(this).find('.toggle-icon');
                    const isOpen = detailRow.hasClass('show');
                    detailRow.toggleClass('show', !isOpen);
                    $(this).toggleClass('minus', !isOpen);
                    icon.text(isOpen ? '+' : '-');
                });
            }

            function updateSummary(summary = {}) {
                $('#total-amount').text(formatAmount(summary.total_amount || 0));
                $('#total-received').text(formatAmount(summary.total_received || 0));
                $('#total-discount').text(formatAmount(summary.total_discount || 0));
                $('#total-pending').text(formatAmount(summary.total_pending || 0));
            }

            function updatePaginationUI(pagination = null) {
                if (!pagination || pagination.total === 0) {
                    $('#jobcards-pagination-controls').hide();
                    $('#jobcards-pagination-from').text(0);
                    $('#jobcards-pagination-to').text(0);
                    $('#jobcards-pagination-total').text(0);
                    $('#jobcards-pagination-numbers').html('');
                    return;
                }

                const from = (pagination.current_page - 1) * pagination.per_page + 1;
                const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#jobcards-pagination-from').text(from);
                $('#jobcards-pagination-to').text(to);
                $('#jobcards-pagination-total').text(pagination.total);

                let paginationHtml = '';
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);

                if ((endPage - startPage) < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let page = startPage; page <= endPage; page++) {
                    paginationHtml += `
                        <li class="page-item ${page === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${page}">${page}</a>
                        </li>
                    `;
                }

                $('#jobcards-pagination-numbers').html(paginationHtml);
                $('#jobcards-pagination-controls').css('display', 'flex');
            }

            function loadJobCards(page = 1) {
                currentPage = page;

                $.ajax({
                    url: '/api/daily-data/ajax',
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: getFilterPayload(true),
                    success: function(res) {
                        if (res.status && Array.isArray(res.data)) {
                            currentPage = res.pagination?.current_page || 1;
                            lastPage = res.pagination?.last_page || 1;

                            renderJobCardRows(res.data);
                            updateSummary(res.summary || {});
                            updatePaginationUI(res.pagination || null);
                        } else {
                            renderJobCardRows([]);
                            updateSummary({});
                            updatePaginationUI(null);
                        }
                    },
                    error: function() {
                        alert('Failed to load jobcard data.');
                    }
                });
            }

            function loadAllFilteredJobCards(callback) {
                $.ajax({
                    url: '/api/daily-data/ajax',
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: getFilterPayload(false),
                    success: function(res) {
                        callback(Array.isArray(res.data) ? res.data : []);
                    },
                    error: function() {
                        alert('Failed to load filtered jobcard data.');
                    }
                });
            }

            loadJobCards(1);

            $('#filter-name, #filter-month, #filter-year, #filter-date').on('change', function() {
                loadJobCards(1);
            });

            $('#jobcards-search-input').on('keyup', function() {
                searchQuery = $(this).val().trim();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadJobCards(1);
                }, 400);
            });

            $('#jobcards-per-page-select').on('change', function() {
                perPage = parseInt($(this).val(), 10) || 10;
                loadJobCards(1);
            });

            $(document).on('click', '#jobcards-pagination-numbers .page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'), 10);
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    loadJobCards(page);
                }
            });

            $('#download-csv').on('click', function() {
                loadAllFilteredJobCards(function(cards) {
                    const wb = XLSX.utils.book_new();
                    const ws_data = [];

                    // Skip the "Details" toggle column (index 1)
                    const headers = [];
                    $('#jobcards-table thead th').each(function(i) {
                        if (i !== 1) headers.push($(this).text().trim());
                    });
                    ws_data.push(headers);

                    cards.forEach(card => {
                        const paymentTotal = getPaymentTotal(card.payments);
                        const totalAmount = parseFloat(card.total_subtotal_amount || 0);
                        const discountAmount = parseFloat(card.discount || 0);
                        const pendingAmount = totalAmount - paymentTotal - discountAmount;

                        ws_data.push([
                            card.customer_name || '-',
                            card.record_date || '-',
                            card.autocarmodel?.model_name || '-',
                            card.vehicle_no || '-',
                            card.technician_user?.name || '-',
                            card.payment_type === 'partial' ? 'Due' : 'Received',
                            card.payment_method === 'cashonline' ? 'Cash + Online' :
                            (card.payment_method || '-'),
                            formatAmount(totalAmount),
                            formatAmount(paymentTotal),
                            formatAmount(discountAmount),
                            formatAmount(pendingAmount),
                            formatAmount(card.extra_pay)
                        ]);
                    });

                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    XLSX.utils.book_append_sheet(wb, ws, "Daily Data");
                    XLSX.writeFile(wb, "filtered_daily_data.xlsx");
                });
            });

            $('#download-pdf').on('click', function() {
                loadAllFilteredJobCards(function(cards) {
                    // Skip the "Details" toggle column (index 1)
                    const headers = [];
                    $('#jobcards-table thead th').each(function(i) {
                        if (i !== 1) headers.push($(this).text().trim());
                    });

                    const rows = cards.map(card => {
                        const paymentTotal = getPaymentTotal(card.payments);
                        const totalAmount = parseFloat(card.total_subtotal_amount || 0);
                        const discountAmount = parseFloat(card.discount || 0);
                        const pendingAmount = totalAmount - paymentTotal - discountAmount;

                        return [
                            card.customer_name || '-',
                            card.record_date || '-',
                            card.autocarmodel?.model_name || '-',
                            card.vehicle_no || '-',
                            card.technician_user?.name || '-',
                            card.payment_type === 'partial' ? 'Due' : 'Received',
                            card.payment_method === 'cashonline' ? 'Cash + Online' : (
                                card.payment_method || '-'),
                            formatAmount(totalAmount),
                            formatAmount(paymentTotal),
                            formatAmount(discountAmount),
                            formatAmount(pendingAmount),
                            formatAmount(card.extra_pay)
                        ];
                    });

                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    const logoUrl =
                        '{{ !empty($companyData) && !empty($companyData->logo) ? env('ImagePath') . '/storage/' . $companyData->logo : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}';

                    const img = new Image();
                    img.crossOrigin = "anonymous";
                    img.src = logoUrl;

                    img.onload = function() {
                        const maxWidth = 25;
                        const aspectRatio = img.height / img.width;
                        const imgWidth = maxWidth;
                        const imgHeight = imgWidth * aspectRatio;

                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        const imgData = canvas.toDataURL('image/png');
                        doc.addImage(imgData, 'PNG', 14, 10, imgWidth, imgHeight);

                        const pageWidth = doc.internal.pageSize.getWidth();
                        doc.setFont("helvetica", "bold");
                        doc.setFontSize(14);
                        doc.text('{{ $companyData->name ?? 'Your Company Name' }}',
                            pageWidth - 14, 15, {
                                align: 'right'
                            });

                        doc.setFont("helvetica", "normal");
                        doc.setFontSize(10);
                        doc.text(
                            '{{ $companyData->address ?? '123 Main Street, Your City, Country' }}',
                            pageWidth - 14, 20, {
                                align: 'right'
                            });

                        doc.text(
                            'Phone: {{ $companyData->phone ?? '+91 98765 43210' }} | Email: {{ $companyData->email ?? 'info@company.com' }}',
                            pageWidth - 14,
                            25, {
                                align: 'right'
                            }
                        );

                        doc.setLineWidth(0.5);
                        doc.line(10, 35, pageWidth - 10, 35);

                        doc.setFontSize(13);
                        doc.setFont("helvetica", "bold");
                        doc.text("Daily Data Report", 14, 45);

                        doc.autoTable({
                            head: [headers],
                            body: rows,
                            startY: 50,
                            styles: {
                                fontSize: 8
                            },
                            headStyles: {
                                fillColor: [220, 53, 69],
                                textColor: [255, 255, 255],
                                halign: 'center'
                            },
                        });

                        const pageCount = doc.internal.getNumberOfPages();
                        for (let i = 1; i <= pageCount; i++) {
                            doc.setPage(i);
                            doc.setDrawColor(0);
                            doc.setLineWidth(0.5);
                            doc.rect(5, 5, doc.internal.pageSize.getWidth() - 10, doc.internal
                                .pageSize.getHeight() - 10, 'S');
                        }

                        doc.save('filtered_daily_data.pdf');
                    };

                    img.onerror = function() {
                        console.error('Logo image failed to load.');
                        doc.text('{{ $companyData->name ?? 'Your Company Name' }}', 55, 15);
                        doc.save('filtered_daily_data.pdf');
                    };
                });
            });
        });


        // $(document).ready(function () {
        //     // Load selected filters from URL and set inputs
        //     const urlParams = new URLSearchParams(window.location.search);
        //     const selectedMonth = urlParams.get('month');
        //     const selectedYear = urlParams.get('year');
        //     const selectedDate = urlParams.get('date');

        //     if (selectedMonth) $('#filter-month').val(selectedMonth);
        //     if (selectedYear) $('#filter-year').val(selectedYear);
        //     if (selectedDate) $('#filter-date').val(selectedDate);



        //     // When date is selected → clear month and year
        //     $('#filter-date').on('change', function () {
        //         $('#filter-month').val('');
        //         $('#filter-year').val('');
        //         applyFilters('date');
        //     });

        //     // When month or year is selected → clear date
        //     $('#filter-month, #filter-year').on('change', function () {
        //         $('#filter-date').val('');
        //         applyFilters('monthYear');
        //     });

        //     // Apply filters with specific clearing logic
        //     function applyFilters(source) {
        //         const params = new URLSearchParams(window.location.search);

        //         if (source === 'date') {
        //             const date = $('#filter-date').val();
        //             if (date) params.set('date', date); else params.delete('date');
        //             params.delete('month');
        //             params.delete('year');
        //         } else {
        //             const month = $('#filter-month').val();
        //             const year = $('#filter-year').val();
        //             if (month) params.set('month', month); else params.delete('month');
        //             if (year) params.set('year', year); else params.delete('year');
        //             params.delete('date');
        //         }

        //         window.location.search = params.toString(); // Reload with query
        //     }
        // });

        $(document).ready(function() {
            function filterTable() {
                let month = $('#filter-month').val();
                let year = $('#filter-year').val();
                let date = $('#filter-date').val(); // format: yyyy-mm-dd

                $('.group-row').hide();

                let totalProfit = 0,
                    totalExpense = 0,
                    totalReceived = 0,
                    totalPending = 0,
                    totalEarned = 0,
                    totalDiscount = 0;
                let visibleCount = 0;

                $('.group-row').each(function() {
                    let rowDate = $(this).find('td:first').text().replace(/[^0-9\-]/g, '').trim();
                    let [day, m, y] = rowDate.split('-');
                    let show = true;

                    if (date) {
                        let formatted = `${y}-${m.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        if (date !== formatted) show = false;
                    } else {
                        if (month && parseInt(m) !== parseInt(month)) show = false;
                        if (year && parseInt(y) !== parseInt(year)) show = false;
                    }

                    if (show) {
                        $(this).show();
                        visibleCount++;

                        totalProfit += parseFloat($(this).data('totalProfit')) || 0;
                        totalExpense += parseFloat($(this).data('totalExpense')) || 0;
                        totalReceived += parseFloat($(this).data('totalReceived')) || 0;
                        totalDiscount += parseFloat($(this).data('totalDiscount')) || 0;
                        totalPending += parseFloat($(this).data('totalPending')) || 0;
                        totalEarned += parseFloat($(this).data('totalEarned')) || 0;
                    }
                });

                if (visibleCount > 0) {
                    $('#grand-total-box').html(`
                                    <div style="
                                        border: 1px solid #ccc; 
                                        padding: 8px 12px; 
                                        display: inline-block; 
                                        border-radius: 4px;
                                        font-weight: bold;
                                    ">
                                        
                                        <span style="color: black;">Total Profit: <span style="color: orange;">${totalProfit.toFixed(2)}</span></span>
                                        &nbsp;&nbsp;
                                        <span style="color: black;">Total Expense: <span style="color: orange;">${totalExpense.toFixed(2)}</span></span>
                                        &nbsp;&nbsp;
                                        <span style="color: black;">Total Received: <span style="color: orange;">${totalReceived.toFixed(2)}</span></span>
                                        &nbsp;&nbsp;
                                        <span style="color: black;">Total Discount: <span style="color: orange;">${totalDiscount.toFixed(2)}</span></span>
                                        &nbsp;&nbsp;
                                        <span style="color: black;">Total Pending: <span style="color: orange;">${totalPending.toFixed(2)}</span></span>
                                        &nbsp;&nbsp;
                                        <span style="color: black;">Total Earned: <span style="color: orange;">${totalEarned.toFixed(2)}</span></span>
                                    </div>
                                `);
                } else {
                    $('#grand-total-box').removeAttr('style');
                    $('#grand-total-box').html(`
                                    <div style="border: unset !important; display: block !important; border-radius: unset !important; font-weight: bold !important; text-align: center !important; padding: 10px; color: #637381; font-weight: 500; border-bottom: 1px solid #e9ecef; vertical-align: middle; white-space: nowrap; font-size: 13px !important;">No Data Found</div>
                                `);
                }
            }

            $('#filter-date').on('change', function() {
                if ($(this).val()) {
                    $('#filter-month').val('');
                    $('#filter-year').val('');
                }
                filterTable();
            });

            $('#filter-month, #filter-year').on('change', function() {
                if ($('#filter-month').val() || $('#filter-year').val()) {
                    $('#filter-date').val('');
                }
                filterTable();
            });

            filterTable();
        });






        function setMobileSectionToggleIcon(button, isExpanded) {
            let icon = button.querySelector('i');
            if (!icon) {
                icon = document.createElement('i');
                icon.setAttribute('aria-hidden', 'true');
                button.innerHTML = '';
                button.appendChild(icon);
            }

            icon.classList.remove('fa-plus-circle', 'fa-minus-circle');
            icon.classList.add('fa', isExpanded ? 'fa-minus-circle' : 'fa-plus-circle');
        }

        function initializeMobileSectionToggles() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            const toggleButtons = document.querySelectorAll('.mobile-section-toggle-btn');

            toggleButtons.forEach(button => {
                const targetId = button.getAttribute('data-mobile-toggle-target');
                const targetElement = targetId ? document.getElementById(targetId) : null;
                if (!targetElement) {
                    return;
                }

                if (!button.dataset.mobileToggleBound) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const isCollapsed = targetElement.classList.toggle('mobile-collapsed');
                        const isExpanded = !isCollapsed;
                        button.classList.toggle('active', isExpanded);
                        button.setAttribute('aria-expanded', String(isExpanded));
                        setMobileSectionToggleIcon(button, isExpanded);
                    });
                    button.dataset.mobileToggleBound = 'true';
                }

                if (isMobile) {
                    const collapsed = targetElement.classList.contains('mobile-collapsed');
                    const isExpanded = !collapsed;
                    button.classList.toggle('active', isExpanded);
                    button.setAttribute('aria-expanded', String(isExpanded));
                    setMobileSectionToggleIcon(button, isExpanded);
                } else {
                    targetElement.classList.remove('mobile-collapsed');
                    button.classList.add('active');
                    button.setAttribute('aria-expanded', 'true');
                    setMobileSectionToggleIcon(button, true);
                }
            });

            // Per-row collapse icon swap for job card details
            document.querySelectorAll('.mobile-collapse-row').forEach(function(collapseEl) {
                const id = collapseEl.id;
                if (!id) return;
                collapseEl.addEventListener('shown.bs.collapse', function() {
                    const btn = document.querySelector('[data-bs-target="#' + id + '"]');
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-plus-circle');
                            icon.classList.add('fa-minus-circle');
                        }
                    }
                });
                collapseEl.addEventListener('hidden.bs.collapse', function() {
                    const btn = document.querySelector('[data-bs-target="#' + id + '"]');
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-minus-circle');
                            icon.classList.add('fa-plus-circle');
                        }
                    }
                });
            });
        }

        function syncDailyRowState(collapseId, isExpanded) {
            const row = document.querySelector(`.group-row[data-collapse-id="${collapseId}"]`);
            if (!row) {
                return;
            }

            row.setAttribute('aria-expanded', String(isExpanded));

            const arrow = row.querySelector('.accordion-arrow');
            if (arrow) {
                arrow.classList.toggle('open', isExpanded);
            }

            const toggleButton = row.querySelector('.toggle-details');
            if (toggleButton) {
                toggleButton.classList.toggle('active', isExpanded);

                let icon = toggleButton.querySelector('i');
                if (!icon) {
                    icon = document.createElement('i');
                    icon.setAttribute('aria-hidden', 'true');
                    toggleButton.innerHTML = '';
                    toggleButton.appendChild(icon);
                }

                icon.classList.remove('fa-plus-circle', 'fa-minus-circle');
                icon.classList.add('fa', isExpanded ? 'fa-minus-circle' : 'fa-plus-circle');
            }
        }

        function handleRowClick(event, collapseId, rowElement = null) {
            if (event.target.closest('.page-btn') || event.target.closest('.action-cell')) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const collapseElement = document.getElementById('collapse-' + collapseId);
            if (!collapseElement) {
                return;
            }

            const isExpanded = collapseElement.classList.contains('show');
            const collapseInstance = bootstrap.Collapse.getOrCreateInstance(collapseElement);

            if (isExpanded) {
                collapseInstance.hide();
            } else {
                collapseInstance.show();
            }

            const targetRow = rowElement || document.querySelector(`.group-row[data-collapse-id="${collapseId}"]`);
            if (targetRow) {
                targetRow.setAttribute('aria-expanded', String(!isExpanded));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.group-row');
            rows.forEach(row => {
                const collapseId = row.getAttribute('data-collapse-id');
                const collapseElement = document.getElementById('collapse-' + collapseId);

                if (!collapseElement) {
                    return;
                }

                syncDailyRowState(collapseId, collapseElement.classList.contains('show'));

                collapseElement.addEventListener('shown.bs.collapse', function() {
                    syncDailyRowState(collapseId, true);
                });

                collapseElement.addEventListener('hidden.bs.collapse', function() {
                    syncDailyRowState(collapseId, false);
                });
            });

            initializeMobileSectionToggles();
            window.addEventListener('resize', initializeMobileSectionToggles);
        });



        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");

            // ============================================================
            // SHARED PAYMENT MODAL - Purchase List style
            // ============================================================

            function ddFormatCurrency(amount) {
                return parseFloat(amount || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function ddParseCurrency(value) {
                if (!value) return 0;
                value = value.toString().replace(/[₹,\s]/g, '');
                var n = parseFloat(value);
                return isNaN(n) ? 0 : n;
            }

            function ddCalculatePending(remaining, paidAmount, discountAmount) {
                remaining = parseFloat(remaining) || 0;
                paidAmount = parseFloat(paidAmount) || 0;
                discountAmount = parseFloat(discountAmount) || 0;
                var pending = remaining - paidAmount - discountAmount;
                return pending < 0 ? 0 : pending;
            }

            function ddUpdatePendingAmount() {
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                var discount = parseFloat($('#ddDiscountAmount').val()) || 0;
                var paidAmount = 0;

                if ($('#ddPartialPaidFields').is(':visible')) {
                    paidAmount = ddParseCurrency($('#ddPartialAmount').val());
                } else if ($('#ddOnlineTypeSelect').val() === 'online_partially') {
                    paidAmount = ddParseCurrency($('#ddPartialAmount').val());
                } else if ($('#ddCashOnlineTypeSelect').val() === 'cash_online_partially') {
                    paidAmount = ddParseCurrency($('#ddCashOnlinePartialAmount').val()) + ddParseCurrency($(
                        '#ddUpiOnlinePartialAmount').val());
                }

                $('#ddPendingAmount').val(ddCalculatePending(remaining, paidAmount, discount).toFixed(2));
                if ($('#ddRemainingCashOnlineAmount').is(':visible')) {
                    var cashOnlinePending = ddCalculatePending(remaining, paidAmount, discount);
                    $('#ddRemainingCashOnlineAmount').val(cashOnlinePending.toFixed(2));
                }
            }

            $('#ddDiscountAmount').off('input').on('input', function() {
                ddUpdatePendingAmount();
            });

            // Focus: strip formatting on focus
            $(document).on('focus',
                '#ddPartialAmount, #ddCashAmount, #ddCashOnlineFullAmount, #ddCashOnlinePartialAmount, #ddUpiOnlinePartialAmount',
                function() {
                    var val = ddParseCurrency($(this).val());
                    $(this).val(val > 0 ? val : '');
                });

            // Open shared payment modal
            $(document).on('click', '.dd-make-payment-btn', function() {
                var cardId = $(this).data('id');
                var totalAmount = $(this).data('total-amount');
                var remaining = $(this).data('remaining-amount');
                var isEmi = parseInt($(this).data('is-emi')) || 0;
                var emiDuration = parseInt($(this).data('emi-duration')) || 12;
                var emiPaid = parseInt($(this).data('emi-paid')) || 0;

                // Populate hidden & display fields
                $('#ddJobCardId').val(cardId);
                $('#ddEditPaymentId').val('');
                $('#ddMakePaymentForm').find('button[type="submit"]').text('Submit Payment');
                $('#ddEmiTotal').text(parseFloat(totalAmount).toFixed(2));
                $('#ddRemainingAmountDisplay').text(parseFloat(remaining).toFixed(2));
                $('#ddRemainingAmountHidden').val(remaining);
                $('#ddIsEmi').val(isEmi);
                $('#ddEmiDuration').val(emiDuration);
                $('#ddEmiPaid').val(emiPaid);
                $('#ddTotalAmount').val(totalAmount);
                $('#ddDiscountAmount').val('');
                $('#ddPaymentRemark').val('');
                // Reset all dropdowns
                $('#ddPaymentMethodSelect').val('');
                $('#ddPaidTypeSelect').val('');
                $('#ddOnlineTypeSelect').val('');
                $('#ddCashOnlineTypeSelect').val('');
                $('#ddPaymentTypeSelect').val('');

                // Hide all conditional sections
                $('#ddCashOnlineTypeDiv, #ddFullyCashOnlineFields, #ddPartialCashOnlineFields, #ddOnlineTypeDiv, #ddPaidTypeDiv, #ddUpiAmountDiv, #ddPartialPaidFields, #ddFullyPaidFields')
                    .addClass('d-none');

                // Clear error messages
                $('.text-danger[id^="dd"]').text('');

                // Clear all input values
                $('#ddCashOnlineFullAmount, #ddUpiOnlineFullAmount, #ddCashOnlinePartialAmount, #ddUpiOnlinePartialAmount, #ddRemainingCashOnlineAmount')
                    .val('');
                $('#ddPartialAmount, #ddPendingAmount, #ddCashAmount, #ddUpiAmountInput').val('');

                // Hide history box
                $('#ddPaymentHistoryBox').addClass('d-none');
                $('#ddPaymentHistoryList').html('');

                // Show/hide EMI section
                if (isEmi) {
                    $('#ddPaymentMethodDiv').addClass('d-none');
                    $('#ddDiscountDiv').addClass('d-none');
                    $('#ddEmiSection').removeClass('d-none');
                    var remainingEmiMonths = emiDuration - emiPaid;
                    $('#ddEmiMonthInput').attr('max', remainingEmiMonths).val(1);
                    $('#ddEmiTotalCalculated').val('');
                    $('#ddEmiMonthDiv, #ddEmiTotalDiv').addClass('d-none');
                    $('#ddEmiMonthError').addClass('d-none');
                } else {
                    $('#ddPaymentMethodDiv').removeClass('d-none');
                    $('#ddDiscountDiv').removeClass('d-none');
                    $('#ddEmiSection').addClass('d-none');
                }
            });

            // View Payment History (inside modal)
            $('#ddViewHistoryBtn').on('click', function() {
                var cardId = $('#ddJobCardId').val();
                if (!cardId) return;
                $.ajax({
                    url: '/api/jobcard/' + cardId + '/payment-history',
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        var history = response.data || [];
                        var summary = response.summary || {};
                        if (history.length === 0) {
                            $('#ddPaymentHistoryList').html(
                                '<li>No payment history found.</li>');
                        } else {
                            var html = '';
                            history.forEach(function(payment) {
                                html +=
                                    '<li class="mb-2 d-flex justify-content-between align-items-center">' +
                                    '<div>' +
                                    '<strong>Amount:</strong> ₹' + ddFormatCurrency(
                                        payment.payment_amount) + '<br>' +
                                    '<strong>Date:</strong> ' + payment.payment_date +
                                    '<br>' +
                                    '<strong>Method:</strong> ' + payment
                                    .payment_method + '<br>' +
                                    '<strong>Payment Type:</strong> ' + (payment
                                        .payment_type || 'N/A') + '<br>' +
                                    (payment.payment_type === 'emi' ?
                                        '<strong>EMI Months:</strong> ' + (payment
                                            .emi_month || 0) + '<br>' : '') +
                                    (payment.remark ? '<strong>Remark:</strong> ' + payment.remark + '<br>' : '') +
                                    '</div>' +
                                    '<div class="d-flex align-items-center">' +
                                    ' <button type="button" class="btn btn-sm btn-outline-warning dd-edit-payment-btn me-1" ' +
                                    ' data-card-id="' + cardId + '" ' +
                                    ' data-id="' + payment.id + '" ' +
                                    ' data-amount="' + payment.payment_amount + '" ' +
                                    ' data-method="' + payment.payment_method + '" ' +
                                    ' data-type="' + (payment.payment_type || '') +
                                    '" ' +
                                    ' data-cash-amount="' + (payment.cash_amount || 0) +
                                    '" ' +
                                    ' data-upi-amount="' + (payment.upi_amount || 0) +
                                    '" ' +
                                    ' data-discount-amount="' + (payment
                                        .discount_amount || 0) + '" ' +
                                    ' data-emi-month="' + (payment.emi_month || 0) +
                                    '" ' +
                                    ' data-card-remaining="' + (summary.remaining ||
                                    0) + '" ' +
                                    ' data-card-total="' + (summary.order_total || 0) +
                                    '" ' +
                                    ' data-remark="' + (payment.remark || '') + '" ' +
                                    ' title="Edit Payment"><i class="fas fa-edit"></i></button>' +
                                    ' <button type="button" class="btn btn-sm btn-outline-danger dd-delete-payment-btn" data-id="' +
                                    payment.id +
                                    '" title="Delete Payment"><i class="fas fa-trash"></i></button>' +
                                    '</div>' +
                                    '</li><hr class="my-1"/>';
                            });
                            $('#ddPaymentHistoryList').html(html);
                        }
                        $('#ddPaymentHistoryBox').removeClass('d-none');
                    },
                    error: function() {
                        $('#ddPaymentHistoryList').html(
                            '<li class="text-danger">Failed to load payment history.</li>');
                        $('#ddPaymentHistoryBox').removeClass('d-none');
                    }
                });
            });

            // Close inline history
            $('#ddCloseHistoryBtn').on('click', function() {
                $('#ddPaymentHistoryBox').addClass('d-none');
            });

            // Payment Method change
            $('#ddPaymentMethodSelect').on('change', function() {
                var method = $(this).val();
                $('#ddCashOnlineTypeDiv, #ddFullyCashOnlineFields, #ddPartialCashOnlineFields, #ddOnlineTypeDiv, #ddPaidTypeDiv, #ddUpiAmountDiv, #ddPartialPaidFields, #ddFullyPaidFields')
                    .addClass('d-none');

                if (method === 'cash') {
                    $('#ddPaidTypeDiv').removeClass('d-none');
                } else if (method === 'online') {
                    $('#ddOnlineTypeDiv').removeClass('d-none');
                } else if (method === 'cash_online') {
                    $('#ddCashOnlineTypeDiv').removeClass('d-none');
                }
            });

            // Paid Type change (Cash)
            $('#ddPaidTypeSelect').on('change', function() {
                var type = $(this).val();
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                $('#ddPartialPaidFields, #ddFullyPaidFields').addClass('d-none');
                $('#ddPartialPaidFields input, #ddFullyPaidFields input').prop('disabled', true);

                if (type === 'cash_partially') {
                    $('#ddPartialPaidFields').removeClass('d-none');
                    $('#ddPartialPaidFields input').prop('disabled', false);
                    $('#ddPartialAmount').val('');
                    $('#ddPendingAmount').val(ddCalculatePending(remaining, 0, $('#ddDiscountAmount')
                    .val() || 0).toFixed(2));
                    $('#ddCashAmount').val('').prop('readonly', false).prop('disabled', true);

                    $('#ddPartialAmount').off('input').on('input', function() {
                        ddUpdatePendingAmount();
                    });
                } else if (type === 'cash_fully') {
                    $('#ddFullyPaidFields').removeClass('d-none');
                    $('#ddFullyPaidFields input').prop('disabled', false);
                    $('#ddCashAmount').val(remaining.toFixed(2)).prop('readonly', true);
                    $('#ddPartialAmount, #ddPendingAmount').val('');
                    $('#ddPartialPaidFields input').prop('disabled', true);
                }
            });

            // Online Type change
            $('#ddOnlineTypeSelect').on('change', function() {
                var type = $(this).val();
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                $('#ddUpiAmountDiv, #ddPartialPaidFields').addClass('d-none');
                $('#ddUpiAmountDiv input, #ddPartialPaidFields input').prop('disabled', true);

                if (type === 'online_partially') {
                    $('#ddPartialPaidFields').removeClass('d-none');
                    $('#ddPartialPaidFields input').prop('disabled', false);
                    $('#ddPartialAmount').val('');
                    $('#ddPendingAmount').val(ddCalculatePending(remaining, 0, $('#ddDiscountAmount')
                    .val() || 0).toFixed(2));
                    $('#ddUpiAmountInput').val('').prop('readonly', false).prop('disabled', true);

                    $('#ddPartialAmount').off('input').on('input', function() {
                        ddUpdatePendingAmount();
                    });
                } else if (type === 'online_fully') {
                    $('#ddUpiAmountDiv').removeClass('d-none');
                    $('#ddUpiAmountDiv input').prop('disabled', false);
                    $('#ddUpiAmountInput').val(remaining.toFixed(2)).prop('readonly', true);
                    $('#ddPartialAmount, #ddPendingAmount').val('');
                    $('#ddPartialPaidFields input').prop('disabled', true);
                }
            });

            // Cash + Online Type change
            $('#ddCashOnlineTypeSelect').on('change', function() {
                var type = $(this).val();
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                $('#ddFullyCashOnlineFields, #ddPartialCashOnlineFields').addClass('d-none');
                $('#ddFullyCashOnlineFields input, #ddPartialCashOnlineFields input').prop('disabled',
                true);

                if (type === 'cash_online_fully') {
                    $('#ddFullyCashOnlineFields').removeClass('d-none');
                    $('#ddFullyCashOnlineFields input').prop('disabled', false);
                    $('#ddCashOnlineFullAmount').val('');
                    $('#ddUpiOnlineFullAmount').val(remaining.toFixed(2));

                    $('#ddCashOnlineFullAmount').off('input').on('input', function() {
                        var cash = ddParseCurrency($(this).val()) || 0;
                        $('#ddUpiOnlineFullAmount').val(ddFormatCurrency(Math.max(remaining - cash,
                            0)));
                    });
                    $('#ddPartialCashOnlineFields input').prop('disabled', true);

                } else if (type === 'cash_online_partially') {
                    $('#ddPartialCashOnlineFields').removeClass('d-none');
                    $('#ddPartialCashOnlineFields input').prop('disabled', false);
                    $('#ddCashOnlinePartialAmount, #ddUpiOnlinePartialAmount').val('');
                    $('#ddRemainingCashOnlineAmount').val(ddCalculatePending(remaining, 0, $(
                        '#ddDiscountAmount').val() || 0).toFixed(2));

                    $('#ddCashOnlinePartialAmount').off('input').on('input', function() {
                        ddUpdatePendingAmount();
                    });
                    $('#ddUpiOnlinePartialAmount').off('input').on('input', function() {
                        ddUpdatePendingAmount();
                    });
                    $('#ddFullyCashOnlineFields input').prop('disabled', true);
                }
            });

            // EMI Payment Type change
            $('#ddPaymentTypeSelect').on('change', function() {
                var type = $(this).val();
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                var emiDuration = parseInt($('#ddEmiDuration').val()) || 12;
                var emiPaid = parseInt($('#ddEmiPaid').val()) || 0;
                var totalAmount = parseFloat($('#ddTotalAmount').val()) || 0;
                var remainingEmiMonths = emiDuration - emiPaid;
                var emiAmount = emiDuration > 0 ? (totalAmount / emiDuration) : 0;

                $('#ddEmiMonthDiv, #ddEmiTotalDiv').addClass('d-none');
                $('#ddEmiMonthError').addClass('d-none');

                if (type === 'emi') {
                    $('#ddEmiMonthDiv, #ddEmiTotalDiv').removeClass('d-none');
                    $('#ddEmiMonthInput').val(1);
                    $('#ddEmiTotalCalculated').val((emiAmount * 1).toFixed(2));

                    $('#ddEmiMonthInput').off('input').on('input', function() {
                        var months = parseInt($(this).val()) || 1;
                        if (months < 1) {
                            months = 1;
                            $(this).val(1);
                        }
                        if (months > remainingEmiMonths) {
                            months = remainingEmiMonths;
                            $('#ddEmiMonthError').removeClass('d-none').text(
                                'Cannot pay more than ' + remainingEmiMonths + ' month(s).');
                        } else {
                            $('#ddEmiMonthError').addClass('d-none').text('');
                        }
                        $(this).val(months);
                        $('#ddEmiTotalCalculated').val((emiAmount * months).toFixed(2));
                    });
                }
            });

            // Reset modal on close
            $('#ddMakePaymentModal').on('hidden.bs.modal', function() {
                $('#ddMakePaymentForm')[0].reset();
                $('#ddCashOnlineTypeDiv, #ddFullyCashOnlineFields, #ddPartialCashOnlineFields, #ddOnlineTypeDiv, #ddPaidTypeDiv, #ddUpiAmountDiv, #ddPartialPaidFields, #ddFullyPaidFields')
                    .addClass('d-none');
                $('#ddEmiSection, #ddEmiMonthDiv, #ddEmiTotalDiv').addClass('d-none');
                $('.text-danger[id^="dd"]').text('');
                $('#ddPaymentHistoryBox').addClass('d-none');
                $('#ddPaymentHistoryList').html('');
                $('#ddCashOnlineFullAmount, #ddUpiOnlineFullAmount, #ddCashOnlinePartialAmount, #ddUpiOnlinePartialAmount, #ddRemainingCashOnlineAmount')
                    .val('');
                $('#ddPartialAmount, #ddPendingAmount, #ddCashAmount, #ddUpiAmountInput').val('');
                $('#ddPaymentMethodSelect').val('');
                $('#ddCashOnlineTypeSelect, #ddOnlineTypeSelect, #ddPaidTypeSelect, #ddPaymentTypeSelect')
                    .val('');
                $('#ddEmiTotal').text('0.00');
                $('#ddRemainingAmountDisplay').text('0.00');
                $('#ddJobCardId').val('');
                $('#ddRemainingAmountHidden').val('');
                $('#ddPaymentMethodDiv').removeClass('d-none');
                $('#ddDiscountDiv').removeClass('d-none');
            });

            // Form submit
            $('#ddMakePaymentForm').on('submit', function(e) {
                e.preventDefault();

                var isValid = true;
                $('.text-danger[id^="dd"]').text('');

                var isEmi = parseInt($('#ddIsEmi').val()) || 0;
                var remaining = parseFloat($('#ddRemainingAmountHidden').val()) || 0;
                var cardId = $('#ddJobCardId').val();
                var paymentMode = '';
                var paymentAmount = 0;
                var emiMonths = 0;
                var discountAmount = parseFloat($('#ddDiscountAmount').val()) || 0;
                // Non-EMI variables hoisted for later use
                var method = '';
                var cashOnlineType = '';

                if (isEmi) {
                    // EMI card
                    var emiType = $('#ddPaymentTypeSelect').val();
                    var emiDuration = parseInt($('#ddEmiDuration').val()) || 12;
                    var emiPaid = parseInt($('#ddEmiPaid').val()) || 0;
                    var totalAmount = parseFloat($('#ddTotalAmount').val()) || 0;
                    var remainingEmiMonths = emiDuration - emiPaid;

                    if (!emiType) {
                        isValid = false;
                        $('#ddPaymentMethodError').text('Please select payment type.');
                        return false;
                    }
                    if (emiType === 'emi') {
                        emiMonths = parseInt($('#ddEmiMonthInput').val()) || 1;
                        paymentAmount = parseFloat($('#ddEmiTotalCalculated').val()) || 0;
                        paymentMode = 'emi';
                    } else if (emiType === 'fully') {
                        emiMonths = remainingEmiMonths;
                        paymentAmount = remaining;
                        paymentMode = 'emifully';
                    }
                } else {
                    // Non-EMI card
                    method = $('#ddPaymentMethodSelect').val();
                    var paidType = $('#ddPaidTypeSelect').val();
                    var onlineType = $('#ddOnlineTypeSelect').val();
                    cashOnlineType = $('#ddCashOnlineTypeSelect').val();

                    if (!method) {
                        isValid = false;
                        $('#ddPaymentMethodError').text('Please select a payment method.');
                        return false;
                    }

                    if (method === 'cash') {
                        if (!paidType) {
                            isValid = false;
                            $('#ddPaidTypeError').text('Please select paid type.');
                            return false;
                        }
                        if (paidType === 'cash_partially') {
                            paymentAmount = ddParseCurrency($('#ddPartialAmount').val()) || 0;
                            if (!paymentAmount || paymentAmount <= 0) {
                                isValid = false;
                                $('#ddPartialAmountError').text(
                                    'Enter a valid positive partial cash amount.');
                                return false;
                            }
                            if (paymentAmount > remaining) {
                                isValid = false;
                                $('#ddPartialAmountError').text(
                                    'Partial cash amount cannot exceed remaining amount (' + remaining
                                    .toFixed(2) + ').');
                                return false;
                            }
                        } else {
                            paymentAmount = ddParseCurrency($('#ddCashAmount').val()) || 0;
                            if (!paymentAmount || paymentAmount <= 0) {
                                isValid = false;
                                $('#ddCashAmountError').text('Enter a valid cash amount.');
                                return false;
                            }
                        }
                        paymentMode = method;
                    }

                    if (method === 'online') {
                        if (!onlineType) {
                            isValid = false;
                            $('#ddOnlineTypeError').text('Please select online type.');
                            return false;
                        }
                        var onlineAmount = onlineType === 'online_partially' ?
                            ddParseCurrency($('#ddPartialAmount').val()) :
                            ddParseCurrency($('#ddUpiAmountInput').val());

                        if (!onlineAmount || onlineAmount <= 0) {
                            isValid = false;
                            if (onlineType === 'online_partially') {
                                $('#ddPartialAmountError').text(
                                    'Enter a valid positive online partial amount.');
                            } else {
                                $('#ddUpiAmountError').text('Enter a valid positive online amount.');
                            }
                            return false;
                        }
                        if (onlineType === 'online_partially' && onlineAmount > remaining) {
                            isValid = false;
                            $('#ddPartialAmountError').text(
                                'Partial online amount cannot exceed remaining amount (' + remaining
                                .toFixed(2) + ').');
                            return false;
                        }
                        paymentAmount = onlineAmount;
                        paymentMode = method;
                    }

                    if (method === 'cash_online') {
                        if (!cashOnlineType) {
                            isValid = false;
                            $('#ddCashOnlineTypeError').text('Please select Cash + Online type.');
                            return false;
                        }
                        if (cashOnlineType === 'cash_online_fully') {
                            var cashAmt = ddParseCurrency($('#ddCashOnlineFullAmount').val()) || 0;
                            var onlineAmt = ddParseCurrency($('#ddUpiOnlineFullAmount').val()) || 0;
                            if (!cashAmt || cashAmt <= 0 || !onlineAmt || onlineAmt <= 0) {
                                isValid = false;
                                $('#ddCashOnlineFullAmountError').text('Enter a valid cash amount.');
                                $('#ddUpiOnlineFullAmountError').text('Enter a valid online amount.');
                                return false;
                            }
                            paymentAmount = cashAmt + onlineAmt;
                        }
                        if (cashOnlineType === 'cash_online_partially') {
                            var cashAmt = ddParseCurrency($('#ddCashOnlinePartialAmount').val()) || 0;
                            var onlineAmt = ddParseCurrency($('#ddUpiOnlinePartialAmount').val()) || 0;
                            var pendingAmt = ddParseCurrency($('#ddRemainingCashOnlineAmount').val()) ||
                                remaining;

                            if (cashAmt <= 0 && onlineAmt <= 0) {
                                isValid = false;
                                $('#ddCashOnlinePartialAmountError').text(
                                    'Enter at least one valid amount.');
                                $('#ddUpiOnlinePartialAmountError').text(
                                'Enter at least one valid amount.');
                                return false;
                            }
                            if (cashAmt < 0 || onlineAmt < 0) {
                                isValid = false;
                                $('#ddCashOnlinePartialAmountError').text('Negative amount not allowed.');
                                return false;
                            }
                            if ((cashAmt + onlineAmt) > remaining) {
                                isValid = false;
                                $('#ddCashOnlinePartialAmountError').text(
                                    'Total payment cannot exceed remaining amount (' + remaining
                                    .toFixed(2) + ').');
                                return false;
                            }
                            paymentAmount = cashAmt + onlineAmt;
                        }
                        paymentMode = method;
                    }
                }

                if (!isValid) return false;

                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Processing...');

                var formData = {
                    payment_amount: paymentAmount,
                    payment_mode: paymentMode,
                    payment_method: paymentMode,
                    emi_months: emiMonths,
                    discount_amount: discountAmount,
                    remark: $('#ddPaymentRemark').val(),
                    _token: '{{ csrf_token() }}'
                };

                // Pass cash/online split for cash_online payment
                if (!isEmi && method === 'cash_online') {
                    if (cashOnlineType === 'cash_online_fully') {
                        formData.fully_cash_amount = ddParseCurrency($('#ddCashOnlineFullAmount').val()) ||
                            0;
                        formData.full_online_amount = ddParseCurrency($('#ddUpiOnlineFullAmount').val()) ||
                            0;
                    } else if (cashOnlineType === 'cash_online_partially') {
                        formData.cash_amount = ddParseCurrency($('#ddCashOnlinePartialAmount').val()) || 0;
                        formData.online_amount = ddParseCurrency($('#ddUpiOnlinePartialAmount').val()) || 0;
                    }
                }

                var editPaymentId = $('#ddEditPaymentId').val();
                var ajaxUrl = editPaymentId ?
                    '/api/payment-history/' + editPaymentId + '/update' :
                    '/api/jobcard/' + cardId + '/make-payment';

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function() {
                        $('#ddMakePaymentModal').modal('hide');
                        submitButton.prop('disabled', false).text(editPaymentId ?
                            'Save Changes' : 'Submit Payment');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: editPaymentId ? 'Payment updated successfully.' :
                                'Payment submitted successfully.',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false).text(editPaymentId ?
                            'Save Changes' : 'Submit Payment');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            });

            // Global payment history modal (history icon buttons)
            $(document).on('click', '.dd-open-history', function() {
                var cardId = $(this).data('id');
                $('#ddGlobalPaymentHistoryList').html('<li class="list-group-item">Loading...</li>');
                $.ajax({
                    url: '/api/jobcard/' + cardId + '/payment-history',
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        var history = response.data || [];
                        var summary = response.summary || {};
                        var html = '';

                        if (history.length === 0) {
                            html = '<li class="list-group-item">No payment history found.</li>';
                        } else {
                            html = history.map(function(p) {
                                return '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<div>' +
                                    '<strong>' + (p.payment_date || p.created_at) +
                                    '</strong><br>' +
                                    '<small class="text-muted">Method: ' + (p
                                        .payment_method || '-') + (p.emi_month ? ' – ' +
                                        p.emi_month + ' month(s)' : '') + '</small>' +
                                    '</div>' +
                                    '<div class="d-flex align-items-center">' +
                                    '<strong class="me-3">₹' + ddFormatCurrency(p
                                        .payment_amount) + '</strong>' +
                                    '<button type="button" class="btn btn-sm btn-outline-warning dd-edit-payment-btn me-1" ' +
                                    ' data-card-id="' + cardId + '" ' +
                                    ' data-id="' + p.id + '" ' +
                                    ' data-amount="' + p.payment_amount + '" ' +
                                    ' data-method="' + p.payment_method + '" ' +
                                    ' data-type="' + (p.payment_type || '') + '" ' +
                                    ' data-cash-amount="' + (p.cash_amount || 0) +
                                    '" ' +
                                    ' data-upi-amount="' + (p.upi_amount || 0) + '" ' +
                                    ' data-discount-amount="' + (p.discount_amount ||
                                    0) + '" ' +
                                    ' data-emi-month="' + (p.emi_month || 0) + '" ' +
                                    ' data-card-remaining="' + (summary.remaining ||
                                    0) + '" ' +
                                    ' data-card-total="' + (summary.order_total || 0) +
                                    '" ' +
                                    ' data-remark="' + (p.remark || '') + '" ' +
                                    ' title="Edit Payment"><i class="fas fa-edit"></i></button>' +
                                    '<button type="button" class="btn btn-sm btn-outline-danger dd-delete-payment-btn" data-id="' +
                                    p.id +
                                    '" title="Delete Payment"><i class="fas fa-trash"></i></button>' +
                                    '</div>' +
                                    '</li>';
                            }).join('');
                        }

                        if (summary && summary.order_total !== undefined) {
                            html +=
                                '<li class="list-group-item mt-2 bg-light"><strong>Total:</strong> ₹' +
                                ddFormatCurrency(summary.order_total) + '</li>';
                            html +=
                                '<li class="list-group-item bg-light"><strong>Total Paid:</strong> ₹' +
                                ddFormatCurrency(summary.total_paid) + '</li>';
                            if (parseFloat(summary.discount) > 0) {
                                html +=
                                    '<li class="list-group-item bg-light"><strong>Discount:</strong> ₹' +
                                    ddFormatCurrency(summary.discount) + '</li>';
                            }
                            if (parseFloat(summary.extra_paid) > 0) {
                                html +=
                                    '<li class="list-group-item bg-warning"><strong>Extra Paid:</strong> ₹' +
                                    ddFormatCurrency(summary.extra_paid) +
                                    ' <span class="text-danger">(Advance / Refund)</span></li>';
                            } else {
                                html +=
                                    '<li class="list-group-item bg-light"><strong>Remaining:</strong> ₹' +
                                    ddFormatCurrency(summary.remaining) + '</li>';
                            }
                        }

                        $('#ddGlobalPaymentHistoryList').html(html);
                        new bootstrap.Modal(document.getElementById('ddPaymentHistoryModal'))
                            .show();
                    },
                    error: function() {
                        $('#ddGlobalPaymentHistoryList').html(
                            '<li class="list-group-item text-danger">Failed to load payment history.</li>'
                            );
                        new bootstrap.Modal(document.getElementById('ddPaymentHistoryModal'))
                            .show();
                    }
                });
            });
        });


        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            $('.delete-jobcard').click(function(e) {
                e.preventDefault();

                let button = $(this);
                let id = button.data('id');

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the record.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/daily-data/' + id + '/delete',
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                // Show success SweetAlert
                                Swal.fire(
                                    'Deleted!',
                                    'The record has been deleted.',
                                    'success'
                                );

                                // Optionally remove the row
                                // button.closest('tr').remove();
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

        $(document).ready(function() {
            // Handle All Records tab click
            $("#all-records-tab").click(function(e) {
                e.preventDefault();

                // Toggle tab active state
                $(this).addClass("active");
                $("#search-daily-tab").removeClass("active");

                // Hide all tab contents
                $(".tab-pane").removeClass("show active");

                // Show only All Records content
                $("#all-records-content").addClass("show active");
            });

            // Handle Search Daily Data tab click
            $("#search-daily-tab").click(function(e) {
                e.preventDefault();

                // Toggle tab active state
                $(this).addClass("active");
                $("#all-records-tab").removeClass("active");

                // Hide all tab contents
                $(".tab-pane").removeClass("show active");

                // Show only Search content
                $("#search-daily-content").addClass("show active");

                // Optional: clear previous results if needed
                // $("#search-daily-content").html("");
            });
        });

        $(document).ready(function() {
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            // Get current month (yyyy-mm)
            const now = new Date();
            const currentMonth = now.toISOString().slice(0, 7); // e.g. "2025-08"
            $("#filter-month123").val(currentMonth); // set default value

            // Function to fetch technicians
            function fetchTechnicians(month) {
                $.ajax({
                    url: "/api/get-top-technicians",
                    type: "GET",
                    data: {
                        month: month,
                        branch_id: selectedSubAdminId
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    beforeSend: function() {
                        $("#technicians-result").html("<p>Loading...</p>");
                    },
                    success: function(response) {
                        let html = "<ul class='list-group'>";
                        if (response.length > 0) {
                            response.forEach((item, index) => {
                                let rankBadge = "";
                                let rowClass = "";

                                if (index === 0) {
                                    rankBadge =
                                    `<span class="badge text-dark me-2">🥇 1</span>`;

                                } else if (index === 1) {
                                    rankBadge =
                                    `<span class="badge text-dark me-2">🥈 2</span>`;
                                    rowClass = "list-group-item-light";
                                } else if (index === 2) {
                                    rankBadge =
                                    `<span class="badge text-dark me-2">🥉 3</span>`;
                                    rowClass = "list-group-item-light";
                                }

                                html += `<li class="list-group-item d-flex justify-content-between align-items-center ${rowClass}">
                                                                <div class="fw-semibold">${rankBadge} ${item.technician_name}</div>
                                                                <div class="fw-bold text-success">₹ ${parseFloat(item.total_amount).toFixed(2)}</div>
                                                            </li>`;
                            });
                        } else {
                            html =
                                "<p class='text-center text-muted small'>No data found for this branch & month.</p>";
                        }
                        html += "</ul>";
                        $("#technicians-result").html(html);
                    },
                    error: function() {
                        $("#technicians-result").html("<p class='text-danger'>Error loading data.</p>");
                    }
                });
            }

            // Open modal → auto load this month
            $("#top-technicians-btn").on("click", function() {
                $("#topTechniciansModal").modal("show");

                // fetch immediately with current month
                fetchTechnicians($("#filter-month123").val());
            });

            // Fetch again on month change
            $("#filter-month123").on("change", function() {
                fetchTechnicians($(this).val());
            });

            // Edit payment history button click
            $(document).on('click', '.dd-edit-payment-btn', function() {
                // Hide payment history modals first
                $('#ddPaymentHistoryModal').modal('hide');
                $('#ddPaymentHistoryBox').addClass('d-none');

                var cardId = $(this).data('card-id');
                var paymentId = $(this).data('id');
                var amount = parseFloat($(this).data('amount')) || 0;
                var method = $(this).data('method');
                var paymentType = $(this).data('type');
                var cashAmount = parseFloat($(this).data('cash-amount')) || 0;
                var upiAmount = parseFloat($(this).data('upi-amount')) || 0;
                var discountAmount = parseFloat($(this).data('discount-amount')) || 0;
                var emiMonth = parseInt($(this).data('emi-month')) || 0;
                var remark = $(this).data('remark') || '';

                var cardRemaining = parseFloat($(this).data('card-remaining')) || 0;
                var cardTotal = parseFloat($(this).data('card-total')) || 0;

                // Base remaining amount before this payment was made
                var baseRemaining = cardRemaining + amount + discountAmount;

                // Populate fields in ddMakePaymentModal
                $('#ddJobCardId').val(cardId);
                $('#ddEditPaymentId').val(paymentId);

                $('#ddEmiTotal').text(cardTotal.toFixed(2));
                $('#ddRemainingAmountDisplay').text(baseRemaining.toFixed(2));
                $('#ddRemainingAmountHidden').val(baseRemaining);
                $('#ddTotalAmount').val(cardTotal);
                $('#ddDiscountAmount').val(discountAmount > 0 ? discountAmount : '');
                $('#ddPaymentRemark').val(remark);

                // Reset all dropdowns
                $('#ddPaymentMethodSelect').val('');
                $('#ddPaidTypeSelect').val('');
                $('#ddOnlineTypeSelect').val('');
                $('#ddCashOnlineTypeSelect').val('');

                // Hide conditional sections
                $('#ddCashOnlineTypeDiv, #ddFullyCashOnlineFields, #ddPartialCashOnlineFields, #ddOnlineTypeDiv, #ddPaidTypeDiv, #ddUpiAmountDiv, #ddPartialPaidFields, #ddFullyPaidFields')
                    .addClass('d-none');
                $('.text-danger[id^="dd"]').text('');
                $('#ddCashOnlineFullAmount, #ddUpiOnlineFullAmount, #ddCashOnlinePartialAmount, #ddUpiOnlinePartialAmount, #ddRemainingCashOnlineAmount')
                    .val('');
                $('#ddPartialAmount, #ddPendingAmount, #ddCashAmount, #ddUpiAmountInput').val('');

                // Populate Payment Method
                $('#ddPaymentMethodSelect').val(method).trigger('change');

                // Populate conditional fields
                if (method === 'cash') {
                    if (paymentType === 'fully') {
                        $('#ddPaidTypeSelect').val('cash_fully').trigger('change');
                    } else {
                        $('#ddPaidTypeSelect').val('cash_partially').trigger('change');
                        $('#ddPartialAmount').val(amount).trigger('input');
                    }
                } else if (method === 'online') {
                    if (paymentType === 'fully') {
                        $('#ddOnlineTypeSelect').val('online_fully').trigger('change');
                    } else {
                        $('#ddOnlineTypeSelect').val('online_partially').trigger('change');
                        $('#ddPartialAmount').val(amount).trigger('input');
                    }
                } else if (method === 'cash_online') {
                    if (paymentType === 'fully') {
                        $('#ddCashOnlineTypeSelect').val('cash_online_fully').trigger('change');
                        $('#ddCashOnlineFullAmount').val(cashAmount);
                        $('#ddUpiOnlineFullAmount').val(upiAmount);
                    } else {
                        $('#ddCashOnlineTypeSelect').val('cash_online_partially').trigger('change');
                        $('#ddCashOnlinePartialAmount').val(cashAmount);
                        $('#ddUpiOnlinePartialAmount').val(upiAmount).trigger('input');
                    }
                } else if (method === 'emi') {
                    $('#ddEmiMonthInput').val(emiMonth);
                }

                // Change submit button text to Save Changes
                $('#ddMakePaymentForm').find('button[type="submit"]').text('Save Changes');

                // Open Make Payment Modal
                var makePaymentModal = new bootstrap.Modal(document.getElementById('ddMakePaymentModal'));
                makePaymentModal.show();
            });

            // Handle Delete Payment Button
            $(document).on('click', '.dd-delete-payment-btn', function() {
                var paymentId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete this payment? The remaining amount on the jobcard will be increased.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/payment-history/' + paymentId + '/delete',
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + authToken
                            },
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'The payment has been deleted.',
                                    'success'
                                ).then(function() {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete payment.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
