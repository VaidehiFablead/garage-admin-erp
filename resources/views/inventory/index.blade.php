@extends('layout.app')

@section('title', 'Inventory List')

@section('content')
    <style>
        .table tbody tr td {
            white-space: normal !important;
        }

        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 20px;
            width: 100%;
            margin-bottom: 5px;
            display: none;
        }

        .table-scroll-top div {
            height: 1px;
        }

        /* Responsive breakpoints for all screen sizes */

        /* Extra small devices (phones, less than 576px) */
        @media screen and (max-width: 575.98px) {
            /* Filter Section Mobile Styles */
            /* .card-body > .d-flex { */
            /* flex-direction: column !important; */
            /* gap: 10px !important; */
            /* } */

            .search-set {
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }

            .search-set.d-flex.mt-2 {
                width: 100% !important;
                margin: 0 0 15px 0 !important;
            }

            .search-set.d-flex.mt-2>.d-flex {
                flex-direction: column !important;
                width: 100% !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex>div {
                width: 100% !important;
            }

            .search-set.d-flex.mt-2 label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .search-set.d-flex.mt-2 input[type="date"] {
                width: 100% !important;
                font-size: 14px;
                padding: 8px;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 6px 3px;
            }

            /* Show only Product Name and Details */
            .table thead th:nth-child(3),
            .table tbody td:nth-child(3),
            .table thead th:nth-child(4),
            .table tbody td:nth-child(4),
            .table thead th:nth-child(5),
            .table tbody td:nth-child(5),
            .table thead th:nth-child(6),
            .table tbody td:nth-child(6),
            .table thead th:nth-child(7),
            .table tbody td:nth-child(7) {
                display: none;
            }

            /* Center Details column */
            .table thead th:nth-child(2),
            .table tbody td:nth-child(2) {
                text-align: center;
                width: 60px;
                min-width: 60px;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }

            select#filterCategory,
            select#filterBrand {
                font-size: 12px !important;
            }

            .form-control-sm {
                min-height: calc(1.5em + .5rem + 2px);
                padding: .25rem .5rem;
                font-size: 13px !important;
                border-radius: .2rem;
            }

            div#DataTables_Table_0_filter {
                margin-top: 10px !important;
            }

            .table-top {
                flex-direction: row;
                margin-bottom: 0 !important;
            }

            .dataTables_length {
                margin-left: .8rem !important;
                margin-bottom: .1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }

        /* Small devices (landscape phones, 576px and up) */
        @media screen and (min-width: 576px) and (max-width: 767.98px) {

            /* Filter Section Small Mobile Styles */
            .card-body>.d-flex {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex {
                flex-direction: row !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex>div {
                flex: 1;
                min-width: 0;
            }

            .search-set.d-flex.mt-2 input[type="date"] {
                width: 100% !important;
                font-size: 14px;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 4px;
            }

            /* Show Product Name, Details, SKU, Price */
            .table thead th:nth-child(5),
            .table tbody td:nth-child(5),
            .table thead th:nth-child(6),
            .table tbody td:nth-child(6),
            .table thead th:nth-child(7),
            .table tbody td:nth-child(7) {
                display: none;
            }

            /* Center Details column */
            .table thead th:nth-child(2),
            .table tbody td:nth-child(2) {
                text-align: center;
                width: 60px;
                min-width: 60px;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }
        }

        /* Medium devices (tablets, 768px and up to 1023px) */
        @media screen and (min-width: 768px) and (max-width: 1023px) {
            .table-responsive {
                display: block !important;
                overflow-x: auto;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 13px;
            }

            .table th,
            .table td {
                padding: 8px 6px;
            }

            /* Hide Details column (2nd column) on tablets */
            .table thead th:nth-child(2),
            .table tbody td:nth-child(2) {
                display: none;
            }

            /* Hide expandable rows on tablets */
            .inventory-details-row {
                display: none !important;
            }
        }

        /* Large devices (desktops, 1024px and up) */
        @media screen and (min-width: 1024px) {
            .table-responsive {
                display: block !important;
            }

            .table {
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 12px 10px;
            }

            /* Hide Details column (2nd column) on 1025px and above */
            .table thead th:nth-child(2),
            .table tbody td:nth-child(2) {
                display: none;
            }

            /* Hide expandable rows on larger screens */
            .inventory-details-row {
                display: none !important;
            }
        }

        /* Expandable row details - available for all screen sizes */
        .inventory-details-row {
            display: none;
        }

        .inventory-details-row.show {
            display: table-row;
        }

        /* Expandable content styles */
        .inventory-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .inventory-details-list {
            margin-bottom: 15px;
        }

        .inventory-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .inventory-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .inventory-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .inventory-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        .inventory-action-buttons-simple {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        /* Toggle button styles */
        .inventory-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .inventory-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .inventory-toggle-btn-table.minus {
            background: #dc3545;
        }

        .inventory-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        .modal-content {
            width: 80% !important;
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

    <!-- Quantity History Modal -->
    <div class="modal fade" id="quantityHistoryModal" tabindex="-1" aria-labelledby="quantityHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityHistoryLabel">Quantity History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="quantityHistoryContent">
                    <!-- History content will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Inventory</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('product.add') }}" class="btn btn-sm btn-added"><img
                        src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1">New
                    Product</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="justify-content-between align-items-center">
                    <div class="search-set d-flex mt-2 mx-2">
                        <div class="search-path"></div>
                        <div class="search-input d-flex align-items-center">
                            <input type="text" id="search-input" class="form-control" placeholder="   Search...">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                        </div>
                    </div>

                    {{-- <div class="search-set d-flex mt-2 mx-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <label for="">Start Date</label>
                                <input type="date" id="startDate" class="form-control form-control-sm">
                            </div>
                            <div>
                                <label for="">End Date</label>
                                <input type="date" id="endDate" class="form-control form-control-sm">
                            </div>

                        </div>
                    </div> --}}
                    <div class="row g-2 justify-content-lg-end mb-2">
                        <div class="col-6 col-lg-2">
                            <label>Start Date</label>
                            <input type="date" id="startDate" class="form-control form-control-sm">
                        </div>

                        <div class="col-6 col-lg-2">
                            <label>End Date</label>
                            <input type="date" id="endDate" class="form-control form-control-sm">
                        </div>
                    </div>

                </div>

                <div class="table-scroll-top">
                    <div></div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="inventory-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Details</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Initial Stock</th>
                                <th>Current Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Form will be loaded dynamically -->
                    <form id="editProductForm">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" id="editProductName" readonly>
                        </div>
                        <input type="hidden" name="sku" id="editProductSku">
                        <input type="hidden" name="price" id="editProductPrice">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Initial Stock</label>
                                <input type="number" class="form-control" name="initial_stock" id="editProductinitialStock"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Stock</label>
                                <input type="number" class="form-control" name="current_stock" id="editProductStock"
                                    readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Update</label>
                            <div>
                                <input type="radio" name="stock_action" id="addStock" value="add">
                                <label for="addStock">Add Quantity</label>

                                <input type="radio" name="stock_action" id="minusStock" value="minus"
                                    class="ms-3">
                                <label for="minusStock">Minus Quantity</label>
                            </div>
                            <div id="stockError" class="text-danger mb-2" style="display:none;"></div>
                        </div>

                        <!-- Hidden textbox for entering quantity -->
                        <div class="mb-3 d-none" id="stockQuantityBox">
                            <label class="form-label" id="stockQuantityLabel">Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" id="stockQuantity">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        // Global variables
        var inventoryTable;
        var currentPage = 1;
        var lastPage = 1;
        var perPage = 10;
        var searchQuery = '';

        function formatInventoryPrice(value) {
            if (value === null || value === undefined || value === '' || value === 'N/A') {
                return 'N/A';
            }

            const numericValue = Number(value);
            if (Number.isNaN(numericValue)) {
                return value;
            }

            return numericValue.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Helper function to build expandable row content
        function buildInventoryExpandableRowContent(product) {
            let actionBtns = `
                <a class="btn btn-sm btn-primary me-2" href="/inventory-View/${product.id}" style="color: white;font-size: 13px;">
                    View History
                </a>
                <a class="btn btn-sm btn-primary edit-product" data-id="${product.id}" href="javascript:void(0);" style="color: white;font-size: 13px;">
                    Add / Edit Stock
                </a>
            `;

            return `
                <td colspan="7" class="inventory-details-content">
                    <div class="inventory-details-list">
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">SKU:</span>
                            <span class="inventory-detail-value-simple">${product.sku || 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Price:</span>
                            <span class="inventory-detail-value-simple">${formatInventoryPrice(product.price)}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Initial Stock:</span>
                            <span class="inventory-detail-value-simple">${product.initial_stock ?? 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Current Stock:</span>
                            <span class="inventory-detail-value-simple">${product.current_stock ?? 'N/A'}</span>
                        </div>
                    </div>
                    <div class="inventory-action-buttons-simple">
                        ${actionBtns}
                    </div>
                </td>
            `;
        }

        // Toggle function for table rows - must be global
        window.toggleInventoryRowDetails = function(productId) {
            const btn = $(`.inventory-toggle-btn-table[data-product-id="${productId}"]`);
            if (btn.length === 0) {
                console.error('Toggle button not found for product:', productId);
                return;
            }

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.inventory-details-row[data-product-id="${productId}"]`);
            const icon = btn.find('.toggle-icon');

            // If expandable row doesn't exist, create it
            if (detailsRow.length === 0) {
                const productData = window.inventoryDataMap && window.inventoryDataMap[productId];
                if (productData) {
                    detailsRow = $('<tr>')
                        .addClass('inventory-details-row')
                        .attr('data-product-id', productId)
                        .html(buildInventoryExpandableRowContent(productData));
                    row.after(detailsRow);
                } else {
                    console.error('Product data not found for product:', productId);
                    return;
                }
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
        }

        // Function to add expandable rows - must be global
        window.addInventoryExpandableRows = function(dt) {
            if (!dt) return;

            const currentWidth = $(window).width();
            const isMobileOrTablet = currentWidth < 1024;

            if (!isMobileOrTablet) {
                // Remove expandable rows on desktop
                $('tr.inventory-details-row').remove();
                return;
            }

            dt.rows().every(function() {
                const row = this.node();
                const toggleBtn = $(row).find('.inventory-toggle-btn-table');
                if (toggleBtn.length > 0) {
                    const productId = toggleBtn.data('product-id');
                    const productData = window.inventoryDataMap && window.inventoryDataMap[productId];
                    if (productData && !$(row).next('tr.inventory-details-row[data-product-id="' + productId +
                            '"]').length) {
                        const expandableRow = $('<tr>')
                            .addClass('inventory-details-row')
                            .attr('data-product-id', productId)
                            .html(buildInventoryExpandableRowContent(productData));
                        $(row).after(expandableRow);
                    }
                }
            });
        };

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            inventoryTable = null;

            function updatePaginationUI(pagination) {
                let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

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

            window.loadInventory = function(page = 1) {
                const categoryId = $('#filterCategory').val();
                const brandId = $('#filterBrand').val();
                const subBranchId = localStorage.getItem('selectedSubAdminId');
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();

                $.ajax({
                    url: "/api/inventory-list",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        category_id: categoryId,
                        brand_id: brandId,
                        sub_branch_id: subBranchId,
                        start_date: startDate || '',
                        end_date: endDate || ''
                    },
                    success: function(response) {
                        if (response.pagination) {
                            currentPage = response.pagination.current_page;
                            lastPage = response.pagination.last_page;
                            updatePaginationUI(response.pagination);
                        } else {
                            $('.pagination-controls').hide();
                        }

                        if (response.status) {
                            let products = response.inventory || [];
                            let tableBody = [];

                            // Store product data for expandable rows
                            if (!window.inventoryDataMap) {
                                window.inventoryDataMap = {};
                            }

                            $.each(products, function(index, product) {
                                let productName = capitalizeWords(product.product_name || 'N/A');
                                let sku = product.sku || 'N/A';
                                let rawPrice = product.price;
                                let price = formatInventoryPrice(rawPrice);
                                let initialStock = product.initial_stock ?? 'N/A';
                                let currentStock = product.current_stock ?? 'N/A';
                                let id = product.id;

                                // Store product data for expandable row
                                const productData = {
                                    ...product,
                                    sku: sku,
                                    price: rawPrice,
                                    initial_stock: initialStock,
                                    current_stock: currentStock
                                };
                                window.inventoryDataMap[id] = productData;

                                // Low stock badge
                                let stockDisplay =
                                    `<span class="quantity-history" data-id="${id}" style="cursor:pointer;">${currentStock}</span>`;
                                if (currentStock !== 'N/A' && parseInt(currentStock) <= response
                                    .lowStockThreshold) {
                                    stockDisplay +=
                                        ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                }

                                // Action buttons
                                let actionBtns = `
        <a class="btn btn-primary" href="/inventory-View/${id}" style="color: white;font-size: 13px;">View History</a>
        <a class="edit-product btn btn-primary" data-id="${id}" href="javascript:void(0);" style="color: white;font-size: 13px;margin-top:5px;">Add / Edit Stock</a>
    `;

                                // Toggle button for Details column
                                let detailsColumn = `
        <button class="inventory-toggle-btn-table" onclick="toggleInventoryRowDetails('${id}')" data-product-id="${id}">
            <span class="toggle-icon">+</span>
        </button>
    `;

                                // ✅ Match thead columns exactly (7 columns)
                                tableBody.push([
                                    productName,
                                    detailsColumn,
                                    sku,
                                    price,
                                    initialStock,
                                    stockDisplay,
                                    actionBtns
                                ]);
                            });

                            if (inventoryTable) {
                                inventoryTable.clear().rows.add(tableBody).draw();
                                inventoryTable.off('draw').on('draw', function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                });
                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                }, 100);
                            }

                            if (!products || products.length === 0) {
                                $('.pagination-controls').hide();
                                $('#pagination-from').text(0);
                                $('#pagination-to').text(0);
                                $('#pagination-total').text(0);
                            }

                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const table = document.getElementById('inventory-table');
                            if (topScroll && tableResponsive && table) {
                                topScroll.querySelector('div').style.width = table.scrollWidth + 'px';
                                topScroll.onscroll = () => {
                                    tableResponsive.scrollLeft = topScroll.scrollLeft;
                                };
                                tableResponsive.onscroll = () => {
                                    topScroll.scrollLeft = tableResponsive.scrollLeft;
                                };
                            }
                        } else {
                            if (inventoryTable) {
                                inventoryTable.clear().draw();
                            }
                            $(".table tbody").html('<tr><td colspan="7">No inventory found</td></tr>');
                            $('.pagination-controls').hide();
                            $('#pagination-from').text(0);
                            $('#pagination-to').text(0);
                            $('#pagination-total').text(0);
                        }
                    },
                    error: function() {
                        alert("Error fetching inventory.");
                    }
                });
            };

            // Handle search input
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    loadInventory(1);
                }, 400);
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = parseInt($(this).val(), 10) || 10;
                loadInventory(1);
            });

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    loadInventory(page);
                }
            });

            // Listen for changes on startDate and endDate
            $(document).on("change", "#startDate, #endDate", function() {
                let startDate = $("#startDate").val();
                let endDate = $("#endDate").val();

                if (!startDate || !endDate) {
                    loadInventory(1);
                    return;
                }

                loadInventory(1);
            });


            // Capitalize helper
            function capitalizeWords(str) {
                if (!str || str.trim() === '') return 'N/A';
                return str.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }
            const stockBox = $("#stockQuantityBox");
            const stockLabel = $("#stockQuantityLabel");

            $("#addStock").on("change", function() {
                stockBox.removeClass("d-none");
                stockLabel.text("How much to Add?");
            });

            $("#minusStock").on("change", function() {
                stockBox.removeClass("d-none");
                stockLabel.text("How much to Minus?");
            });

            // ✅ Initialize DataTable
            inventoryTable = $('#inventory-table').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false,
                autoWidth: false,
                ordering: false,
                dom: 't'
            });

            // ✅ Load products on page load
            loadInventory(1);

            // ✅ Reload when filters change
            $('#filterCategory, #filterBrand').on('change', function() {
                loadInventory(1);
            });
            // ✅ Open Edit Modal
            $(document).on("click", ".edit-product", function() {
                var authToken = localStorage.getItem("authToken");
                let productId = $(this).data("id");

                // alert(productId)
                // Fetch product details from API
                $.ajax({
                    url: "/api/products_edit_inventroy/" + productId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            let product = response.product;
                            console.log(product);

                            // Fill modal fields
                            $("#editProductId").val(product.id);
                            $("#editProductName").val(product.product_name);
                            $("#editProductSku").val(product.sku);
                            $("#editProductPrice").val(product.price);
                            $("#editProductinitialStock").val(product.initial_stock);
                            $("#editProductStock").val(product.current_stock);

                            // Show modal
                            $("#editProductModal").modal("show");
                        } else {
                            alert("Product not found!");
                        }
                    }
                });
            });

            // ✅ Handle Save Changes
            // $("#editProductForm").on("submit", function(e) {
            //     e.preventDefault();

            //     let productId = $("#editProductId").val();
            //     const sub_branch_id = localStorage.getItem('selectedSubAdminId');
            //     var authToken = localStorage.getItem("authToken");
            //     let stockAction = $("input[name='stock_action']:checked").val(); // 'add' or 'minus'
            //     let stockQuantity = $("#stockQuantity").val(); // the textbox value
            //     let formData = {
            //         name: $("#editProductName").val(),
            //         sku: $("#editProductSku").val(),
            //         price: $("#editProductPrice").val(),
            //         current_stock: $("#editProductStock").val(),
            //         initial_stock: $("#editProductinitialStock").val(),
            //         sub_branch_id: sub_branch_id,
            //         stock_action: stockAction, // <-- add this
            //         stock_quantity: stockQuantity // <-- and this
            //     };

            //     $.ajax({
            //         url: "/api/inventory_update/" + productId,
            //         type: "POST",
            //         headers: {
            //             "Authorization": "Bearer " + authToken
            //         },
            //         data: formData,
            //         success: function(response) {
            //             if (response.status) {
            //                 $("#editProductModal").modal("hide");
            //                 fetchinventory(); // refresh table
            //             } else {
            //                 alert("Update failed!");
            //             }
            //         }
            //     });
            // });
            $("#editProductForm").on("submit", function(e) {
                e.preventDefault();

                let productId = $("#editProductId").val();
                let stockAction = $("input[name='stock_action']:checked").val();
                let stockQuantity = $("#stockQuantity").val();

                $("#stockError").hide().text("");

                // Validate radio
                if (!stockAction) {
                    $("#stockError").text("Please select Add Quantity or Minus Quantity").show();
                    return;
                }

                // Validate quantity
                if (!stockQuantity || stockQuantity <= 0) {
                    $("#stockError").text("Please enter quantity").show();
                    return;
                }

                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                var authToken = localStorage.getItem("authToken");

                let formData = {
                    name: $("#editProductName").val(),
                    sku: $("#editProductSku").val(),
                    price: $("#editProductPrice").val(),
                    current_stock: $("#editProductStock").val(),
                    initial_stock: $("#editProductinitialStock").val(),
                    sub_branch_id: sub_branch_id,
                    stock_action: stockAction,
                    stock_quantity: stockQuantity
                };

                $.ajax({
                    url: "/api/inventory_update/" + productId,
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: formData,
                    success: function(response) {

                        if (response.status) {

                            $("#editProductModal").modal("hide");

                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Quantity successfully updated",
                                confirmButtonColor: "#ff9f43"
                            }).then(() => {
                                location.reload();
                            });

                        } else {

                            $("#stockError").text("Update failed").show();

                        }

                    }
                });

            });

            // Resize handler for responsive behavior
            let resizeTimer;
            let lastWidth = $(window).width();

            function forceInventoryCSSRecalculation() {
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                void window.innerWidth;
                void window.innerHeight;
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            function handleInventoryResize() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastWidth = currentWidth;

                    // Force CSS recalculation
                    forceInventoryCSSRecalculation();

                    const table = document.getElementById('inventory-table');
                    const tableResponsive = document.querySelector('.table-responsive');

                    [table, tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    if (inventoryTable && table) {
                        $('tr.inventory-details-row').remove();

                        try {
                            inventoryTable.columns.adjust();
                            inventoryTable.draw(false);

                            setTimeout(function() {
                                inventoryTable.columns.adjust();
                                inventoryTable.draw(false);

                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                    forceInventoryCSSRecalculation();
                                    void table.offsetHeight;
                                }, 100);
                            }, 100);
                        } catch (e) {
                            console.error('DataTables adjustment error:', e);
                            inventoryTable.draw(false);
                            setTimeout(function() {
                                if (window.addInventoryExpandableRows) {
                                    window.addInventoryExpandableRows(inventoryTable);
                                }
                                forceInventoryCSSRecalculation();
                            }, 150);
                        }
                    } else {
                        forceInventoryCSSRecalculation();
                    }
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.inventory').on('resize.inventory', handleInventoryResize);

            if (window.inventoryResizeHandler) {
                window.removeEventListener('resize', window.inventoryResizeHandler);
            }
            window.inventoryResizeHandler = handleInventoryResize;
            window.addEventListener('resize', window.inventoryResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.inventory').on('orientationchange.inventory', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const queries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1023px)'),
                window.matchMedia('(min-width: 1024px)')
            ];

            queries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            setTimeout(function() {
                if (inventoryTable) {
                    handleInventoryResize();
                }
            }, 1000);

            window.handleInventoryResize = handleInventoryResize;

        });
    </script>
@endpush
