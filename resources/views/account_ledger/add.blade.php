@extends('layout.app')
@section('title', 'Account Ledger')
@section('content')
    <style>
        @media screen and (max-width: 767px) {
            .form-group {
                margin-bottom: 10px;
            }
        }

        /* Global Select2 improvements */
        .select2-dropdown {
            z-index: 9999 !important;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .select2-container--open .select2-selection {
            z-index: 9998 !important;
        }

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            table thead th,
            table tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table thead th.details-column,
            table tbody td.details-cell {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {

            table thead th:nth-child(n+3),
            table tbody td:nth-child(n+3) {
                display: none !important;
            }

            table thead th.details-column,
            table tbody td.details-cell {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .toggle-details i {
                font-size: 18px;
            }

            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #ff9f43;
            }

            .toggle-details i {
                color: #fff;
                font-size: 12px;
                line-height: 1;
            }

            .toggle-details.is-open {
                background: #dc3545;
            }

            /* Style for first column wrapping */
            table tbody td:first-child {
                display: block !important;
                max-width: calc(100vw - 100px) !important;
            }

            table tbody td:first-child .collapse,
            table tbody td:first-child .collapse .card {
                width: 100% !important;
            }
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            /* Match Bootstrap form-control height */
            border: 1px solid #ced4da;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f1f2f3;
            /* Bootstrap primary */
            color: #474747;
            border: none;
            border-radius: 3px;
            padding: 2px 6px;
            margin-top: 4px;
        }

        /* Mobile only: wrap long vendor/customer names in collapse detail cards */
        @media (max-width: 575.98px) {
            .collapse .card-body p {
                word-break: break-word;
                overflow-wrap: break-word;
            }
        }
        @media (max-width: 575.98px) {
            .collapse .card-body p,
            .collapse .card-body span {
                word-break: break-word;
                overflow-wrap: break-word;
                white-space: normal;
            }
        }

        /* iPad Pro (1024x1366) and Nest Hub (1024x600) - Tablet Optimization */
        @media only screen and (width: 1024px) {
            /* Filter form row optimization */
            #appointmentForm .row {
                gap: 0;
            }

            /* Adjust filter field columns for tablet */
            #appointmentForm .col-lg-3 {
                flex: 0 0 25%;
                max-width: 25%;
                padding-right: 8px;
                padding-left: 8px;
            }

            #appointmentForm .col-lg-2 {
                flex: 0 0 16.66%;
                max-width: 16.66%;
                padding-right: 8px;
                padding-left: 8px;
            }

            /* Ensure buttons stay on same row */
            .d-flex.justify-content-end {
                gap: 8px;
            }

            .btn-sm {
                white-space: nowrap;
                font-size: 13px;
                padding: 6px 10px;
            }

            /* Remove extra padding on form groups */
            #appointmentForm .form-group {
                margin-bottom: 0;
            }

            /* Optimize Select2 dropdown width */
            #appointmentForm .select2-container {
                width: 100% !important;
            }

            /* Vehicle number field styling */
            .v_num {
                padding-right: 8px;
                padding-left: 8px;
            }

            .v_num .form-group {
                margin-bottom: 0;
            }

            /* Ensure proper alignment in filter row */
            #appointmentForm .form-control,
            #appointmentForm .select2-container--default .select2-selection--single {
                height: 38px;
                font-size: 13px;
            }
        }

        /* iPad Pro Landscape (1366x1024) adjustment */
        @media only screen and (width: 1366px) and (height: 1024px) {
            #appointmentForm .col-lg-3 {
                flex: 0 0 20%;
                max-width: 20%;
            }

            #appointmentForm .col-lg-2 {
                flex: 0 0 15%;
                max-width: 15%;
            }
        }

        /* Nest Hub specific (1024x600) - more horizontal layout */
        @media only screen and (width: 1024px) and (height: 600px) {
            #appointmentForm .row {
                display: flex;
                flex-wrap: nowrap;
                gap: 8px;
            }

            #appointmentForm .col-lg-3 {
                flex: 0 0 auto;
                max-width: none;
                min-width: 180px;
                padding: 0;
            }

            #appointmentForm .col-lg-2 {
                flex: 0 0 auto;
                max-width: none;
                min-width: 130px;
                padding: 0;
            }

            .v_num {
                flex: 0 0 auto;
                max-width: none;
                min-width: 150px;
                padding: 0;
            }

            /* Buttons on Nest Hub */
            .d-flex.justify-content-end {
                flex: 1;
                justify-content: flex-end !important;
                margin-left: auto;
                gap: 6px;
            }

            .btn-sm {
                font-size: 12px;
                padding: 5px 8px;
            }
        }

        /* Nest Hub Max (1280x800) - wider horizontal layout */
        @media only screen and (width: 1280px) and (height: 800px) {
            #appointmentForm .row {
                display: flex;
                flex-wrap: nowrap;
                gap: 12px;
                align-items: flex-end;
            }

            #appointmentForm .col-lg-3 {
                flex: 0 0 auto;
                max-width: none;
                min-width: 200px;
                padding: 0;
            }

            #appointmentForm .col-lg-2 {
                flex: 0 0 auto;
                max-width: none;
                min-width: 150px;
                padding: 0;
            }

            .v_num {
                flex: 0 0 auto;
                max-width: none;
                min-width: 180px;
                padding: 0;
            }

            /* Buttons on Nest Hub Max */
            .d-flex.justify-content-end {
                flex: 1;
                justify-content: flex-end !important;
                margin-left: auto;
                gap: 8px;
            }

            .btn-sm {
                font-size: 13px;
                padding: 6px 10px;
            }

            /* Filter form styling for Max */
            #appointmentForm .form-control,
            #appointmentForm .select2-container--default .select2-selection--single {
                height: 38px;
                font-size: 13px;
            }
        }

    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Account Ledger</h4>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- View PDF & Excel buttons -->
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm text-white btn-success me-2" id="viewCsvBtn">
                        <i class="fas fa-file-csv"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-sm text-white btn-danger" id="viewPdfBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
                <form id="appointmentForm">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Select Type</label>
                                <select id="type_selector" class="form-control select2">
                                    <option value="">-- Select Type --</option>
                                    <option value="customer">Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <select name="customer_id" id="customer_id" class="form-control select2">
                                    <option value="">Select Customer</option>
                                    <option value="all">All Customers</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id ?? 'null' }}|{{ ucwords($customer->name) }}"
                                            data-subbranch-id="{{ $customer->subbranch_id }}">
                                            {{ ucwords($customer->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-customer_id"></span>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Vendor Name</label>
                                <select name="vendor_id" id="vendor_id" class="form-control select2">
                                    <option value="">Select Vendor</option>
                                    <option value="all">All Vendors</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id ?? 'null' }}|{{ ucwords($vendor->name) }}"
                                            data-branch-id="{{ $vendor->branch_id }}">
                                            {{ ucwords($vendor->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-vendor_id"></span>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-6" style="overflow: hidden;">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="month[]" id="month" class="form-control select2" multiple>
                                    {{-- ✅ Multiple select --}}
                                    <option value="all">All Months</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                                <span class="text-danger error-month"></span>
                            </div>
                        </div>


                        <div class="col-lg-2 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Year</label>
                                @php
                                    $currentYear = date('Y');
                                    $years = range($currentYear, $currentYear - 5);
                                @endphp

                                <select name="year" id="year" class="form-control select2">
                                    <option value="">Select Year</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-year"></span>
                            </div>
                        </div>
                        @php
                            $subAdminId = session('selectedSubAdminId');
                        @endphp
                        @if(empty($subAdminId))
                            <div class="col-lg-2 col-sm-6 col-6 v_num">
                                <div class="form-group">
                                    <label>Vehicle Number</label>
                                    <input type="text" name="vehicle_number" id="vehicle_number" class="form-control">
                                    <span class="text-danger error-vehicle_number"></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
                <div id="paymentDetails">
                    <!-- Payment details will be displayed here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // Detect if device is iPad Pro, Nest Hub, or Nest Hub Max
            const isTabletDevice = () => {
                const width = window.innerWidth;
                const height = window.innerHeight;
                // iPad Pro: 1024x1366, Nest Hub: 1024x600, Nest Hub Max: 1280x800
                return (width === 1024 && (height === 1366 || height === 600)) || 
                       (width === 1024 && height === 1024) ||
                       (width === 1280 && height === 800);
            };

            const isTablet = isTabletDevice();

            // Function to position Select2 dropdown properly
            const positionSelect2Dropdown = (selectElement) => {
                if (!isTablet) return;

                selectElement.on('select2:open', function() {
                    setTimeout(() => {
                        const dropdown = $('.select2-dropdown--below');
                        if (dropdown.length) {
                            const select2Container = selectElement.next('.select2-container');
                            const offset = select2Container.offset();
                            const height = select2Container.height();

                            dropdown.css({
                                'position': 'fixed',
                                'top': (offset.top + height + 8) + 'px',
                                'left': offset.left + 'px',
                                'width': select2Container.width() + 'px',
                                'z-index': 9999
                            });
                        }
                    }, 50);
                });
            };

            const select2Config = {
                placeholder: 'Select an option',
                width: '100%',
                dropdownParent: isTablet ? $(document.body) : undefined
            };

            $('.select2').select2(select2Config);
            $('.select2').each(function() {
                positionSelect2Dropdown($(this));
            });

            $("#customer_id").closest(".col-lg-3").hide();
            $("#vendor_id").closest(".col-lg-3").hide();

            // Show dropdown based on type selector
            $("#type_selector").on("change", function () {
                let type = $(this).val();
                if (type === "customer") {
                    $("#customer_id").closest(".col-lg-3").show();
                    $("#vendor_id").closest(".col-lg-3").hide().val("");
                    $(".v_num").show();
                } else if (type === "vendor") {
                    $("#vendor_id").closest(".col-lg-3").show();
                    $("#customer_id").closest(".col-lg-3").hide().val("");
                    $(".v_num").hide();
                } else {
                    $("#customer_id, #vendor_id").closest(".col-lg-3").hide().val("");
                    $(".v_num").show();
                }

                fetchPayments(); // call after selecting type
            });

            // Handle customer or vendor selection
            $("#customer_id, #vendor_id, #month, #year").on("change", fetchPayments);

            // Vehicle input with debounce
            let debounceTimer;
            $("#vehicle_number").on("input", function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchPayments, 500);
            });
            var selectedSubAdminId1 = localStorage.getItem("selectedSubAdminId");

            function fetchPayments() {
                var authToken = localStorage.getItem("authToken");
                var months = $("#month").val();
                var year = $("#year").val();
                var vehicleNumber = $("#vehicle_number").val()?.trim() || "";


                let type = $("#type_selector").val(); // dynamically get type
                if (!type) return; // do nothing if type not selected

                let selectedValue = type === "customer" ? $("#customer_id").val() : $("#vendor_id").val();
                if (!selectedValue) return;

                let parts = selectedValue.split("|");
                let userId = parts[0] !== "null" ? parts[0] : null;
                let name = parts[1] || null;

                // Store branch/subbranch dynamically
                let branchKey = type === "customer" ? "selectedSubBranchId" : "selectedBranchId";
                let branchId = $("#" + (type === "customer" ? "customer_id" : "vendor_id")).find(":selected").data(type === "customer" ? "subbranch-id" : "branch-id");
                if (branchId) localStorage.setItem(branchKey, branchId);

                $.ajax({
                    url: "/api/get-payment-details",
                    type: "GET",
                    headers: { "Authorization": "Bearer " + authToken },
                    data: {
                        type: type,
                        id: userId,
                        name: name,
                        month: months,
                        year: year,
                        selectedSubAdminId1: selectedSubAdminId1,
                        vehicle_number: vehicleNumber,
                    },
                    success: function (response) {
                        displayPaymentDetails(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        $("#paymentDetails").html('<div class="text-danger">Failed to fetch payment details.</div>');
                    },
                });
            }


            function displayPaymentDetails(data) {
                const currency = data.settings.currency_symbol || '₹';
                const currencyPosition = data.settings.currency_position || 'left';

                function formatCurrency(amount) {
                    return currencyPosition === 'left' ? `${currency}${amount}` : `${amount}${currency}`;
                }

                const type = data.type; // 'customer' or 'vendor'
                let html = '';

                // === PAID PAYMENTS ===
                html += `<div class="card shadow mb-4">
                        <div class="card-header text-dark">
                            <h5 class="mb-0 fw-bold">Paid Payments</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>`;

                // determine whether to show vehicle column
                const vehicleShown = !selectedSubAdminId1;

                if (type === 'customer') {
                    html += `<th>Job Card</th>
                         <th class="details-column">Details</th>
                         <th>Total Amount</th>
                         <th>Discount Amount</th>
                         <th>Paid Amount</th>
                         <th>Payment Date</th>
                         <th>Payment Method</th>
                         <th>Type</th>
                         ${vehicleShown ? '<th>Vehicle No.</th>' : ''}`;
                } else if (type === 'vendor') {
                    html += `<th>Purchase No</th>
                         <th class="details-column">Details</th>
                         <th>Total Amount</th>
                         <th>Paid Amount</th>
                         <th>Payment Date</th>
                         <th>Payment Method</th>
                         <th>Vendor</th>`;
                }

                html += `       </tr>
                            </thead>
                            <tbody>`;

                let totalPaid = 0;

                // compute paid table column count for correct colspan when no records
                const paidCols = type === 'customer' ? (8 + (vehicleShown ? 1 : 0)) : 7;

                if (data.paidPayments.length > 0) {
                    
                    data.paidPayments.forEach((payment, index) => {
                        const id = `paid-${index}-${payment.id || Math.random().toString(36).substr(2, 9)}`;
                        html += '<tr>';

                        if (type === 'customer') {
                            const jobcard = payment.jobcard;
                            const jobCardNo = payment.jobcard ? payment.jobcard.job_card_number : (payment.order ? payment.order.order_number : '-');
                            const totalAmount = payment.jobcard ? payment.jobcard.total_subtotal_amount : (payment.order ? payment.order.total_amount : 0);
                            const discountAmount = payment.jobcard ? payment.discount_amount : (payment.discount_amount ? payment.discount_amount : 0);
                            const paidAmount = payment.payment_amount;
                            const paymentDate = payment.payment_date || '-';
                            const paymentMethod = payment.payment_method || '-';
                            const jobCardType = jobcard ? jobcard.type.charAt(0).toUpperCase() + jobcard.type.slice(1) : (payment.order ? payment.order.type : '-');
                            const vehicleNo = payment.jobcard && payment.jobcard.vehicle_no != null ? payment.jobcard.vehicle_no : '-';

                            html += `
                            <td>
                                ${jobCardNo}
                                <div class="collapse mt-2 d-lg-none" id="${id}">
                                    <div class="card card-body p-2 bg-light border">
                                        <p class="mb-1"><strong>Total Amount:</strong> ${formatCurrency(totalAmount)}</p>
                                        <p class="mb-1"><strong>Discount:</strong> ${formatCurrency(discountAmount)}</p>
                                        <p class="mb-1"><strong>Paid Amount:</strong> ${formatCurrency(paidAmount)}</p>
                                        <p class="mb-1"><strong>Date:</strong> ${paymentDate}</p>
                                        <p class="mb-1"><strong>Method:</strong> ${paymentMethod}</p>
                                        <p class="mb-1"><strong>Type:</strong> ${jobCardType}</p>
                                        ${!selectedSubAdminId1 ? `<p class="mb-1"><strong>Vehicle:</strong> ${vehicleNo}</p>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td class="details-cell">
                                <a href="#${id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                            <td>${formatCurrency(totalAmount)}</td>
                            <td>${formatCurrency(discountAmount)}</td>
                            <td>${formatCurrency(paidAmount)}</td>
                            <td>${paymentDate}</td>
                            <td>${paymentMethod}</td>
                            <td>${jobCardType}</td>
                            ${!selectedSubAdminId1 ? `<td>${vehicleNo}</td>` : ''}`;
                        } else if (type === 'vendor') {
                            // const billNo = payment.bill_no || '-';
                            // const payableAmount = payment.total_amount;
                            // const totalPaidAmount = payment.paid_amount || 0;
                            // const paymentDate = payment.created_at || '-';
                            // const paymentMethod = payment.payment_mode || 'Pending';
                            // const vendorName = payment.vendor ? payment.vendor.name : '-';
                            const billNo = payment.invoice_number || payment.invoice_id || '-';  // ✅ FIX
                            const payableAmount = payment.total_amount || 0;  // ✅ FIX
                            const totalPaidAmount = payment.paid_amount || 0; // ✅ FIX
                            const paymentDate = payment.date ? new Date(payment.date).toLocaleDateString('en-IN') : '-';  // ✅ FIX
                            const paymentMethod = 'Paid'; // static or your logic
                            const vendorName = payment.vendor_name || '-'; // vendor not coming in mapped response

                            html += `
                            <td>
                                ${billNo}
                                <div class="collapse mt-2 d-lg-none" id="${id}">
                                    <div class="card card-body p-2 bg-light border">
                                        <p class="mb-1"><strong>Total Amount:</strong> ${formatCurrency(payableAmount)}</p>
                                        <p class="mb-1"><strong>Paid Amount:</strong> ${formatCurrency(totalPaidAmount)}</p>
                                        <p class="mb-1"><strong>Date:</strong> ${paymentDate}</p>
                                        <p class="mb-1"><strong>Method:</strong> ${paymentMethod}</p>
                                       <p class="mb-1">
    <strong>Vendor:</strong>
    <span class="vendor-name">${vendorName}</span>
</p>
                                    </div>
                                </div>
                            </td>
                            <td class="details-cell">
                                <a href="#${id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                            <td>${formatCurrency(payableAmount)}</td>
                            <td>${formatCurrency(totalPaidAmount)}</td>
                            <td>${paymentDate}</td>
                            <td>${paymentMethod}</td>
                            <td>${vendorName}</td>`;
                        }

                        html += '</tr>';
                        if (type === 'customer') {
                            totalPaid += parseFloat(payment.payment_amount);
                        }else{
                            totalPaid += parseFloat(payment.paid_amount || 0);
                        }
                    });
                } else {
                    html += `<tr><td colspan="${paidCols}" class="text-center text-muted">No record available</td></tr>`;
                }

                html += `</tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end pe-3 mt-2 mb-2">
                <div style="border: 1px solid #1b2850; border-radius: 5px; padding: 4px 10px; display: inline-block;">
                    <span style="font-weight: bold; font-size: 15px; color: #1b2850;">Total Paid:</span>
                    <span style="font-weight: bold; font-size: 15px; color: orange;">${formatCurrency(totalPaid.toFixed(2))}</span>
                </div>
            </div>`;

                // === PENDING PAYMENTS ===
                html += `<div class="card shadow mb-4">
                        <div class="card-header text-dark">
                            <h5 class="mb-0 fw-bold">Pending Payments</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>`;

                if (type === 'customer') {
                    html += `<th>Job Card</th>
                         <th class="details-column">Details</th>
                         <th>Total Amount</th>
                         <th>Remaining Amount</th>
                         <th>Type</th>
                         ${vehicleShown ? '<th>Vehicle No.</th>' : ''}`;
                } else if (type === 'vendor') {
                    html += `<th>Purchase No</th>
                         <th class="details-column">Details</th>
                         <th>Total Amount</th>
                         <th>Remaining Amount</th>
                         <th>Vendor</th>`;
                }

                html += `       </tr>
                            </thead>
                            <tbody>`;

                // compute pending table column count for correct colspan when no records
                const pendingCols = type === 'customer' ? (5 + (vehicleShown ? 1 : 0)) : 5;

                let totalPending = 0;

                if (data.pendingPayments.length > 0) {
                    data.pendingPayments.forEach((payment, index) => {
                        const id = `pending-${index}-${payment.id || Math.random().toString(36).substr(2, 9)}`;
                        html += '<tr>';
                        if (type === 'customer') {
                            const jobCardNo = payment.job_card_number;
                            const totalAmount = payment.total_subtotal_amount;
                            const remainingAmount = payment.remaining_amount;
                            const jobCardType = payment.type.charAt(0).toUpperCase() + payment.type.slice(1);
                            const vehicleNo = payment.vehicle_no || '-';

                            html += `
                            <td>
                                ${jobCardNo}
                                <div class="collapse mt-2 d-lg-none" id="${id}">
                                    <div class="card card-body p-2 bg-light border">
                                        <p class="mb-1"><strong>Total Amount:</strong> ${formatCurrency(totalAmount)}</p>
                                        <p class="mb-1"><strong>Remaining:</strong> ${formatCurrency(remainingAmount)}</p>
                                        <p class="mb-1"><strong>Type:</strong> ${jobCardType}</p>
                                        ${vehicleShown ? `<p class="mb-1"><strong>Vehicle:</strong> ${vehicleNo}</p>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td class="details-cell">
                                <a href="#${id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                            <td>${formatCurrency(totalAmount)}</td>
                            <td>${formatCurrency(remainingAmount)}</td>
                            <td>${jobCardType}</td>
                            ${vehicleShown ? `<td>${vehicleNo}</td>` : ''}`;

                        } else if (type === 'vendor') {
                            const billNo = payment.invoice_number || payment.id || '-'; // ✅ FIX
                            const payableAmount = payment.total_amount || 0; // ✅ FIX
                            const remainingAmount = payment.remaining_amount;
                            const vendorName = payment.vendor ? payment.vendor.name : '-';

                            html += `
                            <td>
                                ${billNo}
                                <div class="collapse mt-2 d-lg-none" id="${id}">
                                    <div class="card card-body p-2 bg-light border">
                                        <p class="mb-1"><strong>Total Amount:</strong> ${formatCurrency(payableAmount)}</p>
                                        <p class="mb-1"><strong>Remaining:</strong> ${formatCurrency(remainingAmount)}</p>
                                        <p class="mb-1"><strong>Vendor:</strong> ${vendorName}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="details-cell">
                                <a href="#${id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                            <td>${formatCurrency(payableAmount)}</td>
                            <td>${formatCurrency(remainingAmount)}</td>
                            <td>${vendorName}</td>`;
                        }
                        html += '</tr>';
                        totalPending += parseFloat(payment.remaining_amount);
                    });
                } else {
                    html += `<tr><td colspan="${pendingCols}" class="text-center text-muted">No record available</td></tr>`;
                }

                html += `</tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end pe-3 mt-2 mb-2">
                <div style="border: 1px solid #1b2850; border-radius: 5px; padding: 4px 10px; display: inline-block;">
                    <span style="font-weight: bold; font-size: 15px; color: #1b2850;">Total Pending:</span>
                    <span style="font-weight: bold; font-size: 15px; color: orange;">${formatCurrency(totalPending.toFixed(2))}</span>
                </div>
            </div>`;

                $('#paymentDetails').html(html);
            }


            $('#viewCsvBtn').on('click', function () {
                const type = $('#type_selector').val(); // 'customer' or 'vendor'
                if (!type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Type',
                        text: 'Please select Customer or Vendor before generating PDF.',
                    });
                    return;
                }

                let id, name, gstNum = '';
                if (type === 'customer') {
                    const selectedValue = $('#customer_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Customer',
                            text: 'Please select a customer before generating PDF.',
                        });
                        return;
                    }
                    const parts = selectedValue.split('|');
                    id = parts[0] !== 'null' ? parts[0] : '';
                    name = parts[1] || '';
                    gstNum = $('#customer_id option:selected').data('gst') || '';
                } else if (type === 'vendor') {
                    const selectedValue = $('#vendor_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Vendor',
                            text: 'Please select a vendor before generating PDF.',
                        });
                        return;
                    }
                    const parts = selectedValue.split('|');
                    id = parts[0] !== 'null' ? parts[0] : '';
                    name = parts[1] || '';
                }

                const months = $('#month').val();
                const year = $('#year').val();
                const vehicleNumber = $('#vehicle_number').length ? $('#vehicle_number').val().trim() : '';
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");

                Swal.fire({
                    title: 'Generating Excel...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/api/export-account-ledger',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                    },
                    data: {
                        type: type,               // send type to backend
                        id: id,                   // customer_id or vendor_id
                        name: name,               // customer_name or vendor_name
                        gst_num: gstNum,          // only for customer
                        month: months,
                        year: year,
                        vehicle_number: vehicleNumber,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (blob, status, xhr) {
                        Swal.close(); // close the loading modal

                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = "account_ledger.csv";
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/['"]/g, '');
                        }

                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);

                        Swal.fire({
                            icon: 'success',
                            title: 'Download Ready',
                            text: 'The Excel file has been successfully downloaded.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Failed to generate Excel file. Please try again.',
                        });
                        console.error(error);
                    }
                });
            });


            $('#viewPdfBtn').on('click', function () {
                const type = $('#type_selector').val(); // 'customer' or 'vendor'
                if (!type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Type',
                        text: 'Please select Customer or Vendor before generating PDF.',
                    });
                    return;
                }

                let id, name, gstNum = '';
                if (type === 'customer') {
                    const selectedValue = $('#customer_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Customer',
                            text: 'Please select a customer before generating PDF.',
                        });
                        return;
                    }
                    const parts = selectedValue.split('|');
                    id = parts[0] !== 'null' ? parts[0] : '';
                    name = parts[1] || '';
                    gstNum = $('#customer_id option:selected').data('gst') || '';
                } else if (type === 'vendor') {
                    const selectedValue = $('#vendor_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Vendor',
                            text: 'Please select a vendor before generating PDF.',
                        });
                        return;
                    }
                    const parts = selectedValue.split('|');
                    id = parts[0] !== 'null' ? parts[0] : '';
                    name = parts[1] || '';
                }

                const months = $('#month').val();
                const year = $('#year').val();
                const vehicleNumber = $('#vehicle_number').length ? $('#vehicle_number').val().trim() : '';
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                console.log(selectedSubAdminId);
                
                const authToken = localStorage.getItem("authToken");

                Swal.fire({
                    title: 'Generating PDF...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '/api/account-ledger/download-pdf',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        type: type,               // send type to backend
                        id: id,                   // customer_id or vendor_id
                        name: name,               // customer_name or vendor_name
                        gst_num: gstNum,          // only for customer
                        month: months,
                        year: year,
                        vehicle_number: vehicleNumber,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (blob, status, xhr) {
                        Swal.close(); // Close the loading popup

                        const contentType = xhr.getResponseHeader('Content-Type');
                        if (contentType && contentType.includes('application/json')) {
                            const reader = new FileReader();
                            reader.onload = function() {
                                const json = JSON.parse(reader.result);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Download Failed',
                                    text: json.message
                                });
                            };
                            reader.readAsText(blob);
                            return;
                        }

                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = "account_ledger.pdf";
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/['"]/g, '');
                        }

                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);

                        Swal.fire({
                            icon: 'success',
                            title: 'Download Ready',
                            text: 'PDF has been successfully downloaded.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Total records (paid + pending) exceed 1000. Please refine your filter.',
                        });
                        console.error(error);
                    }
                });
            });
        });
    </script>
@endpush