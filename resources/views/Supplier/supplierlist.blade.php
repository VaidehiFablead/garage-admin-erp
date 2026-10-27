@extends('layout.app')

@section('title', 'Vendor List')

@section('content')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

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

        .table-responsive {
            display: block !important;
            overflow-x: auto;
            width: 100% !important;
            -webkit-overflow-scrolling: touch;
        }

        .vendor-name {
            font-weight: 500;
            color: #212B36;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        /* ── Desktop: show all columns, hide Details toggle column ── */
        @media (min-width: 769px) {
            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }

            .vendor-details-row {
                display: none !important;
            }
        }

        /* ── Mobile: hide non-essential columns, show Details toggle ── */
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

        /* ── Expandable details row ── */
        .vendor-details-row {
            display: none;
        }

        .vendor-details-row.show {
            display: table-row;
        }

        .vendor-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.vendor-details-row td.vendor-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .vendor-details-list {
            margin-bottom: 15px;
        }

        .vendor-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .vendor-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .vendor-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .vendor-detail-value-simple {
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

        .btn-icon-mobile,
        button.btn-icon-mobile {
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

        /* ── Toggle button ── */
        .mobile-toggle-btn-vendor {
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

        .mobile-toggle-btn-vendor:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-vendor.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-vendor.minus:hover {
            background: #c82333;
        }

        /* ── Pagination ── */
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
                <h4>All Vendors</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(5 , 'add'))
                    <a href="{{ route('vendor.add') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">Add
                        Vendor
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
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew" style="width:100% !important;">
                        <thead>
                            <tr>
                                <th>Vendor Name</th>
                                <th class="details-column">Details</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>GST Number</th>
                                <th>Country</th>
                                <th>City</th>
                                <th style="width:200px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3" style="display: none;">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm" style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
                    </nav>
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
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable({
                destroy: true,
                paging: false,
                info: false,
                searching: false,
                dom: 't',
                ordering: false,
            });

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            let currentPage = 1;
            let lastPage    = 1;
            let perPage     = 10;
            let searchQuery = '';

            // ── Blade-rendered permission flags (evaluated server-side) ──
            const canView   = {{ app('hasPermission')(10, 'view')   ? 'true' : 'false' }};
            const canEdit   = {{ app('hasPermission')(10, 'edit')   ? 'true' : 'false' }};
            const canDelete = {{ app('hasPermission')(10, 'delete') ? 'true' : 'false' }};

            const imagePath        = "{{ env('ImagePath') . '/storage/' }}";
            const defaultAvatar    = "{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}";
            const eyeIcon          = "{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}";
            const editIcon         = "{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}";
            const deleteIcon       = "{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}";

            // ── Build the expandable details row content ──
            function buildVendorExpandableRow(vendor) {
                let actionBtns = '';

                if (canView) {
                    actionBtns += `<a class="btn-icon-mobile" href="/vendor-view/${vendor.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </a>`;
                }
                if (canEdit) {
                    actionBtns += `<a class="btn-icon-mobile" href="/edit-vendor/${vendor.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>`;
                }
                if (canDelete && userRole !== 'sales-manager' && userRole !== 'purchase-manager' && userRole !== 'inventory-manager') {
                    actionBtns += `<a class="btn-icon-mobile delete-vendor" data-id="${vendor.id}" href="javascript:void(0);" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>`;
                }

                return `
                    <td colspan="8" class="vendor-details-content">
                        <div class="vendor-details-list">
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">Phone:</span>
                                <span class="vendor-detail-value-simple">${vendor.phone || 'N/A'}</span>
                            </div>
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">Email:</span>
                                <span class="vendor-detail-value-simple">${vendor.email || 'N/A'}</span>
                            </div>
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">GST:</span>
                                <span class="vendor-detail-value-simple">${vendor.gst_number || 'N/A'}</span>
                            </div>
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">Country:</span>
                                <span class="vendor-detail-value-simple">${vendor.country || 'N/A'}</span>
                            </div>
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">City:</span>
                                <span class="vendor-detail-value-simple">${vendor.city || 'N/A'}</span>
                            </div>
                            <div class="vendor-detail-row-simple">
                                <span class="vendor-detail-label-simple">PAN:</span>
                                <span class="vendor-detail-value-simple">${vendor.pan_number || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            // ── Toggle expandable row ──
            window.toggleVendorRowDetails = function (vendorId) {
                const btn = $(`.mobile-toggle-btn-vendor[data-vendor-id="${vendorId}"]`);
                if (!btn.length) return;

                const row        = btn.closest('tr');
                let   detailsRow = row.next(`tr.vendor-details-row[data-vendor-id="${vendorId}"]`);
                const icon       = btn.find('.toggle-icon');

                if (!detailsRow.length) {
                    const vendorData = window.vendorDataMap && window.vendorDataMap[vendorId];
                    if (!vendorData) return;

                    detailsRow = $('<tr>')
                        .addClass('vendor-details-row')
                        .attr('data-vendor-id', vendorId)
                        .html(buildVendorExpandableRow(vendorData));
                    row.after(detailsRow);
                }

                if (detailsRow.hasClass('show')) {
                    detailsRow.removeClass('show');
                    btn.removeClass('minus');
                    icon.text('+');
                } else {
                    detailsRow.addClass('show');
                    btn.addClass('minus');
                    icon.text('−');
                }
            };

            // ── Fetch vendors ──
            function fetchVendors(page = 1) {
                let url = `/api/getAllSupplier?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) url += `&selectedSubAdminId=${selectedSubAdminId}`;
                if (searchQuery)        url += `&search=${encodeURIComponent(searchQuery)}`;

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function (response) {
                        if (response.status) {
                            const vendors    = response.data;
                            const pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage    = pagination.last_page;

                            updatePaginationUI(pagination);

                            window.vendorDataMap = {};
                            let tableBody = [];

                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, c => c.toUpperCase());
                            }

                            vendors.forEach(vendor => {
                                const vendorName = capitalizeWords(vendor.name);
                                const imageUrl   = vendor.profile_image
                                    ? imagePath + vendor.profile_image
                                    : defaultAvatar;

                                // Store vendor data for expandable row
                                window.vendorDataMap[vendor.id] = vendor;

                                // Desktop action buttons
                                let deleteBtn = '';
                                if (canDelete && userRole !== 'sales-manager' && userRole !== 'purchase-manager' && userRole !== 'inventory-manager') {
                                    deleteBtn = `<a class="me-2 delete-vendor" data-id="${vendor.id}" href="javascript:void(0);">
                                        <img src="${deleteIcon}" alt="Delete">
                                    </a>`;
                                }

                                let actionButtons = '';
                                if (canView)  actionButtons += `<a class="me-2" href="/vendor-view/${vendor.id}"><img src="${eyeIcon}" alt="View"></a>`;
                                if (canEdit)  actionButtons += `<a class="me-2" href="/edit-vendor/${vendor.id}"><img src="${editIcon}" alt="Edit"></a>`;
                                actionButtons += deleteBtn;

                                // Mobile toggle button
                                const detailsToggle = `
                                    <button class="mobile-toggle-btn-vendor"
                                            onclick="toggleVendorRowDetails('${vendor.id}')"
                                            data-vendor-id="${vendor.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>`;

                                // First column: image + name link
                                const firstColumn = `
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <a href="/vendor-view/${vendor.id}">
                                            <img src="${imageUrl}" alt="vendor" style="width:50px; height:auto; border-radius:4px;">
                                        </a>
                                        <a href="/vendor-view/${vendor.id}" class="vendor-name" style="text-decoration:none;">
                                            ${vendorName}
                                        </a>
                                    </div>`;

                                tableBody.push([
                                    firstColumn,
                                    detailsToggle,
                                    vendor.email       || 'N/A',
                                    vendor.phone       || 'N/A',
                                    vendor.gst_number  || 'N/A',
                                    vendor.country     || 'N/A',
                                    vendor.city        || 'N/A',
                                    actionButtons
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();
                            $('.pagination-controls').show();

                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="8">No vendors found</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function (xhr) {
                        console.error("Error fetching vendors:", xhr);
                        table.clear().draw();
                        $(".datanew tbody").html('<tr><td colspan="8">Error loading vendor data</td></tr>');
                        $('.pagination-controls').hide();
                    }
                });
            }

            // ── Pagination UI ──
            function updatePaginationUI(pagination) {
                const from = pagination.from || 0;
                const to   = pagination.to   || 0;
                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage   = Math.min(pagination.last_page, startPage + 4);
                if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

                let html = '';
                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>`;
                }
                $('#pagination-numbers').html(html);
            }

            // ── Event handlers ──
            $('#search-input').on('keyup', function () {
                searchQuery = $(this).val();
                fetchVendors(1);
            });

            $('#per-page-select').on('change', function () {
                perPage = $(this).val();
                fetchVendors(1);
            });

            $(document).on('click', '#pagination-numbers .page-link', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchVendors(page);
                }
            });

            // ── Delete vendor ──
            $(document).on('click', '.delete-vendor', function () {
                const vendorId = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/deleteSupplier/${vendorId}`,
                            type: 'POST',
                            headers: { "Authorization": "Bearer " + authToken },
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    }).then(() => fetchVendors(currentPage));
                                } else {
                                    Swal.fire({ title: "Error!", text: response.message, icon: "error", confirmButtonColor: "#ff9f43", confirmButtonText: "OK" });
                                }
                            },
                            error: function (xhr) {
                                const message = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : "Something went wrong!";
                                Swal.fire({ title: "Error!", text: message, icon: "error", confirmButtonColor: "#ff9f43", confirmButtonText: "OK" });
                            }
                        });
                    }
                });
            });

            // ── Close expanded rows on resize to desktop ──
            $(window).on('resize', function () {
                if ($(window).width() > 768) {
                    $('.vendor-details-row').removeClass('show');
                    $('.mobile-toggle-btn-vendor').removeClass('minus').find('.toggle-icon').text('+');
                }
            });

            // Initial load
            fetchVendors(currentPage);
        });
    </script>
@endpush
