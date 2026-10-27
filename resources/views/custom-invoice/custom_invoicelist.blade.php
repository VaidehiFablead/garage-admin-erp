@extends('layout.app')

@section('title', 'All Invoices')

@section('content')
<style>
    #DataTables_Table_0_info {
        float: left;
    }

    .table-scroll-top {
        display: none;
    }

    .form-select {
        color: #595b5d !important;
        /* Bootstrap's default placeholder/input text color */
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
            <h4>All Invoices</h4>
            <!-- <h6>Manage your purchases</h6> -->
        </div>
        <div class="page-btn">
            @if(app('hasPermission')(4, 'add'))
            <a href="{{ route('custom_invoice.add') }}" class="btn btn-sm btn-added">
                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">New Invoice
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top mb-3">
                <div class="row w-100 align-items-center">
                    <!-- Search -->
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
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

                    <!-- Filter by Month -->
                    <div class="col-md-2 col-4 d-flex justify-content-md-end align-items-end">
                        <div class="date-filter w-100 w-md-auto">
                            <select id="filterMonth" class="form-select form-select-sm" style="font-size: 0.875rem;">
                                <option value="">All Months</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter by Year -->
                    <div class="col-md-2 col-4 d-flex justify-content-md-end align-items-end">
                        <div class="date-filter w-100 w-md-auto">
                            <select id="filterYear" class="form-select form-select-sm" style="font-size: 0.875rem;">
                                <option value="">All Years</option>
                                @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-md-2 col-4 d-flex justify-content-md-end align-items-end">
                        <div class="date-filter w-100 w-md-auto">
                            <input type="text" id="filter-date" placeholder="Choose Date"
                                class="datetimepicker form-control form-control-sm">
                        </div>
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
            <div class="table-scroll-top"
                style="overflow-x: auto; overflow-y: hidden; height: 20px; margin-bottom: 5px;">
                <div style="height: 1px;"></div> <!-- Adjust width to match your table width -->
            </div>

            <div class="table-responsive mt-3" style="overflow-x: auto;">

                <table class="table datanew" id="order-table" style="max-width: 2000px;">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Grand Total</th>
                            <th>Purchase Status</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS will populate this -->
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    const userRole = "{{ auth()->user()->role }}";
</script>

<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

        $(function() {

            // --- helpers ------------------------------------------------------------
            function formatDate(dateStr) {
                if (!dateStr) return 'N/A';
                const d = new Date(dateStr);
                const day = String(d.getDate()).padStart(2, '0');
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
                    "Dec"
                ];
                const month = months[d.getMonth()];
                const year = d.getFullYear();
                let hours = d.getHours();
                const minutes = String(d.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
            }

            function badge(text, okClass, warnClass) {
                const positiveStatuses = ['paid', 'completed', 'delivered', 'success'];
                const value = String(text).toLowerCase();
                const isPositive = positiveStatuses.includes(value);
                const cls = isPositive ? okClass : warnClass;
                return `<span class="badges ${cls}" style="text-transform:capitalize;">${text}</span>`;
            }


            function actionLinks(id) {

                let deleteBtn = '';

                if (
                    userRole !== 'sales-manager' &&
                    userRole !== 'purchase-manager' &&
                    userRole !== 'inventory-manager'
                ) {
                    deleteBtn = `
                                                                        <a class="me-3 confirm-text delete-order" data-id="${id}" href="javascript:void(0);">
                                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"></path>
                                                                                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"></path>
                                                                            </svg>
                                                                        </a>`;
                }

                return ` @if(app('hasPermission')(4, 'view'))
                                                            <a class="me-3" href="/invoice-view/${id}">
                                                                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path>
                                                                    <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path>
                                                                    </svg>
                                                                                        </a>
                                                                                              @endif
 
                                                                                        <a class="me-3" href="/custom-invoice-print/${id}">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z" data-name="Download" fill="#092C4C" opacity="1" data-original="#092C4C" class=""></path></g></svg>
                                                                                </a>
                                                                    @if(app('hasPermission')(4, 'edit'))
                                                                                <a class="me-3" href="/edit-custom-invoice/${id}">
                                                                                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path>
                                                                    </svg>
                                                                                </a>
                                                                                      @endif
                                                                                      @if(app('hasPermission')(4, 'delete'))

                                                                                <a class="me-3" href="javascript:void(0);" onclick="window.open('/custom-invoice/pdf/' + ${id});"  title="Print Invoice">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                                                                            <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                                                                        </svg>
                                                                    ${deleteBtn}
                                                                            @endif  `;
            }

            // --- main loader --------------------------------------------------------
            function loadPurchases() {

                function formatToYMD(dateStr) {
                    if (!dateStr || !dateStr.includes('-')) return '';
                    const [day, month, year] = dateStr.split('-');
                    return `${year}-${month}-${day}`;
                }
                const rawDate = $('#filter-date').val();
                const filterDate = formatToYMD(rawDate); // Date filter (YMD)

                const selectedMonth = $('#filterMonth').val(); // Month filter
                const selectedYear = $('#filterYear').val(); // ✅ Year filter

                const filters = {
                    date: filterDate,
                    selectedSubAdminId: selectedSubAdminId,
                    month: selectedMonth,
                    year: selectedYear, // ✅ Send year separately!
                    vendor_name: $('#filter_inputs input[placeholder="Enter Name"]').val().trim(),
                    reference_no: $('#filter_inputs input[placeholder="Enter Reference No"]').val()
                        .trim(),
                    status: $('#filter_inputs select').val()
                };

                $.ajax({
                    url: "/api/custom_invoice_list",
                    type: "GET",
                    dataType: "json",
                    data: filters,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: res => {
                        const rows = [];

                        if (res.success && Array.isArray(res.data)) {
                            const currencySymbol = res.currency_symbol || '₹';
                            const currencyPosition = res.currency_position || 'left';

                            res.data.forEach(o => {
                                let amount = parseFloat(o.grand_total || 0).toFixed(2);
                                let formattedAmount = currencyPosition === 'right' ?
                                    `${amount}${currencySymbol}` :
                                    `${currencySymbol}${amount}`;

                                rows.push([
                                    `<a href="/custom-invoice-print/${o.id}" class="" style="text-decoration: none;">${o.invoice_number || ''}</a>`,
                                    formatDate(o.date),
                                    `<span class="text-bolds" style="text-transform:capitalize;">${o.vendor_name || ''}</span>`,
                                    formattedAmount,
                                    badge(o.purchase_status || '',
                                        'bg-lightgreen', 'bg-lightyellow'),
                                    badge(o.payment_status || '',
                                        'bg-lightgreen', 'bg-lightred'),
                                    actionLinks(o.id)
                                ]);
                            });
                        }

                        const $tbl = $('#order-table');

                        if ($.fn.DataTable.isDataTable($tbl)) {
                            $tbl.DataTable().clear().rows.add(rows).draw();
                        } else {
                            $tbl.DataTable({
                                data: rows,
                                responsive: true,
                                autoWidth: false,
                                pageLength: 10,
                                ordering: true,
                                searching: true,
                                language: {
                                    searchPlaceholder: "Search purchases...",
                                    search: ""
                                }
                            });
                        }

                        const topScroll = document.querySelector('.table-scroll-top');
                        const tableResponsive = document.querySelector('.table-responsive');
                        const table = document.getElementById('order-table');

                        const topInnerDiv = topScroll.querySelector('div');
                        topInnerDiv.style.width = table.scrollWidth + 'px';

                        topScroll.onscroll = () => {
                            tableResponsive.scrollLeft = topScroll.scrollLeft;
                        };
                        tableResponsive.onscroll = () => {
                            topScroll.scrollLeft = tableResponsive.scrollLeft;
                        };
                    },
                    error: xhr => console.error('purchase_list error:', xhr.responseText)
                });
            }

            // --- events -------------------------------------------------------------

            loadPurchases(); // initial load
            $('#filter-date').on('dp.change', loadPurchases);
            $('#filterMonth').on('change', loadPurchases);
            $('#filterYear').on('change', loadPurchases); // ✅ bind year filter too!
            $('.btn-filters').on('click', e => {
                e.preventDefault();
                loadPurchases();
            });



            // Handle delete action
            $(document).on("click", ".delete-order", function() {
                let orderId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43", // Orange confirm button
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/custom_invoice_delete",
                            type: "POST",
                            data: {
                                id: orderId,
                                _token: "{{ csrf_token() }}"
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43"
                                    }).then(() => {
                                        location
                                            .reload(); // ✅ Reload page after user clicks OK
                                    });
                                } else {
                                    // ❌ Show backend error if success = false
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                }
                            },
                            error: function(xhr) {
                                // Try to show backend message from response JSON
                                let errorMessage =
                                    "Something went wrong. Try again.";
                                if (xhr.responseJSON && xhr.responseJSON
                                    .message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43"
                                });

                                console.error(xhr
                                    .responseText); // For debugging
                            }
                        });
                    }
                });
            });

        });

    });
</script>

@endpush