@extends('layout.app')

@section('title', 'Debit Note Items List')

@push('css')
<style>
    .table tbody tr td {
        white-space: normal !important;
        word-break: break-word;
        word-wrap: break-word;
    }
    table#DataTables_Table_0 {
        width: 100% !important;
    }

    .datatable thead th:nth-child(2),
    .datatable tbody td:nth-child(2),
    .datatable thead th.details-column,
    .datatable tbody td.details-control {
        display: none !important;
    }

    /* Responsive Table Styling */
    @media screen and (max-width: 767px) {
        .table-responsive {
            overflow-x: hidden !important;
        }
        
        .datatable {
            width: 100% !important;
            table-layout: fixed;
        }

        .datatable thead th:nth-child(n+3),
        .datatable tbody td:nth-child(n+3) {
            display: none !important;
        }

        .datatable thead th:nth-child(1),
        .datatable tbody td:nth-child(1) {
            display: table-cell !important;
            width: calc(100% - 50px) !important;
            word-wrap: break-word;
        }

        .datatable thead th:nth-child(2),
        .datatable tbody td:nth-child(2),
        .datatable thead th.details-column,
        .datatable tbody td.details-control {
            display: table-cell !important;
            text-align: center;
            vertical-align: middle;
            width: 50px !important;
        }
    }

    .toggle-details {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ff9f43;
        cursor: pointer;
    }

    .toggle-details i {
        color: #fff;
        font-size: 12px;
        line-height: 1;
    }

    .toggle-details.is-open {
        background: #dc3545;
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
    @media (max-width: 767px) {
        .page-header {
            display: block !important;
        }

        .page-header .page-title h4 {
            margin-bottom: 10px;
        }

        .page-header .d-flex.align-items-center.flex-wrap {
            width: 100%;
            justify-content: space-between;
            gap: 8px;
        }

        .page-header .d-flex.align-items-center.flex-wrap .me-2.mb-sm-0 {
            margin-right: 0 !important;
            margin-bottom: 0 !important;
        }

        .page-header .page-btn a {
            height: 35px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 420px) {
        .page-header .d-flex.align-items-center.flex-wrap {
            flex-wrap: nowrap !important;
        }

        .page-header .page-btn a {
            min-width: auto;
            padding: 6px 8px;
        }

        .page-header .page-btn a img {
            margin-right: 4px !important;
        }
    }

    
</style>
@endpush

@section('content')
<div class="content"> 
    <div class="page-header">
        <div class="page-title"> 
            <h4>Debit Note List</h4>
        </div>
        <div class="d-flex align-items-center">
            
            <div class="page-btn me-2 mb-sm-0 mt-0">
                <a href="{{ route('debit-notes-items.create') }}" class="btn btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1">Debit Note Item
                </a>
            </div>

            <div class="mb-sm-0">
                <div class="d-flex align-items-center" style="background-color: #f8f9fa; color: #ff9f43; font-weight: bold; padding: 7px 10px; border: 1px solid #d1d1d1; border-radius: 8px;">
                    <span style="color: #495057; margin-right: 8px; font-size: 14px; ">Total:</span>
                    <span style="font-size: 16px;">₹<span id="total_settlement_amount">0.00</span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <div class="search-input">
                        <input type="text" id="search-input" class="form-control" placeholder="   Search...">
                        <a class="btn btn-searchset">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Job Card / Invoice Number</th>
                            <th class="details-column">Details</th>
                            <th>Customer/ Vendor Name</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Settlement Amount</th>
                            <th>Final Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="debit_note_items_table_body">
                        {{-- Data will be loaded via AJAX --}}
                    </tbody>
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
$(document).ready(function () {
    var authToken = localStorage.getItem("authToken");
    var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
    let currencySymbol = "{{ $currencySymbol ?? '₹' }}";
    let currencyPosition = "{{ $currencyPosition ?? 'left' }}";

    // Pagination state
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let searchQuery = '';

    function formatCurrency(amount) {
        let formatted = parseFloat(amount || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currencyPosition === 'left' ? currencySymbol + formatted : formatted + currencySymbol;
    }
 
    if ($.fn.DataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable().destroy();
    }

    let table = $('.datatable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        order: [],
        columnDefs: [
            { targets: 1, className: 'details-control', orderable: false }
        ]
    });

    function updateDetailsColumnVisibility() {
        const isMobileView = window.matchMedia('(max-width: 767px)').matches;

        if (isMobileView) {
            table.column(0).visible(true, false);
            table.column(1).visible(true, false);
            table.columns([2, 3, 4, 5, 6, 7]).visible(false, false);
        } else {
            table.column(0).visible(true, false);
            table.column(1).visible(false, false);
            table.columns([2, 3, 4, 5, 6, 7]).visible(true, false);
        }

        table.columns.adjust().draw(false);
    }

    updateDetailsColumnVisibility();

    $(window).on('resize', function () {
        updateDetailsColumnVisibility();
    });

    // Toggle details icon
    $(document).on('click', '.toggle-details', function() {
        const $toggle = $(this);
        const $icon = $toggle.find('i');

        if ($icon.hasClass('fa-plus-circle')) {
            $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
        } else if ($icon.hasClass('fa-minus-circle')) {
            $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
        } else {
            $icon.toggleClass('fa-plus fa-minus');
        }

        $toggle.toggleClass('is-open');
    });
 
    function loadDebitNoteItems(page = 1) {
        let url = "{{ url('api/debit-note-items') }}";
        
        let params = {
            page: page,
            per_page: perPage,
            search: searchQuery
        };

        if (selectedSubAdminId && selectedSubAdminId !== 'null') {
            params.selectedSubAdminId = selectedSubAdminId;
        }
 
        $.ajax({
            url: url,
            type: "GET",
            data: params,
            headers: {
                "Authorization": "Bearer " + authToken
            },
            success: function (response) {
                if (response.status === 'success') {
                    table.clear();
                    
                    if (response.pagination) {
                        currentPage = response.pagination.current_page;
                        lastPage = response.pagination.last_page;
                        updatePaginationUI(response.pagination);
                    }
 
                    response.data.forEach(function (item, index) {
                        let displayName = 'N/A';
                        let transactionNumber = 'N/A';

                        if (item.transaction_type === 'receipt') {
                            if (item.job_card && item.job_card.customer) {
                                displayName = item.job_card.customer.name || 'N/A';
                            } else if (item.order && item.order.user) {
                                displayName = item.order.user.name || 'N/A';
                            }

                            if (item.job_card && item.job_card.job_card_number) {
                                transactionNumber = item.job_card.job_card_number;
                            } else if (item.order && item.order.order_number) {
                                transactionNumber = item.order.order_number;
                            }
                        } else {
                            displayName = (item.purchase_invoice && item.purchase_invoice.vendor) 
                                ? item.purchase_invoice.vendor.name 
                                : 'N/A';
                            transactionNumber = item.purchase_invoice ? item.purchase_invoice.invoice_number : 'N/A';
                        }
 
                        let typeName = item.credit_note_type 
                            ? item.credit_note_type.type_name 
                            : 'N/A';

                        let detailsId = `details-${index}`;
 
                        table.row.add([
                            `<div>
                                <span>${transactionNumber}</span>
                                <div class="collapse mt-2 d-lg-none" id="${detailsId}">
                                    <div class="collapse-details">
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
                                            <span class="detail-value">${formatCurrency(item.grand_total)}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Settlement Amount:</span>
                                            <span class="detail-value">${formatCurrency(item.settlement_amount)}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Final Total:</span>
                                            <span class="detail-value">${formatCurrency(item.total)}</span>
                                        </div>
                                        <div class="detail-item text-end">
                                            <a class="" href="{{ url('debit-note-items/view') }}/${item.id}">
                                                <img src="{{ env('ImagePath') . ('admin/assets/img/icons/eye.svg') }}">
                                            </a>
                                            <a class="" href="{{ url('debit-note-items/edit') }}/${item.id}">
                                                <img src="{{ env('ImagePath') . ('admin/assets/img/icons/edit.svg') }}">    
                                            </a>
                                            <a class="confirm-text delete-item" data-id="${item.id}">
                                                <img src="{{ env('ImagePath') . ('admin/assets/img/icons/delete.svg') }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>`,
                            `<a href="#${detailsId}" class="toggle-details" data-bs-toggle="collapse" style="color: #ff9f43;">
                                <i class="fas fa-plus-circle"></i>
                            </a>`,
                            displayName,
                            typeName,
                            formatCurrency(item.grand_total),
                            formatCurrency(item.settlement_amount),
                            formatCurrency(item.total),
                            `<div class="text-end">
                                <a class="" href="{{ url('debit-note-items/view') }}/${item.id}">
                                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/eye.svg') }}">
                                </a>
                                <a class="" href="{{ url('debit-note-items/edit') }}/${item.id}">
                                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/edit.svg') }}">    
                                </a>
                                <a class="confirm-text delete-item" data-id="${item.id}">
                                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/delete.svg') }}">
                                </a>
                            </div>`
                        ]);
                    });
 
                    $('#total_settlement_amount').text(parseFloat(response.total_settlement_amount || 0).toFixed(2));
                    table.draw();
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
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
        $('.pagination-controls').show();
    }

    // Handle page number clicks
    $(document).on('click', '#pagination-numbers .page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page && page !== currentPage && page >= 1 && page <= lastPage) {
            loadDebitNoteItems(page);
        }
    });

    // Handle per-page change
    $('#per-page-select').on('change', function() {
        perPage = $(this).val();
        loadDebitNoteItems(1);
    });

    // Handle search input
    let searchTimer;
    $('#search-input').on('keyup', function() {
        searchQuery = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadDebitNoteItems(1);
        }, 400);
    });
 
    loadDebitNoteItems(1);

    $(document).on('click', '.delete-item', function() {
        let id = $(this).data('id');
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
                    url: `/api/debit-note-items/delete/${id}`,
                    type: "POST",
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire("Deleted!", response.message, "success");
                            loadDebitNoteItems(currentPage);
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
