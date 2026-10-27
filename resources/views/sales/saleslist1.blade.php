@extends('layout.app')

@section('title', 'Sales List')

@section('content')
    <style>
        .table-scroll-top {
            display: none;
        }

        .table-top .total-box {
            border: 2px solid #1b2850;
            /* changed border color */
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
            /* background: #f8f9fa; */
            font-weight: bold;
            font-size: 15px;
            color: #1b2850;
            white-space: nowrap;
        }


        .total-box span {
            color: orange;
        }

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
            }
        }
    </style>
    @if (session('error'))
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
                @if (app('hasPermission')(2, 'add'))
                    <a href="{{ route('sales.add') }}" class="btn btn-sm btn-added"><img
                            src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1">New
                        Sales</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <!-- Top Search and Date Filter Row -->

                <div class="table-top mb-3">
                    <div class="row w-100 align-items-center g-2">
                        <div class="col-lg-6 col-md-6 col-12">
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
                        <!-- Total & Date Filter -->
                        <div
                            class="col-lg-6 col-md-6 col-12 d-flex flex-wrap justify-content-lg-end justify-content-md-end gap-3">
                            <!-- Total -->
                            {{-- <div class="col-md-4 col-12 text-center"> --}}
                            <div id="filtered-total" class="total-box">
                                Total: <span style="color:#ff9f43">₹0.00</span>
                            </div>

                            {{-- </div> --}}

                            <!-- Date Filter -->
                            <div class="input-groupicon">
                                <input type="text" id="filter-date" placeholder="Choose Date"
                                    class="datetimepicker form-control form-control-sm">
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

            </div>
        </div>

    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {

            var authToken = localStorage.getItem("authToken");

            // Helper function to set selected sub-admin and refresh data
            function setSelectedSubAdmin(subAdminId) {
                localStorage.setItem('selectedSubAdminId', subAdminId);
                // Refresh the page or reload data
                location.reload();
            }

            // Helper function to clear selected sub-admin and show all data
            function clearSelectedSubAdmin() {
                localStorage.removeItem('selectedSubAdminId');
                // Refresh the page or reload data
                location.reload();
            }

            // Helper function to refresh data without page reload
            function refreshOrdersData() {
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                const authToken = localStorage.getItem("authToken");
                console.log('call');

                if (!authToken) {
                    console.error("Auth token missing.");
                    return;
                }

                // Use the global table instance

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
                            let tableBody = [];
                            const currencySymbol = response.currency_symbol || '₹';
                            const currencyPosition = response.currency_position || 'left';

                            response.data.forEach(order => {
                                let createdAt = "N/A";
                                console.log("Order created_at:", order);
                                if (order.created_at) {
                                    let date = new Date(order.created_at);
                                    let day = String(date.getDate()).padStart(2, '0');
                                    let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    let month = monthNames[date.getMonth()];
                                    let year = date.getFullYear();
                                    let hours = date.getHours();
                                    let minutes = String(date.getMinutes()).padStart(2, '0');
                                    let ampm = hours >= 12 ? 'PM' : 'AM';
                                    hours = hours % 12 || 12;
                                    createdAt =
                                        `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
                                }

                                let amount = isNaN(parseFloat(order.total_amount)) ? 0 :
                                    parseFloat(order.total_amount).toFixed(2);
                                let displayAmount = currencyPosition === 'right' ?
                                    amount + currencySymbol : currencySymbol + amount;

                                // Capitalize helper
                                const toTitleCase = (str) => str ? str.replace(/\b\w/g, c => c
                                    .toUpperCase()) : '';

                                tableBody.push([
                                    `<a href="/sales-details/${order.id}">${order.order_number || ''}</a>`,
                                    createdAt,
                                    toTitleCase(order.user?.name) || '',
                                    `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_status || ''}</span>`,
                                    `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_method || ''}</span>`,
                                    displayAmount || '0.00',
                                    toTitleCase(order.biller?.name) || 'Admin',
                                    `
                                @if (app('hasPermission')(2, 'view'))
                                            <a class="me-3" href="/sales-details/${order.id}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                            <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                            </svg>


                                            </a>
                                                  @endif
                                                  @if (app('hasPermission')(2, 'edit'))
                                            <a class="me-3" href="/edit-sales/${order.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                </svg>

                                            </a>
                                                  @endif
                                            <a class="me-3" href="/sales-invoice/${order.id}">


                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z" data-name="Download" fill="#092C4C" opacity="1" data-original="#092C4C" class=""></path></g></svg>
                                            </a>
                                            <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/' + ${order.id});"  title="Print Invoice">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                    <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                </svg>
            </a>

            @if (app('hasPermission')(2, 'delete'))
                                            <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                </svg>

                                            </a>

                                                  @endif
                                            `
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();
                            calculateVisibleTotal(table, currencySymbol, currencyPosition);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching order data:", error);
                    }
                });
            }



            // Example usage:
            // To filter by specific sub-admin: setSelectedSubAdmin('123');
            // To show all data (admin only): clearSelectedSubAdmin();
            // To refresh data without reload: refreshOrdersData();

            // Initialize the global table variable
            var table = $('.datanew').DataTable();


            // Get selected sub-admin ID from localStorage
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
                        let tableBody = [];

                        const currencySymbol = response.currency_symbol || '₹';
                        const currencyPosition = response.currency_position || 'left';

                        // Attach the function with parameterized currency


                        response.data.forEach(order => {
                            let createdAt = "N/A";
                            if (order.order_items && order.order_items.length > 0) {
                                // Use created_at of the first order item
                                let date = new Date(order.created_at);

                                let day = String(date.getDate()).padStart(2, '0');
                                let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                let month = monthNames[date.getMonth()];
                                let year = date.getFullYear();

                                let hours = date.getHours();
                                let minutes = String(date.getMinutes()).padStart(2, '0');
                                let ampm = hours >= 12 ? 'PM' : 'AM';
                                hours = hours % 12 || 12;

                                createdAt =
                                    `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
                            }

                            let amount = parseFloat(order.total_amount).toFixed(2);
                            let displayAmount = currencyPosition === 'right' ?
                                amount + currencySymbol // No space before/after
                                :
                                currencySymbol + amount;

                            // Capitalize helper
                            const toTitleCase = (str) => str ? str.replace(/\b\w/g, c => c
                                .toUpperCase()) : '';

                            tableBody.push([
                                `<a href="/sales-details/${order.id}" class="">${order.order_number || ''}</a>`,
                                createdAt,
                                toTitleCase(order.user?.name) || '',
                                `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_status || ''}</span>`,
                                `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_method || ''}</span>`,
                                displayAmount || '0.00',
                                toTitleCase(order.biller?.name) || 'Admin',
                                `
                                @if (app('hasPermission')(2, 'view'))
                                            <a class="me-3" href="/sales-details/${order.id}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                            <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                            </svg>


                                            </a>
                                                  @endif
                                                  @if (app('hasPermission')(2, 'edit'))
                                            <a class="me-3" href="/edit-sales/${order.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                </svg>

                                            </a>
                                                  @endif
                                            <a class="me-3" href="/sales-invoice/${order.id}">


                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z" data-name="Download" fill="#092C4C" opacity="1" data-original="#092C4C" class=""></path></g></svg>
                                            </a>
                                            <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/' + ${order.id});"  title="Print Invoice">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                    <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                </svg>
            </a>

            @if (app('hasPermission')(2, 'delete'))
                                            <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                </svg>

                                            </a>

                                                  @endif
                                            `
                            ]);

                        });


                        table.clear().rows.add(tableBody).draw();
                        table.on('draw', function() {
                            calculateVisibleTotal(table, currencySymbol, currencyPosition);
                        });

                        // Also call it immediately after draw
                        calculateVisibleTotal(table, currencySymbol, currencyPosition);
                        setTimeout(() => {
                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const orderTable = document.getElementById('order-table');

                            if (topScroll && tableResponsive && orderTable) {
                                const topInnerDiv = topScroll.querySelector('div');

                                if (topInnerDiv) {
                                    topInnerDiv.style.width = orderTable.scrollWidth + 'px';

                                    // Avoid duplicate listeners
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
                }
            });
            // $('#filter-date').on('dp.change', function() {
            //     let selectedDate = $(this).val().trim(); // DD-MM-YYYY
            //     console.log("Original selectedDate:", selectedDate);

            //     if (selectedDate === '') {
            //         // If input is cleared, load all data
            //         fetchAllOrders(); // You'll define this function below
            //         return;
            //     }

            //     // Convert to YYYY-MM-DD
            //     if (selectedDate.includes('-')) {
            //         let parts = selectedDate.split('-');
            //         if (parts.length === 3) {
            //             selectedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
            //         }
            //     }

            //     console.log("Formatted selectedDate:", selectedDate);

            //     // Call API to get and filter orders
            //     $.ajax({
            //         url: '/api/orders/filter',
            //         method: 'GET',
            //         dataType: 'json',
            //         headers: {
            //             "Authorization": "Bearer " + authToken,
            //         },
            //         success: function(response) {
            //             if (response.status) {
            //                 renderOrders(response.data, selectedDate, response.currency_position, response.currency_symbol);
            //             }
            //         }
            //     });
            // });

            function fetchAllOrders(selectedDate = null) {
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                const authToken = localStorage.getItem("authToken");

                const requestData = {};
                if (selectedSubAdminId) requestData.selectedSubAdminId = selectedSubAdminId;
                if (selectedDate) requestData.date = selectedDate; // send selected date to API if needed

                $.ajax({
                    url: '/api/orders/filter',
                    method: 'GET',
                    data: requestData,
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            renderOrders(response.data, selectedDate, response.currency_position,
                                response.currency_symbol);
                        } else {
                            renderOrders([], null, response.currency_position, response
                                .currency_symbol); // clear table if no data
                        }
                    }
                });
            }

        });

        function renderOrders(data, selectedDate, currency_position, currency_symbol) {
            let tableBody = [];
            const currencySymbol = currency_symbol || '₹';
            const currencyPosition = currency_position || 'left';


            data.forEach(order => {

                if (!order.created_at) return;

                const orderDate = new Date(order.created_at).toISOString().split('T')[0];
                if (selectedDate && selectedDate !== orderDate) return;

                let date = new Date(order.created_at);
                let day = String(date.getDate()).padStart(2, '0');
                let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                let month = monthNames[date.getMonth()];
                let year = date.getFullYear();

                let hours = date.getHours();
                let minutes = String(date.getMinutes()).padStart(2, '0');
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;

                const currencySymbol = order.currency_symbol || '₹';
                const currencyPosition = order.currency_position || 'left';

                let amount = parseFloat(order.total_amount).toFixed(2);
                let displayAmount = currencyPosition === 'right' ?
                    amount + currencySymbol :
                    currencySymbol + amount;

                const formattedDate = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;

                tableBody.push([
                    `<a href="/sales-details/${order.id}" class="">${order.order_number || ''}</a>`,
                    formattedDate,
                    order.user?.name || '',
                    `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_status || ''}</span>`,
                    `<span class="badges bg-lightgreen" style="text-transform: capitalize;">${order.payment_method || ''}</span>`,
                    displayAmount || '0.00',
                    order.biller?.name || 'Admin',

                    `
                                @if (app('hasPermission')(2, 'view'))
                                            <a class="me-3" href="/sales-details/${order.id}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                            <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                            </svg>


                                            </a>
                                                  @endif
                                                  @if (app('hasPermission')(2, 'edit'))
                                            <a class="me-3" href="/edit-sales/${order.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                </svg>

                                            </a>
                                                  @endif
                                            <a class="me-3" href="/sales-invoice/${order.id}">


                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z" data-name="Download" fill="#092C4C" opacity="1" data-original="#092C4C" class=""></path></g></svg>
                                            </a>
                                            <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/' + ${order.id});"  title="Print Invoice">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                    <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                </svg>
            </a>

            @if (app('hasPermission')(2, 'delete'))
                                            <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                </svg>

                                            </a>

                                                  @endif
                                            `
                ]);
            });

            const $tbl = $('.datanew');
            if ($.fn.DataTable.isDataTable($tbl)) {
                const dt = $tbl.DataTable();
                dt.clear().rows.add(tableBody).draw();

                dt.off('draw').on('draw', function() {
                    calculateVisibleTotal(dt, currencySymbol, currencyPosition);
                });

                calculateVisibleTotal(dt, currencySymbol, currencyPosition);
            } else {
                const dt = $tbl.DataTable({
                    data: tableBody,
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    ordering: true,
                    searching: true,
                    language: {
                        searchPlaceholder: "Search sales...",
                        search: ""
                    }
                });

                dt.on('draw', function() {
                    calculateVisibleTotal(dt, currencySymbol, currencyPosition);
                });

                calculateVisibleTotal(dt, currencySymbol, currencyPosition);
            }
            // table.clear().rows.add(tableBody).draw();
        }

        // Initial fetch
        // Get selected sub-admin ID from localStorage
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        const authToken = localStorage.getItem("authToken");

        const table = $('#ordersTable').DataTable();

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
                    let tableBody = [];
                    const currencySymbol = response.currency_symbol || '₹';
                    const currencyPosition = response.currency_position || 'left';

                    response.data.forEach(order => {
                        // Format created_at
                        let createdAt = "N/A";
                        if (order.created_at) {
                            let date = new Date(order.created_at);
                            let day = String(date.getDate()).padStart(2, '0');
                            let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
                                "Sep", "Oct", "Nov", "Dec"
                            ];
                            let month = monthNames[date.getMonth()];
                            let year = date.getFullYear();

                            let hours = date.getHours();
                            let minutes = String(date.getMinutes()).padStart(2, '0');
                            let ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12 || 12;

                            createdAt = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
                        }

                        let amount = parseFloat(order.total_amount).toFixed(2);
                        let displayAmount = currencyPosition === 'right' ?
                            amount + currencySymbol :
                            currencySymbol + amount;

                        tableBody.push([
                            `<a href="/sales-details/${order.id}">${order.order_number || ''}</a>`,
                            createdAt,
                            order.user.name || '',
                            `<span class="badges bg-lightgreen">${order.payment_status || ''}</span>`,
                            `<span class="badges bg-lightgreen">${order.payment_method || ''}</span>`,
                            displayAmount,
                            order.biller?.name || 'Admin',

                            `
                                @if (app('hasPermission')(2, 'view'))
                                            <a class="me-3" href="/sales-details/${order.id}">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                            <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                            </svg>


                                            </a>
                                                  @endif
                                                  @if (app('hasPermission')(2, 'edit'))
                                            <a class="me-3" href="/edit-sales/${order.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                </svg>

                                            </a>
                                                  @endif
                                            <a class="me-3" href="/sales-invoice/${order.id}">


                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M28 24v-4a1 1 0 0 0-2 0v4a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h18a3 3 0 0 0 3-3zm-6.38-5.22-5 4a1 1 0 0 1-1.24 0l-5-4a1 1 0 0 1 1.24-1.56l3.38 2.7V6a1 1 0 0 1 2 0v13.92l3.38-2.7a1 1 0 1 1 1.24 1.56z" data-name="Download" fill="#092C4C" opacity="1" data-original="#092C4C" class=""></path></g></svg>
                                            </a>
                                            <a class="me-3" href="javascript:void(0);" onclick="window.open('/sales/invoice/pdf/' + ${order.id});"  title="Print Invoice">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#092C4C" viewBox="0 0 24 24">
                    <path d="M19 7V4a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v9a2 2 0 0 0 2 2h2v3h12v-3h2a2 2 0 0 0 2-2V8a1 1 0 0 0-1-1h-2zM7 4h10v3H7V4zm10 16H7v-4h10v4zm3-6a1 1 0 0 1-1 1h-2v-2H7v2H5a1 1 0 0 1-1-1V9h16v5z"/>
                </svg>
            </a>

            @if (app('hasPermission')(2, 'delete'))
                                            <a class="me-3 delete-order" href="javascript:void(0);" data-id="${order.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                </svg>

                                            </a>

                                                  @endif
                                            `
                        ]);
                    });

                    table.clear().rows.add(tableBody).draw();

                    table.on('draw', function() {
                        calculateVisibleTotal(table, currencySymbol, currencyPosition);
                    });

                    calculateVisibleTotal(table, currencySymbol, currencyPosition);
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
            let selectedDate = $(this).val().trim(); // Format: DD-MM-YYYY
            const authToken = localStorage.getItem("authToken");

            if (selectedDate === '') {
                fetchAllOrders();
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
            let url = `/api/orders/filter?date=${selectedDate}`;

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
                    if (response.status) {
                        console.log("Filtered response:", response.data);
                        console.log("currency_symbol response:", response.currency_symbol);
                        console.log("currency_position response:", response.currency_position);
                        renderOrders(response.data, selectedDate, response.currency_symbol, response
                            .currency_position);
                    }
                }
            });
        });


        function calculateVisibleTotal(dt) {
            let total = 0;

            dt.rows({
                search: 'applied'
            }).every(function() {
                const row = this.data();
                const rawAmount = parseFloat(row[5].replace(/[^0-9.-]+/g, '')) || 0; // ✅ use column 5
                total += rawAmount;
            });

            const firstRow = dt.rows({
                search: 'applied'
            }).data()[0];
            const currencySymbol = firstRow ? firstRow.currency_symbol || '₹' : '₹';
            const currencyPosition = firstRow ? firstRow.currency_position || 'left' : 'left';

            const formatted = currencyPosition === 'right' ?
                total.toFixed(2) + currencySymbol :
                currencySymbol + total.toFixed(2);

            $('#filtered-total span').text(formatted);
        }



        $(document).on('click', '.delete-order', function() {
            var orderId = $(this).data('id'); // ✅ Correct usage
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
                                    confirmButtonColor: '#ff9f43', // Set OK button color here
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
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
        // });
    </script>
@endpush
