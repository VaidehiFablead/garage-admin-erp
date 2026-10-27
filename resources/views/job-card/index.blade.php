@extends('layout.app')

@section('title', 'All Job Cards')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .ft_size {
            height: 31px;
            width: 119px;
        }

        #clear-filter {
            width: 64px;
            height: 30px;
            font-size: 0.875rem;
        }

        #closeHistoryBtn {
            line-height: 1;
            font-size: 1.2rem;
            padding: 0.25rem 0.5rem;
        }

        .form-select {
            font-size: 0.875rem !important;
        }

        #filter-date {
            height: 35px;
        }

        .grand-total-box {
            border: 2px solid #0d1b3e;
            padding: 4px 12px;
            border-radius: 5px;
            /* dis  play: inline-block; */
            white-space: nowrap;
            /* Prevent text wrapping */
        }

        .grand-total-box span {
            color: #ff6600;
            font-weight: bold;
        }

        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px !important;
        }

        .mobile-show {
            display: none;
        }

        .detail-row {
            display: none;
            background-color: #f8f9fa;
        }

        @media (max-width: 767px) {
            .grand-total-box {
                display: block;
                width: 100%;
                text-align: center;
                margin-top: 5px;
                font-size: 13px;
                padding: 10px;
            }

            #filterYear,
            #filterMonth,
            #filter-date,
            #clear-filter {
                width: 100% !important;
            }

            .mobile-hide {
                display: none !important;
            }

            .mobile-show {
                display: table-cell;
            }

            .detail-row {
                display: none;
                background-color: #f8f9fa;
            }

            .detail-row.active {
                display: table-row;
            }

            .toggle-details {
                cursor: pointer;
                color: #ff9f43;
                /* Orange for + */
                font-size: 1.1rem;
                vertical-align: middle;
                text-align: center;
                width: 30px;
            }

            .toggle-details.active {
                color: #dc3545;
                /* Red for - */
            }

            .table-responsive {
                border: none;
            }

            table.table {
                min-width: 100% !important;
            }
        }

        @media screen and (min-width:320px) and (max-width:390px) {

            #filterCustomer,
            #filterCustomer+.select2-container {
                width: 100% !important;
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Job Card</h4>
                <!-- <h6>Manage your purchases</h6> -->
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(16, 'add'))
                    <a href="{{ route('job_card.create') }}" class="btn btn-sm btn-added">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">New Job Card
                    </a>
                @endif
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top mb-3">
                    <div class="row align-items-center text-center g-2">


                        @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                            <!-- Total Labour -->
                            <div class="col-md-auto col-12">
                                <div class="grand-total-box" style="font-size: 14px;">
                                    <strong>Total Labour :</strong>
                                    <span id="totallabour">₹0.00</span>
                                </div>
                            </div>

                            <!-- Total Parts -->
                            <div class="col-md-auto col-12">
                                <div class="grand-total-box" style="font-size: 14px;">
                                    <strong>Total Parts :</strong>
                                    <span id="partstotal">₹0.00</span>
                                </div>
                            </div>
                        @endif
                        @php
                            $user = auth()->user();

                        @endphp

                        @if ($user->role == 'admin')
                            <!-- Grand Total -->
                            <div class="col-md-auto col-12">
                                <div class="grand-total-box" style="font-size: 14px;">
                                    <strong>Grand Total :</strong>
                                    <span id="grandTotal">₹0.00</span>
                                </div>
                            </div>
                        @endif

                        <!-- Year Filter -->
                        <div class="col-md-auto col-6">
                            <select id="filterYear" class="form-select form-select-sm"
                                style="font-size: 0.875rem; width: 100px;">
                                <option value="" {{ request('year') == '' ? 'selected' : '' }}>All Years</option>
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}"
                                        {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="col-md-auto col-6">
                            <select id="filterMonth" class="form-select form-select-sm"
                                style="font-size: 0.875rem; width: 110px;">
                                <option value="">All Months</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Customer Filter -->
                        <div class="col-md-auto col-12" style="min-width: 180px;">
                            <select id="filterCustomer" class="form-select form-select-sm select2"
                                style="font-size: 0.875rem;">
                                <option value="">All Customers</option>
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="col-md-auto col-6">
                            <input type="date" id="filter-date" placeholder="Choose Date"
                                class="form-control form-control-sm" style="width: 130px;" value="{{ request('date') }}">
                        </div>

                        <!-- Clear Button -->
                        <div class="col-md-auto col-6">
                            <button type="button" id="clear-filter" class="btn btn-secondary btn-sm"
                                style="width: 80px;">Clear</button>
                        </div>

                    </div>
                </div>







                <!-- Filter Inputs Card -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Name" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Reference No" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <select class="form-select">
                                        <option>Completed</option>
                                        <option>Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group d-flex justify-content-end">
                                    <a class="btn btn-filters ms-auto">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-whites.svg' }}"
                                            alt="img">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <!-- <div class="table-scroll-top"
                                                                                                    style="overflow-x: auto; overflow-y: hidden; height: 20px; margin-bottom: 5px;">
                                                                                                    <div style="height: 1px;"></div>
                                                                                                </div> -->

                <div class="table-responsive mt-3" style="overflow-x: auto;">

                    <table class="table" id="job-card-table-body">
                        <thead>
                            <tr>

                                <th>Job Card No</th>
                                <th class="mobile-show text-center">Details</th>
                                <th class="mobile-hide">Date</th>
                                <th class="mobile-hide">Customer Name</th>
                                <th class="mobile-hide">Vehicle No</th>
                                <th class="mobile-hide">Total Labour</th>
                                <th class="mobile-hide">Total Parts</th>
                                <th class="mobile-hide">Grand Total</th>
                                <th class="text-center mobile-hide">Action</th>
                            </tr>
                        </thead>
                        <tbody id="job-card-tbody"></tbody>
                    </table>
                    <div class="pagination-controls mt-3 text-end">
                        <button id="prevBtn" class="btn btn-sm btn-secondary">Prev</button>
                        <span id="pageInfo"></span>
                        <button id="nextBtn" class="btn btn-sm btn-secondary">Next</button>
                    </div>
                    <div id="dynamic-modals"></div>

                </div>

            </div>
        </div>

    </div>

    <!-- Make Payment Modal -->
    <form id="makePaymentForm">
        <div class="modal fade" id="makePaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">

                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-sm btn-cancel text-white" id="viewHistoryBtn">View
                                Payment History</button>
                        </div>

                        <!-- ✅ Payment History Container -->
                        <div id="paymentHistoryBox" class="border p-2 rounded bg-white d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Payment History</h6>

                                <!-- 👇 Visible Close Button -->
                                <button type="button" class="btn btn-sm btn-danger" id="closeHistoryBtn">
                                    &times; <!-- This renders an “×” symbol -->
                                </button>
                            </div>

                            <ul id="paymentHistoryList" class="list-unstyled mb-0"
                                style="max-height: 200px; overflow-y: auto;">
                                <!-- Populated via JavaScript -->
                            </ul>
                        </div>



                        <div class="border p-2 rounded bg-light">
                            <strong>Total Amount:</strong> ₹<span id="emiTotal"></span><br>
                            <strong>Remaining Amount:</strong> ₹<span id="remainingAmountDisplay">0.00</span>
                            <div id="emiMonthlyRow">
                                <strong>Monthly EMI:</strong> ₹<span id="emiMonthly"></span><br>
                            </div>
                            <div id="emiDurationRow">
                                <strong>Duration:</strong> <span id="emiDuration"></span> months<br>
                            </div>
                        </div>

                        <!-- ✅ View Payment History Button -->



                        <br>

                        <!-- Payment Method -->
                        <div class="mb-3" id="paymentMethodDiv">
                            <label for="paymentMethodSelect" class="form-label">Select Payment Method</label>
                            <select class="form-select" id="paymentMethodSelect" name="payment_method">
                                <option value="" selected disabled>Select</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cash_online">Cash + Online</option>
                                <option value="emi">EMI</option>
                            </select>
                            <div class="text-danger" id="paymentMethodError"></div>
                        </div>

                        <div class="mb-3 d-none" id="cashOnlineTypeDiv">
                            <label for="cashOnlineTypeSelect" class="form-label">Select Cash + Online Type</label>
                            <select class="form-select" id="cashOnlineTypeSelect" name="cash_online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_online_fully">Cash + Online Fully</option>
                                <option value="cash_online_partially">Cash + Online Partially</option>
                            </select>
                            <div class="text-danger" id="cashOnlineTypeError"></div>
                        </div>

                        <!-- Fully Cash + Online -->
                        <!-- Fully Cash + Online -->
                        <div class="mb-3 d-none" id="fullyCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="number" id="cashOnlineFullAmount" name="fully_cash_amount"
                                class="form-control" min="0" step="0.01">
                            <div class="text-danger" id="cashOnlineFullAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="number" id="upiOnlineFullAmount" name="full_online_amount"
                                class="form-control" readonly>
                            <div class="text-danger" id="upiOnlineFullAmountError"></div>
                        </div>

                        <!-- Partial Cash + Online -->
                        <div class="mb-3 d-none" id="partialCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="number" id="cashOnlinePartialAmount" name="cash_amount" class="form-control"
                                min="0" step="0.01">
                            <div class="text-danger" id="cashOnlinePartialAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="number" id="upiOnlinePartialAmount" name="online_amount" class="form-control"
                                min="0" step="0.01">
                            <div class="text-danger" id="upiOnlinePartialAmountError"></div>
                            <label class="mt-2">Remaining Amount</label>
                            <input type="number" id="remainingCashOnlineAmount" name="remaining_amount"
                                class="form-control" readonly>
                        </div>







                        <div class="mb-3 d-none" id="onlineTypeDiv">
                            <label for="onlineTypeSelect" class="form-label">Select Online Type</label>
                            <select class="form-select" id="onlineTypeSelect" name="online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="online_fully">Online Fully</option>
                                <option value="online_partially">Online Partially</option>
                            </select>
                            <div class="text-danger" id="onlineTypeError"></div>
                        </div>

                        <!-- Paid Type Dropdown (Hidden by default) -->
                        <div class="mb-3 d-none" id="paidTypeDiv">
                            <label for="paidTypeSelect" class="form-label">Paid Type</label>
                            <select class="form-select" id="paidTypeSelect" name="paid_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_partially">Cash Partially</option>
                                <option value="cash_fully">Cash Fully</option>
                            </select>
                            <div class="text-danger" id="paidTypeError"></div>
                        </div>

                        <!-- UPI Amount Input -->
                        <div class="mb-3 d-none" id="upiAmountDiv">
                            <label for="upiAmountInput" class="form-label">Online Amount</label>
                            <input type="number" class="form-control" id="upiAmountInput" name="upi_online_amount"
                                readonly>
                            <div class="text-danger" id="upiAmountError"></div>
                        </div>



                        <!-- Partially Paid Fields -->
                        <div class="mb-3 d-none" id="partialPaidFields">
                            <label for="partialAmount" class="form-label">Enter Amount</label>
                            <input type="number" class="form-control mb-2" id="partialAmount" name="amount"
                                min="1" step="0.01">
                            <div class="text-danger" id="partialAmountError"></div>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                <div style="flex: 1;">
                                    <label for="pendingAmount" class="form-label">Pending Amount</label>
                                    <input type="number" class="form-control" id="pendingAmount"
                                        name="cash_pending_amount" readonly>
                                </div>
                                <div style="flex: 1;">
                                    <label for="pending_date" class="form-label">Next Payment Date</label>
                                    <input type="date" class="form-control" id="pending_date" name="pending_date">
                                </div>
                            </div>
                        </div>

                        <!-- Fully Paid Fields -->
                        <div class="mb-3 d-none" id="fullyPaidFields">
                            <label class="form-label">Cash Amount</label>
                            <input type="number" class="form-control" id="cashAmount" name="cashAmount" min="0"
                                step="0.01">
                            <div class="text-danger" id="cashAmountError"></div>
                        </div>


                        <!-- Payment Type Dropdown -->
                        <div class="mb-3" id="paymentTypeDiv">
                            <label for="paymentType" class="form-label">Payment Type</label>
                            <select class="form-select" id="paymentType">
                                <option value="emi">EMI</option>
                                <option value="fully">Fully</option>
                            </select>
                        </div>


                        <!-- EMI Month Field (editable) -->
                        <div class="mb-3 d-none" id="emiMonthDiv">
                            <label for="emiMonthInput" class="form-label">Enter EMI Month</label>
                            <input type="number" class="form-control" id="emiMonthInput" name="emi_month"
                                min="1">
                            <small class="text-danger d-none" id="emiMonthError">Month exceeds allowed EMI
                                duration.</small>
                        </div>

                        <!-- Calculated EMI Total -->
                        <div class="mb-3 d-none" id="emiTotalCalculatedDiv">
                            <label for="emiTotalCalculated" class="form-label">EMI Total</label>
                            <!-- <input type="number" class="form-control" id="emiTotalCalculated" name="emi_total" readonly> -->
                            <input type="number" class="form-control" id="emiTotalCalculated" readonly>
                        </div>

                        <!-- Fully Payment Field (editable) -->
                        <div class="mb-3 d-none" id="fullyAmountDiv">
                            <label for="amount" class="form-label">Enter Amount</label>
                            <!-- <input type="number" class="form-control" id="amount" name="emi_total" readonly> -->
                            <input type="number" class="form-control" id="amount" readonly>
                            <div class="text-danger" id="amountError"></div>


                        </div>

                        <div class="mb-3 d-none" id="paymentTypeDiv1">
                            <label for="paymentType1" class="form-label">Payment Type</label>
                            <select class="form-select" id="paymentType1">
                                <option value="emi">EMI</option>
                                <option value="fully">Fully</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="emiMonthDivNew">
                            <label for="emiMonthInputNew" class="form-label">Enter EMI Month</label>
                            <input type="number" class="form-control" id="emiMonthInputNew" name="emi_month_new"
                                min="1">
                            <small class="text-danger d-none" id="emiMonthErrorNew">Month exceeds allowed EMI
                                duration.</small>
                            <div class="text-danger" id="emiMonthErrornew"></div>
                            <input type="hidden" id="newEmiHidden" name="new_emi_value" value="new_emi">
                        </div>

                        <!-- Calculated EMI Total -->
                        <div class="mb-3 d-none" id="emiTotalCalculatedDivNew">
                            <label for="emiTotalCalculatedNew" class="form-label">EMI Amount</label>
                            <input type="number" class="form-control" id="emiTotalCalculatedNew" name="emi_total_new"
                                readonly>
                        </div>




                        <!-- Cleaned Hidden Inputs (no duplicate name attributes) -->
                        <input type="hidden" id="paymentJobCardId" name="job_card_id">
                        <input type="hidden" id="remainingAmountHidden" name="remaining_amount">
                        <input type="hidden" id="paymentMethodHidden" name="payment_type">
                        <!-- <input type="hidden" id="emiTotalHidden" name="emi_total"> -->
                        <!-- <input type="hidden" id="cashAmountHidden" name="cash_amount"> -->
                        <!-- <input type="hidden" id="upiAmountHidden" name="upi_amount"> -->



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
        </div>



        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        {{-- script for convert quotation to jobcard --}}
        <script>
            $(document).on('click', '.convert-to-sales-btn', function() {

                let id = $(this).data('id');
                let authToken = localStorage.getItem("authToken");

                Swal.fire({
                    title: "Convert to Sales?",
                    text: "Quotation will be converted to Job Card",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    confirmButtonText: "Yes Convert"
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: "/api/convert-quotation-to-jobcard/" + id,
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken
                            },
                            success: function(response) {

                                if (response.status) {

                                    Swal.fire({
                                        icon: "success",
                                        title: "Converted",
                                        text: response.message
                                    }).then(() => {
                                        location.reload();
                                    });

                                } else {

                                    Swal.fire("Error", response.message, "error");

                                }

                            }
                        });

                    }

                });

            });
        </script>

        <script>
            $(document).ready(function() {
                const authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const canEdit = @json(app('hasPermission')(16, 'edit'));
                const canView = @json(app('hasPermission')(16, 'view'));
                const canDelete = @json(app('hasPermission')(16, 'delete'));

                let currentPage = 1;
                let lastPage = 1;

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function renderCustomerOptions(customers, selectedCustomer) {
                    let options = '<option value="">All Customers</option>';

                    $.each(customers || [], function(_, customer) {
                        const isSelected = String(selectedCustomer || '') === String(customer.id) ? 'selected' :
                            '';
                        options +=
                            `<option value="${customer.id}" ${isSelected}>${escapeHtml(customer.name || '-')}</option>`;
                    });

                    $('#filterCustomer').html(options).val(selectedCustomer || '').trigger('change.select2');
                }

                function buildActionButtons(jobCard, mobile = false) {
                    let buttons = '';
                    const linkClass = mobile ? 'btn btn-sm' : 'me-3';
                    const deleteClass = mobile ? 'btn btn-sm delete-jobcard-btn' : 'delete-jobcard-btn mt-2';
                    const deleteStyle = mobile ? '' : 'style="border: none; background: none; padding: 0;"';

                    // if (jobCard.quotation_status === 'quotation') {
                    //     buttons += `
            //         <button class="btn btn-success btn-sm convert-to-sales-btn" data-id="${jobCard.id}">
            //             Convert to JobCard
            //         </button>
            //     `;
                    // }

                    if (canEdit) {
                        buttons += `
                            <a class="${linkClass}" href="/job-cards/${jobCard.id}/edit" title="Edit">
                                <i class="fas fa-edit" style="${mobile ? '' : 'font-size: 20px;'}"></i>
                            </a>
                        `;
                    }

                    if (canView) {
                        buttons += `
                            <a class="${linkClass}" href="/job-card/${jobCard.id}/download-pdf" title="Download PDF">
                                <i class="fas fa-download" style="${mobile ? '' : 'font-size: 20px;'}"></i>
                            </a>
                            <a class="${mobile ? 'btn btn-sm' : 'me-3 mt-2'}" href="/job-card/${jobCard.id}/view-pdf" title="View">
                                <i class="fas fa-eye" style="${mobile ? '' : 'font-size: 20px;'}"></i>
                            </a>
                        `;
                    }

                    if (canDelete) {
                        buttons += `
                            <button class="${deleteClass}" data-id="${jobCard.id}" ${deleteStyle}>
                                <i class="fas fa-trash-alt" style="${mobile ? 'color: red;' : 'font-size: 20px; color: red;'}"></i>
                            </button>
                        `;
                    }

                    return buttons;
                }
                $(document).on('click', '.convert-to-jobcard-btn', function() {

                    let id = $(this).data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You want to convert this quotation to Job Card!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff9f43',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, convert it!'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            $.ajax({

                                url: '/job-cards/' + id + '/convert',

                                type: 'POST',

                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },

                                success: function(res) {

                                    Swal.fire(
                                        'Converted!',
                                        'Quotation converted successfully.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });

                                }

                            });

                        }

                    });

                });

                function renderTable(jobCards) {
                    let tableRows = '';

                    $.each(jobCards || [], function(_, jobCard) {
                        const customerName = escapeHtml(jobCard.customer_name || '-');
                        const vehicleNo = escapeHtml(jobCard.vehicle_no || '-');
                        const dateFormatted = escapeHtml(jobCard.date_formatted || '-');
                        const actionButtons = buildActionButtons(jobCard, false);
                        const mobileButtons = buildActionButtons(jobCard, true);
                        let quotationBadge = '';
                        // console.log(jobCard);
                        // console.log("Type:", jobCard.type);
                        // console.log("Quotation:", jobCard.quotation_status);
                        if (jobCard.quotation_status == 'quotation') {
                            quotationBadge = `
                                <button
                                    class="btn btn-success btn-sm convert-to-jobcard-btn"
                                    data-id="${jobCard.id}"
                                    style="margin-right:8px;">
                                    Convert to JobCard
                                </button>
                            `;
                        }
                        tableRows += `
                            <tr class="main-row">
                                <td>${escapeHtml(jobCard.job_card_number || '-')}</td>
                                <td class="mobile-show toggle-details">
                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                </td>
                                <td class="mobile-hide">${dateFormatted}</td>
                                <td class="mobile-hide" data-customer-id="${jobCard.customer_id || ''}">${customerName}</td>
                                <td class="mobile-hide">${vehicleNo}</td>
                                <td class="mobile-hide">${escapeHtml(jobCard.total_labour || '0.00')}</td>
                                <td class="mobile-hide">${escapeHtml(jobCard.total_parts || '0.00')}</td>
                                <td class="mobile-hide">${escapeHtml(jobCard.grand_total || '0.00')}</td>
                                <td class="text-center mobile-hide">${quotationBadge}${actionButtons}</td>
                            </tr>
                            <tr class="detail-row mobile-show">
                                <td colspan="2">
                                    <div class="p-3 border-top">
                                        <div class="mb-2"><strong>Date:</strong> ${dateFormatted}</div>
                                        <div class="mb-2"><strong>Customer:</strong> ${customerName}</div>
                                        <div class="mb-2"><strong>Vehicle:</strong> ${vehicleNo}</div>
                                        <div class="mb-2"><strong>Labour:</strong> ₹${escapeHtml(jobCard.total_labour || '0.00')}</div>
                                        <div class="mb-2"><strong>Parts:</strong> ₹${escapeHtml(jobCard.total_parts || '0.00')}</div>
                                        <div class="mb-2"><strong>Grand Total:</strong> ₹${escapeHtml(jobCard.grand_total || '0.00')}</div>
                                        <div class="mb-2">${quotationBadge}</div>
                                        <td class="mobile-hide">
  
                                        <div class="mt-2">
                                            <strong>Actions:</strong>
                                            <div class="d-flex flex-wrap gap-1 mt-1">${mobileButtons}</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (!tableRows) {
                        tableRows = '<tr><td colspan="9" class="text-center">No job cards found.</td></tr>';
                    }

                    $("#job-card-tbody").html(tableRows);
                }

                function fetchJobCards(page = 1) {
                    let month = $('#filterMonth').val();
                    let year = $('#filterYear').val();
                    let customer = $('#filterCustomer').val();
                    let date = $('#filter-date').val();

                    $.ajax({
                        url: "/api/getAllJobCards",
                        type: "GET",
                        data: {
                            month: month,
                            year: year,
                            customer: customer,
                            date: date,
                            selectedSubAdminId: selectedSubAdminId || '',
                            page: page,
                            per_page: 10
                        },
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        beforeSend: function() {
                            $("#job-card-tbody").html(
                                '<tr><td colspan="9" class="text-center">Loading...</td></tr>'
                            );
                        },
                        success: function(res) {
                            renderTable(res.data || []);
                            renderCustomerOptions(res.customers || [], customer);
                            $("#dynamic-modals").empty();

                            currentPage = res.pagination?.current_page || 1;
                            lastPage = res.pagination?.last_page || 1;
                            $("#pageInfo").text(`Page ${currentPage} of ${lastPage}`);
                            $("#prevBtn").prop("disabled", currentPage === 1);
                            $("#nextBtn").prop("disabled", currentPage === lastPage || lastPage === 0);

                            if (res.totals) {
                                $("#totallabour").text("₹" + res.totals.labour);
                                $("#partstotal").text("₹" + res.totals.parts);
                                $("#grandTotal").text("₹" + res.totals.grand);
                            }
                        },
                        error: function() {
                            $("#job-card-tbody").html(
                                '<tr><td colspan="9" class="text-center text-danger">Error loading job cards.</td></tr>'
                            );
                            $("#pageInfo").text('');
                            $("#prevBtn").prop("disabled", true);
                            $("#nextBtn").prop("disabled", true);
                        }
                    });
                }

                $('#filterMonth, #filterYear, #filterCustomer, #filter-date').on('change', function() {
                    fetchJobCards(1);
                });

                $('#clear-filter').on('click', function() {
                    $('#filterMonth').val('');
                    $('#filterYear').val('');
                    $('#filterCustomer').val('').trigger('change');
                    $('#filter-date').val('');
                    fetchJobCards(1);
                });

                $(document).on("click", "#prevBtn", function() {
                    if (currentPage > 1) {
                        fetchJobCards(currentPage - 1);
                    }
                });

                $(document).on("click", "#nextBtn", function() {
                    if (currentPage < lastPage) {
                        fetchJobCards(currentPage + 1);
                    }
                });

                fetchJobCards(1);

                const $remainingAmountDisplay = $("#remainingAmountDisplay");
                const $emiMonthInput = $("#emiMonthInputNew");
                const $emiTotalCalculated = $("#emiTotalCalculatedNew");
                const $emiMonthError = $("#emiMonthErrorNew");

                $emiMonthInput.on("input", function() {
                    let remainingAmount = parseFloat($remainingAmountDisplay.text().replace(/[₹,]/g, "")) || 0;
                    let months = parseInt($emiMonthInput.val()) || 0;

                    // Limit months to 12
                    if (months > 12) {
                        months = 12;
                        $emiMonthInput.val(12);
                        $emiMonthError.removeClass("d-none");
                    } else {
                        $emiMonthError.addClass("d-none");
                    }

                    // Calculate EMI per month
                    let emiPerMonth = months > 0 ? remainingAmount / months : 0;
                    $emiTotalCalculated.val(emiPerMonth.toFixed(2));
                });
            });
            $(document).ready(function() {
                $(".select2").select2({
                    tags: true,
                    width: "100%"
                });
                // Toggle input fields based on payment type
                $('#paymentType').on('change', function() {
                    // 🚫 Prevent if hidden (means we're in "cash" mode)
                    if ($('#paymentTypeDiv').hasClass('d-none')) return;

                    const selected = $(this).val();
                    if (selected === 'fully') {
                        $('#fullyAmountDiv').removeClass('d-none');
                        $('#emiMonthDiv').addClass('d-none');
                        $('#emiTotalCalculatedDiv').addClass('d-none');
                        $('#emiMonthError').addClass('d-none');

                        $('#emiMonthInput').val('');
                        $('#emiTotalCalculated').val('');

                        $('#amount').val(window.currentRemainingAmount.toFixed(2)).prop('readonly', true);
                    } else if (selected === 'emi') {
                        $('#emiMonthDiv').removeClass('d-none');
                        $('#fullyAmountDiv').addClass('d-none');
                    }
                });

                $('#paymentType1').on('change', function() {
                    // 🚫 Prevent if hidden (means we're in "cash" mode)
                    if ($('#paymentTypeDiv1').hasClass('d-none')) return;

                    const selected = $(this).val();
                    if (selected === 'fully') {
                        $('#fullyAmountDiv').removeClass('d-none');
                        $('#emiMonthDiv').addClass('d-none');
                        $('#emiTotalCalculatedDiv').addClass('d-none');
                        $('#emiMonthError').addClass('d-none');

                        $('#emiMonthInput').val('');
                        $('#emiTotalCalculated').val('');

                        $('#amount').val(window.currentRemainingAmount.toFixed(2)).prop('readonly', true);
                    } else if (selected === 'emi') {
                        $('#emiMonthDiv').removeClass('d-none');
                        $('#fullyAmountDiv').addClass('d-none');
                    }
                });

                // Validate EMI month input
                $('#emiMonthInput').on('input', function() {
                    const maxDuration = parseInt($('#emiDuration').text());
                    let enteredMonth = parseInt($(this).val());

                    if (enteredMonth > maxDuration) {
                        $(this).val(maxDuration); // Limit value to max
                        $('#emiMonthError').removeClass('d-none');
                    } else {
                        $('#emiMonthError').addClass('d-none');
                    }
                });

                // In emiMonthInput input handler
                $('#emiMonthInput').on('input', function() {
                    const maxDuration = parseInt($('#emiDuration').text());
                    const monthlyEMI = parseFloat($('#emiMonthly').text());
                    const remainingAmount = parseFloat(window.currentRemainingAmount);
                    let enteredMonth = parseInt($(this).val());

                    console.log('--- EMI Input Changed ---');
                    console.log('Entered Month:', enteredMonth);
                    console.log('Max Duration:', maxDuration);
                    console.log('Monthly EMI (raw):', monthlyEMI);
                    console.log('Remaining Amount (original):', remainingAmount);

                    if (isNaN(enteredMonth) || enteredMonth < 1) {
                        console.log('Entered month invalid, resetting...');
                        $('#emiTotalCalculatedDiv').addClass('d-none');
                        $('#emiTotalCalculated').val('');
                        $('#remainingAmountDisplay').text(remainingAmount.toFixed(2));
                        $('#remainingAmountInput').val('');
                        $('#emiMonthError').addClass('d-none');
                        return;
                    }

                    if (enteredMonth > maxDuration) {
                        $(this).val(maxDuration);
                        enteredMonth = maxDuration;
                        $('#emiMonthError').removeClass('d-none');
                    } else {
                        $('#emiMonthError').addClass('d-none');
                    }

                    let calculatedEmiTotal, newRemaining;

                    if (enteredMonth === maxDuration) {
                        // Last month → take remaining exactly
                        calculatedEmiTotal = remainingAmount;
                        newRemaining = 0;
                        console.log('Last month adjustment: set remaining to 0');
                    } else {
                        // Regular calculation
                        calculatedEmiTotal = enteredMonth * monthlyEMI;
                        calculatedEmiTotal = Math.round((calculatedEmiTotal + Number.EPSILON) * 100) / 100;

                        newRemaining = remainingAmount - calculatedEmiTotal;
                        newRemaining = Math.round((newRemaining + Number.EPSILON) * 100) / 100;
                    }

                    $('#emiTotalCalculatedDiv').removeClass('d-none');
                    $('#emiTotalCalculated').val(calculatedEmiTotal.toFixed(2));
                    $('#remainingAmountDisplay').text(newRemaining.toFixed(2));
                    $('#remainingAmountInput').val(newRemaining.toFixed(2));

                    console.log('Calculated EMI Total:', calculatedEmiTotal.toFixed(2));
                    console.log('Remaining Amount:', newRemaining.toFixed(2));
                    console.log('----------------------------');
                });

                $('.make-payment-btn').on('click', function() {
                    var jobCardId = $(this).data('id');
                    var method = $(this).data('method');
                    var totalAmount = parseFloat($(this).data('total-amount')) || 0;
                    var remainingAmount = parseFloat($(this).data('remaining-amount')) || 0;
                    var emiDuration = parseInt($(this).data('emi-duration')) || 0;
                    var remainingEmiMonths = parseInt($(this).data('remaining-emi-months')) || 0;

                    // Calculate EMI per month
                    var monthlyEMI = emiDuration > 0 ? (totalAmount / emiDuration).toFixed(2) : '0.00';

                    // Reset form
                    $('#fullyAmountDiv, #emiMonthDiv, #emiMonthError').addClass('d-none');
                    $('#amount').val('');
                    $('#emiMonthInput').val('');
                    $('#emiTotalCalculated').val('');
                    $('#emiTotalCalculatedDiv').addClass('d-none');

                    // Set values in modal
                    $('#paymentJobCardId').val(jobCardId);
                    $('#remainingAmountDisplay').text(remainingAmount.toFixed(2));
                    $('#remainingAmountInput').val(remainingAmount.toFixed(2));
                    $('#emiMonthly').text(monthlyEMI);
                    $('#emiDuration').text(remainingEmiMonths);
                    $('#emiTotal').text(totalAmount.toFixed(2));
                    window.currentRemainingAmount = remainingAmount;

                    $('#emiMonthInput').attr('max', remainingEmiMonths);

                    // Show/hide sections based on method
                    if (method.toLowerCase() === 'cash' || method === 'Cash_online' || method === 'Online' ||
                        method.toLowerCase() === 'pending') {
                        $('#paymentTypeDiv').addClass('d-none'); // Hide Paid Type dropdown
                        $('#paymentMethodDiv').removeClass('d-none'); // Show Payment Method dropdown
                        $('#paymentMethodSelect').val('');
                        $('#amount').val('').prop('readonly', false); // Enable editing amount for cash

                        // Hide EMI monthly & duration in summary box
                        $('#emiMonthlyRow, #emiDurationRow').addClass('d-none');
                    } else {
                        console.log('asd');

                        $('#paymentTypeDiv').removeClass('d-none'); // Show Paid Type dropdown
                        $('#paymentMethodDiv').addClass('d-none'); // Hide Payment Method dropdown
                        $('#emiMonthDivNew').addClass('d-none'); // Hide Payment Method dropdown
                        $('#emiTotalCalculatedDivNew').addClass('d-none'); // Hide Payment Method dropdown
                        // $('#paymentMethodSelect').val('');
                        $('#amount').val(remainingAmount.toFixed(2)).prop('readonly',
                            true); // Lock amount for online etc.

                        // Show EMI monthly & duration in summary box
                        $('#emiMonthlyRow, #emiDurationRow').removeClass('d-none');
                    }

                    $('#paymentMethodSelect').off('change').on('change', function() {
                        const selectedPaymentMethod = $(this).val().toLowerCase();

                        // Reset all common fields first
                        $('#paidTypeDiv, #paidTypeDiv1, #onlineTypeDiv, #cashOnlineTypeDiv').addClass(
                            'd-none');
                        $('#paidTypeSelect, #onlineTypeSelect, #cashOnlineTypeSelect').val('');
                        $('#upiAmountDiv').addClass('d-none');
                        $('#upiAmountInput').val('').attr('readonly', true);
                        $('#partialPaidFields, #fullyCashOnlineFields, #partialCashOnlineFields')
                            .addClass('d-none');

                        // Hide EMI fields
                        $('#emiMonthDivNew, #emiTotalCalculatedDivNew, #fullyAmountDivNew').addClass(
                            'd-none');

                        if (selectedPaymentMethod === 'cash') {
                            $('#paidTypeDiv, #paidTypeDiv1').removeClass(
                                'd-none'); // Show Paid Type dropdown
                        } else if (selectedPaymentMethod === 'online') {
                            $('#onlineTypeDiv').removeClass('d-none'); // Show Online Type dropdown
                            $('#fullyPaidFields').addClass('d-none');
                        } else if (selectedPaymentMethod === 'cash_online') {
                            $('#cashOnlineTypeDiv').removeClass(
                                'd-none'); // Show Cash + Online dropdown
                            $('#fullyPaidFields').addClass('d-none');
                        } else if (selectedPaymentMethod === 'emi') {
                            // EMI-specific logic
                            $('#paymentType1').val('emi').trigger('change'); // Set EMI type
                            $('#paidTypeDiv, #partialPaymentFields, #onlineTypeDiv').addClass('d-none');
                            $('#paidTypeSelect, #partialAmount, #pendingAmount').val('');

                            // Show EMI fields
                            $('#emiMonthDivNew, #emiTotalCalculatedDivNew, #fullyAmountDivNew')
                                .removeClass('d-none');
                        } else {
                            // default/fallback
                            $('#paymentType').val('fully').trigger('change');
                        }
                    });



                    $('#onlineTypeSelect').off('change').on('change', function() {
                        const selectedOnlineType = $(this).val();

                        if (selectedOnlineType === 'online_fully') {
                            $('#cashAmount').val('').prop('readonly', true);
                            $('#upiAmountDiv').removeClass('d-none'); // Show UPI Amount field
                            $('#upiAmountInput').removeAttr('readonly');
                            $('#partialPaidFields').addClass('d-none');
                            const remaining = parseFloat(window.currentRemainingAmount) || $(
                                '#remainingAmountInput').val() || 0;
                            console.log(remaining);

                            $('#upiAmountInput').val(remaining.toFixed(2)).prop('readonly', true);

                            // Optional: make editable if needed
                        } else if (selectedOnlineType === 'online_partially') {
                            $('#cashAmount').val('').prop('readonly', true);
                            $('#partialPaidFields').removeClass('d-none');



                            $('#upiAmountDiv').addClass('d-none');

                            // $('#upiAmountInput').removeAttr(
                            //     'readonly'); // Optional: make editable if needed
                        } else {
                            $('#upiAmountDiv').addClass('d-none'); // Hide UPI Amount field
                            $('#upiAmountInput').val('').attr('readonly', true);
                        }
                    });



                    // Listen for Paid Type changes
                    $('#paidTypeSelect').off('change').on('change', function() {
                        let selected = $(this).val();

                        if (selected === 'cash_partially') {
                            $('#partialPaidFields').removeClass('d-none');
                            $('#fullyPaidFields').addClass('d-none');
                            $('#cashAmount').val('').prop('readonly', true);
                        } else if (selected === 'cash_fully') {
                            $('#fullyPaidFields').removeClass('d-none');
                            $('#partialPaidFields').addClass('d-none');

                            // Set cash amount as remaining amount and make readonly
                            const remaining = parseFloat(window.currentRemainingAmount) || 0;
                            $('#cashAmount').val(remaining.toFixed(2)).prop('readonly', true);
                        } else {
                            $('#partialPaidFields, #fullyPaidFields').addClass('d-none');
                        }
                    });



                    // Partial amount input handler
                    $('#partialAmount').off('input').on('input', function() {
                        const paid = parseFloat($(this).val()) || 0;
                        const remaining = parseFloat(window.currentRemainingAmount) || 0;

                        let pending = remaining - paid;
                        if (pending < 0) pending = 0;

                        $('#pendingAmount').val(pending.toFixed(2));
                    });

                    // Cash amount input handler for calculating UPI amount
                    $('#cashAmount').off('input').on('input', function() {
                        const cashAmount = parseFloat($(this).val()) || 0;
                        const remaining = parseFloat(window.currentRemainingAmount) || 0;

                        let calculatedUPI = remaining - cashAmount;
                        if (calculatedUPI < 0) calculatedUPI = 0;

                        $('#upiAmount').val(calculatedUPI.toFixed(2));
                    });

                    // Cash + Online Type Change
                    $('#cashOnlineTypeSelect').off('change').on('change', function() {
                        const selectedType = $(this).val();

                        $('#fullyCashOnlineFields, #partialCashOnlineFields').addClass('d-none');

                        if (selectedType === 'cash_online_fully') {
                            $('#fullyCashOnlineFields').removeClass('d-none');
                            $('#cashAmount').val('').prop('readonly', true);
                            $('#cashOnlineFullAmount').off('input').on('input', function() {
                                const cash = parseFloat($(this).val()) || 0;
                                let calculatedUPI = window.currentRemainingAmount - cash;
                                if (calculatedUPI < 0) calculatedUPI = 0;
                                $('#upiOnlineFullAmount').val(calculatedUPI.toFixed(2));
                            });
                        } else if (selectedType === 'cash_online_partially') {
                            $('#partialCashOnlineFields').removeClass('d-none');
                            $('#cashAmount').val('').prop('readonly', true);
                            $('#cashOnlinePartialAmount, #upiOnlinePartialAmount').off('input').on(
                                'input',
                                function() {
                                    const cash = parseFloat($('#cashOnlinePartialAmount').val()) ||
                                        0;
                                    const upi = parseFloat($('#upiOnlinePartialAmount').val()) || 0;
                                    let remaining = window.currentRemainingAmount - (cash + upi);
                                    if (remaining < 0) remaining = 0;
                                    $('#remainingCashOnlineAmount').val(remaining.toFixed(2));
                                });
                        }
                    });


                    // Fully Cash + Online - Auto UPI Calculation
                    $('#cashOnlineFullAmount').off('input').on('input', function() {
                        const cash = parseFloat($(this).val()) || 0;
                        let calculatedUPI = window.currentRemainingAmount - cash;
                        if (calculatedUPI < 0) calculatedUPI = 0;
                        $('#upiOnlineFullAmount').val(calculatedUPI.toFixed(2));
                    });

                    // Partial Cash + Online - Auto Remaining Calculation
                    $('#cashOnlinePartialAmount, #upiOnlinePartialAmount').off('input').on('input', function() {
                        const cash = parseFloat($('#cashOnlinePartialAmount').val()) || 0;
                        const upi = parseFloat($('#upiOnlinePartialAmount').val()) || 0;
                        let remaining = window.currentRemainingAmount - (cash + upi);
                        if (remaining < 0) remaining = 0;
                        $('#remainingCashOnlineAmount').val(remaining.toFixed(2));
                    });


                    if (method.toLowerCase() === 'emi') {
                        $('#paymentType').val('emi').trigger('change');
                        $('#paymentMethodDiv').addClass('d-none');
                        $('#paidTypeDiv').addClass('d-none');
                        $('#partialPaymentFields').addClass('d-none');
                        $('#onlineTypeDiv').addClass('d-none');

                        $('#paymentMethodSelect').val('');
                        $('#paidTypeSelect').val('');
                        $('#partialAmount').val('');
                        $('#pendingAmount').val('');

                        // Show EMI monthly & duration in summary box
                        $('#emiMonthlyRow, #emiDurationRow').removeClass('d-none');
                    } else {
                        $('#paymentType').val('fully').trigger('change');
                    }


                    var jobCardId = $(this).data('id');

                    // Hide previous history
                    $('#paymentHistoryBox').addClass('d-none');
                    $('#paymentHistoryList').empty();

                    // Attach click handler for viewing history
                    $('#viewHistoryBtn').off('click').on('click', function() {
                        $.ajax({
                            url: '/job-card/payment-history/' + jobCardId,
                            method: 'GET',
                            success: function(response) {
                                const history = response.data;

                                if (!history || history.length === 0) {
                                    $('#paymentHistoryList').html(
                                        '<li>No payment history found.</li>');
                                } else {
                                    let historyHtml = '';
                                    history.forEach(function(payment) {
                                        historyHtml += `
                                                                                    <li class="mb-2">
                                                                                        <strong>Amount:</strong> ₹${parseFloat(payment.payment_amount).toFixed(2)}<br>
                                                                                        <strong>Date:</strong> ${payment.payment_date}<br>
                                                                                        <strong>Method:</strong> ${payment.payment_method}<br>
                                                                                        <strong>Payment Type:</strong> ${payment.payment_type ? payment.payment_type : 'N/A'}<br>
                                                                                `;

                                        if (payment.payment_type === 'emi') {
                                            historyHtml +=
                                                `<strong>EMI Months:</strong> ${payment.emi_month || 0}<br>`;
                                        }

                                        historyHtml += `</li><hr class="my-1"/>`;
                                    });

                                    $('#paymentHistoryList').html(historyHtml);
                                }

                                $('#paymentHistoryBox').removeClass('d-none');
                            },

                            error: function() {
                                $('#paymentHistoryList').html(
                                    '<li class="text-danger">Failed to load payment history.</li>'
                                );
                                $('#paymentHistoryBox').removeClass('d-none');
                            }
                        });
                    });



                });


            });
        </script>


        <script>
            $('#makePaymentForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;

                console.log("Form submission started");

                // Clear all previous errors
                $('.text-danger').text('');

                // Get values
                let paymentMethod = $('#paymentMethodSelect').val();
                let paymentTypeemionly = $('#paymentType').val();
                let paidType = $('#paidTypeSelect').val();
                let onlineType = $('#onlineTypeSelect').val();
                let cashOnlineType = $('#cashOnlineTypeSelect').val();

                console.log("Payment Method:", paymentMethod, "Paid Type:", paidType, "Online Type:", onlineType,
                    "Cash+Online Type:", cashOnlineType);

                // EMI Validation
                if (paymentMethod === 'emi') {
                    console.log("EMI payment selected");
                    let emiMonth = $('#emiMonthInputNew').val();
                    if (!emiMonth || parseInt(emiMonth) < 1) {
                        isValid = false;
                        console.log("Validation failed: Invalid EMI month");
                        $('#emiMonthErrornew').removeClass('d-none').text("Please enter a valid EMI month.");
                        return false;
                    } else {
                        console.log("EMI month valid:", emiMonth);
                    }
                }
                console.log(paymentTypeemionly + ' paymentTypeemionly');

                // Payment Method validation
                if (paymentTypeemionly) {
                    console.log('asdasdsad');

                    if ($("#paymentMethodDiv").is(":visible") && !paymentMethod) {
                        isValid = false;
                        console.log("Validation failed: Payment method not selected");
                        $('#paymentMethodError').text("Please select a payment method.");
                        return false;
                    } else {
                        console.log("Payment method selected:", paymentMethod);
                    }
                } else {
                    console.log("Payment type EMI only, skipping payment method validation");
                }

                // Cash Payment Validation
                if (paymentMethod === 'cash') {
                    console.log("Cash payment selected");

                    if (!paidType) {
                        isValid = false;
                        console.log("Validation failed: Paid type not selected");
                        $('#paidTypeError').text("Please select paid type.");
                        return false;
                    } else {
                        console.log("Paid type selected:", paidType);
                    }

                    if (paidType === 'cash_partially') {
                        let partialAmount = $('#partialAmount').val();
                        console.log("Cash partially selected, amount:", partialAmount);
                        if (!partialAmount || parseFloat(partialAmount) <= 0) {
                            isValid = false;
                            console.log("Validation failed: Invalid partial cash amount");
                            $('#partialAmountError').text("Enter a valid partial cash amount.");
                            return false;
                        } else {
                            console.log("Partial cash amount valid");
                        }
                    } else {
                        let cashAmount = $('#cashAmount').val();
                        console.log("Cash fully selected, amount:", cashAmount);
                        if (!cashAmount || parseFloat(cashAmount) <= 0) {
                            isValid = false;
                            console.log("Validation failed: Invalid full cash amount");
                            $('#cashAmountError').text("Enter a valid cash amount.");
                            return false;
                        } else {
                            console.log("Full cash amount valid");
                        }
                    }
                }

                // Online Payment Validation
                if (paymentMethod === 'online') {
                    console.log("Online payment selected");

                    if (!onlineType) {
                        isValid = false;
                        console.log("Validation failed: Online type not selected");
                        $('#onlineTypeError').text("Please select online type.");
                        return false;
                    } else {
                        console.log("Online type selected:", onlineType);
                    }

                    let onlineAmount = $('#partialAmount').val() || $('#upiAmountInput').val();
                    console.log("Online amount:", onlineAmount);

                    if (!onlineAmount || parseFloat(onlineAmount) <= 0) {
                        isValid = false;
                        if (onlineType === 'online_partially') {
                            console.log("Validation failed: Invalid partial online amount");
                            $('#partialAmountError').text("Enter a valid online partial amount.");
                        } else {
                            console.log("Validation failed: Invalid full online amount");
                            $('#upiAmountError').text("Enter a valid online amount.");
                        }
                        return false;
                    } else {
                        console.log("Online amount valid");
                    }
                }

                // Cash + Online Validation
                if (paymentMethod === 'cash_online') {
                    console.log("Cash + Online payment selected");

                    if (!cashOnlineType) {
                        isValid = false;
                        console.log("Validation failed: Cash + Online type not selected");
                        $('#cashOnlineTypeError').text("Please select Cash + Online type.");
                        return false;
                    } else {
                        console.log("Cash + Online type selected:", cashOnlineType);
                    }

                    if (cashOnlineType === 'cash_online_fully') {
                        let cashAmt = $('#cashOnlineFullAmount').val();
                        let onlineAmt = $('#upiOnlineFullAmount').val();
                        console.log("Cash+Online fully amounts:", cashAmt, onlineAmt);

                        if (!cashAmt || parseFloat(cashAmt) <= 0 || !onlineAmt || parseFloat(onlineAmt) <= 0) {
                            isValid = false;
                            console.log("Validation failed: Invalid fully cash + online amounts");
                            $('#cashOnlineFullAmountError').text("Enter a valid cash amount.");
                            $('#upiOnlineFullAmountError').text("Enter a valid online amount.");
                            return false;
                        } else {
                            console.log("Fully cash + online amounts valid");
                        }
                    }

                    if (cashOnlineType === 'cash_online_partially') {
                        let cashAmt = $('#cashOnlinePartialAmount').val();
                        let onlineAmt = $('#upiOnlinePartialAmount').val();
                        console.log("Cash+Online partially amounts:", cashAmt, onlineAmt);

                        if ((!cashAmt || parseFloat(cashAmt) <= 0) && (!onlineAmt || parseFloat(onlineAmt) <= 0)) {
                            isValid = false;
                            console.log("Validation failed: Invalid partially cash + online amounts");
                            $('#cashOnlinePartialAmountError').text("Enter at least one valid amount.");
                            $('#upiOnlinePartialAmountError').text("Enter at least one valid amount.");
                            return false;
                        } else {
                            console.log("Partially cash + online amounts valid");
                        }
                    }
                }


                // EMI validation
                if (paymentTypeemionly == 'emi') {

                    let emiMonth = $('#emiMonthInput').val();
                    console.log("EMI months:", emiMonth);
                    if (!emiMonth || parseInt(emiMonth) < 1) {
                        isValid = false;
                        $('#emiMonthError').removeClass('d-none').text("Please enter a valid EMI month.");
                        console.log("Validation failed: Invalid EMI month");
                        return false;
                    }
                }
                console.log('done pay');

                // return false;

                // If form is valid, you can submit it
                if (isValid) {
                    this.submit(); // submit the form
                }

                let selectedPaymentType = $('#paymentType').val();
                $('#paymentMethodHidden').val(selectedPaymentType);

                if ($("#paymentMethodDiv").hasClass("d-none")) {
                    $("#newEmiHidden").prop("disabled", true).val("");
                }

                let formElement = $(this)[0];
                let formData = new FormData(formElement);

                let submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Processing...');
                if (paymentMethodSelect === 'emi') {
                    let emiTotal = $('#emiTotalCalculated').val();
                    formData.append('emi_paid_value', emiTotal);
                }

                if (selectedPaymentType === 'emi') {
                    let emi_val = $('#emiTotalCalculated').val();
                    formData.append('amount', emi_val);
                }

                $.ajax({
                    url: "{{ route('job_card.make_payment_submit') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#makePaymentModal').modal('hide');
                        submitButton.prop('disabled', false).text('Submit Payment');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payment submitted successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false).text('Submit Payment');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = '';
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
        </script>




        <script>
            $(document).on('click', '.delete-jobcard-btn', function(e) {
                e.preventDefault();
                let jobCardId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/job-cards/${jobCardId}/delete`, // direct URL here
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', 'Job card has been deleted.', 'success').then(
                                    () => {
                                        location.reload(); // Or remove DOM element
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        </script>

        <script>
            const viewBtn = document.getElementById('viewHistoryBtn');
            const closeBtn = document.getElementById('closeHistoryBtn');
            const historyBox = document.getElementById('paymentHistoryBox');

            viewBtn.addEventListener('click', () => {
                historyBox.classList.remove('d-none');
            });

            closeBtn.addEventListener('click', () => {
                historyBox.classList.add('d-none');
            });
        </script>

        <script>
            $(document).on('click', '.toggle-details', function() {
                const $btn = $(this);
                const $mainRow = $btn.closest('tr');
                const $detailRow = $mainRow.next('.detail-row');
                const $icon = $btn.find('i');

                $detailRow.toggleClass('active');
                // $btn.toggleClass('active');

                // if ($detailRow.hasClass('active')) {
                //     $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                // } else {
                //     $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                // }
                if ($detailRow.hasClass('active')) {
                    $icon.removeClass('fa-plus-circle')
                        .addClass('fa-minus-circle')
                        .css('color', '#dc3545'); // Red
                } else {
                    $icon.removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43'); // Orange
                }
            });
        </script>
    @endsection
