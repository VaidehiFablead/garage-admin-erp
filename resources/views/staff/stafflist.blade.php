@extends('layout.app')

@section('title', 'Staff List')

@section('content')
    <style>
        /* Hide default DataTables controls */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Search input */
        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }

        /* Custom Pagination Styling */
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

        /* Staff image */
        .staff-image {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5%;
            margin-right: 8px;
        }

        /* Expandable details row (hidden by default, shown on mobile only) */
        .staff-details-row {
            display: none;
        }

        .staff-details-row.show {
            display: table-row;
        }

        .staff-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.staff-details-row td.staff-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        /* Detail rows inside expanded panel */
        .staff-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .staff-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .staff-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .staff-detail-value-simple {
            color: #1b2850;
            font-size: 13px;
            text-align: right;
            word-break: break-word;
        }

        /* Mobile action buttons inside expanded panel */
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

        /* Toggle button in Details column */
        .mobile-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
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

        /* Desktop: show all columns, hide Details column */
        @media (min-width: 769px) {
            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }

            .staff-details-row {
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

            .search-set {
                margin-right: 1rem !important;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }
        }

        /* Tablet specific */
        @media screen and (width: 768px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
            }
        }

        /* Fade out */
        .fade-out {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-out.hidden {
            opacity: 0;
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Staffs</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('staff.add') }}" class="btn btn-sm" id="new-staff-btn"
                    style="background:#ff9f43; color:#fff; font-weight:500; border-radius:4px; padding:8px 15px; white-space:nowrap;">
                    <i class="fa fa-plus me-1"></i> New Staff
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th class="details-column">Details</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>GST Number</th>
                                <th>PAN Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
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
                            <!-- page numbers will be inserted here -->
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const userLimit = {{ tenant_user_limit() !== null ? (int) tenant_user_limit() : 'null' }};
            var table = $('.datanew').DataTable({
                destroy: true,
                paging: false,
                info: false,
                searching: false,
                dom: 't',
                ordering: false
            });

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Initial fetch
            fetchStaff(currentPage);

            // Search input handler
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchStaff(1);
            });

            // Per-page change handler
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchStaff(1);
            });

            // Page number click handler
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchStaff(page);
                }
            });

            // Build the expandable details row content (same pattern as product list)
            function buildStaffExpandableRowContent(staff) {
                let actionBtns = `
                    <a class="btn-icon-mobile" href="/staff-view/${staff.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a class="btn-icon-mobile" href="/edit-staff/${staff.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a class="btn-icon-mobile delete-staff" data-id="${staff.id}" href="javascript:void(0);" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                `;

                return `
                    <td colspan="9" class="staff-details-content">
                        <div class="staff-details-list">
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">Email:</span>
                                <span class="staff-detail-value-simple">${staff.email || 'N/A'}</span>
                            </div>
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">Phone:</span>
                                <span class="staff-detail-value-simple">${staff.phone || 'N/A'}</span>
                            </div>
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">Country:</span>
                                <span class="staff-detail-value-simple">${staff.country || 'N/A'}</span>
                            </div>
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">City:</span>
                                <span class="staff-detail-value-simple">${staff.city || 'N/A'}</span>
                            </div>
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">GST Number:</span>
                                <span class="staff-detail-value-simple">${staff.gst_number || 'N/A'}</span>
                            </div>
                            <div class="staff-detail-row-simple">
                                <span class="staff-detail-label-simple">PAN Number:</span>
                                <span class="staff-detail-value-simple">${staff.pan_number || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            // Toggle expandable row (same pattern as product list)
            window.toggleStaffRowDetails = function(staffId) {
                const btn = $(`.mobile-toggle-btn-table[data-staff-id="${staffId}"]`);
                if (btn.length === 0) return;

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.staff-details-row[data-staff-id="${staffId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const staffData = window.staffDataMap && window.staffDataMap[staffId];
                    if (!staffData) return;

                    detailsRow = $('<tr>')
                        .addClass('staff-details-row')
                        .attr('data-staff-id', staffId)
                        .html(buildStaffExpandableRowContent(staffData));
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

            // Fetch staff with server-side pagination & search
            function fetchStaff(page = 1) {
                let url = `/api/getAllStaff?page=${page}&per_page=${perPage}`;
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
                            let staffs = response.data;
                            let pagination = response.pagination;

                            // Update state
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;

                            // Update pagination UI
                            updatePaginationUI(pagination);

                            // ✅ Enforce dynamic user/staff limit
                            let activeStaffCount = (typeof response.active_staff_count !== 'undefined') ? response.active_staff_count : pagination.total;
                            let effectiveLimit = (response.user_limit !== undefined && response.user_limit !== null) ? response.user_limit : userLimit;
                            if (effectiveLimit !== null && effectiveLimit > 0 && activeStaffCount >= effectiveLimit) {
                                $('#new-staff-btn')
                                    .attr('href', 'javascript:void(0);')
                                    .css({'opacity': '0.5', 'cursor': 'not-allowed', 'pointer-events': 'none'})
                                    .attr('title', `Maximum ${effectiveLimit} staff members limit reached`);
                            } else {
                                $('#new-staff-btn')
                                    .attr('href', "{{ route('staff.add') }}")
                                    .css({'opacity': '', 'cursor': '', 'pointer-events': ''})
                                    .removeAttr('title');
                            }

                            // Build table rows
                            let tableBody = [];
                            window.staffDataMap = {};

                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }

                            staffs.forEach((staff) => {
                                let staffName = capitalizeWords(staff.name);
                                let imageUrl = staff.profile_image
                                    ? '{{ env('ImagePath') . '/storage/' }}' + staff.profile_image
                                    : '{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}';

                                // Store staff data for expandable row
                                window.staffDataMap[staff.id] = {
                                    id: staff.id,
                                    email: staff.email,
                                    phone: staff.phone,
                                    country: staff.country,
                                    city: staff.city,
                                    gst_number: staff.gst_number,
                                    pan_number: staff.pan_number,
                                };

                                // Toggle button for Details column (mobile only)
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table"
                                        onclick="toggleStaffRowDetails('${staff.id}')"
                                        data-staff-id="${staff.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                // Action buttons for desktop Action column
                                let actionButtons = `
                                    <a class="me-2" href="/staff-view/${staff.id}">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path>
                                            <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path>
                                        </svg>
                                    </a>
                                    <a class="me-2" href="/edit-staff/${staff.id}">
                                        <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path>
                                        </svg>
                                    </a>
                                    <a class="me-2 confirm-text delete-staff" data-id="${staff.id}" href="javascript:void(0);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"></path>
                                            <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"></path>
                                        </svg>
                                    </a>
                                `;

                                // First column: image + name only (no inline collapse)
                                let firstColumn = `
                                    <div style="display: flex; align-items: center;">
                                        <img src="${imageUrl}" alt="staff" class="staff-image">
                                        <span style="color: #1b2850; font-weight: 500; word-break: break-word;">${staffName}</span>
                                    </div>
                                `;

                                tableBody.push([
                                    firstColumn,       // col 1: Staff Name
                                    detailsToggle,     // col 2: Details toggle (mobile only)
                                    staff.email || 'N/A',
                                    staff.phone || 'N/A',
                                    staff.country || 'N/A',
                                    staff.city || 'N/A',
                                    staff.gst_number || 'N/A',
                                    staff.pan_number || 'N/A',
                                    actionButtons,     // col 9: Action (desktop only)
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();

                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="9">No staff found</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching staff:", xhr);
                        table.clear().draw();
                        $(".datanew tbody").html('<tr><td colspan="9">Error loading staff data</td></tr>');
                        $('.pagination-controls').hide();
                    }
                });
            }

            // Update pagination numbers and info
            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page + 1;
                let to = pagination.current_page * pagination.per_page;
                if (to > pagination.total) to = pagination.total;
                if (pagination.total === 0) from = 0;

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

            // Delete staff
            $(document).on('click', '.delete-staff', function() {
                var staffId = $(this).data('id');
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
                            url: `/api/deleteStaff/${staffId}`,
                            type: 'POST',
                            headers: { "Authorization": "Bearer " + authToken },
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        fetchStaff(currentPage);
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
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
        });
    </script>
@endpush
