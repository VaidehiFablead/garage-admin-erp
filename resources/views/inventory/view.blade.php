@extends('layout.app')

@section('title', 'Inventory List')

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
        display: none;
    }

    /* Responsive breakpoints for all screen sizes */

    /* Extra small devices (phones, less than 576px) */
    @media screen and (max-width: 575.98px) {
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

        /* Show only Type and Details - hide Initial Stock, Current Stock, Quantity, Create By, Date */
        .table thead th:nth-child(2),
        .table tbody td:nth-child(2),
        .table thead th:nth-child(3),
        .table tbody td:nth-child(3),
        .table thead th:nth-child(4),
        .table tbody td:nth-child(4),
        .table thead th:nth-child(5),
        .table tbody td:nth-child(5),
        .table thead th:nth-child(6),
        .table tbody td:nth-child(6) {
            display: none;
        }

        /* Center Details column (7th column) */
        .table thead th:nth-child(7),
        .table tbody td:nth-child(7) {
            text-align: center;
            width: 60px;
            min-width: 60px;
        }

        .history-toggle-btn-table {
            margin: 0 auto;
            display: block;
        }
    }

    /* Small devices (landscape phones, 576px and up) */
    @media screen and (min-width: 576px) and (max-width: 767.98px) {
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

        /* Show Type, Current Stock, Quantity, Details - hide Initial Stock, Create By, Date */
        .table thead th:nth-child(2),
        .table tbody td:nth-child(2),
        .table thead th:nth-child(5),
        .table tbody td:nth-child(5),
        .table thead th:nth-child(6),
        .table tbody td:nth-child(6) {
            display: none;
        }

        /* Center Details column (7th column) */
        .table thead th:nth-child(7),
        .table tbody td:nth-child(7) {
            text-align: center;
            width: 60px;
            min-width: 60px;
        }

        .history-toggle-btn-table {
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

        /* Hide Details column (7th column) on tablets */
        .table thead th:nth-child(7),
        .table tbody td:nth-child(7) {
            display: none;
        }

        /* Hide expandable rows on tablets */
        .history-details-row {
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

        /* Hide Details column (7th column) on 1024px and above */
        .table thead th:nth-child(7),
        .table tbody td:nth-child(7) {
            display: none;
        }

        /* Hide expandable rows on larger screens */
        .history-details-row {
            display: none !important;
        }
    }

    /* Expandable row details - available for all screen sizes */
    .history-details-row {
        display: none;
    }

    .history-details-row.show {
        display: table-row;
    }

    /* Expandable content styles */
    .history-details-content {
        padding: 15px;
        background: #fff;
        border-top: 2px solid #e0e0e0;
    }

    .history-details-list {
        margin-bottom: 15px;
    }

    .history-detail-row-simple {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .history-detail-row-simple:last-of-type {
        border-bottom: none;
    }

    .history-detail-label-simple {
        font-weight: 600;
        color: #595b5d;
        font-size: 14px;
    }

    .history-detail-value-simple {
        color: #1b2850;
        font-size: 14px;
        text-align: right;
    }

    /* Toggle button styles */
    .history-toggle-btn-table {
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

    .history-toggle-btn-table:hover {
        background: #ff8c2e;
    }

    .history-toggle-btn-table.minus {
        background: #dc3545;
    }

    .history-toggle-btn-table.minus:hover {
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
@endif

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>View Inventory</h4>
        </div>
        <div class="page-btn">
            <a href="{{ route('inventory.list') }}" class="btn btn-sm btn-added">Back</a>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-scroll-top">
                <div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="history-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Initial Stock</th>
                            <th>Current Stock</th>
                            <th>Quantity</th>
                            <th>Create By</th>
                            <th>Date</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

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
    // Global variables
    var historyTable;
    var currentPage = 1;
    var lastPage = 1;
    var perPage = 10;

    // Helper function to build expandable row content
    function buildHistoryExpandableRowContent(item) {
        return `
                <td colspan="7" class="history-details-content">
                    <div class="history-details-list">
                        <div class="history-detail-row-simple">
                            <span class="history-detail-label-simple">Quantity:</span>
                            <span class="history-detail-value-simple">${item.signedQty || 'N/A'}</span>
                        </div>
                        <div class="history-detail-row-simple">
                            <span class="history-detail-label-simple">Initial Stock:</span>
                            <span class="history-detail-value-simple">${item.initialStock || 'N/A'}</span>
                        </div>
                        <div class="history-detail-row-simple">
                            <span class="history-detail-label-simple">Current Stock:</span>
                            <span class="history-detail-value-simple">${item.currentStock || 'N/A'}</span>
                        </div>
                        <div class="history-detail-row-simple">
                            <span class="history-detail-label-simple">Create By:</span>
                            <span class="history-detail-value-simple">${item.createdBy || 'N/A'}</span>
                        </div>
                        <div class="history-detail-row-simple">
                            <span class="history-detail-label-simple">Date:</span>
                            <span class="history-detail-value-simple">${item.date || 'N/A'}</span>
                        </div>
                    </div>
                </td>
            `;
    }

    // Toggle function for table rows - must be global
    window.toggleHistoryRowDetails = function(itemIndex) {
        const btn = $(`.history-toggle-btn-table[data-item-index="${itemIndex}"]`);
        if (btn.length === 0) {
            console.error('Toggle button not found for item:', itemIndex);
            return;
        }

        const row = btn.closest('tr');
        let detailsRow = row.next(`tr.history-details-row[data-item-index="${itemIndex}"]`);
        const icon = btn.find('.toggle-icon');

        // If expandable row doesn't exist, create it
        if (detailsRow.length === 0) {
            const itemData = window.historyDataMap && window.historyDataMap[itemIndex];
            if (itemData) {
                detailsRow = $('<tr>')
                    .addClass('history-details-row')
                    .attr('data-item-index', itemIndex)
                    .html(buildHistoryExpandableRowContent(itemData));
                row.after(detailsRow);
            } else {
                console.error('History data not found for item:', itemIndex);
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
    window.addHistoryExpandableRows = function(dt) {
        if (!dt) return;

        const currentWidth = $(window).width();
        const isMobileOrTablet = currentWidth < 1024;

        if (!isMobileOrTablet) {
            // Remove expandable rows on desktop
            $('tr.history-details-row').remove();
            return;
        }

        dt.rows().every(function() {
            const row = this.node();
            const toggleBtn = $(row).find('.history-toggle-btn-table');
            if (toggleBtn.length > 0) {
                const itemIndex = toggleBtn.data('item-index');
                const itemData = window.historyDataMap && window.historyDataMap[itemIndex];
                if (itemData && !$(row).next('tr.history-details-row[data-item-index="' + itemIndex + '"]').length) {
                    const expandableRow = $('<tr>')
                        .addClass('history-details-row')
                        .attr('data-item-index', itemIndex)
                        .html(buildHistoryExpandableRowContent(itemData));
                    $(row).after(expandableRow);
                }
            }
        });
    };

    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");

        // Get current product ID from URL
        let url = window.location.href;
        let id = url.substring(url.lastIndexOf('/') + 1);

        // ✅ Initialize DataTable
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

        historyTable = $('#history-table').DataTable({
            responsive: true,
            searching: false,
            paging: false,
            info: false,
            autoWidth: false,
            ordering: false,
            dom: 't'
        });

        // Set table reference
        var table = historyTable;


        // ✅ Fetch Inventory History
        function fetchinventoryHistory(productId, page = 1) {
            $.ajax({
                url: "/api/getQuantityHistory_inventory/" + productId,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                data: {
                    page: page,
                    per_page: perPage
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
                        let history = response.history || [];
                        let tableBody = [];

                        // Store history data for expandable rows
                        if (!window.historyDataMap) {
                            window.historyDataMap = {};
                        }

                        window.historyDataMap = {};

                        $.each(history, function(index, item) {
                            let rowIndex = `${currentPage}-${index}`;
                            let initialStock = item.initial_stock ?? '-';
                            let currentStock = item.current_stock ?? '-';
                            let type = item.type ?? 'System';
                            let createdBy = item.name ?? 'System';

                            // Parse numeric initial/current if present
                            let numInitial = (item.initial_stock !== undefined && item.initial_stock !== null && item.initial_stock !== '-') ? Number(item.initial_stock) : NaN;
                            let numCurrent = (item.current_stock !== undefined && item.current_stock !== null && item.current_stock !== '-') ? Number(item.current_stock) : NaN;

                            // Compute quantity: prefer difference between initial and current when both are numbers
                            let qtyNum = 0;
                            if (!isNaN(numInitial) && !isNaN(numCurrent)) {
                                qtyNum = Math.abs(numCurrent - numInitial);
                            } else {
                                // Fallback to explicit quantity fields if difference isn't available
                                let rawQty = item.quantity ?? item.qty ?? item.change ?? item.qty_change ?? 0;
                                qtyNum = Math.abs(Number(rawQty)) || 0;
                            }

                            // Decide sign based on type (sale => -, purchase => +). If type unknown, infer from initial/current.
                            let signedQty = '';
                            let typeLower = String(type).toLowerCase();
                            if (typeLower.includes('sale')) {
                                signedQty = '-' + qtyNum;
                            } else if (typeLower.includes('purchase')) {
                                signedQty = '+' + qtyNum;
                            } else if (!isNaN(numInitial) && !isNaN(numCurrent)) {
                                if (numCurrent > numInitial) signedQty = '+' + qtyNum;
                                else if (numCurrent < numInitial) signedQty = '-' + qtyNum;
                                else signedQty = '+' + qtyNum;
                            } else {
                                signedQty = '+' + qtyNum;
                            }
                            let date = item.date ?? '-';

                            // Store history data for expandable row
                            const historyData = {
                                signedQty: signedQty,
                                initialStock: initialStock,
                                currentStock: currentStock,
                                createdBy: createdBy,
                                date: date
                            };
                            window.historyDataMap[rowIndex] = historyData;

                            // Toggle button for Details column
                            let detailsColumn = `
                                <button class="history-toggle-btn-table" onclick="toggleHistoryRowDetails('${rowIndex}')" data-item-index="${rowIndex}">
                                    <span class="toggle-icon">+</span>
                                </button>
                            `;

                            // Push each row
                            tableBody.push([
                                type,
                                initialStock,
                                currentStock,
                                signedQty,
                                createdBy,
                                date,
                                detailsColumn
                            ]);

                        });

                        if (historyTable) {
                            historyTable.clear().rows.add(tableBody).draw();
                            historyTable.off('draw').on('draw', function() {
                                if (window.addHistoryExpandableRows) {
                                    window.addHistoryExpandableRows(historyTable);
                                }
                            });
                            setTimeout(function() {
                                if (window.addHistoryExpandableRows) {
                                    window.addHistoryExpandableRows(historyTable);
                                }
                            }, 100);
                        } else {
                            table.clear().rows.add(tableBody).draw();
                        }
                        if (!history || history.length === 0) {
                            $('.pagination-controls').hide();
                            $('#pagination-from').text(0);
                            $('#pagination-to').text(0);
                            $('#pagination-total').text(0);
                        }
                    } else {
                        table.clear().draw();
                        $('.table tbody').html(
                            '<tr><td colspan="7" class="text-center">No inventory history found</td></tr>'
                        );
                        $('.pagination-controls').hide();
                        $('#pagination-from').text(0);
                        $('#pagination-to').text(0);
                        $('#pagination-total').text(0);
                    }
                },
                error: function() {
                    alert("Error fetching inventory history.");
                }
            });
        }

        // Load data
        fetchinventoryHistory(id, 1);

        $('#per-page-select').on('change', function() {
            perPage = parseInt($(this).val(), 10) || 10;
            fetchinventoryHistory(id, 1);
        });

        $(document).on('click', '#pagination-numbers .page-link', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                fetchinventoryHistory(id, page);
            }
        });

        // Resize handler for responsive behavior
        let resizeTimer;
        let lastWidth = $(window).width();

        function forceHistoryCSSRecalculation() {
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

        function handleHistoryResize() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const currentWidth = $(window).width();
                lastWidth = currentWidth;

                // Force CSS recalculation
                forceHistoryCSSRecalculation();

                const table = document.getElementById('history-table');
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

                if (historyTable && table) {
                    $('tr.history-details-row').remove();

                    try {
                        historyTable.columns.adjust();
                        historyTable.draw(false);

                        setTimeout(function() {
                            historyTable.columns.adjust();
                            historyTable.draw(false);

                            setTimeout(function() {
                                if (window.addHistoryExpandableRows) {
                                    window.addHistoryExpandableRows(historyTable);
                                }
                                forceHistoryCSSRecalculation();
                                void table.offsetHeight;
                            }, 100);
                        }, 100);
                    } catch (e) {
                        console.error('DataTables adjustment error:', e);
                        historyTable.draw(false);
                        setTimeout(function() {
                            if (window.addHistoryExpandableRows) {
                                window.addHistoryExpandableRows(historyTable);
                            }
                            forceHistoryCSSRecalculation();
                        }, 150);
                    }
                } else {
                    forceHistoryCSSRecalculation();
                }
            }, 50);
        }

        // Window resize handler
        $(window).off('resize.history').on('resize.history', handleHistoryResize);

        if (window.historyResizeHandler) {
            window.removeEventListener('resize', window.historyResizeHandler);
        }
        window.historyResizeHandler = handleHistoryResize;
        window.addEventListener('resize', window.historyResizeHandler, {
            passive: true
        });

        // Orientation change handler
        $(window).off('orientationchange.history').on('orientationchange.history', function() {
            setTimeout(function() {
                lastWidth = $(window).width();
                handleHistoryResize();
            }, 300);
        });

        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                lastWidth = $(window).width();
                handleHistoryResize();
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
                    setTimeout(handleHistoryResize, 100);
                });
            } else if (query.addListener) {
                query.addListener(function() {
                    setTimeout(handleHistoryResize, 100);
                });
            }
        });

        // Initial width set and call
        lastWidth = $(window).width();

        $(window).on('load', function() {
            setTimeout(function() {
                lastWidth = $(window).width();
                handleHistoryResize();
            }, 500);
        });

        setTimeout(function() {
            if (historyTable) {
                handleHistoryResize();
            }
        }, 1000);

        window.handleHistoryResize = handleHistoryResize;
    });
</script>
@endpush
