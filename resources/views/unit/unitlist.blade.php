@extends('layout.app')

@section('title', 'Unit List')

@section('content')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
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

        .table-scroll-top {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
            }

            .table-responsive {
                overflow-x: auto;
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

            /* Hide expandable detail rows on desktop */
            .unit-details-row {
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
        .unit-details-row {
            display: none;
        }

        .unit-details-row.show {
            display: table-row;
        }

        .unit-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.unit-details-row td.unit-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .unit-details-list {
            margin-bottom: 15px;
        }

        .unit-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .unit-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .unit-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .unit-detail-value-simple {
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

        /* Hide default DataTables search box completely */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
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
                <h4>All Units</h4>
            </div>
            <div class="page-btn">
                <a href="javascript:void(0);" class="btn btn-sm btn-added" data-bs-toggle="modal"
                    data-bs-target="#addUnitModal">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">
                    New Unit
                </a>
            </div>
        </div>

        <!-- Add Unit Modal -->
        <div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Unit</h5>
                        <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal">x</button>
                    </div>
                    <form id="addUnitForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" name="unitname" class="form-control" placeholder="Enter Unit Name">
                                <span class="error_unitname text-danger"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm submitUnit" id="saveUnitBtn">
                                Save Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Unit Modal -->
        <div class="modal fade" id="editUnitModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Unit</h5>
                        <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal">x</button>
                    </div>
                    <form id="editUnitForm">
                        @csrf
                        <input type="hidden" id="edit_unit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" id="edit_unit_name" class="form-control">
                                <span class="error_edit_unitname text-danger"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm" id="updateUnitBtn">
                                Update Unit
                            </button>
                        </div>
                    </form>
                </div>
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

                <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th class="details-column">Details</th>
                                <th>Created At</th>
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

            // Clear Add Unit modal errors when closed
            $('#addUnitModal').on('hidden.bs.modal', function() {
                $('#addUnitForm')[0].reset();
                $('.error_unitname').text('');
                $('#saveUnitBtn').prop('disabled', false).html('Save Unit');
            });

            var authToken = localStorage.getItem("authToken");

            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            });

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Store unit data for expandable rows
            window.unitDataMap = {};

            // Initial fetch
            fetchUnits(currentPage);

            // Handle search input (debounced)
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchUnits(1);
                }, 500);
            });

            // Build expandable row content (product-list style)
            function buildUnitExpandableRowContent(unit) {
                let actionBtns = `
                    <a class="btn-icon-mobile edit-unit"
                       href="javascript:void(0);"
                       data-id="${unit.id}"
                       data-name="${unit.unit_name}"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a class="btn-icon-mobile delete-unit"
                       href="javascript:void(0);"
                       data-id="${unit.id}"
                       title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                `;

                return `
                    <td colspan="4" class="unit-details-content">
                        <div class="unit-details-list">
                            <div class="unit-detail-row-simple">
                                <span class="unit-detail-label-simple">Created At:</span>
                                <span class="unit-detail-value-simple">${unit.createdAt}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            // Toggle expandable row
            window.toggleUnitRowDetails = function(unitId) {
                const btn = $(`.mobile-toggle-btn-table[data-unit-id="${unitId}"]`);
                if (btn.length === 0) return;

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.unit-details-row[data-unit-id="${unitId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const unitData = window.unitDataMap && window.unitDataMap[unitId];
                    if (!unitData) return;

                    detailsRow = $('<tr>')
                        .addClass('unit-details-row')
                        .attr('data-unit-id', unitId)
                        .html(buildUnitExpandableRowContent(unitData));
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

            function getActionButtons(unit) {
                return `
                    <a href="javascript:void(0);"
                       data-id="${unit.id}"
                       data-name="${unit.unit_name}"
                       class="me-3 edit-unit">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                    </a>
                    <a href="javascript:void(0);"
                       data-id="${unit.id}"
                       class="delete-unit">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                    </a>
                `;
            }

            // ================= FETCH UNITS =================
            function fetchUnits(page = 1) {
                table.clear().draw();
                window.unitDataMap = {};

                var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                $.ajax({
                    url: "/api/units",
                    type: "GET",
                    dataType: "json",
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Accept": "application/json"
                    },
                    success: function(response) {
                        if (response.status === true && Array.isArray(response.data)) {
                            let pagination = response.pagination;
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            let tableBody = [];

                            response.data.forEach(function(unit) {
                                let createdAt = unit.created_at ?
                                    unit.created_at.split("T")[0] : "N/A";

                                let unitName = unit.unit_name ?? 'N/A';

                                // Store data for expandable row
                                window.unitDataMap[unit.id] = {
                                    id: unit.id,
                                    unit_name: unit.unit_name,
                                    createdAt: createdAt
                                };

                                // Mobile toggle button (product-list style)
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table"
                                            onclick="toggleUnitRowDetails('${unit.id}')"
                                            data-unit-id="${unit.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                tableBody.push([
                                    // Column 1: Unit Name
                                    `<div style="display: flex; align-items: center;">
                                        <span style="color: #1b2850; font-weight: 500;">${unitName}</span>
                                    </div>`,

                                    // Column 2: Toggle button (mobile only)
                                    detailsToggle,

                                    // Column 3: Created At (desktop)
                                    createdAt,

                                    // Column 4: Action Buttons (desktop)
                                    getActionButtons(unit)
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();

                        } else {
                            table.clear().draw();
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to fetch units.",
                            icon: "error",
                            confirmButtonColor: "#ff9f43"
                        });
                    }
                });
            }

            // Update pagination controls and info
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

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchUnits(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchUnits(1);
            });

            // ================= ADD UNIT =================
            $('#addUnitForm').submit(function(e) {
                e.preventDefault();

                var $btn = $('#saveUnitBtn');
                var originalText = $btn.html();

                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...')
                    .prop('disabled', true);

                $('.error_unitname').text('');

                var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let formData = $(this).serialize();

                if (selectedSubAdminId) {
                    formData += '&selectedSubAdminId=' + selectedSubAdminId;
                }

                $.ajax({
                    url: "/api/add-units",
                    type: "POST",
                    data: formData,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Accept": "application/json"
                    },
                    success: function(response) {
                        $btn.html(originalText).prop('disabled', false);

                        if (response.status === true) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#ff9f43"
                            });

                            fetchUnits();
                            $('#addUnitModal').modal('hide');
                            $('#addUnitForm')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop('disabled', false);
                        $('.error_unitname').text('');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.unitname) {
                                $('.error_unitname').text(errors.unitname[0]);
                            }
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                confirmButtonColor: "#ff9f43"
                            });
                        }
                    }
                });
            });

            // ================= OPEN UPDATE MODAL =================
            $(document).on('click', '.edit-unit', function() {
                let unitId = $(this).data('id');
                let unitName = $(this).data('name');

                $('#edit_unit_id').val(unitId);
                $('#edit_unit_name').val(unitName);
                $('.error_edit_unitname').text('');
                $('#editUnitModal').modal('show');
            });

            // ================= UPDATE UNIT =================
            $('#editUnitForm').submit(function(e) {
                e.preventDefault();

                var unitId = $('#edit_unit_id').val();
                var unitName = $('#edit_unit_name').val();
                var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                var $btn = $('#updateUnitBtn');
                var originalText = $btn.html();

                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...')
                    .prop('disabled', true);

                $('.error_edit_unitname').text('');

                $.ajax({
                    url: "/api/update-unit/" + unitId,
                    type: "POST",
                    data: {
                        unitname: unitName,
                        selectedSubAdminId: selectedSubAdminId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Accept": "application/json"
                    },
                    success: function(response) {
                        $btn.html(originalText).prop('disabled', false);
                        $('.error_edit_unitname').text('');

                        if (response.status === true) {
                            Swal.fire({
                                title: "Updated!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#ff9f43"
                            });

                            $('#editUnitModal').modal('hide');
                            fetchUnits();
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop('disabled', false);
                        $('.error_edit_unitname').text('');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.unitname) {
                                $('.error_edit_unitname').text(errors.unitname[0]);
                            }
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                confirmButtonColor: "#ff9f43"
                            });
                        }
                    }
                });
            });

            // ================= DELETE UNIT =================
            $(document).on('click', '.delete-unit', function() {
                let unitId = $(this).data('id');
                var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

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
                            url: "/api/delete-unit/" + unitId,
                            type: "DELETE",
                            data: {
                                selectedSubAdminId: selectedSubAdminId,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken,
                                "Accept": "application/json"
                            },
                            success: function(response) {
                                if (response.status === true) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                    fetchUnits();
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message || "Failed to delete unit.",
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43"
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
                                    confirmButtonColor: "#ff9f43"
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
