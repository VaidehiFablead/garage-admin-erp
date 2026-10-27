@extends('layout.app')

@section('title', 'Sales List')

@section('content')
<style>
    .table-scroll-top {
        display: none;
    }

    @media screen and (max-width: 768px) {
        .table-scroll-top {
            display: block;
        }
    }
</style>
@if(session('error'))
<div class="alert alert-danger" id="error-message">
    {{ session('error') }}
</div>

<style>
    .fade-out {
        opacity: 1;
        transition: opacity 0.5s ease-out;
    }

    .fade-out.hidden {
        opacity: 0;
    }
</style>

<script>
    setTimeout(function() {
        let alert = document.getElementById('error-message');
        if (alert) {
            alert.classList.add('hidden'); // Triggers the fade-out transition
            // Remove the element from DOM after fadeout (optional)
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500); // match the CSS transition duration (0.5s)
        }
    }, 4000);
</script>
@endif
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Sales List</h4>
            <!-- <h6>Manage your sales</h6> -->
        </div>
        <div class="page-btn">
            @if(app('hasPermission')(2, 'add'))
            <a href="{{route('sales.add')}}" class="btn btn-sm btn-added"><img
                    src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1">New
                Sales</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- Top Search and Date Filter Row -->

            <div class="table-top mb-3">
                <div class="row w-100 align-items-center">
                    <!-- Search -->
                    <div class="col-md-4 col-6 d-flex align-items-center">
                        <div class="search-set w-100">
                            <div class="search-path"></div>
                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                        alt="img">
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date Filter (right aligned on desktop) -->
                    <div class="col-md-8 col-6 d-flex align-center">

                        <div class="input-groupicon me-2">
                            <!-- Year Filter -->
                            <select id="filter-year" class="form-control form-control-sm">
                                <option value="">-- Select Year --</option>
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="input-groupicon me-2">
                            <select id="filter-month" class="form-control form-control-sm">
                                <option value="">-- Select Month --</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>



                        <!-- Date Filter -->
                        <div class="input-groupicon me-2">
                            <input type="text" id="filter-date" placeholder="Choose Date"
                                class="datetimepicker form-control form-control-sm">
                        </div>


                        <!-- Customer Filter -->
                        <div class="input-groupicon me-2">
                            <select id="filter-customer" class="form-control form-control-sm">
                                <option value="">-- Select Customer --</option>
                            </select>
                        </div>


                        <!-- Export Button -->
                        <button id="exportAllChallan" class="btn btn-sm btn-success float-end">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>

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
                                    <img src="admin/assets/img/icons/search-whites.svg" alt="img">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-scroll-top"
                style="overflow-x: auto; overflow-y: hidden; height: 20px; margin-bottom: 5px;">
                <div style="height: 1px;"></div> <!-- Adjust width to match your table width -->
            </div>
            <!-- Orders Table -->
            <div class="table-responsive mt-3" style="overflow-x: auto;">
                <table class="table datanew" id="order-table" style="max-width: 2000px;">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Payment Status</th>
                            <th>Payment Method</th>
                            <th>Total</th>
                            <th>Biller</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS will populate this -->
                    </tbody>
                </table>
            </div>
            <div id="filtered-total" style="margin: 10px 0; font-weight: bold; text-align: center;">
                Total: ₹0.00
            </div>
        </div>
    </div>
</div>

