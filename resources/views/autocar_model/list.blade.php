@extends('layout.app')
@section('title', 'Model List')
@section('content')
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
    @endif
    <style>
        table#DataTables_Table_0 { width: 100% !important; }

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
            display: none;
        }

        .table-scroll-top div { height: 1px; }

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }
            .search-set { margin-right: 1rem !important; }
        }

        /* Desktop */
        @media (min-width: 769px) {
            table.datanew thead th,
            table.datanew tbody td { display: table-cell !important; }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) { display: none !important; }

            .model-details-row { display: none !important; }
        }

        /* Mobile */
        @media (max-width: 768px) {
            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) { display: none !important; }

            table.datanew {
                table-layout: fixed;
                width: 100% !important;
            }

            table.datanew thead th:first-child,
            table.datanew tbody td:first-child { width: 80% !important; }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 20% !important;
            }
        }

        /* Expandable details row */
        .model-details-row { display: none; }
        .model-details-row.show { display: table-row; }

        .model-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.model-details-row td.model-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .model-details-list { margin-bottom: 15px; }

        .model-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .model-detail-row-simple:last-of-type { border-bottom: none; }

        .model-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .model-detail-value-simple {
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

        .mobile-toggle-btn-table:hover { background: #ff8c2e; }
        .mobile-toggle-btn-table.minus { background: #dc3545; }
        .mobile-toggle-btn-table.minus:hover { background: #c82333; }

        .fade-out { opacity: 1; transition: opacity 0.5s ease-out; }
        .fade-out.hidden { opacity: 0; }

        /* Hide default DataTables controls */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate { display: none !important; }

        .dataTables_wrapper .row:first-child { display: none !important; }
        .dataTables_wrapper { margin-top: 0 !important; padding-top: 0 !important; }

        /* Custom Pagination */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 4px;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link { background-color: #ff9f43 !important; color: #fff; }
        .pagination .page-item .page-link:hover { background-color: #4a5766; color: #fff; }
        .pagination .page-item.active .page-link:hover { background-color: #e68a35 !important; }

        /* Search input */
        .search-input input { padding-left: 35px !important; border-radius: 5px; }
        .search-input { position: relative; display: flex; align-items: center; }
        .btn-searchset { position: absolute; left: 10px; z-index: 10; padding: 0; top: 7px !important; }
    </style>

    <script>
        setTimeout(function() {
            let alert = document.getElementById('error-message');
            if (alert) {
                alert.classList.add('hidden');
                setTimeout(function() { alert.style.display = 'none'; }, 500);
            }
        }, 4000);
    </script>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Models</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(22, 'add'))
                    <a href="#" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#addModelModal">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New Model
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

                <div class="table-scroll-top"><div></div></div>

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Model Name</th>
                                <th class="details-column">Details</th>
                                <th>Model Brand</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
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

    <!-- Add Model Modal -->
    <div class="modal fade" id="addModelModal" tabindex="-1" aria-labelledby="addModelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModelModalLabel">Add Model</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" onclick="event.preventDefault();" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addModelForm">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="model_name">Model Name</label>
                                    <input type="text" name="model_name" id="model_name" class="form-control" required>
                                    <span class="error_model_name text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="model_brand">Model Brand</label>
                                    <input type="text" name="model_brand" id="model_brand" class="form-control" required>
                                    <span class="error_model_brand text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div style="padding: 15px;">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal" data-dismiss="modal" onclick="event.preventDefault();">Close</button>
                    <button type="button" class="btn btn-submit submit-add">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Model Modal -->
    <div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModelModalLabel">Edit Model</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" onclick="event.preventDefault();" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editModelForm">
                        <input type="hidden" name="model_id" id="edit_model_id">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="edit_model_name">Model Name</label>
                                    <input type="text" name="model_name" id="edit_model_name" class="form-control" required>
                                    <span class="error_model_name text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="edit_model_brand">Model Brand</label>
                                    <input type="text" name="model_brand" id="edit_model_brand" class="form-control" required>
                                    <span class="error_model_brand text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div style="padding: 15px;">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal" data-dismiss="modal" onclick="event.preventDefault();">Close</button>
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
            });

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            window.modelDataMap = {};

            // Initial fetch
            fetchAutoCarModels(currentPage);

            // Debounced search
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() { fetchAutoCarModels(1); }, 500);
            });

            // Build expandable row content (product-list style)
            function buildModelExpandableRowContent(model) {
                let actionBtns = '';

                @if (app('hasPermission')(22, 'edit'))
                    actionBtns += `
                        <a class="btn-icon-mobile edit-model"
                           href="javascript:void(0);"
                           data-id="${model.id}"
                           data-name="${model.model_name}"
                           data-brand="${model.model_brand}"
                           data-bs-toggle="modal"
                           data-bs-target="#editModelModal"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>`;
                @endif

                @if (app('hasPermission')(22, 'delete'))
                    actionBtns += `
                        <a class="btn-icon-mobile confirm-text delete-model"
                           data-id="${model.id}"
                           href="javascript:void(0);"
                           title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>`;
                @endif

                return `
                    <td colspan="5" class="model-details-content">
                        <div class="model-details-list">
                            <div class="model-detail-row-simple">
                                <span class="model-detail-label-simple">Model Brand:</span>
                                <span class="model-detail-value-simple">${model.modelBrand}</span>
                            </div>
                            <div class="model-detail-row-simple">
                                <span class="model-detail-label-simple">Created At:</span>
                                <span class="model-detail-value-simple">${model.formattedDate}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            // Toggle expandable row
            window.toggleModelRowDetails = function(modelId) {
                const btn = $(`.mobile-toggle-btn-table[data-model-id="${modelId}"]`);
                if (btn.length === 0) return;

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.model-details-row[data-model-id="${modelId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const modelData = window.modelDataMap && window.modelDataMap[modelId];
                    if (!modelData) return;

                    detailsRow = $('<tr>')
                        .addClass('model-details-row')
                        .attr('data-model-id', modelId)
                        .html(buildModelExpandableRowContent(modelData));
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

            function fetchAutoCarModels(page = 1) {
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                window.modelDataMap = {};

                $.ajax({
                    url: "/api/getAllModels",
                    type: "GET",
                    dataType: "json",
                    headers: { "Authorization": "Bearer " + authToken },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    success: function(response) {
                        if (response.status) {
                            let autoCarModels = response.data;
                            let pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            let tableBody = [];

                            autoCarModels.forEach((model) => {
                                let createdAt = model.created_at ? new Date(model.created_at) : null;
                                let formattedDate = createdAt ?
                                    createdAt.toLocaleDateString('en-GB', {
                                        day: 'numeric', month: 'short', year: 'numeric',
                                        hour: 'numeric', minute: 'numeric', hour12: true
                                    }).replace(/(\d+)\s(\w+)\s(\d+)/, '$1-$2-$3') : "N/A";

                                let modelName = model.model_name && model.model_name.trim() !== "" ?
                                    model.model_name.replace(/\b\w/g, c => c.toUpperCase()) : "N/A";

                                let modelBrand = model.model_brand && model.model_brand.trim() !== "" ?
                                    model.model_brand.replace(/\b\w/g, c => c.toUpperCase()) : "N/A";

                                // Store data for expandable row
                                window.modelDataMap[model.id] = {
                                    id: model.id,
                                    model_name: model.model_name,
                                    model_brand: model.model_brand,
                                    modelBrand: modelBrand,
                                    formattedDate: formattedDate
                                };

                                // Mobile toggle button (product-list style)
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table"
                                            onclick="toggleModelRowDetails('${model.id}')"
                                            data-model-id="${model.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                tableBody.push([
                                    // Column 1: Model Name
                                    `<div style="display: flex; align-items: center;">
                                        <span style="color: #1b2850; font-weight: 500;">${modelName}</span>
                                    </div>`,

                                    // Column 2: Toggle button (mobile only)
                                    detailsToggle,

                                    // Column 3: Model Brand (desktop)
                                    modelBrand,

                                    // Column 4: Created At (desktop)
                                    formattedDate,

                                    // Column 5: Action Buttons (desktop)
                                    getActionButtons(model)
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();

                            // Sync top scrollbar
                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const tableElement = document.querySelector('.datanew');
                            if (topScroll && tableResponsive && tableElement) {
                                const topInnerDiv = topScroll.querySelector('div');
                                topInnerDiv.style.width = tableElement.scrollWidth + 'px';
                                topScroll.onscroll = () => { tableResponsive.scrollLeft = topScroll.scrollLeft; };
                                tableResponsive.onscroll = () => { topScroll.scrollLeft = tableResponsive.scrollLeft; };
                            }
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="5">No models found</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) { console.log("Error:", xhr); }
                });
            }

            // Desktop action buttons
            function getActionButtons(model) {
                return `
                    @if (app('hasPermission')(22, 'edit'))
                    <a class="me-3 edit-model" href="#"
                       data-id="${model.id}"
                       data-name="${model.model_name}"
                       data-brand="${model.model_brand}"
                       data-bs-toggle="modal" data-bs-target="#editModelModal">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="img">
                    </a>
                    @endif
                    @if (app('hasPermission')(22, 'delete'))
                    <a class="me-3 confirm-text delete-model" data-id="${model.id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="img">
                    </a>
                    @endif
                `;
            }

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
                if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

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

            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchAutoCarModels(page);
                }
            });

            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchAutoCarModels(1);
            });

            // ================= ADD MODEL =================
            $(document).on('click', '.submit-add', function(e) {
                e.preventDefault();
                var authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let formData = new FormData($('#addModelForm')[0]);

                if (selectedSubAdminId) formData.append("sub_admin_id", selectedSubAdminId);

                $('.error_model_name, .error_model_brand').text('');

                $.ajax({
                    url: "/api/addmodel",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Model added successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addModelModal').modal('hide');
                                    fetchAutoCarModels(currentPage);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) { $('.error_' + key).text(value[0]); });
                        }
                    }
                });
            });

            // ================= EDIT MODEL =================
            $(document).on('click', '.submit-edit', function(e) {
                e.preventDefault();
                var authToken = localStorage.getItem("authToken");
                let formData = new FormData($('#editModelForm')[0]);
                $('.error_model_name, .error_model_brand').text('');

                $.ajax({
                    url: "/api/updatemodel",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Model updated successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#editModelModal').modal('hide');
                                    fetchAutoCarModels(currentPage);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) { $('.error_' + key).text(value[0]); });
                        }
                    }
                });
            });

            // ================= DELETE MODEL =================
            $(document).on('click', '.delete-model', function() {
                var modelId = $(this).data('id');
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
                            url: `/api/deletemodel/${modelId}`,
                            type: 'DELETE',
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
                                    });
                                    fetchAutoCarModels(currentPage);
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr) {
                                let message = "Something went wrong!";
                                if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                                Swal.fire({ title: "Error!", text: message, icon: "error", confirmButtonColor: "#ff9f43" });
                            }
                        });
                    }
                });
            });

            // Populate edit modal from data attributes
            $('#editModelModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#edit_model_id').val(button.data('id'));
                modal.find('#edit_model_name').val(button.data('name'));
                modal.find('#edit_model_brand').val(button.data('brand'));
            });

            // Clear errors and reset form when Add Model modal is closed
            $('#addModelModal').on('hidden.bs.modal', function() {
                $('#addModelForm')[0].reset();
                $('.error_model_name, .error_model_brand').text('');
            });

            // Clear errors and reset form when Edit Model modal is closed
            $('#editModelModal').on('hidden.bs.modal', function() {
                $('#editModelForm')[0].reset();
                $('.error_model_name, .error_model_brand').text('');
            });

        });
    </script>
@endpush
