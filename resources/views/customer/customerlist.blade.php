@extends('layout.app')

@section('title', 'Customer List')

@section('content')
<style>
    /* Hide default DataTables controls */
    .dataTables_filter,
    .dataTables_length,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }

    .dataTables_wrapper .row:first-child {
        display: none !important;
    }

    table.datanew {
        width: 100% !important;
    }

    /* Desktop: show all columns, hide Details toggle column */
    @media (min-width: 769px) {
        table.datanew thead th,
        table.datanew tbody td {
            display: table-cell !important;
        }

        table.datanew thead th.details-column,
        table.datanew tbody td:nth-child(2) {
            display: none !important;
        }

        .customer-details-row {
            display: none !important;
        }
        
    }

    /* Mobile: hide non-essential columns, show Details toggle */
    @media (max-width: 768px) {
        table.datanew thead th:nth-child(n+3),
        table.datanew tbody td:nth-child(n+3) {
            display: none !important;
        }

        table.datanew {
            table-layout: fixed;
            width: 100% !important;
        }

        table.datanew thead th:first-child,
        table.datanew tbody td:first-child {
            width: 80% !important;
        }

        table.datanew thead th.details-column,
        table.datanew tbody td:nth-child(2) {
            display: table-cell !important;
            text-align: center;
            vertical-align: middle;
            width: 20% !important;
        }
    }
    

    /* Expandable details row */
    .customer-details-row {
        display: none;
    }

    .customer-details-row.show {
        display: table-row;
    }

    .customer-details-content {
        padding: 14px 8px !important;
        background: #fff;
        border-top: 2px solid #e0e0e0;
    }

    table.datanew tbody tr.customer-details-row td.customer-details-content {
        display: table-cell !important;
        width: 100% !important;
    }

    .customer-details-list {
        margin-bottom: 15px;
    }

    .customer-detail-row-simple {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        gap: 12px;
    }

    .customer-detail-row-simple:last-of-type {
        border-bottom: none;
    }

    .customer-detail-label-simple {
        font-weight: 600;
        color: #595b5d;
        font-size: 13px;
        white-space: nowrap;
    }

    .customer-detail-value-simple {
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

    /* Toggle button (orange +, red -) */
    .mobile-toggle-btn-table {
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

    .mobile-toggle-btn-table:hover {
        background: #ff8c2e;
    }

    .mobile-toggle-btn-table.minus {
        background: #dc3545;
    }

    .mobile-toggle-btn-table.minus:hover {
        background: #c82333;
    }

    .customer-name {
        font-weight: 500;
        color: #212B36;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>All Customers</h4>
        </div>
        <div class="page-btn">
            @if(app('hasPermission')(4, 'add'))
            <a href="{{ route('customer.add') }}" class="btn btn-added btn-sm">
                <img src="{{ env('ImagePath').'admin/assets/img/icons/plus.svg' }}" alt="img"> New Customer
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search customers...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th class="details-column">Details</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>GST Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
                <div class="pagination-controls mt-3 text-end">
                    <button id="prevBtn" class="btn btn-sm btn-secondary">Prev</button>
                    <span id="pageInfo" class="mx-2"></span>
                    <button id="nextBtn" class="btn btn-sm btn-secondary">Next</button>
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
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        let currentPage = 1;
        let lastPage = 1;

        // Debounced search
        let typingTimer;
        let delay = 500;
        $(document).on('keyup', '#searchInput', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                fetchCustomers(1);
            }, delay);
        });

        // -------------------------------------------------------
        // Build expandable details row content (product-list style)
        // -------------------------------------------------------
        function buildCustomerExpandableRowContent(customer) {
            let actionBtns = '';

            @if(app('hasPermission')(9, 'view'))
                actionBtns += `<a class="btn-icon-mobile btn-view" href="/customer-view/${customer.id}" title="View">
                    <i class="fas fa-eye"></i>
                </a>`;
            @endif

            @if(app('hasPermission')(9, 'edit'))
                actionBtns += `<a class="btn-icon-mobile btn-edit" href="/edit-customer/${customer.id}" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>`;
            @endif

            @if(app('hasPermission')(9, 'delete'))
                actionBtns += `<a class="btn-icon-mobile btn-delete confirm-text delete-btn" data-id="${customer.id}" href="javascript:void(0);" title="Delete">
                    <i class="fas fa-trash"></i>
                </a>`;
            @endif

            return `
                <td colspan="8" class="customer-details-content">
                    <div class="customer-details-list">
                        <div class="customer-detail-row-simple">
                            <span class="customer-detail-label-simple">Phone:</span>
                            <span class="customer-detail-value-simple">${customer.phone || 'N/A'}</span>
                        </div>
                        <div class="customer-detail-row-simple">
                            <span class="customer-detail-label-simple">Email:</span>
                            <span class="customer-detail-value-simple">${customer.email || 'N/A'}</span>
                        </div>
                        <div class="customer-detail-row-simple">
                            <span class="customer-detail-label-simple">Country:</span>
                            <span class="customer-detail-value-simple">${customer._country}</span>
                        </div>
                        <div class="customer-detail-row-simple">
                            <span class="customer-detail-label-simple">City:</span>
                            <span class="customer-detail-value-simple">${customer._city}</span>
                        </div>
                        <div class="customer-detail-row-simple">
                            <span class="customer-detail-label-simple">GST Number:</span>
                            <span class="customer-detail-value-simple">${customer.gst_num || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="mobile-action-buttons-simple">
                        ${actionBtns}
                    </div>
                </td>
            `;
        }

        // -------------------------------------------------------
        // Toggle expandable row (same pattern as product list)
        // -------------------------------------------------------
        window.toggleCustomerRowDetails = function(customerId) {
            const btn = $(`.mobile-toggle-btn-table[data-customer-id="${customerId}"]`);
            if (btn.length === 0) return;

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.customer-details-row[data-customer-id="${customerId}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.length === 0) {
                const customerData = window.customerDataMap && window.customerDataMap[customerId];
                if (!customerData) return;

                detailsRow = $('<tr>')
                    .addClass('customer-details-row')
                    .attr('data-customer-id', customerId)
                    .html(buildCustomerExpandableRowContent(customerData));
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

        // -------------------------------------------------------
        // Fetch customers
        // -------------------------------------------------------
        function fetchCustomers(page = 1) {
            let search = $('#searchInput').val();
            let url = `/api/getAllCustomer?page=${page}&search=${search}`;
            if (selectedSubAdminId) {
                url += `&selectedSubAdminId=${selectedSubAdminId}`;
            }

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $(".datanew tbody").html(
                        '<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status) {
                        let customers = response.data;
                        let pagination = response.pagination;
                        let tableBody = "";
                        window.customerDataMap = {};

                        currentPage = pagination.current_page;
                        lastPage = pagination.last_page;

                        $("#pageInfo").text(`Page ${currentPage} of ${lastPage}`);
                        $("#prevBtn").prop("disabled", currentPage === 1);
                        $("#nextBtn").prop("disabled", currentPage === lastPage);

                        function capitalizeWords(str) {
                            if (!str) return 'N/A';
                            return str.replace(/\b\w/g, function(char) {
                                return char.toUpperCase();
                            });
                        }

                        $.each(customers, function(index, customer) {
                            // Precompute capitalized values
                            customer._country = capitalizeWords(customer.country);
                            customer._city    = capitalizeWords(customer.city);

                            // Store in map for later use by the expandable row builder
                            window.customerDataMap[customer.id] = customer;

                            let actionButtons = "";
                            @if(app('hasPermission')(9, 'view'))
                                actionButtons += `<a class="me-3" href="/customer-view/${customer.id}">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/eye.svg' }}" alt="img">
                                </a>`;
                            @endif
                            @if(app('hasPermission')(9, 'edit'))
                                actionButtons += `<a class="me-3" href="/edit-customer/${customer.id}">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                </a>`;
                            @endif
                            @if(app('hasPermission')(9, 'delete'))
                                actionButtons += `<a class="me-3 confirm-text delete-btn" href="javascript:void(0);" data-id="${customer.id}">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                </a>`;
                            @endif

                            let detailsToggle = `
                                <button class="mobile-toggle-btn-table" onclick="toggleCustomerRowDetails('${customer.id}')" data-customer-id="${customer.id}">
                                    <span class="toggle-icon">+</span>
                                </button>
                            `;

                            let profileImage = customer.profile_image
                                ? '{{ env('ImagePath').'/storage/' }}' + customer.profile_image
                                : '{{ env('ImagePath').'admin/assets/img/customer/customer5.jpg' }}';

                            tableBody += `
                                <tr>
                                    <td class="productimgname">
                                        <a href="/customer-view/${customer.id}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                                            <img src="${profileImage}" alt="Customer" style="width: 50px; height: auto;">
                                            <span class="customer-name">${capitalizeWords(customer.name)}</span>
                                        </a>
                                    </td>
                                    <td class="details-cell" style="text-align:center; vertical-align:middle;">${detailsToggle}</td>
                                    <td>${customer.phone || 'N/A'}</td>
                                    <td>${customer.email || 'N/A'}</td>
                                    <td>${customer._country}</td>
                                    <td>${customer._city}</td>
                                    <td>${customer.gst_num || 'N/A'}</td>
                                    <td>${actionButtons}</td>
                                </tr>
                            `;
                        });

                        // Destroy existing DataTable
                        if ($.fn.DataTable.isDataTable(".datanew")) {
                            $(".datanew").DataTable().clear().destroy();
                        }

                        $(".datanew tbody").html(tableBody);

                        // Reinitialize DataTable without built-in pagination/searching
                        $(".datanew").DataTable({
                            destroy: true,
                            paging: false,
                            searching: false,
                            info: false,
                            ordering: false
                        });

                    } else {
                        $(".datanew tbody").html(
                            '<tr><td colspan="8" class="text-center">No customers found.</td></tr>');
                    }
                },
                error: function() {
                    $(".datanew tbody").html(
                        '<tr><td colspan="8" class="text-center text-danger">Error loading data.</td></tr>'
                    );
                },
            });
        }

        // Initial Load
        fetchCustomers();

        // Pagination buttons
        $(document).on("click", "#prevBtn", function() {
            if (currentPage > 1) fetchCustomers(currentPage - 1);
        });

        $(document).on("click", "#nextBtn", function() {
            if (currentPage < lastPage) fetchCustomers(currentPage + 1);
        });

        // Delete handler
        $(document).on("click", ".delete-btn", function() {
            let customerId = $(this).data("id");

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
                        url: `/api/deleteCustomer/${customerId}`,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                            fetchCustomers(currentPage);
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
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                        },
                    });
                }
            });
        });

    });
</script>
@endpush
