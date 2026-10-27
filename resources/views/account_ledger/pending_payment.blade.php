@extends('layout.app')
@section('title', 'Pending Payment')
@section('content')
    <style>
        @media screen and (max-width: 767px) {
            .form-group {
                margin-bottom: 10px;
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
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Pending Payment</h4>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- View PDF & Excel buttons -->
                {{-- <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm text-white btn-success me-2" id="viewCsvBtn">
                        <i class="fas fa-file-csv"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-sm text-white btn-danger" id="viewPdfBtn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div> --}}
                <form id="appointmentForm">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Select Type</label>
                                <select id="type_selector" class="form-control select2">
                                    <option value="">-- Select Type --</option>
                                    <option value="customer" selected>Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <select name="customer_id" id="customer_id" class="form-control select2">
                                    <option value="">Select Customer</option>
                                    <option value="all|All Customers" selected>All Customers</option> <!-- default all -->
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
                                    <option value="all|All Vendors">All Vendors</option>
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

                        <!-- New Filter -->
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Date Filter</label>
                                <select id="date_filter" class="form-control select2">
                                    <option value="today" selected>Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-lg-2 col-sm-6 col-6" style="overflow: hidden;">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="month[]" id="month" class="form-control select2" multiple>

                                    <option value="all">All Months</option>
                                    @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}">
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                        @endfor
                                </select>
                                <span class="text-danger error-month"></span>
                            </div>
                        </div> --}}


                        {{-- <div class="col-lg-2 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Year</label>
                                @php
                                $currentYear = date('Y');
                                $years = range($currentYear, $currentYear - 5);
                                @endphp

                                <select name="year" id="year" class="form-control select2">
                                    <option value="">Select Year</option>
                                    @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year==$currentYear ? 'selected' : '' }}>
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
                        @if (empty($subAdminId))
                        <div class="col-lg-2 col-sm-6 col-6 v_num">
                            <div class="form-group">
                                <label>Vehicle Number</label>
                                <input type="text" name="vehicle_number" id="vehicle_number" class="form-control">
                                <span class="text-danger error-vehicle_number"></span>
                            </div>
                        </div>
                        @endif --}}
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
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            });

            // Hide vendor by default, show customer
            $("#vendor_id").closest(".col-lg-3").hide();
            $("#customer_id").closest(".col-lg-3").show();

            // Trigger fetch on page load with default values
            fetchPayments();

            // Show dropdown based on type selector
            $("#type_selector").on("change", function () {
                let type = $(this).val();
                if (type === "customer") {
                    $("#customer_id").closest(".col-lg-3").show();
                    $("#vendor_id").closest(".col-lg-3").hide().val("");
                    $("#customer_id").val("all|All Customers").trigger("change"); // default All Customers
                } else if (type === "vendor") {
                    $("#vendor_id").closest(".col-lg-3").show();
                    $("#customer_id").closest(".col-lg-3").hide().val("");
                } else {
                    $("#customer_id, #vendor_id").closest(".col-lg-3").hide().val("");
                }
                fetchPayments();
            });

            // Handle customer, vendor, month, year, filter
            $("#customer_id, #vendor_id, #month, #year, #date_filter").on("change", fetchPayments);

            // Vehicle input with debounce
            let debounceTimer;
            $("#vehicle_number").on("input", function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchPayments, 500);
            });



            function fetchPayments() {
                var selectedSubAdminId1 = localStorage.getItem("selectedSubAdminId");
                var authToken = localStorage.getItem("authToken");
                var months = $("#month").val();
                var year = $("#year").val();
                var vehicleNumber = $("#vehicle_number").val()?.trim() || "";
                var filter = $('#date_filter').val()?.trim() || "";

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
                    url: "/api/get-payment-pending",
                    type: "GET",
                    headers: { "Authorization": "Bearer " + authToken },
                    data: {
                        type: type,
                        id: userId,
                        name: name,
                        month: months,
                        year: year,
                        filter: filter,
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
                var selectedSubAdminId1 = localStorage.getItem("selectedSubAdminId");
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
                                <h5 class="mb-0 fw-bold">${type === 'customer' && selectedSubAdminId1 == null ? 'Job Card' : (selectedSubAdminId1 == null ? 'Purchases' : (selectedSubAdminId1 && type === 'vendor' ? 'Purchases' : 'Sales'))}</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>`;

                if (type === 'customer') {
                    html += `<th>Job Card</th>
                             <th>Total Amount</th>
                             <th>Remaining Amount</th>
                             <th>Type</th>
                             ${!selectedSubAdminId1 ? '<th>Vehicle No.</th>' : ''}`;
                } else if (type === 'vendor') {
                    html += `<th>Purchase No</th>
                             <th>Total Amount</th>
                             <th>Remaining Amount</th>
                             <th>Vendor</th>`;
                }

                html += `       </tr>
                                </thead>
                                <tbody>`;

                let totalPaid = 0;

                if (data.paidPayments.length > 0) {
                    data.paidPayments.forEach(payment => {

                        html += '<tr>';
                        if (type === 'customer') {
                            html += `
                                <td>${payment.job_card_number}</td>
                                <td>${formatCurrency(payment.total_subtotal_amount)}</td>
                                <td>${formatCurrency(payment.remaining_amount)}</td>
                                <td>${payment.type.charAt(0).toUpperCase() + payment.type.slice(1)}</td>
                                ${!selectedSubAdminId1 ? `<td>${payment.vehicle_no || '-'}</td>` : ''}`;

                        } else if (type === 'vendor') {
                            html += `
                                <td>${payment.bill_no || '-'}</td>
                                <td>${formatCurrency(payment.payable_amount)}</td>
                                <td>${formatCurrency(payment.remaining_amount)}</td>
                                <td>${payment.vendor ? payment.vendor.name : '-'}</td>`;
                        }
                        html += '</tr>';
                        totalPaid += parseFloat(payment.remaining_amount);
                    });
                } else {
                    html += `<tr><td colspan="${type === 'customer' ? 5 : 4}" class="text-center text-muted">No record available</td></tr>`;
                }

                html += `</tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-end pe-3 mt-2 mb-2">
                    <div style="border: 1px solid #1b2850; border-radius: 5px; padding: 4px 10px; display: inline-block;">
                        <span style="font-weight: bold; font-size: 15px; color: #1b2850;">Total Pending:</span>
                        <span style="font-weight: bold; font-size: 15px; color: orange;">${formatCurrency(totalPaid.toFixed(2))}</span>
                    </div>
                </div>`;
                if (type === 'customer' && selectedSubAdminId1 == null) {



                    // === PENDING PAYMENTS ===
                    html += `<div class="card shadow mb-4">
                            <div class="card-header text-dark">
                                <h5 class="mb-0 fw-bold">Daily Data</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>`;

                    if (type === 'customer') {
                        html += `<th>Job Card</th>
                             <th>Total Amount</th>
                             <th>Remaining Amount</th>
                             <th>Type</th>
                             ${!selectedSubAdminId1 ? '<th>Vehicle No.</th>' : ''}`;
                    } else if (type === 'vendor') {
                        html += `<th>Purchase No</th>
                             <th>Total Amount</th>
                             <th>Remaining Amount</th>
                             <th>Vendor</th>`;
                    }

                    html += `       </tr>
                                </thead>
                                <tbody>`;

                    let totalPending = 0;

                    if (data.pendingPayments.length > 0) {
                        data.pendingPayments.forEach(payment => {

                            html += '<tr>';
                            if (type === 'customer') {
                                html += `
                                <td>${payment.job_card_number}</td>
                                <td>${formatCurrency(payment.total_subtotal_amount)}</td>
                                <td>${formatCurrency(payment.remaining_amount)}</td>
                                <td>${payment.type.charAt(0).toUpperCase() + payment.type.slice(1)}</td>
                                ${!selectedSubAdminId1 ? `<td>${payment.vehicle_no || '-'}</td>` : ''}`;

                            } else if (type === 'vendor') {
                                html += `
                                <td>${payment.bill_no || '-'}</td>
                                <td>${formatCurrency(payment.payable_amount)}</td>
                                <td>${formatCurrency(payment.remaining_amount)}</td>
                                <td>${payment.vendor ? payment.vendor.name : '-'}</td>`;
                            }
                            html += '</tr>';
                            totalPending += parseFloat(payment.remaining_amount);
                        });
                    } else {
                        html += `<tr><td colspan="${type === 'customer' ? 5 : 4}" class="text-center text-muted">No record available</td></tr>`;
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
                }

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
                        type: type, // send type to backend
                        id: id, // customer_id or vendor_id
                        name: name, // customer_name or vendor_name
                        gst_num: gstNum, // only for customer
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
                        type: type, // send type to backend
                        id: id, // customer_id or vendor_id
                        name: name, // customer_name or vendor_name
                        gst_num: gstNum, // only for customer
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
                            reader.onload = function () {
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