@extends('layout.app')

@section('title', 'Advance Payment Details')

@section('content')
    <style>
        table.datanew {
            width: 100% !important;
        }
        #DataTables_Table_0_info {
            float: left;
        }

        .table-scroll-top {
            display: none;
        }

        button#exportBtn {
            padding: 3px;
        }

        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 20px;
            width: 100%;
            margin-bottom: 5px;
        }

        .table-scroll-top div {
            height: 1px;
        }

        .grand-total-box {
            display: block;
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f8f9fa;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .grand-total-box strong {
            color: #1b2850;
            margin-right: 4px;
        }

        .grand-total-box span {
            color: #ff9f43;
        }

        .pagination-controls {
            display: none;
        }

        /* Ensure select shows full placeholder text */
        .form-select-sm {
            min-width: 130px;
        }


        /* Desktop: show all columns normally */
        @media (min-width: 769px) {
            #advance-payment-table thead th,
            #advance-payment-table tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            #advance-payment-table thead th.details-column,
            #advance-payment-table tbody td.details-cell {
                display: none !important;
            }

            /* Hide expandable detail rows on desktop */
            .payment-details-row {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {

            #advance-payment-table thead th:nth-child(n+3),
            #advance-payment-table tbody td:nth-child(n+3) {
                display: none !important;
            }

            #advance-payment-table {
                table-layout: fixed;
                width: 100% !important;
            }

            #advance-payment-table thead th:first-child,
            #advance-payment-table tbody td:first-child {
                width: 80% !important;
            }

            #advance-payment-table thead th.details-column,
            #advance-payment-table tbody td.details-cell {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 20% !important;
            }

            .staff-name {
                display: inline-block !important;
                max-width: 100% !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            .staff-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        /* Expandable details row styles (matches productlist pattern) */
        .payment-details-row {
            display: none;
        }

        .payment-details-row.show {
            display: table-row;
        }

        .payment-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        #advance-payment-table tbody tr.payment-details-row td.payment-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .payment-details-list {
            margin-bottom: 15px;
        }

        .payment-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .payment-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .payment-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .payment-detail-value-simple {
            color: #1b2850;
            font-size: 13px;
            text-align: right;
            word-break: break-word;
        }

        .mobile-action-buttons-simple {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-icon-mobile {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #1b2850;
            background: transparent;
            transition: all 0.3s;
            border: 2px solid #1b2850;
            cursor: pointer;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .btn-icon-mobile:hover {
            background: #1b2850;
            color: white;
            transform: scale(1.1);
        }

        .mobile-toggle-btn-payment {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 16px;
            font-weight: bold;
            line-height: 1;
            transition: all 0.3s;
        }

        .mobile-toggle-btn-payment:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-payment.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-payment.minus:hover {
            background: #c82333;
        }

        /* Pagination styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 4px;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        @media screen and (max-width: 767px) {
            .table-scroll-top {
                display: block;
            }

            div#DataTables_Table_0_filter {
                margin-top: 10px !important;
            }

            .table-top {
                flex-direction: row;
                margin-bottom: 0 !important;
            }

            .table-top .wordset {
                margin-top: 0 !important;
            }

            .dataTables_length {
                margin-left: .8rem !important;
                margin-bottom: .1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Advance Payment Details</h4>

            </div>
            <div class="page-btn">
                @if (app('hasPermission')(23, 'add'))
                    <a href="{{ route('advance_pay.create') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img"> New Advance
                        Payment
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="table-top mb-3 d-block">
                    <div class="row g-3 align-items-end">

                        <!-- Staff Name -->
                        <div class="col-6 col-sm-4 col-md-3">
                            <label for="filterStaffName" class="form-label mb-1">Staff Name</label>
                            <select id="filterStaffName" class="form-select form-select-sm">
                                <option value="">Select Staff</option>
                                <!-- Dynamic staff options here -->
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="col-6 col-sm-4 col-md-3">
                            <label for="filterYear" class="form-label mb-1">Year</label>
                            <select id="filterYear" class="form-select form-select-sm">
                                <option value="">Select Year</option>
                                <!-- Dynamic year options -->
                            </select>
                        </div>

                        <!-- Month -->
                        <div class="col-6 col-sm-4 col-md-3">
                            <label for="filterMonth" class="form-label mb-1">Month</label>
                            <select id="filterMonth" class="form-select form-select-sm">
                                <option value="">Select Month</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Export Button -->
                        <div class="col-6 col-sm-12 col-md-2 text-start text-md-end">
                            <button id="exportBtn" class="btn btn-success btn-sm w-100 w-md-auto">
                                <i class="fa fa-download me-1"></i> Export
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Total Payments -->
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <div class="grand-total-box">
                                <strong>Total Payments:</strong>
                                <span id="grandTotalAmount">₹0.00</span>
                            </div>
                        </div>

                        <!-- Total Paid -->
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <div class="grand-total-box">
                                <strong>Total Paid:</strong>
                                <span id="grandPaidAmount">₹0.00</span>
                            </div>
                        </div>

                        <!-- Total Pending -->
                        <div class="col-12 col-md-auto">
                            <div class="grand-total-box">
                                <strong>Total Pending:</strong>
                                <span id="grandPendingAmount">₹0.00</span>
                            </div>
                        </div>

                    </div>
                </div>


                
                <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="advance-payment-table">
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th class="details-column">Details</th>
                                <th>Amount</th>
                                <th>Paid Amount</th>
                                <th>Pending Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="paymentTableBody">
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="pagination-controls d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="per-page-select" class="mb-0 text-muted" style="font-size: 14px;">Show per page :</label>
                        <select id="per-page-select" class="form-select form-select-sm" style="width: auto; min-width: 70px;">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3 text-muted" style="font-size: 14px;">
                            Showing <span id="pagination-from">0</span> to <span id="pagination-to">0</span> of
                            <span id="pagination-total">0</span> entries
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <nav aria-label="Advance payments pagination">
                            <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            let currentPage = 1;
            let lastPage = 1;
            let perPage = parseInt($("#per-page-select").val(), 10) || 10;

            function getFilters() {
                const filters = {
                    page: currentPage,
                    per_page: perPage
                };

                if (selectedSubAdminId) {
                    filters.selectedSubAdminId = selectedSubAdminId;
                }

                const staffId = $("#filterStaffName").val();
                const year = $("#filterYear").val();
                const month = $("#filterMonth").val();

                if (staffId) filters.staff_id = staffId;
                if (year) filters.year = year;
                if (month) filters.month = month;

                return filters;
            }

            function formatMoney(value) {
                return "\u20B9" + (parseFloat(value) || 0).toFixed(2);
            }

            function formatTitleCase(value, fallback = 'N/A') {
                if (!value) {
                    return fallback;
                }

                return String(value).replace(/\b\w/g, c => c.toUpperCase());
            }

            function updateSummary(summary = {}) {
                $("#grandTotalAmount").text(formatMoney(summary.total_amount));
                $("#grandPaidAmount").text(formatMoney(summary.total_paid_amount));
                $("#grandPendingAmount").text(formatMoney(summary.total_pending_amount));
            }

            function updateTopScrollbar() {
                const topScroll = document.querySelector('.table-scroll-top');
                const tableResponsive = document.querySelector('.table-responsive');
                const tableElement = document.getElementById('advance-payment-table');

                if (!topScroll || !tableResponsive || !tableElement) {
                    return;
                }

                const topInnerDiv = topScroll.querySelector('div');
                topInnerDiv.style.width = tableElement.scrollWidth + 'px';

                topScroll.onscroll = () => {
                    tableResponsive.scrollLeft = topScroll.scrollLeft;
                };

                tableResponsive.onscroll = () => {
                    topScroll.scrollLeft = tableResponsive.scrollLeft;
                };
            }

            function updatePaginationUI(pagination) {
                if (!pagination) {
                    $('.pagination-controls').hide();
                    $('#pagination-from').text(0);
                    $('#pagination-to').text(0);
                    $('#pagination-total').text(0);
                    $('#pagination-numbers').empty();
                    return;
                }

                let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let paginationHtml = '';
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);

                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }

            // Fetch filters on page load
            fetchFilters();

            // Fetch table data on page load
            fetchAdvancePayments(1);

            // Fetch filters via AJAX
            function fetchFilters() {
                $.ajax({
                    url: "/api/advance-payment-filters",
                    type: "GET",
                    data: selectedSubAdminId ? {
                        selectedSubAdminId: selectedSubAdminId
                    } : {},
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.staff) {
                            let staffOptions = '<option value="">Select Staff</option>';
                            $.each(response.staff, function(i, staff) {
                                staffOptions +=
                                    `<option value="${staff.id}">${staff.name}</option>`;
                            });
                            $("#filterStaffName").html(staffOptions);
                        }

                        if (response.years) {
                            let yearOptions = '<option value="">Select Year</option>';
                            $.each(response.years, function(i, year) {
                                yearOptions +=
                                    `<option value="${year}">${year}</option>`;
                            });
                            $("#filterYear").html(yearOptions);
                        }
                    }
                });
            }

            // Fetch advance payments
            function fetchAdvancePayments(page = 1) {
                currentPage = page;

                $.ajax({
                    url: "/api/advance-payments",
                    type: "GET",
                    dataType: "json",
                    data: getFilters(),
                    beforeSend: function() {
                        $("#paymentTableBody").html(
                            '<tr><td colspan="9" class="text-center">Loading...</td></tr>'
                        );
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status && Array.isArray(response.data)) {
                            let tableBody = "";

                            if (response.pagination) {
                                currentPage = response.pagination.current_page;
                                lastPage = response.pagination.last_page;
                                updatePaginationUI(response.pagination);
                            } else {
                                updatePaginationUI(null);
                            }

                            window.paymentDataMap = {};

                            $.each(response.data, function(index, payment) {
                                let amount = parseFloat(payment.amount) || 0;
                                let paid = parseFloat(payment.paid_amount) || 0;
                                let pending = parseFloat(payment.pending) || 0;
                                let staffName = formatTitleCase(payment.staff_name, 'N/A');
                                let method = formatTitleCase(payment.method, 'N/A');
                                let reason = formatTitleCase(payment.reason, '-');

                                // Store data for expandable row
                                window.paymentDataMap[payment.id] = {
                                    id: payment.id,
                                    amount: amount,
                                    paid: paid,
                                    pending: pending,
                                    date: payment.date,
                                    method: method,
                                    reason: reason
                                };

                                // Toggle button (matches productlist style)
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-payment" onclick="togglePaymentRowDetails('${payment.id}')" data-payment-id="${payment.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                tableBody += `
                                    <tr>
                                        <td><span class="staff-name truncated">${staffName}</span></td>
                                        <td class="details-cell">${detailsToggle}</td>
                                        <td>${formatMoney(amount)}</td>
                                        <td>${formatMoney(paid)}</td>
                                        <td>${formatMoney(pending)}</td>
                                        <td>${payment.date}</td>
                                        <td>${method}</td>
                                        <td style="white-space: normal; word-wrap: break-word; word-break: break-word;">${reason}</td>
                                        <td>${getActionButtons(payment)}</td>
                                    </tr>
                                `;
                            });

                            if (!tableBody) {
                                tableBody =
                                    '<tr><td colspan="9" class="text-center">No advance payments found.</td></tr>';
                            }

                            $("#paymentTableBody").html(tableBody);
                            updateSummary(response.summary || {});
                            updateTopScrollbar();

                        } else {
                            $("#paymentTableBody").html(
                                '<tr><td colspan="9" class="text-center">No advance payments found.</td></tr>'
                            );
                            updateSummary();
                            updatePaginationUI(null);
                        }
                    },
                    error: function() {
                        $("#paymentTableBody").html(
                            '<tr><td colspan="9" class="text-center text-danger">Error loading data.</td></tr>'
                        );
                        updateSummary();
                        updatePaginationUI(null);
                    }
                });
            }

            // Helper function to generate action buttons (desktop table)
            function getActionButtons(payment) {
                let buttons = '';
                @if (app('hasPermission')(23, 'view'))
                buttons += `<a href="/advance_pay/${payment.id}" class="me-2">
                               <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="img">
                            </a>`;
                @endif
                @if (app('hasPermission')(23, 'edit'))
                buttons += `<a href="/advance_pay/${payment.id}/edit" class="me-2">
                                 <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="img">
                            </a>`;
                @endif
                @if (app('hasPermission')(23, 'delete'))
                buttons += `<a href="javascript:void(0);" class="delete-btn" data-id="${payment.id}">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="img">
                            </a>`;
                @endif
                return buttons;
            }

            // Helper function to generate mobile circular action buttons
            function getMobileActionButtons(payment) {
                let buttons = '';
                @if (app('hasPermission')(23, 'view'))
                buttons += `<a class="btn-icon-mobile" href="/advance_pay/${payment.id}" title="View">
                                <i class="fas fa-eye"></i>
                            </a>`;
                @endif
                @if (app('hasPermission')(23, 'edit'))
                buttons += `<a class="btn-icon-mobile" href="/advance_pay/${payment.id}/edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>`;
                @endif
                @if (app('hasPermission')(23, 'delete'))
                buttons += `<a class="btn-icon-mobile delete-btn" href="javascript:void(0);" data-id="${payment.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>`;
                @endif
                return buttons;
            }

            // Build expandable details row content (matches productlist pattern)
            function buildPaymentExpandableRowContent(data) {
                return `
                    <td colspan="9" class="payment-details-content">
                        <div class="payment-details-list">
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Amount:</span>
                                <span class="payment-detail-value-simple" style="font-weight: bold;">${formatMoney(data.amount)}</span>
                            </div>
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Paid Amount:</span>
                                <span class="payment-detail-value-simple">${formatMoney(data.paid)}</span>
                            </div>
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Pending Amount:</span>
                                <span class="payment-detail-value-simple">${formatMoney(data.pending)}</span>
                            </div>
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Date:</span>
                                <span class="payment-detail-value-simple">${data.date}</span>
                            </div>
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Method:</span>
                                <span class="payment-detail-value-simple">${data.method}</span>
                            </div>
                            <div class="payment-detail-row-simple">
                                <span class="payment-detail-label-simple">Reason:</span>
                                <span class="payment-detail-value-simple">${data.reason}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${getMobileActionButtons(data)}
                        </div>
                    </td>
                `;
            }

            // Toggle expandable row (matches productlist toggleProductRowDetails pattern)
            window.togglePaymentRowDetails = function(paymentId) {
                const btn = $(`.mobile-toggle-btn-payment[data-payment-id="${paymentId}"]`);
                if (btn.length === 0) return;

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.payment-details-row[data-payment-id="${paymentId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const paymentData = window.paymentDataMap && window.paymentDataMap[paymentId];
                    if (!paymentData) return;

                    detailsRow = $('<tr>')
                        .addClass('payment-details-row')
                        .attr('data-payment-id', paymentId)
                        .html(buildPaymentExpandableRowContent(paymentData));
                    row.after(detailsRow);
                }

                if (detailsRow.hasClass('show')) {
                    detailsRow.removeClass('show');
                    btn.removeClass('minus');
                    icon.text('+');
                } else {
                    detailsRow.addClass('show');
                    btn.addClass('minus');
                    icon.text('-');
                }
            };

            // When any filter changes, reload data
            $("#filterStaffName, #filterYear, #filterMonth").on("change", function() {
                fetchAdvancePayments(1);
            });

            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = parseInt($(this).data('page'), 10);
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchAdvancePayments(page);
                }
            });

            $('#per-page-select').on('change', function() {
                perPage = parseInt($(this).val(), 10) || 10;
                fetchAdvancePayments(1);
            });

            // Handle Delete
            $(document).on("click", ".delete-btn", function() {
                let paymentId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43", // Confirm button color (orange)
                    cancelButtonColor: "#6c757d", // Cancel button color (gray)
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/advance-payments/${paymentId}`,
                            type: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"),
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                // Swal.fire("Deleted!", response.message, "success");
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43", // Custom OK button color
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    fetchAdvancePayments(currentPage);
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = "Failed to delete customer.";
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43", // custom orange color
                                    confirmButtonText: "OK"
                                });
                            },
                        });
                    }
                });
            });

            document.getElementById('exportBtn').addEventListener('click', function() {
                let staffId = document.getElementById('filterStaffName')?.value || '';
                let year = document.getElementById('filterYear')?.value || '';
                let month = document.getElementById('filterMonth')?.value || '';

                // Prepare query params object
                let params = {};

                if (staffId) params.staff_id = staffId;
                if (year) params.year = year;
                if (month) params.month = month;
                if (typeof selectedSubAdminId !== 'undefined' && selectedSubAdminId) {
                    params.selectedSubAdminId = selectedSubAdminId;
                }

                // Convert params object to query string
                let query = new URLSearchParams(params).toString();
                if (query.length > 0) {
                    query = '?' + query;
                }

                let token = localStorage.getItem("authToken");

                fetch('/api/export-advance-payments' + query, {
                        method: 'GET',
                        headers: {
                            "Authorization": "Bearer " + token
                        }
                    })
                    .then(async res => {
                        if (!res.ok) {
                            // Try to parse JSON error message
                            let errorData;
                            try {
                                errorData = await res.json();
                            } catch {
                                errorData = {
                                    message: 'Unknown error occurred'
                                };
                            }
                            // Show SweetAlert error popup
                            Swal.fire({
                                icon: 'error',
                                title: 'Export Failed',
                                text: errorData.message || 'Failed to export data',
                            });
                            throw new Error(errorData.message || 'Export failed');
                        }
                        return res.blob();
                    })
                    .then(blob => {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = 'advance_payments.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    })
                    .catch(err => {
                        if (err.message !== 'Export failed') {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred',
                            });
                        }
                    });
            });

            $(window).on('resize', function() {
                updateTopScrollbar();
            });




        });
    </script>
@endpush
