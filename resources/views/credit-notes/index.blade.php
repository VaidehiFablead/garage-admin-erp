@extends('layout.app')
@section('title', 'Notes List')

@push('css')
    <style>
        .table tbody tr td {
            white-space: normal !important;
            word-break: break-word;
            word-wrap: break-word;
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

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td.details-control {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {
            .table-responsive {
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew {
                width: 100% !important;
                table-layout: fixed;
            }

            /* Adjust toggle icon size and alignment */
            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                cursor: pointer;
            }

            .toggle-details i {
                font-size: 24px;
            }

            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td.details-control {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px !important;
            }

            /* Style for first column wrapping on mobile */
            table.datanew tbody td:first-child {
                display: flex !important;
                flex-direction: column;
                max-width: calc(100vw - 100px) !important;
                width: calc(100% - 50px) !important;
            }

            table.datanew tbody td:first-child > div {
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                white-space: normal !important;
            }
        }

        .collapse-details {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #ff9b44;
        }

        .detail-item {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .detail-label {
            font-weight: 600;
            min-width: 100px;
            color: #495057;
        }

        .detail-value {
            color: #212529;
            flex: 1;
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
        #DataTables_Table_0_filter {
            display: none !important;
        }
        .search {
            padding: 0.375rem 1.75rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Credit Notes</h4>
                
            </div>
            <div class="page-btn">
                <a href="javascript:void(0);" class="btn btn-added btn-sm" data-bs-toggle="modal" data-bs-target="#addCreditNoteModal">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New
                    Credit Notes
                </a>
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
                            <input type="text" id="search-input" class="form-control search" placeholder="   Search...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Type Name</th>
                                <th class="details-column">Details</th>
                                <th>Created At</th>
                                <th class="text-end">Action</th>
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

    <!-- Add Credit Note Modal -->
    <div class="modal fade" id="addCreditNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Credit Note</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Type Name<span class="manitory">*</span></label>
                                <input type="text" id="add_type_name" class="form-control" placeholder="Enter type name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-submit btn-submit-add">Save</button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Credit Note Modal -->
    <div class="modal fade" id="editCreditNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Credit Note</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_credit_note_id">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Type Name<span class="manitory">*</span></label>
                                <input type="text" id="edit_type_name" class="form-control" placeholder="Enter type name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-submit btn-submit-edit">Update</button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var table;

            function updateDetailsColumnVisibility() {
                if (!table) return;
                const isMobileView = window.matchMedia('(max-width: 768px)').matches;

                if (isMobileView) {
                    table.column(0).visible(true, false);
                    table.column(1).visible(true, false);
                    table.columns([2, 3]).visible(false, false);
                } else {
                    table.column(0).visible(true, false);
                    table.column(1).visible(false, false);
                    table.columns([2, 3]).visible(true, false);
                }

                table.columns.adjust().draw(false);
            }

            table = $(".datanew").DataTable({
                searching: false,
                paging: false,
                ordering: false,
                destroy: true,
                autoWidth: false,
                dom: 't',
                columnDefs: [
                    { targets: 1, className: 'details-control', orderable: false }
                ]
            });
            setTimeout(function () {
                $('#DataTables_Table_0_filter').remove();
            }, 100);

            $(window).on('resize', updateDetailsColumnVisibility);

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            loadCreditNotes(currentPage);

            // Handle search input
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadCreditNotes(1);
                }, 500);
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

            function loadCreditNotes(page = 1) {
                $.ajax({
                    url: "{{ route('credit-notes-types.index') }}",
                    type: "GET",
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
                        let rows = [];
                        let pagination = response.pagination;

                        // Update pagination state
                        currentPage = pagination.current_page;
                        lastPage = pagination.last_page;

                        // Update pagination UI
                        updatePaginationUI(pagination);

                        response.creditNotes.forEach(function(item, index) {
                            let createdAt = item.created_at ? new Date(item.created_at).toLocaleDateString('en-GB') : "N/A";
                            let detailsId = `details-${index}`;

                            rows.push([
                                `<div>
                                    <span>${item.type_name}</span>
                                    <div class="collapse mt-2 d-lg-none" id="${detailsId}">
                                        <div class="card card-body p-2 bg-light border">
                                            <div class="detail-item">
                                                <span class="detail-label">Created At:</span>
                                                <span class="detail-value">${createdAt}</span>
                                            </div>
                                            <div class="mt-2">
                                                <a href="javascript:void(0);" class="edit-credit-note me-2" data-id="${item.id}">
                                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                                </a>
                                                <a href="javascript:void(0);" class="delete-credit-note" data-id="${item.id}">
                                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`,
                                `<a href="#${detailsId}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                </a>`,
                                createdAt,
                                `<div class="text-end">
                                    <a href="javascript:void(0);" class="edit-credit-note me-2" data-id="${item.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                    </a>
                                    <a href="javascript:void(0);" class="delete-credit-note" data-id="${item.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                    </a>
                                </div>`
                            ]);
                        });

                        table.clear().rows.add(rows).draw();
                        updateDetailsColumnVisibility();
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
                    loadCreditNotes(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                loadCreditNotes(1);
            });

            function clearErrors() {
                $(".error-text").remove();
                $("input").removeClass("is-invalid");
            }

            function validateForm(modalId) {
                clearErrors();
                let isValid = true;
                let typeName = $(modalId + " [id$='_type_name']").val().trim();

                if (typeName === "") {
                    $(modalId + " [id$='_type_name']").addClass("is-invalid");
                    $(modalId + " [id$='_type_name']").after('<small class="text-danger error-text">Type Name is required.</small>');
                    isValid = false;
                }

                return isValid;
            }

            $(".btn-submit-add").on("click", function() {
                if (!validateForm("#addCreditNoteModal")) return;

                let data = {
                    type_name: $("#add_type_name").val(),
                    sub_admin_id: selectedSubAdminId,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('credit-notes-types.store') }}",
                    type: "POST",
                    data: data,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#addCreditNoteModal").modal("hide");
                        resetForm("#addCreditNoteModal");
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                        });
                        loadCreditNotes(currentPage);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                $("#add_" + key).addClass("is-invalid");
                                $("#add_" + key).after(`<small class="text-danger error-text">${errors[key][0]}</small>`);
                            }
                        }
                    }
                });
            });

            $(document).on("click", ".edit-credit-note", function() {
                let id = $(this).data("id");
                $.ajax({
                    url: `/api/credit-notes-types/${id}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#edit_credit_note_id").val(response.data.id);
                        $("#edit_type_name").val(response.data.type_name);
                        $("#editCreditNoteModal").modal("show");
                    }
                });
            });

            $(".btn-submit-edit").on("click", function() {
                if (!validateForm("#editCreditNoteModal")) return;

                let id = $("#edit_credit_note_id").val();
                let data = {
                    type_name: $("#edit_type_name").val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: `/api/credit-notes-types/update/${id}`,
                    type: "POST",
                    data: data,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#editCreditNoteModal").modal("hide");
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                        });
                        loadCreditNotes();
                    }
                });
            });

            $(document).on("click", ".delete-credit-note", function() {
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
                            url: `/api/credit-notes-types/delete/${id}`,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                });
                                loadCreditNotes();
                            }
                        });
                    }
                });
            });

            function resetForm(modalId) {
                $(modalId).find("input").val("");
                clearErrors();
            }

            $("#addCreditNoteModal, #editCreditNoteModal").on("hidden.bs.modal", function() {
                resetForm("#" + this.id);
            });
        });
    </script>
@endpush
