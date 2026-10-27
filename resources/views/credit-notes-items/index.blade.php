@extends('layout.app')

@section('title', 'Credit Note Items List')

@push('css')
    <style>
        table#creditNoteItemsTable {
            width: 100% !important;
        }

        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        #creditNoteItemsTable tbody tr td {
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

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            #creditNoteItemsTable thead th,
            #creditNoteItemsTable tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            #creditNoteItemsTable thead th.details-column,
            #creditNoteItemsTable tbody td.details-control {
                display: none !important;
            }
        }

        /* Mobile/Tablet: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: hidden !important;
            }

            #creditNoteItemsTable {
                width: 100% !important;
                table-layout: fixed;
            }

            .collapse .card-body .mt-2 {
                display: flex;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                gap: 15px;
                flex-wrap: wrap;
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

            #creditNoteItemsTable thead th:nth-child(n+3),
            #creditNoteItemsTable tbody td:nth-child(n+3) {
                display: none !important;
            }

            #creditNoteItemsTable thead th.details-column,
            #creditNoteItemsTable tbody td.details-control {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px !important;
            }

            /* Style for first column wrapping on mobile */
            #creditNoteItemsTable tbody td:first-child {
                display: flex !important;
                flex-direction: column;
                max-width: calc(100vw - 100px) !important;
                width: calc(100% - 50px) !important;
            }

            #creditNoteItemsTable tbody td:first-child > div {
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                white-space: normal !important;
            }
        }

        .credit-mobile {
            background-color: #f8f9fa;
            color: #ff9f43;
            font-weight: bold;
            padding: 7px 10px;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
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
            min-width: 120px;
            color: #495057;
        }

        .detail-value {
            color: #212529;
            flex: 1;
        }

        @media (max-width: 575px) {
            .page-header .page-btn {
                margin-top: 0px !important;
            }
        }

        @media (max-width: 768px) {
            .page-header .page-title h4 {
                font-size: 14px !important;
                margin: 0 !important;
                white-space: nowrap;
            }

            /* Row 1: title + button, Row 2: Total badge */
            .total-badge {
                width: 100%;
                margin-top: 4px;
            }

            /* Compact Total badge */
            .credit-mobile {
                padding: 5px 10px !important;
                white-space: nowrap;
            }
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
@endpush

@section('content')
    <div class="content">
        <div class="page-header" style="flex-wrap: wrap; gap: 8px;">
            <div class="page-title">
                <h4>Credit Note List</h4>
            </div>
            <div class="d-flex align-items-center gap-2" style="flex-wrap: wrap;">
                <div class="page-btn mt-0">
                    <a href="{{ route('credit-notes-items.create') }}" class="btn btn-added" style="white-space:nowrap;">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1">
                        Add Credit Note Item
                    </a>
                </div>
                {{-- Total badge — on mobile it wraps to next line via CSS --}}
                <div class="d-flex align-items-center credit-mobile total-badge">
                    <span style="color: #495057; margin-right: 8px; font-size: 14px;">Total:</span>
                    <span style="font-size: 16px; color:#ff9f43; font-weight:bold;">{{ $currencySymbol }}<span id="total_settlement_amount">0.00</span></span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="   Search...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="creditNoteItemsTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Job Card / Invoice Number</th>
                                <th class="details-column">Details</th>
                                <th>Customer Name / Vendor Name</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                                {{-- <th>Paid Amount</th> --}}
                                {{-- <th>Remaining Amount</th> --}}
                                <th>Settlement Amount</th>
                                <!-- <th>Final Total</th> -->
                                {{-- <th>Reason</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded via AJAX --}}
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
                            <!-- Page numbers will be populated by JS -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 10;
        let searchQuery = '';

        $(document).ready(function() {
            let currencySymbol = "{{ $currencySymbol }}";
            let currencyPosition = "{{ $currencyPosition }}";
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const authToken = localStorage.getItem("authToken");

            function formatCurrency(amount) {
                let formatted = parseFloat(amount || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                return currencyPosition === 'left' ?
                    currencySymbol + formatted :
                    formatted + currencySymbol;
            }

            function getInvoiceNumber(row) {
                if (row.type_id == 2 || row.type_id == 'payment') {
                    return row.purchase_invoice ? row.purchase_invoice.invoice_number : "N/A";
                }
                if (row.order) {
                    return row.order.order_number || "N/A";
                }
                if (row.jobcard) {
                    return row.jobcard.job_card_number || "N/A";
                }
                return "N/A";
            }

            function getDisplayName(row) {
                if (row.type_id == 2 || row.type_id == 'payment') {
                    return row.purchase_invoice && row.purchase_invoice.vendor ? row.purchase_invoice.vendor.name : "N/A";
                }
                if (row.order && row.order.user) {
                    return row.order.user.name || "N/A";
                }
                if (row.jobcard) {
                    return (row.jobcard.customer && row.jobcard.customer.name) ? row.jobcard.customer.name : (row.jobcard.customer_name || "N/A");
                }
                return "N/A";
            }

            function getTypeName(row) {
                return row.credit_note && row.credit_note.type_name ? row.credit_note.type_name : "N/A";
            }

            function renderActionButtons(id) {
                return `
                    <a class="" href="/view-credit-note-items/${id}">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}">
                    </a>
                    <a class="" href="/edit-credit-note-items/${id}">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}">
                    </a>
                    <a class="confirm-text delete-item" data-id="${id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}">
                    </a>
                `;
            }

            function updateDetailsColumnVisibility() {
                if (!table) return;
                const isMobileView = window.matchMedia('(max-width: 768px)').matches;

                if (isMobileView) {
                    table.column(0).visible(true, false);
                    table.column(1).visible(true, false);
                    table.columns([2, 3, 4, 5, 6]).visible(false, false);
                } else {
                    table.column(0).visible(true, false);
                    table.column(1).visible(false, false);
                    table.columns([2, 3, 4, 5, 6]).visible(true, false);
                }

                table.columns.adjust().draw(false);
            }

            window.loadCreditNoteItems = function(page = 1) {
                let ajaxUrl = "/api/credit-note-items-api";
                let params = {
                    page: page,
                    per_page: perPage,
                    search: searchQuery,
                    selectedSubAdminId: selectedSubAdminId || null
                };

                $.ajax({
                    url: ajaxUrl,
                    type: "GET",
                    data: params,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(json) {
                        const rows = [];
                        if (json.data) {
                            json.data.forEach(row => {
                                const invoiceNumber = getInvoiceNumber(row);
                                const displayName = getDisplayName(row);
                                const typeName = getTypeName(row);
                                const detailsId = `credit-note-details-${row.id}`;

                                const invoiceColumn = `
                                    <div>
                                        <span>${invoiceNumber}</span>
                                        <div class="collapse mt-2 mobile-details-panel" id="${detailsId}">
                                            <div class="card card-body p-2 bg-light border">
                                                <div class="detail-item">
                                                    <span class="detail-label">Name:</span>
                                                    <span class="detail-value">${displayName}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Type:</span>
                                                    <span class="detail-value">${typeName}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Total Amount:</span>
                                                    <span class="detail-value">${formatCurrency(row.total_amt)}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Settlement Amount:</span>
                                                    <span class="detail-value">${formatCurrency(row.settlement_amount)}</span>
                                                </div>
                                                <div class="mt-2">
                                                    ${renderActionButtons(row.id)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                const detailsColumn = `
                                    <a href="#${detailsId}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                    </a>
                                `;

                                rows.push([
                                    invoiceColumn,
                                    detailsColumn,
                                    displayName,
                                    typeName,
                                    formatCurrency(row.total_amt),
                                    formatCurrency(row.settlement_amount),
                                    renderActionButtons(row.id)
                                ]);
                            });
                        }

                        $('#total_settlement_amount').text(json.total_settlement || '0.00');

                        if ($.fn.DataTable.isDataTable('#creditNoteItemsTable')) {
                            table.clear().rows.add(rows).draw();
                        } else {
                            table = $('#creditNoteItemsTable').DataTable({
                                data: rows,
                                paging: false,
                                searching: false,
                                info: false,
                                ordering: true,
                                order: [],
                                bFilter: false,
                                sDom: 't',
                                columnDefs: [{
                                    targets: 1,
                                    className: 'details-control',
                                    orderable: false,
                                    searchable: false
                                }]
                            });
                        }

                        if (json.pagination) {
                            currentPage = json.pagination.current_page;
                            lastPage = json.pagination.last_page;
                            updatePaginationUI(json.pagination);
                        }

                        updateDetailsColumnVisibility();
                    },
                    error: function(xhr) {
                        console.error('credit-note-items-api error:', xhr.responseText);
                    }
                });
            }

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
                $('.pagination-controls').toggle(pagination.total > 0);
            }

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    loadCreditNoteItems(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                loadCreditNoteItems(1);
            });

            // Handle search input
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadCreditNoteItems(1);
                }, 400);
            });

            // Initialize
            loadCreditNoteItems(1);

            $(window).on('resize', updateDetailsColumnVisibility);

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

            // Handle delete action
            $(document).on("click", ".delete-item", function() {
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
                            url: `/api/credit-note-items-api/delete/${id}`,
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43"
                                }).then(() => {
                                    loadCreditNoteItems(currentPage);
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = "Something went wrong. Try again.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
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
