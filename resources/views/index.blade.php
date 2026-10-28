@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $role = auth()->user()->role ?? '';
        $currentYear = \Carbon\Carbon::now()->year;
        $previousYear = $currentYear - 1;
    @endphp
    <style>
        .color_box {
            display: block;
            width: 100%;
            text-decoration: none;
            color: inherit;
        }

        h5.mb-3.text-dark.fw-bold {
            font-size: 14px;
        }

        h5#todayAppointmentsLabel {
            font-size: 16px;
        }

        .filterclass {
            max-width: 250px;
        }

        /* Transform solid color dashboard boxes to white cards with colored text/icons */
        .color_box .dash-count {
            background: #ffffff !important;
            border: 1px solid #f0f0f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
        }

        .color_box .dash-counts h4 {
            color: #1f2937 !important;
            font-weight: 700;
        }

        .color_box .dash-counts h5 {
            color: #667085 !important;
            font-weight: 600;
        }

        .color_box .dash-imgs {
            background-color: transparent !important;
        }

        /* Assign icon colors based on their original box classes */
        .color_box.box1 .dash-imgs > i, .color_box.box1 .dash-imgs > svg { stroke: #28c76f !important; color: #28c76f !important; } /* Green */
        .color_box.box2 .dash-imgs > i, .color_box.box2 .dash-imgs > svg { stroke: #1e1e2d !important; color: #1e1e2d !important; } /* Dark Blue */
        .color_box.box3 .dash-imgs > i, .color_box.box3 .dash-imgs > svg { stroke: #ff9f43 !important; color: #ff9f43 !important; } /* Orange */
        .color_box.box4 .dash-imgs > i, .color_box.box4 .dash-imgs > svg { stroke: #00cfe8 !important; color: #00cfe8 !important; } /* Light Blue */

        /* Make icons smaller to match the reference design */
        .color_box .dash-imgs > i {
            font-size: 24px !important;
        }
        .color_box .dash-imgs > svg {
            width: 24px !important;
            height: 24px !important;
        }

        /* Dashboard widget styles */
        .dash-widget {
            transition: all 0.3s ease;
            border-radius: 8px !important;
        }

        .dash-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Tablet view (768px - 1400px) */
        @media screen and (min-width: 768px) and (max-width: 1400px) {
            .dash-widget {
                padding: 15px !important;
            }

            .dash-widgetimg span img {
                width: 40px !important;
                height: 36px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
                line-height: 1.2 !important;
            }

            .dash-widgetcontent h5 {
                font-size: 10.5px !important;
                margin-bottom: 5px !important;
            }

            .dash-widget {
                min-height: 90px !important;
                margin: 0 0 15px !important;
            }

            /* Ensure proper alignment in tablet */
            .col-lg-4.col-sm-6.col-4 {
                padding-left: 7.5px !important;
                padding-right: 7.5px !important;
            }
        }

        /* Mobile view (max-width: 767px) */
        @media screen and (max-width: 767px) {
            .filterclass {
                max-width: 110px;
            }

            .col-md-6:has(.latest-sales-table) {
                margin-bottom: 20px;
            }

            .dash-widget.dash1 {
                height: 136px;
            }

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 15px;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count .dash-imgs svg {
                width: 37px !important;
                height: 37px !important;
            }

            .dash-count h4 {
                font-size: 18px !important;
            }

            /* Dashboard widgets container */
            .dashboard-widgets-row {
                margin-left: -5px !important;
                margin-right: -5px !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }

            /* First row (Purchase, Expense, Sales) - 3 in one row */
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child,
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2),
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) {
                width: 33.333% !important; /* Equal width for 3 widgets */
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 10px !important;
                float: left !important;
            }

            /* Remaining widgets (Invoice counts, Vendors, Customers) - 2 per row */
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(4),
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(5),
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(6),
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(7) {
                width: 50% !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 10px !important;
                float: left !important;
            }

            /* Clear floats */
            .dashboard-widgets-row::after {
                content: "";
                display: table;
                clear: both;
            }

            /* Dashboard widget styling */
            .dash-widget {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                padding: 12px 5px !important;
                margin-bottom: 0 !important;
                height: 110px !important; /* Slightly shorter for 3 in a row */
                justify-content: center !important;
            }

            .dash-widgetimg {
                margin-bottom: 6px !important;
            }

            .dash-widgetimg span img {
                width: 30px !important;
                height: 30px !important;
            }

            .dash-widgetcontent {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 3px !important;
            }

            .dash-widgetcontent h5 {
                margin-top: 0.2rem !important;
                margin-bottom: 0.2rem !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
                word-break: break-word !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
                line-height: 1.2 !important;
                margin-bottom: 0 !important;
                color: #6c757d !important;
            }

            /* Currency symbol and amount styling */
            .dash-widgetcontent h5 .counters {
                font-size: 12px !important;
                font-weight: 600 !important;
            }

            /* Adjust for first row (financial amounts) */
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h5,
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h5,
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h5 {
                font-size: 11px !important; /* Slightly smaller for amounts */
            }

            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h6,
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h6,
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h6 {
                font-size: 9px !important; /* Slightly smaller labels */
            }

            /* Adjust for count widgets (4th onwards) */
            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h5 {
                font-size: 14px !important; /* Larger for counts */
            }

            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h6 {
                font-size: 11px !important;
            }

            .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widget {
                height: 100px !important; /* Slightly shorter for count widgets */
            }

            .table-responsive {
                font-size: 12px !important;
            }
        }

        /* ── Toggle button shared styles ── */
        .toggle-details {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ff9f43;
            cursor: pointer;
        }

        .toggle-details i {
            color: #fff;
            font-size: 12px;
            line-height: 1;
        }

        .toggle-details.is-open {
            background: #dc3545;
        }

        /* ── Desktop (≥768px): hide Action column on Job Card table,
               hide Details column on Daily Data table ── */
        @media (min-width: 768px) {
            /* Latest Job Card – hide Action column */
            .latest-sales-table thead th.action-column,
            .latest-sales-table tbody td.action-cell {
                display: none !important;
            }

            /* Latest Job Card – always hide expanded details rows on desktop */
            .latest-sales-table tr.sales-details-row {
                display: none !important;
            }

            /* Latest Daily Data – hide Details column */
            .latest-purchases-table thead th.details-column,
            .latest-purchases-table tbody td.details-cell {
                display: none !important;
            }

            /* Latest Daily Data – always hide expanded details rows on desktop */
            .latest-purchases-table tr.purchase-details-row {
                display: none !important;
            }

            /* Desktop widget spacing */
            .col-lg-4.col-sm-6.col-4 {
                /* margin-bottom: 25px !important; */
            }
        }

        /* ── Mobile (<768px): collapse Job Card columns, keep Details toggle ── */
        @media (max-width: 767px) {
            /* Job Card: hide Vehicle, Customer, Grand Total columns */
            .latest-sales-table thead th:nth-child(2),
            .latest-sales-table tbody td:nth-child(2),
            .latest-sales-table thead th:nth-child(3),
            .latest-sales-table tbody td:nth-child(3),
            .latest-sales-table thead th:nth-child(4),
            .latest-sales-table tbody td:nth-child(4) {
                display: none !important;
            }

            /* Job Card: show Action column on mobile */
            .latest-sales-table thead th.action-column,
            .latest-sales-table tbody td.action-cell {
                display: table-cell !important;
                width: 56px;
                min-width: 56px;
                max-width: 56px;
                text-align: center;
                white-space: nowrap;
                padding-left: 6px !important;
                padding-right: 6px !important;
            }

            .latest-sales-table {
                table-layout: auto;
                width: 100%;
            }

            .latest-sales-table th,
            .latest-sales-table td {
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0 !important;
            }

            /* Job Card expanded details row */
            .latest-sales-table .collapse-content {
                margin-top: 6px;
                padding: 0;
                background: transparent;
                border: 0;
            }

            .latest-sales-table tr.sales-details-row td {
                padding-top: 0 !important;
                width: 100% !important;
                max-width: none !important;
                white-space: normal;
            }

            /* Daily Data: hide Job Done By and Work Amount columns on mobile */
            .latest-purchases-table thead th:nth-child(4),
            .latest-purchases-table tbody td:nth-child(4),
            .latest-purchases-table thead th:nth-child(5),
            .latest-purchases-table tbody td:nth-child(5) {
                display: none !important;
            }

            /* Daily Data: show Details column on mobile */
            .latest-purchases-table thead th.details-column,
            .latest-purchases-table tbody td.details-cell {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 56px !important;
                min-width: 56px !important;
                max-width: 56px !important;
            }

            /* Daily Data expanded details */
            .latest-purchases-table .collapse-content {
                margin-top: 8px;
                padding: 0;
                background: transparent;
                border: 0;
                box-shadow: none;
                border-radius: 0;
            }

            table.table tbody td:first-child {
                display: table-cell !important;
            }

            .collapse-content {
                margin-top: 10px;
                padding: 10px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 4px;
            }
        }

        .latest-purchases-table td:nth-child(2),
        .latest-purchases-table th:nth-child(2) {
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        /* ── iPad Mini / tablet range (768px–1024px): fix Daily Data table layout ── */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .latest-purchases-table {
                table-layout: fixed;
                width: 100%;
            }

            /* Vehicle No */
            .latest-purchases-table th:nth-child(1),
            .latest-purchases-table td:nth-child(1) {
                width: 22%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Customer */
            .latest-purchases-table th:nth-child(2),
            .latest-purchases-table td:nth-child(2) {
                width: 28%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                word-break: normal;
                overflow-wrap: normal;
            }

            /* Job Done By */
            .latest-purchases-table th:nth-child(4),
            .latest-purchases-table td:nth-child(4) {
                width: 25%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Work Amount */
            .latest-purchases-table th:nth-child(5),
            .latest-purchases-table td:nth-child(5) {
                width: 25%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }

        /* Desktop: hide details toggle column, reset flex on first cell */
        @media (min-width: 768px) {
            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: none !important;
            }

            /* Undo the mobile flex override on the first td */
            .dataview table tbody td:first-child {
                display: table-cell !important;
                align-items: unset !important;
                max-width: unset !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1180px) {
            .content > .dashboard-widgets-row:first-child > div.col-lg-4.col-sm-6.col-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
                padding-left: 8px !important;
                padding-right: 8px !important;
                margin-bottom: 12px !important;
            }

            .content > .dashboard-widgets-row:first-child > div.col-lg-4.col-sm-6.col-4 .dash-widget {
                min-height: 86px;
                margin: 0 !important;
            }

            .content > .dashboard-widgets-row:first-child > div.d-flex {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 8px !important;
                padding-right: 8px !important;
                margin-bottom: 12px !important;
            }

            .content > .dashboard-widgets-row:first-child > div.d-flex .color_box {
                width: 100%;
                height: 100%;
            }

            .content > .dashboard-widgets-row:first-child > div.d-flex .dash-count {
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-height: 86px;
                height: 100%;
                margin: 0 !important;
                padding: 14px 16px;
            }

            .content > .dashboard-widgets-row:first-child > div.d-flex .dash-counts h4 {
                margin-bottom: 4px;
            }

            .content > .dashboard-widgets-row:first-child > div.d-flex .dash-counts h5 {
                margin-bottom: 0;
                line-height: 1.25;
            }
        }

        /* ── iPad Mini / Air (768px – 1024px): charts side-by-side col-6 ── */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            /* ERP Sales & Purchases charts */
            div.col-sm-12.col-12.d-flex:has(#saleschart),
            div.col-sm-12.col-12.d-flex:has(#purchasechart),
            /* HR charts */
            div.col-sm-12.col-12.d-flex:has(#hrAttendanceChart),
            div.col-sm-12.col-12.d-flex:has(#hrSalaryChart) {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            /* Keep the Sales / Purchases header compact on iPad widths */
            .card-header.d-flex.justify-content-between.align-items-center {
                gap: 8px;
            }

            .card-header.d-flex.justify-content-between.align-items-center .card-title {
                flex: 1 1 auto;
                min-width: 0;
            }

            .chart-select-container {
                flex: 0 0 112px;
                max-width: 112px;
            }

            .chart-select-container .select2-container {
                width: 100% !important;
                min-width: 0 !important;
            }

            .chart-select-container .select2-container--default .select2-selection--single {
                width: 100% !important;
            }

            .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
                font-size: 12px !important;
                padding-left: 8px !important;
                padding-right: 20px !important;
            }
        }

        /* Style for chart select2 dropdowns */
        .chart-select-container .select2-container--default .select2-selection--single {
            height: 31px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 29px !important;
            font-size: 13px !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 29px !important;
            width: 10px !important;
        }

        /* Clearfix for mobile grid */
        @media screen and (max-width: 767px) {
            .dashboard-widgets-row::after {
                content: "";
                display: table;
                clear: both;
            }
        }

        .hr-section {
            position: relative;
            z-index: 1;
        }

        .hr-section::before {
            bottom: -10px;
            content: "";
            left: -4px;
            pointer-events: none;
            position: absolute;
            right: -4px;
            top: -10px;
            z-index: -1;
            background: #f6fbf6;
        }

        .hr-section .card {
            border: 1px solid #d9f0df;
            box-shadow: none;
        }

        .custom-table-scroll {
            max-height: 250px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .custom-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #fff;
        }

        .custom-table-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-table-scroll::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 10px;
        }

        .custom-table-scroll::-webkit-scrollbar-thumb {
            background: #ff9f43; 
            border-radius: 10px;
        }

        .custom-table-scroll::-webkit-scrollbar-thumb:hover {
            background: #e68a35; 
        }

        .hr-metric {
            min-height: 118px;
            height: 80%;
        }

        .hr-metric-label {
            color: #667085;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .hr-metric-value {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
        }

        .hr-metric-note {
            color: #667085;
            font-size: 12px;
            line-height: 1.35;
            margin: 0;
        }

        .hr-mini-badge {
            border-radius: 4px;
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 7px;
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .hr-section .table {
            table-layout: fixed;
            width: 100%;
        }

        .hr-section .table th,
        .hr-section .table td {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        .hr-section .table a,
        .hr-section .table .text-muted,
        .hr-section .table .mobile-details-label,
        .hr-section .table .mobile-details-value {
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .hr-section .table .details-column {
            width: 42px;
            word-break: normal;
            overflow-wrap: normal;
        }

        .hr-chart {
            min-height: 260px;
            width: 100%;
        }

        .hr-section:first-of-type {
            margin-top: 12px;
            padding-top: 18px;
        }

        .hr-section:first-of-type::before {
            border-radius: 8px 8px 0 0;
            top: 0;
        }

        .hr-section:nth-last-of-type(1)::before {
            border-radius: 0 0 8px 8px;
        }

        .pulse-zone .hr-section {
            padding-left: 15px;
            padding-right: 7px;
        }

        .pulse-zone .hr-section::before {
            display: none;
        }
    </style>

    <!-- Today’s Appointments Modal -->
    <!-- Today’s Appointments Modal -->
    {{-- <div class="modal fade" id="todayAppointmentsModal" tabindex="-1" aria-labelledby="todayAppointmentsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="todayAppointmentsLabel">
                        📅 Today's Schedule - {{ now()->format('d M Y') }}
                    </h5>
                </div>
                <div class="modal-body">

                    <h5 class="mb-3 text-dark fw-bold">📌 Today's Appointments</h5>

                    @if ($appointments->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>{{ ucfirst($appointment->name ?? 'N/A') }}</td>
                                        <td>{{ $appointment->phone ?? 'N/A' }}</td>
                                        <td>{{ $appointment->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $appointment->appointment_time ?? 'N/A' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ ($appointment->status ?? '') == 'Confirmed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($appointment->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No appointments today.</p>
                    @endif

                    <hr>


                    <h5 class="mb-3 text-dark fw-bold">💰 Upcoming EMIs (Next 7 Days)</h5>
                    @if ($upcomingEmis->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Vehicle No</th>
                                    <th>Model</th>
                                    <th>EMI Due Date</th>
                                    <th>Remaining Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingEmis as $emi)
                                    <tr>
                                        <td>{{ ucfirst($emi->customer_name ?? 'N/A') }}</td>
                                        <td>{{ $emi->vehicle_no ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($emi->model_name ?? 'N/A') }}</td>
                                        <td>{{ $emi->emi_due_date ?? 'N/A' }}</td>
                                        <td>
                                            {{ isset($emi->total_subtotal_amount, $emi->payment_amount)
                                                ? $emi->total_subtotal_amount - $emi->payment_amount
                                                : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No EMIs due in the next 7 days.</p>
                    @endif
                    <hr>


                    <h5 class="mb-3 text-dark fw-bold">💳 Upcoming Cash/Online Payments (Next 7 Days)</h5>
                    @if ($upcomingPayments->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Vehicle No</th>
                                    <th>Model</th>
                                    <th>Next Pending Date</th>
                                    <th>Remaining Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingPayments as $pay)
                                    <tr>
                                        <td>{{ ucfirst($pay->customer_name ?? 'N/A') }}</td>
                                        <td>{{ $pay->vehicle_no ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($pay->model_name ?? 'N/A') }}</td>
                                        <td>{{ $pay->emi_due_date ?? 'N/A' }}</td>
                                        <td>{{ $pay->remaining_amount ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No Cash/Online payments due in the next 7 days.</p>
                    @endif
                    <hr>


                    <h5 class="mb-3 text-dark fw-bold">🎉 Customers Completing 6 Months (Today)</h5>
                    @if ($customers6Months->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Vehicle No</th>
                                    <th>Model</th>
                                    <th>Record Date</th>
                                    <th>6-Month Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers6Months as $cust)
                                    <tr>
                                        <td>{{ ucfirst($cust->customer_name ?? 'N/A') }}</td>
                                        <td>{{ $cust->vehicle_no ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($cust->model_name ?? 'N/A') }}</td>
                                        <td>{{ $cust->record_date ? \Carbon\Carbon::parse($cust->record_date)->format('d-m-Y') : 'N/A' }}
                                        </td>
                                        <td>{{ $cust->six_month_due_date ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No customers completing 6 months today.</p>
                    @endif

                </div>

                <div style="padding: 1rem;">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="content">
        @php
            $canShowJobCard = tenant_has_module(['Job Card', 'ERP']);
            $canShowSales = tenant_has_module(['Job Card', 'ERP', 'Sales & Orders', 'Sales']);
            $canShowPurchase = tenant_has_module(['Purchase', 'ERP']);
            $canShowExpense = tenant_has_module(['Expense', 'Accounting']);
            $canShowQuotation = tenant_has_module(['Job Card', 'Quotations', 'Sales & Orders', 'ERP']);
            $canShowCustomer = tenant_has_module(['Customer', 'CRM']);
            $canShowVendor = tenant_has_module(['Vendor', 'ERP']);
            $canShowDailyRegister = tenant_has_module(['Daily Register', 'ERP']);
            $hasAnyTopWidget = $canShowSales || $canShowPurchase || $canShowExpense || $canShowQuotation || $canShowCustomer || $canShowVendor;
        @endphp

        @if ($showErpDashboardSection && tenant_has_module('Dashboard'))
        <div class="mb-3 d-flex justify-content-end">
            <select id="dateFilter" class="form-select form-select-sm filterclass">
                <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ request('filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
            </select>
        </div>

        @if ($hasAnyTopWidget)
        <div class="row dashboard-widgets-row">

            @if ($canShowSales)
            <!-- Sales -->
            <div class="col-lg-4 col-sm-6 col-4">
                <a href="{{ route('sales.list') }}">
                    <div class="dash-widget dash1">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash2.svg' }}"
                                    alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters counters123" data-count="{{ $mainAmount ?? 0 }}">
                                    {{ $mainAmountFormatted }}</span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Sales Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowPurchase)
            <!-- Purchase -->
            <div class="col-lg-4 col-sm-6 col-4">
                <a href="{{ route('purchase.lists') }}">
                    <div class="dash-widget">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash1.svg' }}"
                                    alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters counters123" data-count="{{ $totalPurchaseAmount }}">
                                    {{ number_format($totalPurchaseAmount, 2) }}</span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Purchase Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowExpense)
            <!-- Expense -->
            <div class="col-lg-4 col-sm-6 col-4">
                <a href="{{ route('expense.list') }}">
                    <div class="dash-widget dash2">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash3.svg' }}"
                                    alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters counters123" data-count="{{ $totalExpenseAmount }}">
                                    {{ number_format($totalExpenseAmount, 2) }}</span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Expense Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowSales)
            <!-- Mini Cards -->
            <div class="col-lg col-sm-6 col-6 d-flex">
                <a href="{{ route('sales.list') }}" class="color_box box1">
                    <div class="dash-count das3">
                        <div class="dash-counts">
                            <h4 data-sales-invoice-count>{{ number_format($salesInvoiceCount, 0) }}</h4>
                            <h5>Sales Invoice</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="file"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowPurchase)
            <div class="col-lg col-sm-6 col-6 d-flex">
                <a href="{{ route('purchase.lists') }}" class="color_box box2">
                    <div class="dash-count das2">
                        <div class="dash-counts">
                            <h4 data-purchase-invoice-count>{{ number_format($purchaseInvoiceCount, 0) }}</h4>
                            <h5>Purchase Invoice</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowQuotation)
            <div class="col-lg col-sm-6 col-6 d-flex">
                <a href="{{ route('sales.list') }}" class="color_box box3">
                    <div class="dash-count das3">
                        <div class="dash-counts">
                            <h4>{{ number_format($quotationCount, 0) }}</h4>
                            <h5>Total Quotations</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowCustomer)
            <div class="col-lg col-sm-6 col-6 d-flex">
                <a href="{{ route('customer.list') }}" class="color_box box3">
                    <div class="dash-count">
                        <div class="dash-counts">
                            <h4 data-customer-count>{{ number_format($customerCount, 0) }}</h4>
                            <h5>Customers</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($canShowVendor)
            <div class="col-lg col-sm-6 col-6 d-flex">
                <a href="{{ route('vendor.list') }}" class="color_box box4">
                    <div class="dash-count das1">
                        <div class="dash-counts">
                            <h4 data-vendor-count>{{ number_format($vendorCount, 0) }}</h4>
                            <h5>Vendors</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user-check"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endif

        </div>
        @endif

        <div class="row">
            @if ($showErpJobCardChart && $canShowJobCard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin', 'Staff']))
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">{{ $salesChartLabel }}</h4>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="salesYearSelect" style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                            <div class="h-250" style="width: 100%;" id="saleschart"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showErpDailyDataChart && $canShowDailyRegister && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin', 'Staff']))
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Daily Data</h4>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="purchaseYearSelect" style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                            <div class="h-250" style="width: 100%;" id="purchasechart"></div>
                        </div>
                    </div>
                </div>
            @endif


            @if ($showErpLatestJobCard && $canShowJobCard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin', 'Staff']))
                <!-- Latest Sales -->
                <div class="col-md-6">
                    <div class="card mb-0" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Latest {{ $salesChartLabel }}</h4>
                            <a href="{{ route('job_card.index') }}" class="btn btn-sm" style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View All</a>
                        </div>
                        <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
                            <div class="table-responsive dataview">
                                <table class="table latest-sales-table">
                                    <colgroup>
                                        <col class="col-job">
                                        <col class="col-vehicle">
                                        <col class="col-customer">
                                        <col class="col-total">
                                        <col class="col-action">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Job Card No</th>
                                            <th>Vehicle No</th>
                                            <th>Customer Name</th>
                                            <th>Grand Total</th>
                                            <th class="action-column">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($latestSales->count() > 0)
                                            @foreach ($latestSales as $item)
                                                <tr>
                                                    <td>
                                                        <a href="/job-card/{{ $item->id }}/view-pdf">
                                                            {{ $item->job_card_number ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span>{{ $item->vehicle_no ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>{{ $item->customer->name ?? 'Unknown' }}</td>
                                                    <td>
                                                        @if ($currencyPosition === 'left')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                        {{ number_format($item->total_subtotal_amount ?? 0, 2) }}
                                                        @if ($currencyPosition === 'right')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                    </td>
                                                    <td class="action-cell">
                                                        <a href="javascript:void(0);" class="toggle-details"
                                                            data-bs-target="#sales-details-{{ $item->id }}"
                                                            aria-expanded="false">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <!-- Mobile-only expanded details row -->
                                                <tr class="sales-details-row" id="sales-details-{{ $item->id }}" style="display:none;">
                                                    <td colspan="5">
                                                        <div class="collapse-content">
                                                            <p class="mb-1"><strong>Vehicle No:</strong>
                                                                {{ $item->vehicle_no ?? 'N/A' }}</p>
                                                            <p class="mb-1"><strong>Customer Name:</strong>
                                                                {{ $item->customer->name ?? 'Unknown' }}</p>
                                                            <p class="mb-0"><strong>Grand Total:</strong>
                                                                @if ($currencyPosition === 'left')
                                                                    {{ $currencySymbol }}
                                                                @endif
                                                                {{ number_format($item->total_subtotal_amount ?? 0, 2) }}
                                                                @if ($currencyPosition === 'right')
                                                                    {{ $currencySymbol }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">No {{ $salesChartLabel }} Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showErpLatestDailyData && $canShowDailyRegister && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin', 'Staff']))
                <div class="col-md-6">
                    <div class="card mb-0" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Latest Daily Data</h4>
                            <a href="{{ route('daily_data.index') }}" class="btn btn-sm" style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View All</a>
                        </div>
                        <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
                            <div class="table-responsive dataview">
                                <table class="table latest-purchases-table">
                                    <colgroup>
                                        <col class="col-vehicle">
                                        <col class="col-customer">
                                        <col class="col-details">
                                        <col class="col-technician">
                                        <col class="col-total">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Vehicle No.</th>
                                            <th>Customer</th>
                                            <th class="details-column">Details</th>
                                            <th>Job Done By</th>
                                            <th>Work Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($latestPurchases) && count($latestPurchases) > 0)
                                            @foreach ($latestPurchases as $index => $item)
                                                <tr>
                                                    {{-- Vehicle Number --}}
                                                    <td>{{ ucfirst($item->vehicle_no ?? 'N/A') }}</td>

                                                    {{-- Customer Name --}}
                                                    <td>{{ ucfirst($item->customer->name ?? 'N/A') }}</td>

                                                    {{-- Details toggle (mobile only via CSS) --}}
                                                    <td class="details-cell">
                                                        <a href="javascript:void(0);" class="toggle-details"
                                                            data-bs-target="#purchase-details-{{ $index }}"
                                                            aria-expanded="false">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </td>

                                                    {{-- Technician Name --}}
                                                    <td>{{ ucfirst($item->technicianUser->name ?? 'N/A') }}</td>

                                                    {{-- Total Subtotal Amount --}}
                                                    <td>
                                                        @if ($currencyPosition === 'left')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                        {{ number_format($item->total_subtotal_amount ?? 0, 2) }}
                                                        @if ($currencyPosition === 'right')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                {{-- Mobile-only expanded details row --}}
                                                <tr class="purchase-details-row" id="purchase-details-{{ $index }}" style="display:none;">
                                                    <td colspan="5">
                                                        <div class="collapse-content">
                                                            <p class="mb-1"><strong>Job Done By:</strong>
                                                                {{ ucfirst($item->technicianUser->name ?? 'N/A') }}</p>
                                                            <p class="mb-1"><strong>Work Amount:</strong>
                                                                @if ($currencyPosition === 'left')
                                                                    {{ $currencySymbol }}
                                                                @endif
                                                                {{ number_format($item->total_subtotal_amount ?? 0, 2) }}
                                                                @if ($currencyPosition === 'right')
                                                                    {{ $currencySymbol }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">No Job Card Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             HR WORKFORCE OVERVIEW SECTION
        ═══════════════════════════════════════════════════════════════ --}}
        @endif
        @if ($showHrDashboardSection && tenant_has_module(['HR', 'Staff', 'Attendance', 'Payroll', 'Advance Pay', 'Leave']) && in_array($role, ['inventory-manager', 'admin', 'sub-admin']))
        <style>
            .hr-metric { min-height: 118px; }
            .hr-metric-label { color: #667085; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; }
            .hr-metric-value { color: #1f2937; font-size: 25px; font-weight: 700; line-height: 1; margin-bottom: 8px; }
            .hr-metric-note  { color: #667085; font-size: 11px; line-height: 1.35; margin: 0; }
            .hr-mini-badge   { border-radius: 4px; display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 7px; white-space: normal; word-break: break-word; }
            .hr-chart        { min-height: 260px; width: 100%; }

            @media (max-width: 767px) {
                .hr-metric { min-height: unset !important; }
                .hr-metric .card-body { padding: 10px 12px !important; }
                .hr-metric-label { font-size: 10px; margin-bottom: 4px; }
                .hr-metric-value { font-size: 20px; margin-bottom: 4px; }
                .hr-metric-note  { font-size: 9.5px; line-height: 1.25; }
            }
        </style>

        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">HR Workforce Overview</h4>
                    <a href="{{ route('staff.list') }}" class="btn btn-sm btn-primary">View Staff</a>
                </div>
            </div>

            @if ($showHrStaffStrength && tenant_has_module(['Staff', 'HR']))
            <div class="col-lg-3 col-sm-6 col-6 d-flex">
                <a href="{{ route('staff.list') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer; border-radius: 8px; border: 1px solid #d9f0df; box-shadow: none;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="hr-metric-label">Staff</div>
                                <div class="hr-metric-value">{{ number_format($totalStaff, 0) }}</div>
                                <p class="hr-metric-note">{{ number_format($activeStaff, 0) }} active, {{ number_format($newStaffThisMonth, 0) }} joined this month</p>
                            </div>
                            <div style="background-color: #fff3e8; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                <i data-feather="users" style="width: 20px; height: 20px; color: #e87b1e; stroke-width: 2;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($showHrActiveStaff && tenant_has_module(['Attendance', 'HR']))
            <div class="col-lg-3 col-sm-6 col-6 d-flex">
                <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer; border-radius: 8px; border: 1px solid #d9f0df; box-shadow: none;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="hr-metric-label">Today Attendance</div>
                                <div class="hr-metric-value">{{ number_format($todayPresentStaff, 0) }}</div>
                                <p class="hr-metric-note">{{ number_format($todayAbsentStaff, 0) }} absent, {{ number_format($todayUnmarkedStaff, 0) }} unmarked</p>
                            </div>
                            <div style="background-color: #e8f8f0; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                <i data-feather="check-circle" style="width: 20px; height: 20px; color: #10b981; stroke-width: 2;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($showHrMonthlyAttendance && tenant_has_module(['Attendance', 'HR']))
            <div class="col-lg-3 col-sm-6 col-6 d-flex">
                <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer; border-radius: 8px; border: 1px solid #d9f0df; box-shadow: none;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="hr-metric-label">Monthly Attendance</div>
                                <div class="hr-metric-value">{{ $monthlyAttendanceRate }}%</div>
                                <p class="hr-metric-note">Based on attendance</p>
                            </div>
                            <div style="background-color: #eef2ff; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                <i data-feather="bar-chart-2" style="width: 20px; height: 20px; color: #6366f1; stroke-width: 2;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($showHrPersonalProgress && tenant_has_module(['Payroll', 'HR']))
            <div class="col-lg-3 col-sm-6 col-6 d-flex">
                <a href="{{ route('payroll.list') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer; border-radius: 8px; border: 1px solid #d9f0df; box-shadow: none;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="hr-metric-label">Payroll Progress</div>
                                <div class="hr-metric-value">{{ number_format($salaryPaidCount, 0) }}/{{ number_format($totalStaff, 0) }}</div>
                                <p class="hr-metric-note">{{ number_format($salaryPendingCount, 0) }} pending this month</p>
                            </div>
                            <div style="background-color: #fef3c7; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                <i data-feather="credit-card" style="width: 20px; height: 20px; color: #f59e0b; stroke-width: 2;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if ($showHrAttendancePattern && tenant_has_module(['Attendance', 'HR']))
            <div class="col-lg-7 col-sm-12 col-12 d-flex">
                <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                        <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">7 Day Attendance</h4>
                    </div>
                    <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                        <div id="hrAttendanceChart" class="hr-chart"></div>
                    </div>
                </div>
            </div>
            @endif

            @if ($showHrSalaryPayrollTrend && tenant_has_module(['Payroll', 'HR']))
            <div class="col-lg-5 col-sm-12 col-12 d-flex">
                <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                        <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Salary Payout</h4>
                    </div>
                    <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                        <div id="hrSalaryChart" class="hr-chart"></div>
                    </div>
                </div>
            </div>
            @endif

            @if ($showHrPayrollSnapshot && tenant_has_module(['Payroll', 'HR']))
            <div class="col-lg-4 col-sm-12 col-12 d-flex">
                <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                        <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Payroll</h4>
                        <a href="{{ route('payroll.list') }}" class="btn btn-sm" style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View All</a>
                    </div>
                    <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead><tr><th>Metric</th><th>Value</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td>Paid this month</td>
                                        <td>@if($currencyPosition==='left'){{ $currencySymbol }}@endif{{ number_format($salaryPaidAmount, 2) }}@if($currencyPosition==='right'){{ $currencySymbol }}@endif</td>
                                    </tr>
                                    <tr>
                                        <td>Outstanding advances</td>
                                        <td>@if($currencyPosition==='left'){{ $currencySymbol }}@endif{{ number_format($advanceOutstanding, 2) }}@if($currencyPosition==='right'){{ $currencySymbol }}@endif</td>
                                    </tr>
                                    <tr>
                                        <td>Salary pending staff</td>
                                        <td>{{ number_format($salaryPendingCount, 0) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($showHrAttendanceWatch && tenant_has_module(['Attendance', 'HR']))
            <div class="col-lg-4 col-sm-12 col-12 d-flex">
                <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                        <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Attendance</h4>
                        <a href="{{ route('attendence.calendar') }}" class="btn btn-sm" style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View All</a>
                    </div>
                    <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>Staff</th><th>Present</th><th>Absent</th></tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendanceWatch as $staffRow)
                                        <tr>
                                             <td>{{ $staffRow->name }}</td>
                                            <td>{{ number_format($staffRow->present_days, 0) }}</td>
                                            <td>
                                                <span class="hr-mini-badge bg-light text-dark">{{ number_format($staffRow->absent_days, 0) }}</span>
                                                @if ($staffRow->half_days > 0)
                                                    <div class="text-muted small">{{ number_format($staffRow->half_days, 0) }} half-day</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No attendance data found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($showHrPayrollStatus && tenant_has_module(['Payroll', 'HR']))
            <div class="col-lg-4 col-sm-12 col-12 d-flex">
                <div class="card flex-fill" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                        <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Payroll Status</h4>
                        <a href="{{ route('payroll.list') }}" class="btn btn-sm" style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View All</a>
                    </div>
                    <div class="card-body" style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>Staff</th><th>Status</th><th>Amount</th></tr>
                                </thead>
                                <tbody>
                                    @forelse ($payrollStatusRows as $payrollRow)
                                        <tr>
                                            <td>
                                                <a href="{{ route('staff.view', $payrollRow['id']) }}">{{ $payrollRow['name'] }}</a>
                                                <div class="text-muted small">{{ $payrollRow['phone'] ?? 'N/A' }}</div>
                                            </td>
                                            <td><span class="hr-mini-badge bg-light text-dark">{{ $payrollRow['status'] }}</span></td>
                                            <td>@if($currencyPosition==='left'){{ $currencySymbol }}@endif{{ number_format($payrollRow['amount'], 2) }}@if($currencyPosition==='right'){{ $currencySymbol }}@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No staff records found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        {{-- ═══════════════════════════════════════════════════════════════
             END HR SECTION
        ═══════════════════════════════════════════════════════════════ --}}

        
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            @if ($showErpDashboardSection)
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            // console.log(selectedSubAdminId);

            $('#dateFilter').on('change', function() {
                var filter = $(this).val();

                $.ajax({
                    url: '{{ route('dashboard.filter') }}',
                    method: 'GET',
                    data: {
                        filter: filter,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        // Update Total Collections
                        $('.dash-widgetcontent h5 .counters123[data-count]').each(function() {
                            var countType = $(this).closest('.dash-widget').find('h6')
                                .text().trim();

                            if (countType === "Total Collections" || countType === "Total Sales Amount") {
                                $(this).text(response.mainAmountFormatted);
                                $(this).attr('data-count', response.mainAmount);
                            } else if (countType === "Total Expense" || countType === "Total Expense Amount") {
                                $(this).text(response.totalExpenseAmountFormatted);
                                $(this).attr('data-count', response.totalExpenseAmount);
                            } else if (countType === "Total Pending Payment") {
                                $(this).text(response.pendingAmountFormatted);
                                $(this).attr('data-count', response.pendingAmount);
                            }
                        });

                        // Total Job Card Bills
                        // if (selectedSubAdminId !== null && selectedSubAdminId !== undefined &&
                        //     selectedSubAdminId !== '') {
                        //     $('[data-customer-count]').text(response.totalOrdersCount);
                        // } else {
                            $('[data-customer-count]').text(response.totalJobCards);
                        // }

                        // Total Daily Data
                        $('[data-vendor-count]').text(response.totalDailyData);

                        // Appointments
                        $('[data-sales-invoice-count]').text(response.totalAppointments);

                        // Attendance (Present / Absent)
                        $('[data-purchase-invoice-count]').html(
                            `P ${response.totalPresent} / AB ${response.totalAbsent}`
                        );
                    },
                    error: function(xhr) {
                        console.error('Error fetching data');
                    }
                });
            });

            // Optional: trigger to load initial data on page load
            // $('#dateFilter').trigger('change');
            @endif
        });


        const currentYear = {{ $currentYear }};
        const previousYear = {{ $previousYear }};
        const salesThisMonth = @json($salesChartThisMonth);
        const purchaseThisMonth = @json($purchaseChartThisMonth);

        // Make chart data globally accessible
        window.salesChartData = {
            [currentYear]: @json($salesChartthisyear),
            [previousYear]: @json($salesChartpreviousyear),
            'thisMonth': salesThisMonth
        };

        window.purchaseChartData = {
            [currentYear]: @json($purchaseChartthisyear),
            [previousYear]: @json($purchaseChartpreviousyear),
            'thisMonth': purchaseThisMonth
        };

        // Make renderChart function globally accessible
        window.myChartInstances = window.myChartInstances || {};

        window.renderChart = function(containerId, label, data, barColor, isMonthly = true) {
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = () => window.drawChartJS(containerId, label, data, barColor, isMonthly);
                document.head.appendChild(script);
            } else {
                window.drawChartJS(containerId, label, data, barColor, isMonthly);
            }
        };

        window.drawChartJS = function(containerId, label, data, barColor, isMonthly) {
            const container = document.querySelector(containerId);
            if (!container) return;

            let canvas = container.querySelector('canvas');
            if (!canvas) {
                container.innerHTML = '<canvas style="width: 100%; height: 100%;"></canvas>';
                canvas = container.querySelector('canvas');
            }

            if (window.myChartInstances[containerId]) {
                window.myChartInstances[containerId].destroy();
            }

            const ctx = canvas.getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, container.offsetHeight || 250);
            const hex2rgb = (hex) => {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) : '0, 0, 0';
            };
            const rgbColor = hex2rgb(barColor);
            gradient.addColorStop(0, 'rgba(' + rgbColor + ', 0.5)');
            gradient.addColorStop(1, 'rgba(' + rgbColor + ', 0.0)');

            const labels = isMonthly 
                ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                : Array.from({ length: data.length }, (_, i) => (i + 1).toString());

            window.myChartInstances[containerId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: barColor,
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: barColor,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let lbl = context.dataset.label || '';
                                    if (lbl) lbl += ': ';
                                    if (context.parsed.y !== null) {
                                        lbl += new Intl.NumberFormat('en-US').format(context.parsed.y);
                                    }
                                    return lbl;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#8e9cad', font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(67, 87, 133, .09)', borderDash: [5, 5] },
                            ticks: {
                                color: '#8e9cad', font: { size: 11 },
                                callback: function(value) {
                                    return value >= 1000 ? (value / 1000) + 'k' : value;
                                }
                            }
                        }
                    }
                }
            });
        };
        
        function renderChart(containerId, label, data, barColor, isMonthly = true) {
            window.renderChart(containerId, label, data, barColor, isMonthly);
        }

        $(document).ready(function() {
            $('.chart-select2').select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });

            @if ($showErpDashboardSection && $showErpJobCardChart && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin', 'Staff']))
                window.currentSalesYear = currentYear; // Track current year
                renderChart('#saleschart', 'Job Card', window.salesChartData[currentYear], '#44c4fa');
                $('#salesYearSelect').on('change', function() {
                    const year = $(this).val();
                    window.currentSalesYear = year; // Update tracked year
                    if (year === 'month') {
                        renderChart('#saleschart', 'Job Card (This Month)', window.salesChartData[
                                'thisMonth'],
                            '#44c4fa', false);
                    } else {
                        renderChart('#saleschart', 'Job Card', window.salesChartData[year], '#44c4fa');
                    }
                });
            @endif
            // @if (in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin', 'Staff']))
            //     window.currentSalesYear = currentYear;

            //     // Use label from backend (Job Card / Order)
            //     renderChart('#saleschart', '{{ $salesChartLabel }}', window.salesChartData[currentYear],
            //         '#44c4fa');

            //     $('#salesYearSelect').on('change', function() {
            //         const year = $(this).val();
            //         window.currentSalesYear = year;

            //         if (year === 'month') {
            //             renderChart('#saleschart', '{{ $salesChartLabel }} (This Month)', window
            //                 .salesChartData['thisMonth'], '#44c4fa', false);
            //         } else {
            //             renderChart('#saleschart', '{{ $salesChartLabel }}', window.salesChartData[year],
            //                 '#44c4fa');
            //         }
            //     });
            // @endif

            @if ($showErpDashboardSection && $showErpDailyDataChart && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin', 'Staff']))
                window.currentPurchaseYear = currentYear; // Track current year
                renderChart('#purchasechart', 'Daily Data', window.purchaseChartData[currentYear], '#fa6c7c');
                $('#purchaseYearSelect').on('change', function() {
                    const year = $(this).val();
                    window.currentPurchaseYear = year; // Update tracked year
                    if (year === 'month') {
                        renderChart('#purchasechart', 'Daily Data (This Month)', window.purchaseChartData[
                            'thisMonth'], '#fa6c7c', false);
                    } else {
                        renderChart('#purchasechart', 'Daily Data', window.purchaseChartData[year],
                            '#fa6c7c');
                    }
                });
            @endif

            // Toggle +/− icon and show/hide detail rows on mobile
            $(document).on("click", ".toggle-details", function() {
                var $btn = $(this);
                var target = $btn.data("bs-target") || $btn.attr("data-bs-target");
                var $row = $(target);

                if ($row.length) {
                    // It's a <tr> — use jQuery show/hide to avoid Bootstrap's display:block breaking table layout
                    var isOpen = $btn.hasClass("is-open");
                    if (isOpen) {
                        $row.hide();
                        $btn.find("i").removeClass("fa-minus").addClass("fa-plus");
                        $btn.removeClass("is-open");
                    } else {
                        $row.show();
                        $btn.find("i").removeClass("fa-plus").addClass("fa-minus");
                        $btn.addClass("is-open");
                    }
                } else {
                    // Fallback: toggle icon only
                    $btn.find("i").toggleClass("fa-plus fa-minus");
                    $btn.toggleClass("is-open");
                }
            });

        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            if (sessionStorage.getItem("showAppointments") === "1") {
                $('#todayAppointmentsModal').modal('show');
                sessionStorage.removeItem("showAppointments"); // remove so it shows only once
            }
        });
    </script> --}}

    @if ($showHrDashboardSection && in_array($role, ['inventory-manager', 'admin', 'sub-admin']))
    <script>
        const hrAttendanceLabels   = @json($hrAttendanceLabels ?? []);
        const hrAttendancePresent  = @json($hrAttendancePresent ?? []);
        const hrAttendanceAbsent   = @json($hrAttendanceAbsent ?? []);
        const hrAttendanceHalfDay  = @json($hrAttendanceHalfDay ?? []);
        const hrSalaryLabels       = @json($hrSalaryLabels ?? []);
        const hrSalaryPaidAmounts  = @json($hrSalaryPaidAmounts ?? []);
        const hrSalaryPaidCounts   = @json($hrSalaryPaidCounts ?? []);

        $(document).ready(function() {
            @if ($showHrAttendancePattern)
            if (document.querySelector('#hrAttendanceChart')) {
                new ApexCharts(document.querySelector('#hrAttendanceChart'), {
                    chart: { type: 'bar', height: 260, stacked: true, toolbar: { show: false } },
                    series: [
                        { name: 'Present',  data: hrAttendancePresent },
                        { name: 'Half-day', data: hrAttendanceHalfDay },
                        { name: 'Absent',   data: hrAttendanceAbsent  }
                    ],
                    colors: ['#10b981', '#ff9f43', '#ef4444'],
                    plotOptions: { bar: { columnWidth: '42%', borderRadius: 3 } },
                    xaxis: { categories: hrAttendanceLabels },
                    yaxis: { min: 0, forceNiceScale: true, labels: { formatter: v => Math.round(v) } },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    grid:   { borderColor: '#edf0f5' },
                    noData: { text: 'No attendance data' }
                }).render();
            }
            @endif

            @if ($showHrSalaryPayrollTrend)
            if (document.querySelector('#hrSalaryChart')) {
                new ApexCharts(document.querySelector('#hrSalaryChart'), {
                    chart: { height: 260, type: 'line', toolbar: { show: false }, zoom: { enabled: false } },
                    series: [
                        { name: 'Payout',     type: 'column', data: hrSalaryPaidAmounts },
                        { name: 'Paid Staff', type: 'line',   data: hrSalaryPaidCounts  }
                    ],
                    colors: ['#4361ee', '#10b981'],
                    stroke: { width: [0, 3], curve: 'smooth' },
                    plotOptions: { bar: { columnWidth: '45%', borderRadius: 3 } },
                    xaxis: { categories: hrSalaryLabels },
                    yaxis: [
                        { min: 0, labels: { formatter: v => Math.round(v).toLocaleString() } },
                        { opposite: true, min: 0, forceNiceScale: true, labels: { formatter: v => Math.round(v) } }
                    ],
                    grid:   { borderColor: '#edf0f5' },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    noData: { text: 'No salary data' }
                }).render();
            }
            @endif
        });
    </script>
    @endif

@endpush
@include('partials.plan-expiry-alert')