@extends('layout.app')

@section('title', 'Expense Type List')

@section('content')
    <style>
        table.datanew {
            width: 100% !important;
        }

        /* Correct wrapping behaviour for name */
        .expense-type-name {
            display: inline-block;
            max-width: 100%;
            font-size: 14px;
            line-height: 1.4;
            white-space: normal !important;
            word-break: keep-all !important;
            overflow-wrap: normal !important;
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
            .table-responsive {
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
            table.datanew tbody td:nth-child(3) {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {

            .collapse .card-body .mt-2 {
                display: flex;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
            }

            .collapse .card-body .mt-2 a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 44px;
                min-height: 44px;
                padding: 8px;
                background-color: #f8f9fa;
                border-radius: 6px;
                transition: background-color 0.2s;
            }

            .collapse .card-body .mt-2 a:hover {
                background-color: #e9ecef;
            }

            .collapse .card-body .mt-2 img {
                width: 25px;
                height: 25px;
            }

            /* Adjust toggle icon size and alignment */
            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
            }

            .toggle-details i {
                font-size: 24px;
            }

            /* Hide Action column on mobile (nth-child 4) */
            table.datanew thead th:nth-child(4),
            table.datanew tbody td:nth-child(4) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(3) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            /* Style for name wrapping on mobile */
            table.datanew tbody td:nth-child(2) {
                display: flex !important;
                align-items: center !important;
                max-width: calc(100vw - 150px) !important;
            }

            .expense-type-name {
                word-break: break-word !important;
                hyphens: auto !important;
            }
        }

        /* Pagination Styling */
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

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }

            .dataTables_info {
                float: left !important;
            }

            @media screen and (max-width: 768px) {
                input.form-control.form-control-sm {
                    margin-top: 10px;
                }
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
                <h4>All Expenses Type</h4>
                <!--<h6>Manage your purchases</h6>-->
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(5, 'add'))
                    <a href="javascript:void(0);" class="btn btn-sm btn-added" id="openAddModal">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-2" alt="img">
                        New Expense Type
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

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Expense Type</th>
                                <th class="details-column">Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="expense-table-body">
                            <!-- Dynamic rows here -->
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

    <!-- Expense Type Modal -->
    <div class="modal fade" id="expenseTypeModal" tabindex="-1" aria-labelledby="expenseTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="expenseTypeForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="expenseTypeModalLabel">Add Expense Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="expenseTypeId" name="id">
                        <div class="form-group">
                            <label>Expense Type</label>
                            <input type="text" name="type" id="type" class="form-control"
                                placeholder="Expense Name">
                            <span class="text-danger error" id="type_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit">Save</button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
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

            fetchExpenses(currentPage);

            // Handle search input
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchExpenses(1);
                }, 500);
            });

            function fetchExpenses(page = 1) {
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                $.ajax({
                    url: '/api/expense-types',
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    success: function(response) {
                        if (response.status) {
                            let expenses = response.data;
                            let pagination = response.pagination;

                            // Update pagination state
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;

                            // Update pagination UI
                            updatePaginationUI(pagination);

                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }

                            let tableRows = [];
                            expenses.forEach(function(item, index) {
                                let rowNumber = (currentPage - 1) * perPage + (index + 1);
                                let expenseName = capitalizeWords(item.type);
                                
                                let detailsToggle = `
                                    <a href="#details-${item.id}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                    </a>
                                `;

                                let actionButtons = `
                                    @if (app('hasPermission')(5, 'edit'))
                                    <a class="me-3 edit-btn" href="javascript:void(0);" data-id="${item.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                    </a>
                                    @endif
                                    @if (app('hasPermission')(5, 'delete'))
                                    <a class="me-3 delete-btn" href="javascript:void(0);" data-id="${item.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                    </a>
                                    @endif
                                `;

                                tableRows.push([
                                    // Column 1: Row Number
                                    rowNumber,
                                    // Column 2: Name and Collapsible Details
                                    `<div>
                                        <div style="display: flex; align-items: center;">
                                            <span class="expense-type-name">${expenseName}</span>
                                        </div>
                                        <div class="collapse mt-2 d-lg-none" id="details-${item.id}">
                                            <div class="card card-body p-2 bg-light border">
                                                <div class="mt-2">
                                                    ${actionButtons}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`,
                                    // Column 3: Details Toggle
                                    detailsToggle,
                                    // Column 4: Actions
                                    actionButtons
                                ]);
                            });
                            table.clear().rows.add(tableRows).draw();
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="3" class="text-center">No expenses found.</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching expenses:", xhr);
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

                // Page Numbers
                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    if (i < 1) continue;
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
                    fetchExpenses(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchExpenses(1);
            });

            // Toggle details icon
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle')
                        .addClass('fa-minus-circle')
                        .css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                }
            });

            // Open modal for ADD
            $("#openAddModal").click(function() {
                $("#expenseTypeModalLabel").text("Add Expense Type");
                $("#expenseTypeForm")[0].reset();
                $("#type_error").text("");
                $("#expenseTypeId").val("");
                $("#expenseTypeModal").modal("show");
            });

            // Open modal for EDIT
            $(document).on("click", ".edit-btn", function() {
                let id = $(this).data("id");
                $("#expenseTypeModalLabel").text("Edit Expense Type");
                $("#type_error").text("");

                $.ajax({
                    url: `/api/expense-types/${id}`,
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#expenseTypeId").val(response.data.id);
                        $("#type").val(response.data.type);
                        $("#expenseTypeModal").modal("show");
                    }
                });
            });

            $("#expenseTypeForm").submit(function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                $("#type_error").text("");

                let id = $("#expenseTypeId").val();
                let method = id ? "POST" : "POST"; // Always POST
                let endpoint = id ? `/api/expense-types/${id}` : `{{ route('expense-type.store') }}`;

                let formData = new FormData();
                formData.append("type", $("#type").val());
                if (selectedSubAdminId) {
                    formData.append("selectedSubAdminId", selectedSubAdminId);
                }
                if (id) {
                    formData.append("_method", "PUT"); // Laravel will treat it as PUT
                }

                $.ajax({
                    url: endpoint,
                    method: method,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#expenseTypeModal").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            confirmButtonColor: '#ff9f43'
                        });
                        fetchExpenses(id ? currentPage : 1);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors?.type) {
                            $("#type_error").text(xhr.responseJSON.errors.type[0]);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    }
                });
            });


            // Delete record
            $(document).on("click", ".delete-btn", function() {
                let id = $(this).data("id");
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
                            url: `/api/expenses-type/${id}`,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"),
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function() {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Delete Record successfully",
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43"
                                });
                                fetchExpenses(currentPage);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
