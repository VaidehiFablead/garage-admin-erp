@extends('layout.app')
@section('title', 'Service Category List')
@section('content')
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
    @endif
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

        /* Category name styling */
        .category-name {
            display: inline-block;
            margin-left: 8px;
            font-size: 14px;
            white-space: nowrap;
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

            /* .toggle-details i {
                    font-size: 18px;
                } */

            /* Style for category name wrapping */
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

            .category-name {
                display: inline-block !important;
                margin-left: 8px !important;
                font-size: 14px !important;
                white-space: nowrap !important;
            }

            /* Limit to 2 lines with ellipsis */
            .category-name.truncated {
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }
        }

        .category-details-row {
            display: none;
        }

        .category-details-row.show {
            display: table-row;
        }

        .category-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.category-details-row td.category-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .category-details-list {
            margin-bottom: 15px;
        }

        .category-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .category-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .category-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .category-detail-value-simple {
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
            .category-details-row {
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

            /* Category column alignment */
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

            /* Category name styling */
            .category-name {
                display: inline-block;
                margin-left: 8px;
                font-size: 14px;
                white-space: nowrap !important;
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
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Service Categories</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(21, 'add'))
                    <a href="#" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1"
                            alt="img">New Service Category
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
                                <th>Category Name</th>
                                <th class="details-column">Details</th>
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Service Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            <span class="error_name text-danger"></span>
                        </div>
                    </form>
                </div>
                <div style="padding: 15px;">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-submit submit-add">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Service Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" name="category_id" id="edit_category_id">
                        <div class="form-group">
                            <label for="edit_name">Category Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                            <span class="error_name text-danger"></span>
                        </div>
                    </form>
                </div>
                <div style="padding: 15px;">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-submit submit-edit">Submit</button>
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

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            window.autoCarCategoryDataMap = {};

            function buildAutoCarCategoryExpandableRowContent(category) {
                return `
                    <td colspan="4" class="category-details-content">
                        <div class="category-details-list">
                            <div class="category-detail-row-simple">
                                <span class="category-detail-label-simple">Created At:</span>
                                <span class="category-detail-value-simple">${category.formattedDate}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${getActionButtons(category)}
                        </div>
                    </td>
                `;
            }

            window.toggleAutoCarCategoryRowDetails = function(categoryId) {
                const btn = $(`.mobile-toggle-btn-table[data-category-id="${categoryId}"]`);
                if (btn.length === 0) {
                    return;
                }

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.category-details-row[data-category-id="${categoryId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const categoryData = window.autoCarCategoryDataMap && window.autoCarCategoryDataMap[categoryId];
                    if (!categoryData) {
                        return;
                    }

                    detailsRow = $('<tr>')
                        .addClass('category-details-row')
                        .attr('data-category-id', categoryId)
                        .html(buildAutoCarCategoryExpandableRowContent(categoryData));
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

            // Initial fetch
            fetchAutoCarCategories(currentPage);

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchAutoCarCategories(1);
            });

            function fetchAutoCarCategories(page = 1) {
                let url = `/api/getAllCategories?page=${page}&per_page=${perPage}`;
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
                            let autoCarCategories = response.data;
                            let pagination = response.pagination;

                            // Update pagination state
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;

                            // Update pagination UI
                            updatePaginationUI(pagination);

                            let tableBody = [];
                            window.autoCarCategoryDataMap = {};
                            autoCarCategories.forEach((category) => {
                                // Format created_at or set N/A
                                let createdAt = category.created_at ? new Date(category
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

                                // Category name with first letter capitalized or N/A
                                let categoryName = category.name && category.name.trim() !==
                                    "" ?
                                    category.name.charAt(0).toUpperCase() + category.name.slice(
                                        1) :
                                    "N/A";

                                // Details toggle for mobile
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table" onclick="toggleAutoCarCategoryRowDetails('${category.id}')" data-category-id="${category.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                window.autoCarCategoryDataMap[category.id] = {
                                    id: category.id,
                                    name: category.name,
                                    formattedDate: formattedDate
                                };

                                tableBody.push([
                                    // Column 1: Category Name
                                    `<div>
                                        <div style="display: flex; align-items: center;">
                                            <a href="javascript:void(0);" class="d-flex align-items-center">
                                                <span class="category-name truncated">${categoryName}</span>
                                            </a>
                                        </div>
                                    </div>`,

                                    // Column 2: Details Toggle (only for mobile)
                                    detailsToggle,

                                    formattedDate,

                                    // Column 3: Action Buttons (hidden on mobile)
                                    getActionButtons(category)
                                ]);
                            });
                            table.clear().rows.add(tableBody).draw();
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html(
                                '<tr><td colspan="4">No Service categories found</td></tr>'
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
                    fetchAutoCarCategories(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchAutoCarCategories(1);
            });

            // Helper function to generate action buttons
            function getActionButtons(category) {
                return `
                    @if (app('hasPermission')(21, 'edit'))
                    <a class="me-3 edit-category" href="#" data-id="${category.id}" data-name="${category.name}" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="img">
                    </a>
                    @endif
                    @if (app('hasPermission')(21, 'delete'))
                    <a class="me-3 confirm-text delete-category" data-id="${category.id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="img">
                    </a>
                    @endif
                `;
            }

            $(document).on('click', '.submit-add', function(e) {
                e.preventDefault();
                var authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let formData = new FormData($('#addCategoryForm')[0]); // keep your correct form ID

                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }

                $('.error_name').text('');

                // Debug log to confirm all values
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $.ajax({
                    url: "/api/addcategory",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Service Category added successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addCategoryModal').modal('hide');
                                    location
                                        .reload(); // 🔥 reload page instead of refreshing DataTable
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


            $(document).on('click', '.submit-edit', function(e) {
                e.preventDefault();
                var authToken = localStorage.getItem("authToken");
                let formData = new FormData($('#editCategoryForm')[0]);
                $('.error_name').text('');

                $.ajax({
                    url: "/api/autocar/updatecategory",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Service Category updated successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#editCategoryModal').modal('hide');
                                    location
                                        .reload(); // 🔥 reload page instead of refreshing DataTable
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

            $(document).on('click', '.delete-category', function() {
                var categoryId = $(this).data('id');
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
                            url: `/api/deletecategory/${categoryId}`,
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
                                    fetchAutoCarCategories();
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

            $('#editCategoryModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var categoryId = button.data('id');
                var categoryName = button.data('name');
                var modal = $(this);
                modal.find('.modal-body #edit_category_id').val(categoryId);
                modal.find('.modal-body #edit_name').val(categoryName);
            });
        });
    </script>
@endpush
