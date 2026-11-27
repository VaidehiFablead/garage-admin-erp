@extends('layout.app')

@section('title', 'Material List')

@section('content')
    <style>
        /* === Pagination styling === */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
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

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
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

        /* Hide default DataTables elements */
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

        .material-name {
            display: inline-block;
            font-size: 14px;
            line-height: 1.4;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            background-color: #e8f4fd;
            color: #1769aa;
            border: 1px solid #b3d7ff;
            margin: 2px 2px 2px 0;
        }

        /* Desktop */
        @media (min-width: 769px) {
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }
            .mobile-action-buttons {
                display: none !important;
            }
        }

        /* Mobile responsive design */
        @media (max-width: 768px) {
            table.datanew thead th:nth-child(3),
            table.datanew thead th:nth-child(4),
            table.datanew thead th:nth-child(5),
            table.datanew thead th:nth-child(6),
            table.datanew tbody td:nth-child(3),
            table.datanew tbody td:nth-child(4),
            table.datanew tbody td:nth-child(5),
            table.datanew tbody td:nth-child(6) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 56px !important;
                padding: 12px 6px !important;
            }

            .desktop-actions {
                display: none !important;
            }

            table.datanew tbody td:first-child {
                display: table-cell !important;
                width: auto;
                max-width: calc(100vw - 80px);
                padding: 12px 8px;
            }

            .mobile-action-buttons {
                display: flex !important;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                gap: 12px;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid #e9ecef;
            }

            .mobile-action-buttons a {
                display: inline-flex !important;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                text-decoration: none;
                color: #2c3e50;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
            }

            .pagination-controls {
                flex-direction: column;
                gap: 15px;
            }
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .action-buttons a {
            display: inline-flex;
            align-items: center;
            transition: 0.2s;
            cursor: pointer;
        }

        .action-buttons img {
            width: 20px;
            height: 20px;
        }

        .table-container {
            width: 100%;
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .page-wrapper {
                padding-top: 0 !important;
            }

            .mm-page.content {
                max-width: 1024px;
                margin: 0 auto;
                padding: 6px 10px 90px !important;
            }

            .mm-page > .page-header {
                display: none !important;
            }

            .mm-page > .mm-head {
                display: flex !important;
            }

            .mm-page .mm-search {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                margin: 0 0 8px !important;
                padding: 8px 10px;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }

            .mm-page .mm-search > [class*="col-"] {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
                margin: 0 !important;
            }

            .mm-page .mm-search .search-input input,
            .mm-page .mm-search .form-select {
                height: 40px !important;
                border-radius: 10px !important;
                font-size: 13px;
            }

            .mm-page .mm-search .form-select {
                width: 100%;
            }

            .mm-page .mm-card-list {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }
        }

        table.datanew {
            width: 100%;
            min-width: 300px;
        }

        /* Select2 modal styling */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da !important;
            border-radius: 5px !important;
            min-height: 38px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #ff9f43 !important;
        }
        .select2-container {
            z-index: 99999 !important;
        }
    </style>

    <div class="content mm-page">
        <div class="mm-head d-md-none">
            <div>
                <div class="mm-title">Material Master</div>
                <div class="mm-count" id="mmCount"></div>
            </div>
            <a href="javascript:void(0);" class="btn btn-sm mm-add" data-bs-toggle="modal" data-bs-target="#addMaterialModal"><i class="fas fa-plus me-1"></i> New Material</a>
        </div>

        <div class="page-header">
            <div class="page-title">
                <h4>All Materials</h4>
                <h6>Manage Material Master & Type Associations</h6>
            </div>
            <div class="page-btn">
                <a href="javascript:void(0);" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">
                    New Material
                </a>
            </div>
        </div>

        <!-- Add Material Modal -->
        <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <form id="addMaterialForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Material Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Lokhand Rod, Steel Sheet" required>
                                    <span class="error_name text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (&#8377;) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="0.00" required>
                                    <span class="error_price text-danger"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Associated Types <span class="text-danger">*</span></label>
                                    <select name="type_ids[]" id="add_type_ids" class="form-control select2" multiple="multiple" style="width: 100%;" required>
                                        <!-- Dynamically loaded -->
                                    </select>
                                    <span class="error_type_ids text-danger"></span>
                                    <small class="text-muted d-block mt-1">Select one or more types that this material belongs to.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="saveMaterialBtn">Save Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Material Modal -->
        <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <form id="editMaterialForm">
                        @csrf
                        <input type="hidden" id="edit_material_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Material Name <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_name" class="form-control" placeholder="Enter Material Name" required>
                                    <span class="error_edit_name text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (&#8377;) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" id="edit_price" class="form-control" placeholder="0.00" required>
                                    <span class="error_edit_price text-danger"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Associated Types <span class="text-danger">*</span></label>
                                    <select name="type_ids[]" id="edit_type_ids" class="form-control select2" multiple="multiple" style="width: 100%;" required>
                                        <!-- Dynamically loaded -->
                                    </select>
                                    <span class="error_edit_type_ids text-danger"></span>
                                    <small class="text-muted d-block mt-1">Select one or more types that this material belongs to.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="updateMaterialBtn">Update Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-3 mm-search">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search materials or types...">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <select id="type-filter" class="form-select form-select-sm">
                            <option value="">All Types (Filter)</option>
                        </select>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Material Name</th>
                                <th class="details-column">Details</th>
                                <th>Price</th>
                                <th>Linked Types</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="material-table-body">
                            <!-- Populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="mm-card-list d-md-none" id="mmCardList"></div>

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
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                            <!-- Page numbers -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('partials.master-mobile-ui')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function getHeaders() {
                var headers = {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                };
                if (authToken) {
                    headers['Authorization'] = 'Bearer ' + authToken;
                }
                return headers;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Initialize Select2 on modals
            $('#add_type_ids').select2({
                placeholder: 'Select Types',
                dropdownParent: $('#addMaterialModal'),
                width: '100%'
            });

            $('#edit_type_ids').select2({
                placeholder: 'Select Types',
                dropdownParent: $('#editMaterialModal'),
                width: '100%'
            });

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
            let selectedTypeFilter = '';
            let availableTypes = [];

            loadTypesDropdown();
            fetchMaterials(currentPage);

            // Load types for modals & filter dropdown
            function loadTypesDropdown() {
                let url = '/api/types?all=true';
                if (selectedSubAdminId) {
                    url += '&selectedSubAdminId=' + selectedSubAdminId;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status && Array.isArray(response.data)) {
                            availableTypes = response.data;
                            let optionsHtml = '';
                            let filterHtml = '<option value="">All Types (Filter)</option>';

                            availableTypes.forEach(type => {
                                optionsHtml += `<option value="${type.id}">${escapeHtml(type.type_name)}</option>`;
                                filterHtml += `<option value="${type.id}">${escapeHtml(type.type_name)}</option>`;
                            });

                            $('#add_type_ids').html(optionsHtml);
                            $('#edit_type_ids').html(optionsHtml);
                            $('#type-filter').html(filterHtml);
                        }
                    }
                });
            }

            // Search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchMaterials(1);
            });

            // Type filter
            $('#type-filter').on('change', function() {
                selectedTypeFilter = $(this).val();
                fetchMaterials(1);
            });

            function fetchMaterials(page = 1) {
                let url = `/api/materials?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }
                if (selectedTypeFilter) {
                    url += `&type_id=${encodeURIComponent(selectedTypeFilter)}`;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            let materials = response.data;
                            let pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            let rowsHtml = '';
                            let mmCards = [];

                            if (materials.length === 0) {
                                rowsHtml = `<tr><td colspan="6" class="text-center py-4 text-muted">No materials found.</td></tr>`;
                            } else {
                                materials.forEach(material => {
                                    let createdAt = 'N/A';
                                    if (material.created_at) {
                                        let d = new Date(material.created_at);
                                        if (!isNaN(d)) {
                                            createdAt = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
                                        }
                                    }

                                    let matName = escapeHtml(material.name);
                                    let priceFormatted = parseFloat(material.price).toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });

                                    let typesBadges = '';
                                    if (material.types && material.types.length > 0) {
                                        material.types.forEach(t => {
                                            typesBadges += `<span class="type-badge">${escapeHtml(t.type_name)}</span>`;
                                        });
                                    } else {
                                        typesBadges = '<span class="text-muted small">None</span>';
                                    }

                                    let typeIdsJson = escapeHtml(JSON.stringify(material.type_ids || []));

                                    let detailsToggle = `
                                        <a href="javascript:void(0);" class="toggle-details" data-target="mat-details-${material.id}">
                                            <i class="fas fa-plus-circle" style="color: #ff9f43; font-size: 20px;"></i>
                                        </a>
                                    `;

                                    let actionsHtml = `
                                        <div class="action-buttons desktop-actions">
                                            <a class="edit-material" href="javascript:void(0);" 
                                               data-id="${material.id}" 
                                               data-name="${matName}" 
                                               data-price="${material.price}" 
                                               data-types='${typeIdsJson}' 
                                               title="Edit">
                                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                            </a>
                                            <a class="delete-material" href="javascript:void(0);" data-id="${material.id}" title="Delete">
                                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                            </a>
                                        </div>
                                    `;

                                    rowsHtml += `
                                        <tr>
                                            <td>
                                                <span class="material-name fw-bold text-dark">${matName}</span>
                                            </td>
                                            <td class="details-column text-center">${detailsToggle}</td>
                                            <td>&#8377; ${priceFormatted}</td>
                                            <td>${typesBadges}</td>
                                            <td>${createdAt}</td>
                                            <td>${actionsHtml}</td>
                                        </tr>
                                        <tr class="collapse-row" id="mat-details-${material.id}" style="display: none;">
                                            <td colspan="6">
                                                <div class="p-2 bg-light rounded">
                                                    <p class="mb-1"><strong>Price:</strong> &#8377; ${priceFormatted}</p>
                                                    <p class="mb-1"><strong>Types:</strong> ${typesBadges}</p>
                                                    <p class="mb-1"><strong>Created:</strong> ${createdAt}</p>
                                                    <div class="mobile-action-buttons">
                                                        <a class="edit-material text-primary" href="javascript:void(0);" 
                                                           data-id="${material.id}" 
                                                           data-name="${matName}" 
                                                           data-price="${material.price}" 
                                                           data-types='${typeIdsJson}'>
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                        <a class="delete-material text-danger" href="javascript:void(0);" data-id="${material.id}">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `;

                                    let typeChips = (material.types || []).map(t => `<span class="mm-chip">${escapeHtml(t.type_name)}</span>`).join('');
                                    mmCards.push(MasterMobileUI.card({
                                        title: material.name,
                                        sub: 'Created ' + createdAt,
                                        actions: `
                                            <a class="mm-act is-edit edit-material" href="javascript:void(0);" data-id="${material.id}" data-name="${matName}" data-price="${material.price}" data-types='${typeIdsJson}' title="Edit"><i class="fas fa-pen"></i></a>
                                            <a class="mm-act is-delete delete-material" href="javascript:void(0);" data-id="${material.id}" title="Delete"><i class="fas fa-trash"></i></a>`,
                                        stats: [
                                            { label: 'Price', html: `&#8377; ${priceFormatted}` },
                                            { label: 'Linked Types', html: typeChips ? `<span class="mm-chips">${typeChips}</span>` : null }
                                        ]
                                    }));
                                });
                            }

                            $('#material-table-body').html(rowsHtml);
                            MasterMobileUI.render(mmCards, 'No materials found');
                        }
                    },
                    error: function() {
                        $('#material-table-body').html(`<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load materials.</td></tr>`);
                        MasterMobileUI.render([], 'Failed to load materials');
                    }
                });
            }

            // Mobile toggle
            $(document).on('click', '.toggle-details', function(e) {
                e.preventDefault();
                let targetId = $(this).data('target');
                let $target = $('#' + targetId);
                let $icon = $(this).find('i');

                if ($target.is(':visible')) {
                    $target.hide();
                    $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    $target.show();
                    $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                }
            });

            function updatePaginationUI(pagination) {
                let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let html = `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>
                `;

                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>`;
                }

                html += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(html);
            }

            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchMaterials(page);
                }
            });

            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchMaterials(1);
            });

            // Add Material submit
            $('#addMaterialForm').submit(function(e) {
                e.preventDefault();
                let $btn = $('#saveMaterialBtn');
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...').prop('disabled', true);
                $('.error_name, .error_price, .error_type_ids').text('');

                let selectedTypes = $('#add_type_ids').val() || [];

                let payload = {
                    name: $(this).find('input[name="name"]').val(),
                    price: $(this).find('input[name="price"]').val(),
                    type_ids: selectedTypes.map(id => parseInt(id))
                };
                if (selectedSubAdminId) payload.selectedSubAdminId = selectedSubAdminId;

                $.ajax({
                    url: '/api/materials',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43',
                                confirmButtonText: 'OK'
                            });
                            $('#addMaterialModal').modal('hide');
                            $('#addMaterialForm')[0].reset();
                            $('#add_type_ids').val(null).trigger('change');
                            fetchMaterials(currentPage);
                        }
                        $btn.html('Save Material').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $btn.html('Save Material').prop('disabled', false);
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) $('.error_name').text(errors.name[0]);
                            if (errors.price) $('.error_price').text(errors.price[0]);
                            if (errors.type_ids) $('.error_type_ids').text(errors.type_ids[0]);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Something went wrong!',
                                icon: 'error',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    }
                });
            });

            // Edit populate modal
            $(document).on('click', '.edit-material', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let name = $(this).data('name');
                let price = $(this).data('price');
                let typeIds = $(this).data('types');

                if (typeof typeIds === 'string') {
                    try { typeIds = JSON.parse(typeIds); } catch(err) { typeIds = []; }
                }

                $('#edit_material_id').val(id);
                $('#edit_name').val(name);
                $('#edit_price').val(price);
                $('.error_edit_name, .error_edit_price, .error_edit_type_ids').text('');

                // Pre-fill multi-select
                $('#edit_type_ids').val(typeIds).trigger('change');

                $('#editMaterialModal').modal('show');
            });

            // Update Material submit
            $('#editMaterialForm').submit(function(e) {
                e.preventDefault();
                let $btn = $('#updateMaterialBtn');
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...').prop('disabled', true);
                $('.error_edit_name, .error_edit_price, .error_edit_type_ids').text('');

                let id = $('#edit_material_id').val();
                let selectedTypes = $('#edit_type_ids').val() || [];

                let payload = {
                    name: $('#edit_name').val(),
                    price: $('#edit_price').val(),
                    type_ids: selectedTypes.map(tid => parseInt(tid))
                };
                if (selectedSubAdminId) payload.selectedSubAdminId = selectedSubAdminId;

                $.ajax({
                    url: `/api/materials/${id}`,
                    type: 'PUT',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Updated!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43',
                                confirmButtonText: 'OK'
                            });
                            $('#editMaterialModal').modal('hide');
                            fetchMaterials(currentPage);
                        }
                        $btn.html('Update Material').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $btn.html('Update Material').prop('disabled', false);
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) $('.error_edit_name').text(errors.name[0]);
                            if (errors.price) $('.error_edit_price').text(errors.price[0]);
                            if (errors.type_ids) $('.error_edit_type_ids').text(errors.type_ids[0]);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Failed to update material',
                                icon: 'error',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    }
                });
            });

            // Delete Material
            $(document).on('click', '.delete-material', function(e) {
                e.preventDefault();
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this Material? This cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff9f43',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/materials/${id}`,
                            type: 'DELETE',
                            headers: getHeaders(),
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#ff9f43'
                                    });
                                    fetchMaterials(currentPage);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Failed to delete material.',
                                    icon: 'error',
                                    confirmButtonColor: '#ff9f43'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