<form id="makePaymentForm">
    <div class="modal fade" id="makePaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-sm btn-cancel text-white" id="viewHistoryBtn">View Payment History</button>
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

                        <ul id="paymentHistoryList" class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
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

                    <br>

                    <!-- Payment Method -->
                    <div class="mb-3" id="paymentMethodDiv">
                        <label for="paymentMethodSelect" class="form-label">Select Payment Method</label>
                        <select class="form-select" id="paymentMethodSelect" name="payment_method">
                            <option value="" selected disabled>Select</option>
                            <option value="cash">Cash</option>
                            <option value="online">Online</option>
                            <option value="cash_online">Cash + Online</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="cashOnlineTypeDiv">
                        <label for="cashOnlineTypeSelect" class="form-label">Select Cash + Online Type</label>
                        <select class="form-select" id="cashOnlineTypeSelect" name="cash_online_type">
                            <option value="" selected disabled>Select</option>
                            <option value="cash_online_fully">Cash + Online Fully</option>
                            <option value="cash_online_partially">Cash + Online Partially</option>
                        </select>
                    </div>

                    <!-- Fully Cash + Online -->
                    <!-- Fully Cash + Online -->
                    <div class="mb-3 d-none" id="fullyCashOnlineFields">
                        <label>Cash Amount</label>
                        <input type="number" id="cashOnlineFullAmount" name="fully_cash_amount" class="form-control" min="0" step="0.01">
                        <label class="mt-2">Online Amount</label>
                        <input type="number" id="upiOnlineFullAmount" name="full_online_amount" class="form-control" readonly>
                    </div>

                    <!-- Partial Cash + Online -->
                    <div class="mb-3 d-none" id="partialCashOnlineFields">
                        <label>Cash Amount</label>
                        <input type="number" id="cashOnlinePartialAmount" name="cash_amount" class="form-control" min="0" step="0.01">
                        <label class="mt-2">Online Amount</label>
                        <input type="number" id="upiOnlinePartialAmount" name="online_amount" class="form-control" min="0" step="0.01">
                        <label class="mt-2">Remaining Amount</label>
                        <input type="number" id="remainingCashOnlineAmount" name="remaining_amount" class="form-control" readonly>
                    </div>

                    <div class="mb-3 d-none" id="onlineTypeDiv">
                        <label for="onlineTypeSelect" class="form-label">Select Online Type</label>
                        <select class="form-select" id="onlineTypeSelect" name="online_type">
                            <option value="" selected disabled>Select</option>
                            <option value="online_fully">Online Fully</option>
                            <option value="online_partially">Online Partially</option>
                        </select>
                    </div>

                    <!-- Paid Type Dropdown (Hidden by default) -->
                    <div class="mb-3 d-none" id="paidTypeDiv">
                        <label for="paidTypeSelect" class="form-label">Paid Type</label>
                        <select class="form-select" id="paidTypeSelect" name="paid_type">
                            <option value="" selected disabled>Select</option>
                            <option value="cash_partially">Cash Partially</option>
                            <option value="cash_fully">Cash Fully</option>
                        </select>
                    </div>

                    <!-- UPI Amount Input -->
                    <div class="mb-3 d-none" id="upiAmountDiv">
                        <label for="upiAmountInput" class="form-label">Online Amount</label>
                        <input type="number" class="form-control" id="upiAmountInput" name="upi_online_amount" readonly>
                    </div>

                    <!-- Partially Paid Fields -->
                    <div class="mb-3 d-none" id="partialPaidFields">
                        <label for="partialAmount" class="form-label">Enter Amount</label>
                        <input type="number" class="form-control" id="partialAmount" name="cash_partial_amount" min="1" step="0.01">

                        <label class="form-label mt-2">Pending Amount</label>
                        <input type="number" class="form-control" id="pendingAmount" name="cash_pending_amount" readonly>
                    </div>

                    <!-- Fully Paid Fields -->
                    <div class="mb-3 d-none" id="fullyPaidFields">
                        <label class="form-label">Cash Amount</label>
                        <input type="number" class="form-control" id="cashAmount" name="cashAmount" min="0" step="0.01">
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
                        <input type="number" class="form-control" id="emiMonthInput" name="emi_month" min="1">
                        <small class="text-danger d-none" id="emiMonthError">Month exceeds allowed EMI duration.</small>
                    </div>

                    <!-- Calculated EMI Total -->
                    <div class="mb-3 d-none" id="emiTotalCalculatedDiv">
                        <label for="emiTotalCalculated" class="form-label">EMI Total</label>
                        <input type="number" class="form-control" name="amount" id="emiTotalCalculated" readonly>
                    </div>

                    <!-- Fully Payment Field (editable) -->
                    <div class="mb-3 d-none" id="fullyAmountDiv">
                        <label for="amount" class="form-label">Enter Amount</label>
                        <input type="number" class="form-control" id="amount" readonly>


                    </div>

                    <input type="hidden" id="paymentJobCardId" name="order_id">
                    <!-- <input type="hidden" id="remainingAmountHidden" name="remaining_amount"> -->
                    <input type="hidden" id="paymentMethodHidden" name="payment_type">



                    <div class="text-end">
                        <button type="submit" class="btn btn-submit text-white" style="background-color: #ff9f43;">Submit Payment</button>
                        <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @endsection

    @push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable();


            // ✅ Collect unique customers on first draw only
            table.one('draw', function() {
                let customers = new Set();
 
                // Column index 2 → customer name
                table.column(2, {
                    search: 'applied'
                }).data().each(function(d) {
                    customers.add(d);
                });
 
                // Populate dropdown
                let $filter = $("#filter-customer");
                $filter.empty().append('<option value="">-- Select Customer --</option>');
                customers.forEach(function(name) {
                    $filter.append('<option value="' + name + '">' + name + '</option>');
                });
            });
 
            // ✅ On dropdown change → filter table
            $("#filter-customer").on("change", function() {
                let selectedCustomer = $(this).val();
                if (selectedCustomer) {
                    table.column(2) // column index for customer name
                        .search("^" + selectedCustomer + "$", true, false) // exact match
                        .draw();
                } else {
                    table.column(2).search("").draw(); // clear filter
                }
            });
 
 
 
            let currentYear = new Date().getFullYear();
            for (let i = 0; i < 4; i++) {
                let year = currentYear - i;
                $("#filter-year").append(`<option value="${year}">${year}</option>`);
            }
 
            // Month filter
            $("#filter-month").on("change", function() {
                applyMonthYearFilter();
            });
 
            // Year filter
            $("#filter-year").on("change", function() {
                applyMonthYearFilter();
            });
 
            // Function to apply both filters
            function applyMonthYearFilter() {
                let selectedMonth = $("#filter-month").val();
                let selectedYear = $("#filter-year").val();
 
                // Month short name map
                const monthNames = {
                    "01": "Jan",
                    "02": "Feb",
                    "03": "Mar",
                    "04": "Apr",
                    "05": "May",
                    "06": "Jun",
                    "07": "Jul",
                    "08": "Aug",
                    "09": "Sep",
                    "10": "Oct",
                    "11": "Nov",
                    "12": "Dec"
                };
 
                let searchValue = "";
 
                if (selectedMonth && selectedYear) {
                    searchValue = monthNames[selectedMonth] + " " + selectedYear;
                } else if (selectedMonth) {
                    searchValue = monthNames[selectedMonth];
                } else if (selectedYear) {
                    searchValue = selectedYear;
                }
 
                console.log("Applied Filter:", searchValue);
 
                // 👇 Jis column me date hai uska index de
                table.column(1).search(searchValue, true, false).draw();
            }


            // Helper function to set selected sub-admin and refresh data
            function setSelectedSubAdmin(subAdminId) {
                localStorage.setItem('selectedSubAdminId', subAdminId);
                location.reload();
            }

            // Helper function to clear selected sub-admin and show all data
            function clearSelectedSubAdmin() {
                localStorage.removeItem('selectedSubAdminId');
                location.reload();
            }

            // Helper function to refresh data without page reload
            function refreshOrdersData() {
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                const authToken = localStorage.getItem("authToken");

                if (!authToken) {
                    console.error("Auth token missing.");
                    return;
                }

                let selectedMonth = $("#filter-month").val();
                // 01, 02, 03 ...



                $.ajax({
                    url: "/api/get_orders",
                    method: "GET",
                    data: selectedSubAdminId ? {
                        selectedSubAdminId: selectedSubAdminId
                    } : {},
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            renderOrders(response.data, null, response.currency_symbol, response.currency_position, selectedMonth);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching order data:", error);
                    }
                });
            }

            function renderOrders(data, selectedDate, currency_symbol, currency_position, selectedMonth) {
                data = Array.isArray(data) ? data : [];
                let tableBody = [];
                const currencySymbol = currency_symbol || '₹';
                const currencyPosition = currency_position || 'left';

                data.forEach(order => {
                    if (!order || !order.id) return;
                    if (!order.created_at) return;

                    const dateObj = new Date(order.created_at);
                    const orderDate = dateObj.toISOString().split('T')[0];

                    // ---- DATE filter ----
                    if (selectedDate && selectedDate !== orderDate) return;

                    // ---- MONTH filter ----
                    if (selectedMonth) {
                        let orderMonth = String(dateObj.getMonth() + 1).padStart(2, '0'); // "01".."12"
                        if (selectedMonth !== orderMonth) return;
                    }

                    // ---- FORMAT DATE ----
                    let day = String(dateObj.getDate()).padStart(2, '0');
                    let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    let month = monthNames[dateObj.getMonth()];
                    let year = dateObj.getFullYear();

                    let hours = dateObj.getHours();
                    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    let ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12 || 12;

                    let amount = parseFloat(order.total_amount || 0).toFixed(2);
                    let displayAmount = currencyPosition === 'right' ?
                        amount + currencySymbol :
                        currencySymbol + amount;

                    const formattedDate = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;

                    // ---- ACTION BUTTONS ----
                    let actionBtns = ``;

                    // ✅ Show "Make Payment" only if remaining_amount > 0
                    if (parseFloat(order.remaining_amount) > 0) {
                        actionBtns += `
                                <a href="javascript:void(0);" class="me-3 make-payment-btn" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                    data-id="${order.id}" data-amount="${order.formatted_remaining_amount}" data-method="${order.payment_method || ''}"
                                    data-emi-months="${order.remaining_emi_months}" data-emi-duration="${order.emi_duration || 0}"
                                    data-total-amount="${order.total_amount || 0}" data-remaining-amount="${order.formatted_remaining_amount}"
                                    data-remaining-emi-months="${order.remaining_emi_months}" title="Make Payment">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#092C4C" viewBox="0 0 24 24">
                                        <path d="M21 7H3V5h18v2zm0 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h18zm-2 4H5v6h14v-6zM8 12h2v2H8v-2zm6 0h2v2h-2v-2z"/>
                                    </svg>
                                </a>
                            `;
                    }

                    // ✅ Other common buttons (always visible)
                    actionBtns += `
                            <a class="me-3" href="/sales-invoice/${order.id}" title="View">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                    <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                </svg>
                            </a>
 
                            <a class="me-3" href="/edit-sales/${order.id}" title="Edit">
                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987C13.081 0.609 12.579 0.401 12.045 0.401C11.511 0.401 11.009 0.609 10.632 0.986L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                </svg>
                            </a>

                            <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/${order.id}');" title="Print">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                                    <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                                </svg>
                            </a>
                            <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}" title="Delete">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                                </svg>
                            </a>
                        `;

                    // ---- PUSH TO TABLE ----
                    tableBody.push([
                        `<a href="/sales-details/${order.id}">${order.order_number || ''}</a>`,
                        formattedDate,
                        order.user?.name || '',
                        `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_status || ''}</span>`,
                        `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_method || ''}</span>`,
                        displayAmount || '0.00',
                        order.biller?.name || 'Admin',
                        actionBtns
                    ]);
                });

                if (tableBody.length === 0) {
                    table.clear().draw();
                    $(".datanew tbody").html('<tr><td colspan="9">No order found</td></tr>');
                }

                table.clear().rows.add(tableBody).draw();
                calculateVisibleTotal(table, currencySymbol, currencyPosition);
            }


            // function renderOrders(data, selectedDate, currency_symbol, currency_position) {
            //     // Ensure data is an array

            //     data = Array.isArray(data) ? data : [];
            //     let tableBody = [];
            //     const currencySymbol = currency_symbol || '₹';
            //     const currencyPosition = currency_position || 'left';

            //     data.forEach(order => {
            //         if (!order || !order.id) return; // Skip empty or invalid orders
            //         if (!order.created_at) return;

            //         const orderDate = new Date(order.created_at).toISOString().split('T')[0];
            //         if (selectedDate && selectedDate !== orderDate) return;

            //         let date = new Date(order.created_at);
            //         let day = String(date.getDate()).padStart(2, '0');
            //         let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            //         let month = monthNames[date.getMonth()];
            //         let year = date.getFullYear();

            //         let hours = date.getHours();
            //         let minutes = String(date.getMinutes()).padStart(2, '0');
            //         let ampm = hours >= 12 ? 'PM' : 'AM';
            //         hours = hours % 12 || 12;

            //         let amount = parseFloat(order.total_amount || 0).toFixed(2);
            //         let displayAmount = currencyPosition === 'right' ?
            //             amount + currencySymbol :
            //             currencySymbol + amount;

            //         const formattedDate = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;

            //         // Build action buttons
            //         let actionBtns = `
            //                 <a href="javascript:void(0);" class="me-3 make-payment-btn" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
            //                     data-id="${order.id}" data-amount="${order.formatted_remaining_amount}" data-method="${order.payment_method || ''}"
            //                     data-emi-months="${order.remaining_emi_months}" data-emi-duration="${order.emi_duration || 0}"
            //                     data-total-amount="${order.total_amount || 0}" data-remaining-amount="${order.formatted_remaining_amount}"
            //                     data-remaining-emi-months="${order.remaining_emi_months}" title="Make Payment">
            //                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#092C4C" viewBox="0 0 24 24">
            //                         <path d="M21 7H3V5h18v2zm0 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h18zm-2 4H5v6h14v-6zM8 12h2v2H8v-2zm6 0h2v2h-2v-2z"/>
            //                     </svg>
            //                 </a>
            //                 <a class="me-3" href="/sales-details/${order.id}" title="View">
            //                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            //                         <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
            //                         <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
            //                     </svg>
            //                 </a>

            //                 <a class="me-3" href="/sales-invoice/${order.id}" title="Invoice">
            //                     <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 32 32" fill="#092C4C">
            //                         <path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z"/>
            //                     </svg>
            //                 </a>
            //                 <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/${order.id}');" title="Print">
            //                     <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
            //                         <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 极 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
            //                     </svg>
            //                 </a>
            //                 <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}" title="Delete">
            //                                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            //                                         <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
            //                                     </svg>
            //                                 </a>

            //             `;

            //         tableBody.push([
            //             `<a href="/sales-details/${order.id}">${order.order_number || ''}</a>`,
            //             formattedDate,
            //             order.user?.name || '',
            //             `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_status || ''}</span>`,
            //             `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_method || ''}</span>`,
            //             displayAmount || '0.00',
            //             order.biller?.name || 'Admin',
            //             actionBtns
            //         ]);
            //     });

            //     // Correct DataTables-compatible no-data row
            //     if (tableBody.length === 0) {
            //         table.clear().draw();
            //         $(".datanew tbody").html('<tr><td colspan="9">No order found</td></tr>');
            //     }


            //     table.clear().rows.add(tableBody).draw();
            //     calculateVisibleTotal(table, currencySymbol, currencyPosition);
            // }


            // ==============================================================================================================

            // Edit button if needed

            // <a class="me-3" href="/edit-sales/${order.id}" title="Edit">
            //     <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            //         <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987C13.081 0.609 12.579 0.401 12.045 0.401C11.511 0.401 11.009 0.609 10.632 0.986L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
            //     </svg>
            // </a>


            // <a class="me-3" href="/sales-details/${order.id}" title="View">
            //                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            //                         <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
            //                         <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
            //                     </svg>
            //                 </a>
            //                <a class="me-3" href="/sales-invoice/${order.id}" title="View Invoice">
            //                     <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="#092C4C">
            //                         <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z"/>
            //                     </svg>
            //                 </a>
            // ==============================================================================================================



            // Function to calculate visible total
            function calculateVisibleTotal(dt, currencySymbol, currencyPosition) {
                let total = 0;

                dt.rows({
                    filter: 'applied'
                }).every(function() {
                    const row = this.data();
                    const amountText = row[5];
                    const rawAmount = parseFloat(amountText.replace(/[^0-9.-]+/g, '')) || 0;
                    total += rawAmount;
                });

                const formatted = currencyPosition === 'right' ?
                    `${total.toFixed(2)}${currencySymbol}` :
                    `${currencySymbol}${total.toFixed(2)}`;

                $('#filtered-total').text(`Total: ${formatted}`);
            }

            // Initial data load
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            $.ajax({
                url: "/api/get_orders",
                method: "GET",
                data: selectedSubAdminId ? {
                    selectedSubAdminId: selectedSubAdminId
                } : {},
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status) {
                        renderOrders(response.data, null, response.currency_symbol, response.currency_position);

                        // Set up scroll synchronization
                        setTimeout(() => {
                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const orderTable = document.getElementById('order-table');

                            if (topScroll && tableResponsive && orderTable) {
                                const topInnerDiv = topScroll.querySelector('div');
                                if (topInnerDiv) {
                                    topInnerDiv.style.width = orderTable.scrollWidth + 'px';
                                    topScroll.onscroll = () => {
                                        tableResponsive.scrollLeft = topScroll.scrollLeft;
                                    };
                                    tableResponsive.onscroll = () => {
                                        topScroll.scrollLeft = tableResponsive.scrollLeft;
                                    };
                                }
                            }
                        }, 100);
                    } else {
                        table.clear().draw();
                        $(".datanew tbody").html('<tr><td colspan="9">No order found</td></tr>');
                    }
                },
                error: function() {
                    alert("Failed to load orders.");
                }
            });

            // Date filter event
            $('#filter-date').on('dp.change', function() {
                let selectedDate = $(this).val().trim();
                const authToken = localStorage.getItem("authToken");

                if (selectedDate === '') {
                    refreshOrdersData();
                    return;
                }

                // Convert to YYYY-MM-DD
                if (selectedDate.includes('-')) {
                    let parts = selectedDate.split('-');
                    if (parts.length === 3) {
                        selectedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }

                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                let url = `/api/orders/filter/sales?date=${selectedDate}`;

                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            renderOrders(response.data, selectedDate, response.currency_symbol, response.currency_position);
                        }
                    }
                });
            });

            // Delete order handler
            $(document).on('click', '.delete-order', function() {
                var orderId = $(this).data('id');
                var authToken = localStorage.getItem("authToken");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/delete/' + orderId,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                if (response.status === true) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: '#ff9f43',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        refreshOrdersData();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error",
                                        text: response.error,
                                        icon: "error",
                                        confirmButtonColor: '#ff9f43',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = "An error occurred while deleting the order";
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }

                                Swal.fire({
                                    title: "Error",
                                    text: errorMessage,
                                    icon: "error",
                                    confirmButtonColor: '#ff9f43',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            // ---------------- Payment Type (Fully / EMI) ----------------
            $('#paymentType').on('change', function() {
                if ($('#paymentTypeDiv').hasClass('d-none')) return; // Skip if hidden

                const selected = $(this).val();
                if (selected === 'fully') {
                    $('#fullyAmountDiv').removeClass('d-none');
                    $('#emiMonthDiv, #emiTotalCalculatedDiv, #emiMonthError').addClass('d-none');

                    $('#emiMonthInput, #emiTotalCalculated').val('');
                    $('#amount').val(window.currentRemainingAmount.toFixed(2)).prop('readonly', true);
                } else if (selected === 'emi') {
                    $('#emiMonthDiv').removeClass('d-none');
                    $('#fullyAmountDiv').addClass('d-none');
                }
            });

            // ---------------- EMI Month Input Validation ----------------
            $('#emiMonthInput').on('input', function() {
                const maxDuration = parseInt($('#emiDuration').text()) || 0;
                const monthlyEMI = parseFloat($('#emiMonthly').text()) || 0;
                const remainingAmount = parseFloat(window.currentRemainingAmount) || 0;
                let enteredMonth = parseInt($(this).val());

                if (isNaN(enteredMonth) || enteredMonth < 1) {
                    $('#emiTotalCalculatedDiv').addClass('d-none');
                    $('#emiTotalCalculated').val('');
                    $('#remainingAmountDisplay').text(remainingAmount.toFixed(2));
                    $('#remainingAmountHidden').val(remainingAmount.toFixed(2));
                    $('#emiMonthError').addClass('d-none');
                    return;
                }

                if (enteredMonth > maxDuration) {
                    enteredMonth = maxDuration;
                    $(this).val(maxDuration);
                    $('#emiMonthError').removeClass('d-none');
                } else {
                    $('#emiMonthError').addClass('d-none');
                }

                const calculatedEmiTotal = enteredMonth * monthlyEMI;
                const newRemaining = remainingAmount - calculatedEmiTotal;

                $('#emiTotalCalculatedDiv').removeClass('d-none');
                $('#emiTotalCalculated').val(calculatedEmiTotal.toFixed(2));
                $('#remainingAmountDisplay').text(newRemaining.toFixed(2));
                $('#remainingAmountHidden').val(newRemaining.toFixed(2));
            });

            // ---------------- Make Payment Button Click ----------------
            $(document).on('click', '.make-payment-btn', function() {




                var jobCardId = $(this).data('id');
                var method = ($(this).data('method') || '').toLowerCase();
                var totalAmount = parseFloat($(this).data('total-amount')) || 0;
                var remainingAmount = parseFloat($(this).data('remaining-amount')) || 0;
                var emiDuration = parseInt($(this).data('emi-duration')) || 0;
                var remainingEmiMonths = parseInt($(this).data('remaining-emi-months')) || 0;




                var monthlyEMI = emiDuration > 0 ? (totalAmount / emiDuration).toFixed(2) : '0.00';

                // Reset
                $('#fullyAmountDiv, #emiMonthDiv, #emiMonthError, #emiTotalCalculatedDiv').addClass('d-none');
                $('#amount, #emiMonthInput, #emiTotalCalculated').val('');

                // Set values in modal
                $('#paymentJobCardId').val(jobCardId);
                $('#remainingAmountDisplay').text(remainingAmount.toFixed(2));
                $('#remainingAmountHidden').val(remainingAmount.toFixed(2));
                $('#emiMonthly').text(monthlyEMI);
                $('#emiDuration').text(remainingEmiMonths);
                $('#emiTotal').text(totalAmount.toFixed(2));
                window.currentRemainingAmount = remainingAmount;
                $('#emiMonthInput').attr('max', remainingEmiMonths);

                // Show/hide sections based on method
                if (method === 'cash' || method === 'cash_online' || method === 'online') {
                    $('#paymentTypeDiv').addClass('d-none');
                    $('#paymentMethodDiv').removeClass('d-none');
                    $('#paymentMethodSelect').val('');
                    $('#amount').val('').prop('readonly', false);
                    $('#emiMonthlyRow, #emiDurationRow').addClass('d-none');
                } else {
                    $('#paymentTypeDiv').removeClass('d-none');
                    $('#paymentMethodDiv').addClass('d-none');
                    $('#amount').val(remainingAmount.toFixed(2)).prop('readonly', true);
                    $('#emiMonthlyRow, #emiDurationRow').removeClass('d-none');
                }

                // ---------------- Payment Method Dropdown ----------------
                $('#paymentMethodSelect').off('change').on('change', function() {
                    const selected = $(this).val();

                    $('#paidTypeDiv, #onlineTypeDiv, #cashOnlineTypeDiv').addClass('d-none');
                    $('#fullyCashOnlineFields, #partialCashOnlineFields, #partialPaidFields, #fullyPaidFields').addClass('d-none');
                    $('#upiAmountDiv').addClass('d-none');
                    $('#upiAmountInput').val('').attr('readonly', true);

                    if (selected === 'cash') {
                        $('#paidTypeDiv').removeClass('d-none');
                        $('#emiMonthlyRow, #emiDurationRow').addClass('d-none');
                    } else if (selected === 'online') {
                        $('#onlineTypeDiv').removeClass('d-none');
                        $('#emiMonthlyRow, #emiDurationRow').addClass('d-none');
                    } else if (selected === 'cash_online') {
                        $('#cashOnlineTypeDiv').removeClass('d-none');
                        $('#emiMonthlyRow, #emiDurationRow').addClass('d-none');
                    }
                });

                // ---------------- Online Type Dropdown ----------------
                $('#onlineTypeSelect').off('change').on('change', function() {
                    const selected = $(this).val();
                    if (selected === 'online_fully') {
                        $('#upiAmountDiv').removeClass('d-none');
                        $('#upiAmountInput').removeAttr('readonly');
                        $('#partialPaidFields').addClass('d-none');
                    } else if (selected === 'online_partially') {
                        $('#partialPaidFields').removeClass('d-none');
                        $('#upiAmountDiv').addClass('d-none');
                    } else {
                        $('#upiAmountDiv, #partialPaidFields').addClass('d-none');
                    }
                });

                // ---------------- Paid Type Dropdown ----------------
                $('#paidTypeSelect').off('change').on('change', function() {
                    let selected = $(this).val();
                    if (selected === 'cash_partially') {
                        $('#partialPaidFields').removeClass('d-none');
                        $('#fullyPaidFields').addClass('d-none');
                    } else if (selected === 'cash_fully') {
                        $('#fullyPaidFields').removeClass('d-none');
                        $('#partialPaidFields').addClass('d-none');
                        const remaining = parseFloat(window.currentRemainingAmount) || 0;
                        $('#cashAmount').val(remaining.toFixed(2)).prop('readonly', true);
                    } else {
                        $('#partialPaidFields, #fullyPaidFields').addClass('d-none');
                    }
                });

                // ---------------- Partial Payment Input ----------------
                $('#partialAmount').off('input').on('input', function() {
                    const paid = parseFloat($(this).val()) || 0;
                    const remaining = parseFloat(window.currentRemainingAmount) || 0;
                    let pending = remaining - paid;
                    if (pending < 0) pending = 0;
                    $('#pendingAmount').val(pending.toFixed(2));
                });

                // ---------------- Cash + Online Type ----------------
                $('#cashOnlineTypeSelect').off('change').on('change', function() {
                    const selectedType = $(this).val();
                    $('#fullyCashOnlineFields, #partialCashOnlineFields').addClass('d-none');

                    if (selectedType === 'cash_online_fully') {
                        $('#fullyCashOnlineFields').removeClass('d-none');
                    } else if (selectedType === 'cash_online_partially') {
                        $('#partialCashOnlineFields').removeClass('d-none');
                    }
                });

                // Fully Cash + Online
                $('#cashOnlineFullAmount').off('input').on('input', function() {
                    const cash = parseFloat($(this).val()) || 0;
                    let calculatedUPI = window.currentRemainingAmount - cash;
                    if (calculatedUPI < 0) calculatedUPI = 0;
                    $('#upiOnlineFullAmount').val(calculatedUPI.toFixed(2));
                });

                // Partial Cash + Online
                $('#cashOnlinePartialAmount, #upiOnlinePartialAmount').off('input').on('input', function() {
                    const cash = parseFloat($('#cashOnlinePartialAmount').val()) || 0;
                    const upi = parseFloat($('#upiOnlinePartialAmount').val()) || 0;
                    let remaining = window.currentRemainingAmount - (cash + upi);
                    if (remaining < 0) remaining = 0;
                    $('#remainingCashOnlineAmount').val(remaining.toFixed(2));
                });

                // ---------------- EMI Auto-selection ----------------
                if (method === 'emi') {
                    $('#paymentType').val('emi').trigger('change');
                    $('#paymentMethodDiv, #paidTypeDiv, #onlineTypeDiv').addClass('d-none');
                } else {
                    $('#paymentType').val('fully').trigger('change');
                }

                // ---------------- Payment History ----------------
                $('#paymentHistoryBox').addClass('d-none');
                $('#paymentHistoryList').empty();

                $('#viewHistoryBtn').off('click').on('click', function() {
                    $.ajax({
                        url: '/job-card/payment-history/' + jobCardId,
                        method: 'GET',
                        success: function(response) {
                            const history = response.data;
                            if (!history || history.length === 0) {
                                $('#paymentHistoryList').html('<li>No payment history found.</li>');
                            } else {
                                let historyHtml = '';
                                history.forEach(function(payment) {
                                    historyHtml += `
                                <li class="mb-2">
                                    <strong>Amount:</strong> ₹${parseFloat(payment.payment_amount).toFixed(2)}<br>
                                    <strong>Date:</strong> ${payment.payment_date}<br>
                                    <strong>Method:</strong> ${payment.payment_method}<br>
                                    <strong>Payment Type:</strong> ${payment.payment_type}<br>
                                    ${payment.payment_type === 'emi' ? `<strong>EMI Months:</strong> ${payment.emi_month || 0}<br>` : ''}
                                </li><hr class="my-1"/>
                            `;
                                });
                                $('#paymentHistoryList').html(historyHtml);
                            }
                            $('#paymentHistoryBox').removeClass('d-none');
                        },
                        error: function() {
                            $('#paymentHistoryList').html('<li class="text-danger">Failed to load payment history.</li>');
                            $('#paymentHistoryBox').removeClass('d-none');
                        }
                    });
                });

                $('#closeHistoryBtn').off('click').on('click', function() {
                    $('#paymentHistoryBox').addClass('d-none');
                });

            });

        });
    </script>
    <script>
        $('#makePaymentForm').on('submit', function(e) {
            e.preventDefault();

            let selectedPaymentType = $('#paymentType').val();
            $('#paymentMethodHidden').val(selectedPaymentType);
            let remainingAmountDisplay = $('#remainingAmountDisplay').text().trim();



            if (selectedPaymentType === 'emi') {
                let emiMonth = $('#emiMonthInput').val();

                if (!emiMonth || emiMonth < 1) {
                    $('#emiMonthError').removeClass('d-none').text('Please enter a valid EMI month.');
                    $('#emiMonthInput').addClass('is-invalid');
                    return;
                } else {
                    $('#emiMonthError').addClass('d-none');
                    $('#emiMonthInput').removeClass('is-invalid');
                }

                let emiTotal = $('#emiTotalCalculated').val();
                $('#emiTotalHidden').val(emiTotal);

                let remainingAmount = $('#remainingAmountInput').val();
                $('#remainingAmountHidden').val(remainingAmount);

                // 🔹 Update Remaining Amount UI
                $("#remainingAmountDisplay").text(parseFloat(remainingAmount).toFixed(2));

            } else if (selectedPaymentType === 'fully') {
                let cashRaw = $('#cashAmount').val();
                let upiRaw = $('#upiAmount').val();

                $('#cashAmountHidden').val(cashRaw !== '' ? parseFloat(cashRaw) : '');
                $('#upiAmountHidden').val(upiRaw !== '' ? parseFloat(upiRaw) : '');

                let fullAmount = 0;
                if (cashRaw !== '') fullAmount += parseFloat(cashRaw);
                if (upiRaw !== '') fullAmount += parseFloat(upiRaw);

                $('#emiTotalHidden').val(fullAmount);
                $('#remainingAmountHidden').val(0);

                // 🔹 Update Remaining Amount UI
                $("#remainingAmountDisplay").text("0.00");

            } else if (selectedPaymentType === 'cash_online_fully') {
                let cashRaw = $('#cashOnlineFullAmount').val();
                let upiRaw = $('#upiOnlineFullAmount').val();

                $('#cashAmountHidden').val(cashRaw !== '' ? parseFloat(cashRaw) : '');
                $('#upiAmountHidden').val(upiRaw !== '' ? parseFloat(upiRaw) : '');

                let totalAmount = 0;
                if (cashRaw !== '') totalAmount += parseFloat(cashRaw);
                if (upiRaw !== '') totalAmount += parseFloat(upiRaw);

                $('#emiTotalHidden').val(totalAmount);
                $('#remainingAmountHidden').val(0);

                // 🔹 Update Remaining Amount UI
                $("#remainingAmountDisplay").text("0.00");

            } else if (selectedPaymentType === 'cash_online_partially') {
                let cashRaw = $('#cashOnlinePartialAmount').val();
                let upiRaw = $('#upiOnlinePartialAmount').val();
                let remainingRaw = $('#remainingCashOnlineAmount').val();

                $('#cashAmountHidden').val(cashRaw !== '' ? parseFloat(cashRaw) : '');
                $('#upiAmountHidden').val(upiRaw !== '' ? parseFloat(upiRaw) : '');
                $('#remainingAmountHidden').val(remainingRaw !== '' ? parseFloat(remainingRaw) : '');

                let totalAmount = 0;
                if (cashRaw !== '') totalAmount += parseFloat(cashRaw);
                if (upiRaw !== '') totalAmount += parseFloat(upiRaw);

                $('#emiTotalHidden').val(totalAmount);

                // 🔹 Update Remaining Amount UI
                $("#remainingAmountDisplay").text(parseFloat(remainingRaw || 0).toFixed(2));
            }

            let formElement = $(this)[0];
            let formData = new FormData(formElement);

            // formData.append("remaining_amount", remainingAmountDisplay);

            let submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).text('Processing...');

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
        $('#exportAllChallan').click(function() {
            let selectedDate = $('#filter-date').val() || ''; // your date input
            console.log(selectedDate);

            let selectedSubAdminId = $('#subAdminSelect').val() || '';

            let url = `/export-orders?date=${selectedDate}&selectedSubAdminId=${selectedSubAdminId}`;

            // Redirect browser to download Excel
            window.location.href = url;
        });
    </script>



    @endpush