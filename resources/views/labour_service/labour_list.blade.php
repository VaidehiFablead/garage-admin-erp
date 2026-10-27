@extends('layout.app')
@section('title', 'Labour Service List')
@section('content')
    <style>
        table.datanew {
            width: 100% !important;
        }
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        /* Category name proper word wrapping */
        table.datanew tbody td:first-child a {
            display: flex;
            align-items: center;
            max-width: 100%;
        }

        /* Correct wrapping behaviour */
        .service-name {
            display: inline-block;
            /* max-width: calc(100% - 50px); */
            margin-left: 8px;
            font-size: 14px;
            line-height: 1.4;

            white-space: normal !important;
            /* allow wrapping */
            word-break: keep-all !important;
            /* DO NOT break words */
            overflow-wrap: normal !important;
            /* wrap only at spaces */
        }

        /* ✅ Hide default DataTables search box completely */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Remove extra top spacing created by DataTables */
        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        /* Remove unwanted search input alignment space */
        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
            }

            .table-responsive {
                /* overflow-x: auto; */
                -webkit-overflow-scrolling: touch !important;
            }

            .search-set {
                margin-right: 1rem !important;
            }
        }

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
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

            /* Style for service name wrapping */
            table.datanew tbody td:first-child {
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
            }

            table.datanew tbody td:first-child a {
                /* display: flex !important; */
                align-items: center !important;
                text-align: left !important;
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.3 !important;
            }

            .service-name {
                display: inline-block !important;
                /* max-width: calc(100% - 50px) !important; */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* Limit to 2 lines with ellipsis */
            .service-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        .service-details-row {
            display: none;
        }

        .service-details-row.show {
            display: table-row;
        }

        .service-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.service-details-row td.service-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .service-details-list {
            margin-bottom: 15px;
        }

        .service-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .service-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .service-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .service-detail-value-simple {
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

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        @media (min-width: 769px) {
            .service-details-row {
                display: none !important;
            }
        }

        /* Tablet specific fixes */
        /* ===========================
       TABLET VIEW FIX (769px–1024px)
       =========================== */
        @media (min-width: 769px) and (max-width: 1024px) {

            /* Keep table structure intact */
            table.datanew tbody td,
            table.datanew thead th {
                display: table-cell !important;
                vertical-align: middle;
            }

            /* Hide mobile toggle column */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }

            /* service column alignment */
            table.datanew tbody td:first-child {
                display: flex !important;
                display: table-cell !important;
                white-space: normal !important;
            }

            table.datanew tbody td:first-child a {
                display: flex !important;
                align-items: center;
                gap: 10px;
            }

            /* Proper wrapping without breaking layout */
            .service-name {
                max-width: 300px;
                white-space: normal !important;
                word-break: break-word !important;
                overflow-wrap: anywhere;
            }

            /* Improve spacing */
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Pagination alignment */
            .pagination-controls {
                flex-wrap: wrap;
                gap: 10px;
            }
        }

        /* Fade out animation for error messages */
        .fade-out {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-out.hidden {
            opacity: 0;
        }

        @media (max-width: 767.98px) {
            .table-top .search-set {
                display: flex;
                justify-content: flex-start !important;
                width: 100%;
            }

            .table-top .search-input {
                margin-left: 0 !important;
            }
        }

        /* Custom Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            /* Dark gray for other pages */
            color: #fff;
            border: none;
            margin: 0 4px;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            /* Orange for active page */
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        /* Search input styling */
        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }
    </style>
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden');
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }
            }, 4000);
        </script>
    @endif
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Labour Services</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(20, 'add'))
                    <a href="#" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1"
                            alt="img">New Labour Service
                    </a>
                @endif

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th class="details-column">Details</th>
                                <th>Price</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                            <!-- Page numbers will be populated by JS -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add Labour Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="serviceForm">
                        <div class="form-group">
                            <label for="name">Service Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <span class="error_name text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" id="price" class="form-control">
                            <span class="error_price text-danger"></span>
                        </div>
                        {{-- <button type="submit" class="btn btn-submit submit">Submit</button> --}}
                        <div class="">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-submit submit" id="savePayment">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Labour Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm">
                        <input type="hidden" name="service_id" id="edit_service_id">
                        <div class="form-group">
                            <label for="edit_name">Service Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                            <span class="error_name text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="edit_price">Price</label>
                            <input type="number" name="price" id="edit_price" class="form-control">
                            <span class="error_price text-danger"></span>
                        </div>
                        <div class="">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-submit edit-submit" id="savePayment">Save</button>
                        </div>
                        {{-- <button type="submit" class="btn btn-submit edit-submit">Submit</button> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            }); // Initialize DataTable
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            // Initial params for API
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            window.labourServiceDataMap = {};

            function buildLabourServiceExpandableRowContent(service) {
                return `
                    <td colspan="5" class="service-details-content">
                        <div class="service-details-list">
                            <div class="service-detail-row-simple">
                                <span class="service-detail-label-simple">Price:</span>
                                <span class="service-detail-value-simple">${service.price}</span>
                            </div>
                            <div class="service-detail-row-simple">
                                <span class="service-detail-label-simple">Created At:</span>
                                <span class="service-detail-value-simple">${service.formattedDate}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${getActionButtons(service)}
                        </div>
                    </td>
                `;
            }

            window.toggleLabourServiceRowDetails = function(serviceId) {
                const btn = $(`.mobile-toggle-btn-table[data-service-id="${serviceId}"]`);
                if (btn.length === 0) {
                    return;
                }

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.service-details-row[data-service-id="${serviceId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const serviceData = window.labourServiceDataMap && window.labourServiceDataMap[serviceId];
                    if (!serviceData) {
                        return;
                    }

                    detailsRow = $('<tr>')
                        .addClass('service-details-row')
                        .attr('data-service-id', serviceId)
                        .html(buildLabourServiceExpandableRowContent(serviceData));
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

            fetchLabourServices(1);

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchLabourServices(1);
            });

            function fetchLabourServices(page = 1) {
                let url = `/api/getAllServices?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            currentPage = response.pagination.current_page;
                            lastPage = response.pagination.last_page;

                            updatePaginationUI(response.pagination);

                            let labourServices = response.data;
                            let currencySymbol = response.currency_symbol || "₹";
                            let currencyPosition = response.currency_position || "left";
                            let tableBody = [];
                            window.labourServiceDataMap = {};

                            labourServices.forEach((service) => {
                                // Handle Created At
                                let createdAt = service.created_at ? new Date(service
                                    .created_at) : null;
                                let formattedDate = createdAt ?
                                    createdAt.toLocaleDateString('en-GB', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true
                                    }).replace(/(\d+)\s(\w+)\s(\d+)/, '$1-$2-$3') :
                                    "N/A";

                                // Handle Price
                                let price = service.price !== null && service.price !== "" && !
                                    isNaN(service.price) ?
                                    (currencyPosition === 'left' ?
                                        currencySymbol + service.price :
                                        service.price + currencySymbol) :
                                    "N/A";

                                // Handle Service Name
                                let serviceName = service.name && service.name.trim() !== "" ?
                                    service.name.charAt(0).toUpperCase() + service.name.slice(
                                    1) :
                                    "N/A";

                                // Details toggle for mobile
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table" onclick="toggleLabourServiceRowDetails('${service.id}')" data-service-id="${service.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                window.labourServiceDataMap[service.id] = {
                                    id: service.id,
                                    price: price,
                                    formattedDate: formattedDate
                                };

                                tableBody.push([
                                    // Column 1: Service Name
                                    `<div>
                                        <div style="display: flex; align-items: center;">
                                            <a href="javascript:void(0);" class="d-flex align-items-center">
                                                <span class="service-name truncated">${serviceName}</span>
                                            </a>
                                        </div>
                                    </div>`,

                                    // Column 2: Details Toggle (only for mobile)
                                    detailsToggle,

                                    price,
                                    formattedDate,

                                    // Column 3: Action Buttons (hidden on mobile)
                                    getActionButtons(service)
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html(
                                '<tr><td colspan="5">No labour services found</td></tr>'
                            );
                            // Hide pagination if no data
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.log("Error:", xhr);
                    }
                });
            }

            // Update pagination controls and info
            function updatePaginationUI(pagination) {
                let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let paginationHtml = '';

                // Page Numbers
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

                // Show pagination container if there's data
                $('.pagination-controls').show();
            }

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchLabourServices(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchLabourServices(1);
            });

            $(document).on('click', '.delete-service', function() {
                var serviceId = $(this).data('id');
                var authToken = localStorage.getItem("authToken");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/deleteservice/${serviceId}`,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
                                    $(`.delete-service[data-id="${serviceId}"]`)
                                        .closest('tr').remove();
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr) {
                                let message = "Something went wrong!";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: "Error!",
                                    text: message,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });

            // Handle Add Form Submission
            $('#serviceForm').on('submit', function(e) {
                e.preventDefault();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let formData = new FormData($('#serviceForm')[0]);
                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }
                $.ajax({
                    url: "/api/addservice",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + localStorage.getItem("authToken"),
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Labour Service added successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addServiceModal').modal('hide');
                                    location.reload(); // Reload full page
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.error_' + key).text(value[0]);
                            });
                        }
                    }
                });
            });

            // Handle Edit Form Submission
            $('#editServiceForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "/api/updateservice",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + localStorage.getItem("authToken"),
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Labour Service updated successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#editServiceModal').modal('hide');
                                    // fetchLabourServices(); // Refresh the table
                                    location.reload(); // Reload full page
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.error_' + key).text(value[0]);
                            });
                        }
                    }
                });
            });

            // Handle Edit Button Click
            $(document).on('click', '.edit-service', function(e) {
                e.preventDefault();
                let serviceId = $(this).data('id');
                $.ajax({
                    url: `/api/getService/${serviceId}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + localStorage.getItem("authToken"),
                    },
                    success: function(response) {
                        if (response.status) {
                            let service = response.data;
                            $('#edit_service_id').val(service.id);
                            $('#edit_name').val(service.name);
                            $('#edit_price').val(service.price);
                            $('#editServiceModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        console.log("Error:", xhr);
                    }
                });
            });
            // Clear errors on modal open
            $('#addServiceModal, #editServiceModal').on('show.bs.modal', function() {
                $(this).find('.text-danger').text('');
                $(this).find('input').removeClass('is-invalid');
            });

            // Helper function to generate action buttons
            function getActionButtons(service) {
                return `
                    @if (app('hasPermission')(20, 'edit'))
                    <a class="me-3 edit-service" data-id="${service.id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="img">
                    </a>
                    @endif

                    @if (app('hasPermission')(20, 'delete'))
                    <a class="me-3 confirm-text delete-service" data-id="${service.id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="img">
                    </a>
                    @endif
                `;
            }

        });
    </script>
@endpush
