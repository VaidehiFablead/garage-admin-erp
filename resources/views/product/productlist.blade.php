@extends('layout.app')

@section('title', 'Item List')

@section('content')
@php
    $productListSetting = \Illuminate\Support\Facades\DB::table('settings')->first();
    $companyName = $productListSetting->name ?? $productListSetting->shop_name ?? 'AutoCare Garage & Service Center';
    $companyAddress = $productListSetting->address ?? $productListSetting->shop_address ?? '';
@endphp
    <style>

        table#DataTables_Table_0 {
            width: 100% !important;
        }
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        /* Hide default DataTables controls */
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
        /* .category-width{
            width: 207px;
        } */

        #filterCategory,
        #filterBrand {
            width: 200px !important;
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

        @media (min-width: 1200px) {
            .category-width{
                width: 207px;
            }
        }
        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
                /* smooth scrolling on iOS */
            }

            select#filterCategory {
                font-size: 12px !important;
            }

            select#filterBrand {
                font-size: 12px !important;
                width: 164px !important;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }

            .form-control-sm {
                min-height: calc(1.5em + .5rem + 2px);
                padding: .25rem .5rem;
                font-size: 13px !important;
                border-radius: .2rem;
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

            .toggle-details i {
                font-size: 18px;
            }

            /* Add these styles for product name wrapping */
            table.datanew tbody td:first-child {
                /* display: flex !important; */
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
                /* Adjust based on your layout */
            }

            table.datanew tbody td:first-child a {
                /* display: -webkit-box !important; */
                display: -ms-flexbox !important;
                /* display: flex !important; */
                -webkit-box-align: center !important;
                -ms-flex-align: center !important;
                align-items: center !important;
                text-align: left !important;
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.3 !important;
            }

            /* For the product name text specifically */
            table.datanew tbody td:first-child a[style*="font-weight: 500"] {
                display: inline-block !important;
                max-width: calc(100% - 70px) !important;
                /* Account for image width */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* If you want to limit to 2 lines with ellipsis */
            table.datanew tbody td:first-child a.product-name {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .d-flex.flex-wrap.gap-2 {
                width: 100% !important;
                display: flex !important;
                justify-content: space-between !important;
                gap: 0 !important;
                margin-top: 10px;
            }

            #exportAllChallan,
            #exportPdf,
            #downloadAllBarcodes {
                width: 31% !important;
                flex: 0 0 31% !important;
                max-width: 31% !important;
                min-width: 31% !important;
                margin: 0 !important;
                padding: 6px 0px !important;
                font-size: 13px !important;
            }

            /* Optional: Adjust icon size */
            #exportAllChallan i,
            #exportPdf i,
            #downloadAllBarcodes i {
                font-size: 12px !important;
                margin-right: 4px !important;
            }

            .col-6 {
                width: 48% !important;
                margin: 0 1% !important;
            }

            /* Adjust the main container to properly wrap */
            .d-flex.flex-column.flex-md-row.justify-content-between.align-items-md-center.flex-wrap.mb-3.gap-2 {
                gap: 10px !important;
            }
        }

        .product-details-row {
            display: none;
        }

        .product-details-row.show {
            display: table-row;
        }

        .product-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.product-details-row td.product-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .product-details-list {
            margin-bottom: 15px;
        }

        .product-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .product-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .product-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .product-detail-value-simple {
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

        .btn-icon-mobile,
        button.btn-icon-mobile {
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

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        @media (min-width: 769px) {
            .product-details-row {
                display: none !important;
            }
        }

        /* Tablet specific fixes - 768px */
        @media screen and (width: 768px) {

            /* Ensure table is properly responsive */
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            /* Make sure details column is visible and properly sized */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
            }

            /* Ensure toggle icon is visible and clickable */
            .toggle-details {
                display: inline-block !important;
                padding: 8px !important;
                z-index: 10 !important;
            }

            .toggle-details i {
                font-size: 20px !important;
                width: 24px !important;
                height: 24px !important;
                line-height: 24px !important;
            }

            /* Adjust product name width for tablet */
            table.datanew tbody td:first-child a[style*="font-weight: 500"] {
                max-width: calc(100% - 80px) !important;
                font-size: 14px !important;
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

        /* ===== Print & Edit Barcode Modal Styles ===== */
        #printBarcodeModal .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        #printBarcodeModal .modal-header {
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            padding: 16px 24px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        #printBarcodeModal .modal-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
        }
        #printBarcodeModal .modal-title .pb-modal-icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
            font-size: 16px;
            margin-right: 12px;
        }
        #printBarcodeModal .pb-modal-close-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: all 0.2s ease;
            opacity: 1 !important;
        }
        #printBarcodeModal .pb-modal-close-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #ef4444;
            transform: rotate(90deg);
        }
        #printBarcodeModal .modal-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 22px 24px;
            background: #f8fafc;
        }
        #printBarcodeModal .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        #printBarcodeModal .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        #printBarcodeModal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        #printBarcodeModal .pb-card-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        #printBarcodeModal .pb-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #printBarcodeModal .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        #printBarcodeModal .form-control,
        #printBarcodeModal .form-select {
            border-radius: 8px;
            border-color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 7px 12px;
            transition: all 0.2s ease;
        }
        #printBarcodeModal .form-control:focus,
        #printBarcodeModal .form-select:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.15);
        }
        #printBarcodeModal .barcode-chip {
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 6px !important;
            transition: all 0.2s ease;
        }
        #printBarcodeModal .barcode-chip.bg-primary {
            background-color: #ff9f43 !important;
            border-color: #ff9f43 !important;
            box-shadow: 0 2px 6px rgba(255, 159, 67, 0.35);
            color: #ffffff !important;
        }
        #printBarcodeModal .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 1055;
            background: #ffffff;
            border-top: 1px solid #edf2f7;
            padding: 14px 24px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
        }
        #printBarcodeModal .btn-primary {
            background: linear-gradient(135deg, #ff9f43 0%, #ff8510 100%);
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 7px 18px;
            box-shadow: 0 3px 8px rgba(255, 159, 67, 0.35);
            transition: all 0.2s ease;
        }
        #printBarcodeModal .btn-primary:hover {
            background: linear-gradient(135deg, #ff8510 0%, #f37100 100%);
            box-shadow: 0 4px 12px rgba(255, 159, 67, 0.45);
            transform: translateY(-1px);
        }
        #printBarcodeModal .btn-secondary {
            border-radius: 8px;
            font-weight: 600;
            padding: 7px 16px;
        }
        .pb-pill-btn-group .btn-check:checked + .btn {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
        }
        .pb-pill-btn-group .btn {
            border-color: #cbd5e1;
            color: #475569;
            font-size: 0.76rem;
            font-weight: 600;
            padding: 4px 12px;
            transition: all 0.15s ease-in-out;
        }
        .pb-pill-btn-group .btn:hover {
            background-color: #f1f5f9;
        }
        .pb-thermal-sticker-slot {
            flex: 1 1 0;
            min-width: 0;
            max-width: 250px;
            background: #ffffff;
            border: 2px solid #10b981;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .pb-thermal-sticker-slot.blank-slot {
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            cursor: pointer;
            justify-content: center;
            align-items: center;
        }
        .pb-thermal-sticker-slot.blank-slot:hover {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .pb-sticker-top-tag {
            background: #10b981;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            text-align: center;
            padding: 3px 0;
            letter-spacing: 0.5px;
            line-height: 1;
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
                <h4>All Products</h4>
                <!--<h6>Manage your products</h6>-->
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(1, 'add'))
                    <a href="{{ route('product.add') }}" class="btn btn-sm btn-added"><img
                            src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1">New
                        Item</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center flex-wrap mb-3 gap-2">

                    <!-- Left: Filters -->
                    <div class="d-flex gap-2">
                        <!-- Category Filter -->
                        <div class="form-group mb-0 category-width">
                            <label for="filterCategory" class="form-label mb-1">Category</label>
                            <select id="filterCategory" class="form-control form-control-sm select-filter">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand Filter -->
                        <div class="form-group mb-0 category-width">
                            <label for="filterBrand" class="form-label mb-1">Brand</label>
                            <select id="filterBrand" class="form-control form-control-sm select-filter">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Right: Export Buttons -->
                    <div class="d-flex flex-wrap gap-2 col-12 col-md-auto">
                        <button id="exportAllChallan" class="btn btn-sm btn-success col-6 col-md-auto">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </button>
                        <button id="exportPdf" class="btn btn-sm btn-danger col-6 col-md-auto">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </button>
                        <button id="downloadAllBarcodes" class="btn btn-sm col-6 col-md-auto"
                            style="background:#1b2850; color:#fff; border:1px solid #1b2850;">
                            <i class="fas fa-barcode me-1"></i> All Barcodes
                        </button>
                    </div>
                </div>

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
                    <table class="table  datanew">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="details-column">Details</th>
                                <th>SKU</th>
                                <th>Category </th>
                                <th>Unit</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Quantity</th>
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

    <!-- Print & Edit Barcode Modal -->
    <div class="modal fade" id="printBarcodeModal" tabindex="-1" aria-labelledby="printBarcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title mb-0" id="printBarcodeModalLabel">
                        <span class="pb-modal-icon-badge"><i class="fas fa-barcode"></i></span>
                        <span>Print &amp; Edit Barcode &mdash; <span id="pbProductName" class="text-primary fw-bold"></span></span>
                    </h5>
                    <button type="button" class="pb-modal-close-btn" data-bs-dismiss="modal" aria-label="Close" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="printBarcodeForm">
                        <input type="hidden" id="pbProductId" name="product_id">
                        <input type="hidden" id="pbCurrentEditBarcode" name="current_edit_barcode">

                        <!-- Section 1: Barcode & Label Hardware -->
                        <div class="pb-card-section">
                            <div class="pb-section-title">
                                <i class="fas fa-tags text-warning"></i> Barcode &amp; Label Roll Configuration
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Barcode No</label>
                                    <input type="hidden" id="pbBarcode" name="barcode">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="pbBarcode_input" placeholder="Enter barcode..." oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                        <button class="btn btn-secondary px-3" type="button" id="add_pbBarcode_btn" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                            <i class="fas fa-plus me-1"></i> Add
                                        </button>
                                    </div>
                                    <div id="pbBarcode_tags_container" class="d-flex flex-wrap gap-2 mt-2"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Label Size (mm)</label>
                                    <select class="form-select" id="pbLabelSize" name="label_size">
                                        <option value="35x25x3">35 x 25 mm, 3 columns, gap 0 (105 x 25 mm = 4.13 × 0.98 in)</option>
                                        <option value="35x25x1">35 x 25 mm, 1 column</option>
                                        <option value="38x25x2">38 x 25 x 2 (2 labels / row)</option>
                                        <option value="25x50x2">25 x 50 x 2 (2 tall labels / row)</option>
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">TVS LP46 stock: <strong>4.13 × 0.98 in</strong> = 3 × (35×25 mm)</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Label Printer Type</label>
                                    <select class="form-select" id="pbPrinter" name="printer">
                                        <option value="tvs">TVS LP46 Dlite</option>
                                        <option value="tsc">TSC TE244</option>
                                    </select>
                                    <small id="pbPrinterMobileHint" class="text-muted d-none" style="font-size: 0.74rem;">Choose layout for roll.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Template & Price Selection -->
                        <div class="pb-card-section">
                            <div class="pb-section-title">
                                <i class="fas fa-sliders-h text-primary"></i> Template &amp; Pricing Setup
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6" id="pbRateSelectCol">
                                    <label class="form-label">MRP / Selling Rate</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                            &#8377;
                                        </span>
                                        <select class="form-select" id="pbRateSelect" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-weight: 600;">
                                            <option value="">Select MRP &amp; Rate</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="pbPrice" name="price">
                                    <input type="hidden" id="pbMrp" name="mrp">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">MRP with strikethrough &amp; Selling Rate</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Barcode Sticker Template</label>
                                    <select class="form-select" id="pbBarcodeTemplate" name="barcode_template">
                                        <option value="classic">Default (Normal)</option>
                                        <option value="split">Default (Variant / Grocery)</option>
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">Selected template is remembered as default for this product type</small>
                                    <input type="hidden" id="pbLabelFormat" name="label_format" value="classic">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Template Actions</label>
                                    <button type="button" class="btn btn-outline-warning w-100 d-flex align-items-center justify-content-center" style="border-radius: 8px; font-weight: 600; height: 38px;">
                                        <i class="fas fa-paint-brush me-2"></i> Custom Designer
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">No. of Copies</label>
                                    <input type="number" class="form-control" id="pbPrintCopies" name="print_copies" value="1" min="1" max="200" step="1" style="height: 38px;">
                                </div>
                            </div>
                        </div>

                        <!-- Hidden legacy inputs -->
                        <input type="hidden" id="pbShowBatch" value="0">
                        <input type="hidden" id="pbBatchNo" name="batch_no" value="">
                        <input type="hidden" id="pbShowWholesale" value="0">
                        <input type="hidden" id="pbLabelRef" name="label_reference" value="">
                        <input type="hidden" id="pbSpecificQty" name="specific_qty" value="">

                        <!-- Section 3: Print Scope, Options & Live Preview -->
                        <div class="pb-card-section mb-0">
                            <div class="pb-section-title">
                                <i class="fas fa-eye text-success"></i> Print Target &amp; Live Preview
                            </div>
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-md-3">
                                    <label class="form-label mb-1">Company Header</label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="form-check form-switch p-0 m-0">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="pbIncludeTitle" checked style="width: 2.2em; height: 1.2em; cursor: pointer;">
                                        </div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Prints name &amp; compact address</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mb-1">Print Barcodes</label>
                                    <select class="form-select form-select-sm" id="pbPrintScope" name="print_scope" style="font-weight: 500;">
                                        <option value="selected">Selected barcode only</option>
                                        <option value="all">All barcodes</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="pbRowLayoutSection">
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <div>
                                            <label class="form-label mb-1 fw-bold text-dark d-block" style="font-size: 0.8rem;">Row Layout (3-Up):</label>
                                            <div class="btn-group btn-group-sm pb-pill-btn-group" role="group">
                                                <input type="radio" class="btn-check" name="pb_row_layout" id="pbRowLayoutAll" value="all" checked autocomplete="off">
                                                <label class="btn btn-outline-success px-3 py-1" for="pbRowLayoutAll" style="border-radius: 20px 0 0 20px;">
                                                    <i class="fas fa-check-circle me-1" id="pbRowLayoutAllIcon"></i> All 3 in Row
                                                </label>

                                                <input type="radio" class="btn-check" name="pb_row_layout" id="pbRowLayoutSingle" value="single" autocomplete="off">
                                                <label class="btn btn-outline-success px-3 py-1" for="pbRowLayoutSingle" style="border-radius: 0 20px 20px 0;">
                                                    <i class="far fa-circle me-1" id="pbRowLayoutSingleIcon"></i> 1 Sticker Only
                                                </label>
                                            </div>
                                        </div>
                                        <div id="pbStickerSlotWrap" style="display: none;">
                                            <label class="form-label mb-1 fw-bold text-dark d-block" style="font-size: 0.8rem;">Sticker Slot:</label>
                                            <div class="btn-group btn-group-sm pb-pill-btn-group" role="group">
                                                <input type="radio" class="btn-check" name="pb_sticker_slot" id="pbSlotLeft" value="0" checked autocomplete="off">
                                                <label class="btn btn-outline-success px-2 py-1" for="pbSlotLeft" style="border-radius: 20px 0 0 20px;">
                                                    <i class="fas fa-check-circle me-1" id="pbSlotLeftIcon"></i> Left (1)
                                                </label>

                                                <input type="radio" class="btn-check" name="pb_sticker_slot" id="pbSlotMiddle" value="1" autocomplete="off">
                                                <label class="btn btn-outline-success px-2 py-1" for="pbSlotMiddle">
                                                    <i class="far fa-circle me-1" id="pbSlotMiddleIcon"></i> Middle (2)
                                                </label>

                                                <input type="radio" class="btn-check" name="pb_sticker_slot" id="pbSlotRight" value="2" autocomplete="off">
                                                <label class="btn btn-outline-success px-2 py-1" for="pbSlotRight" style="border-radius: 0 20px 20px 0;">
                                                    <i class="far fa-circle me-1" id="pbSlotRightIcon"></i> Right (3)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Physical Thermal Roll Preview Container -->
                            <div class="p-3 rounded-3" style="background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="fw-bold text-dark text-uppercase d-flex align-items-center gap-2" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                        <i class="fas fa-barcode text-success"></i>
                                        <span id="pbRollPreviewTitle">PHYSICAL THERMAL ROLL PREVIEW (105mm × 25mm • 3-UP • BARCODE)</span>
                                    </div>
                                    <span class="badge bg-light text-secondary border fw-bold" id="pbRollPreviewBadge" style="font-size: 0.7rem; border-radius: 6px;">
                                        3 Die-Cut Stickers
                                    </span>
                                </div>

                                <!-- Thermal Roll 3-Up Container -->
                                <div id="pbBarcodeImagesContainer" class="d-flex justify-content-center align-items-stretch w-100" style="min-height: 120px;"></div>

                                <!-- Status Notification Bar -->
                                <div id="pbRollStatusAlert" class="alert alert-success d-flex align-items-center py-2 px-3 mb-0 mt-3 border-0 fw-bold" style="background-color: #d1fae5; color: #065f46; border-radius: 8px; font-size: 0.78rem;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span id="pbRollStatusText">✓ 3-UP FULL: Printing all 3 stickers across each row.</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-block">
                    <div class="pb-print-footer d-flex flex-wrap flex-md-nowrap align-items-center gap-2 w-100">
                        <div class="pb-print-target d-flex align-items-center gap-2">
                            <select class="form-select form-select-sm" id="pbPrintTarget" title="Print To" style="font-weight: 500;">
                                <option value="browser">Print dialog (choose in browser)</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap" id="pbRefreshPrintersBtn" title="Refresh Printers" style="padding: 5px 10px; border-radius: 8px;">
                                <i class="fas fa-sync-alt" id="pbRefreshPrintersIcon"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" id="pbBtChooseBtn" title="Find a Bluetooth printer" style="padding: 5px 12px; border-radius: 8px; font-weight: 600;">
                                <i class="fab fa-bluetooth-b me-1"></i> Bluetooth
                            </button>
                        </div>
                        <div class="pb-print-actions d-flex gap-2 ms-md-auto">
                            <button type="button" class="btn btn-secondary btn-sm text-nowrap" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm text-nowrap" id="saveAndPrintBarcodeBtn">
                                <i class="fas fa-print me-1"></i> <span id="pbPrintBtnText">Print Row (All 3 Barcodes)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
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

            // Initialize Select2 for custom class
            $('.select-filter').select2();

            // Removed subadmin filter fetching for strict data isolation

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            function buildProductExpandableRowContent(product) {
                let actionBtns = '';

                let productName = (product.name || '').replace(/"/g, '&quot;');
                let productBarcode = product.barcode || product.SKU || '';
                let productPrice = product.price || '';
                actionBtns += `<a class="btn-icon-mobile print-barcode-btn" href="javascript:void(0);" data-id="${product.id}" data-barcode="${productBarcode}" data-name="${productName}" data-price="${productPrice}" data-mrp="${productPrice}" data-sku="${product.sku || ''}" title="Print Barcode">
                    <i class="fas fa-print" style="color: #092C4C; font-size: 16px;"></i>
                </a>`;

                @if (app('hasPermission')(1, 'view'))
                    actionBtns += `<a class="btn btn-sm btn-primary me-3" style="background-color: #ff9f43; border-color: #ff9f43; color:white; font-size: 13px;" href="/inventory-View/${product.id}">
                        History
                    </a>`;

                    actionBtns += `<a class="btn-icon-mobile btn-view" href="/product-view/${product.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </a>`;
                @endif

                @if (app('hasPermission')(1, 'edit'))
                    actionBtns += `<a class="btn-icon-mobile btn-edit" href="/edit-product/${product.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>`;
                @endif

                @if (app('hasPermission')(1, 'delete'))
                    actionBtns += `<a class="btn-icon-mobile btn-delete confirm-text delete-product" data-id="${product.id}" href="javascript:void(0);" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>`;
                @endif

                return `
                    <td colspan="9" class="product-details-content">
                        <div class="product-details-list">
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">SKU:</span>
                                <span class="product-detail-value-simple">${product.sku}</span>
                            </div>
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">Category:</span>
                                <span class="product-detail-value-simple">${product.categoryName}</span>
                            </div>
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">Unit:</span>
                                <span class="product-detail-value-simple">${product.unitName}</span>
                            </div>
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">Brand:</span>
                                <span class="product-detail-value-simple">${product.brandName}</span>
                            </div>
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">Price:</span>
                                <span class="product-detail-value-simple" style="font-weight: bold;">${product.formattedPrice}</span>
                            </div>
                            <div class="product-detail-row-simple">
                                <span class="product-detail-label-simple">Quantity:</span>
                                <span class="product-detail-value-simple">${product.quantityDisplay}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            window.toggleProductRowDetails = function(productId) {
                const btn = $(`.mobile-toggle-btn-table[data-product-id="${productId}"]`);
                if (btn.length === 0) {
                    return;
                }

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.product-details-row[data-product-id="${productId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const productData = window.productDataMap && window.productDataMap[productId];
                    if (!productData) {
                        return;
                    }

                    detailsRow = $('<tr>')
                        .addClass('product-details-row')
                        .attr('data-product-id', productId)
                        .html(buildProductExpandableRowContent(productData));
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

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchProducts(1);
            });

            // Define the function FIRST
            function fetchProducts(page = 1) {
                var categoryId = $('#filterCategory').val();
                var brandId = $('#filterBrand').val();
                $.ajax({
                    url: "/api/getAllProduct",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        category_id: categoryId,
                        brand_id: brandId,
                        page: page,
                        per_page: perPage,
                        search: searchQuery
                    },
                    success: function(response) {
                        if (response.status) {
                            let products = response.data;
                            let pagination = response.pagination;
                            let currencySymbol = response.currencySymbol || '₹';
                            let currencyPosition = response.currencyPosition || 'left';
                            let tableBody = [];
                            window.productDataMap = {};

                            let lowStockThreshold = response.lowStockThreshold || 0;

                            if (pagination) {
                                currentPage = pagination.current_page;
                                lastPage = pagination.last_page;
                                updatePaginationUI(pagination);
                            }


                            // Function to capitalize first letter of each word
                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }
                            $.each(products, function(index, product) {
                                let productName = capitalizeWords(product.name || 'N/A');
                                let sku = product.SKU || 'N/A';
                                let quantity = product.quantity ?? 'N/A';
                                let categoryName = capitalizeWords(product.category ? product
                                    .category.name : "N/A");
                                let unitName = capitalizeWords(product.unit ? product.unit
                                    .unit_name : "N/A");
                                let brandName = capitalizeWords(product.brand ? product.brand
                                    .name : "N/A");

                                let createdAt = capitalizeWords(product.product_type ? product
                                    .product_type
                                    .name : "N/A");
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table" onclick="toggleProductRowDetails('${product.id}')" data-product-id="${product.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                let productImage =
                                    '{{ env('ImagePath') . 'admin/assets/img/product/noimage.png' }}';
                                const ImagePath = "{{ env('ImagePath') . 'storage/' }}";
                                if (product.images) {
                                    let imagesArray = typeof product.images === "string" ? JSON
                                        .parse(product.images) : product.images;
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        productImage = `${ImagePath}${imagesArray[0]}`;
                                    }
                                }

                                let formattedPrice = 'N/A';
                                if (product.price !== undefined && product.price !== null &&
                                    product.price !== '') {
                                    let priceVal = parseFloat(product.price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    if (currencyPosition === 'left') {
                                        formattedPrice = currencySymbol + priceVal;
                                    } else {
                                        formattedPrice = priceVal + currencySymbol;
                                    }
                                }

                                let quantityDisplay =
                                    `<span class="quantity-history" data-id="${product.id}" style="cursor:pointer; border: 1px solid #ececec; padding: 4px 8px; border-radius: 4px; display: inline-block;">${quantity}</span>`;
                                if (quantity !== 'N/A' && parseInt(quantity) <=
                                    lowStockThreshold) {
                                    quantityDisplay +=
                                        ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                }

                                window.productDataMap[product.id] = {
                                    id: product.id,
                                    sku: sku,
                                    categoryName: categoryName,
                                    unitName: unitName,
                                    brandName: brandName,
                                    formattedPrice: formattedPrice,
                                    quantityDisplay: quantityDisplay
                                };

                                tableBody.push([
                                    `<div style="display: flex; align-items: center;">
                                    
                                    <a href="/product-view/${product.id}" class="product-img mb-1">
                                        <img src="${productImage}" alt="product" style="max-width: 60px;">
                                    </a>
                                    <a href="/product-view/${product.id}" style="color: #1b2850; font-weight: 500; text-align: center; margin-left: 8px;">
                                        ${productName}
                                    </a>
                                  
                                </div>`,
                                    detailsToggle,
                                    sku,
                                    categoryName,
                                    unitName,
                                    brandName,
                                    formattedPrice,
                                    quantityDisplay,
                                    // createdAt,
                                    `
                                                 <a class="me-3 print-barcode-btn" href="javascript:void(0);" data-id="${product.id}" data-barcode="${product.barcode || product.SKU || ''}" data-name="${productName}" data-price="${product.price || ''}" data-mrp="${product.price || ''}" data-sku="${product.SKU || ''}" title="Print Barcode">
                                                     <i class="fas fa-print" style="color: #092C4C; font-size: 16px;"></i>
                                                 </a>
                                                 @if (app('hasPermission')(1, 'view')) 
                                                 <a class="me-3 btn btn-sm btn-primary" style="color:white; font-size: 13px;" href="/inventory-View/${product.id}">
                                                    History    
                                                </a>
                                                @endif
                                                @if (app('hasPermission')(1, 'view')) 
                                                <a class="me-3" href="/product-view/${product.id}">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
        <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
        </svg>
                                                </a>
                                                @endif
                                                @if (app('hasPermission')(1, 'edit'))
                                                <a class="me-3" href="/edit-product/${product.id}">
                                                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
        </svg>
                                                </a>
                                                @endif
                                                @if (app('hasPermission')(1, 'delete'))
                                                <a class="confirm-text delete-product" data-id="${product.id}" href="javascript:void(0);">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                                            <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                                        </svg>
                                                </a>
                                                @endif
                                                `
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

                                topScroll.onscroll = () => {
                                    tableResponsive.scrollLeft = topScroll.scrollLeft;
                                };
                                tableResponsive.onscroll = () => {
                                    topScroll.scrollLeft = tableResponsive.scrollLeft;
                                };
                            }

                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="9">No products found</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function() {
                        alert("Error fetching products.");
                    }
                });
            }

            window.productListFetch = fetchProducts;
            window.productListGetPage = () => currentPage;

            $(document).on('click', '.quantity-history', function() {
                const productId = $(this).data('id');

                $.ajax({
                    url: `/api/product-quantity-history/${productId}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(res) {
                        if (res.status) {
                            let content = `<h5>${res.product}</h5><ul class="list-group">`;
                            res.history.forEach(item => {
                                content += `
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${item.type}</strong> - ${item.note}<br>
                                                <small>${item.date}</small>
                                            </div>
                                        <span class="badge bg-${item.type === 'Added' ? 'success' : 'danger'} px-4 py-2 small">${item.quantity}</span>
                                        </li>
                                    `;
                            });
                            content += `</ul>`;
                            $('#quantityHistoryContent').html(content);
                            $('#quantityHistoryModal').modal('show');
                        } else {
                            alert(res.message || "Failed to load quantity history.");
                        }
                    },
                    error: function() {
                        alert("Error fetching quantity history.");
                    }
                });
            });

            // Initial fetch
            fetchProducts(currentPage);

            // Filters: bind change events AFTER function is defined
            $('#filterCategory, #filterBrand').on('change', function() {
                fetchProducts(1);
            });

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
                    fetchProducts(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchProducts(1);
            });
        })


        $(document).on('click', '.delete-product', function() {
            var productId = $(this).data('id'); // Get product ID
            var authToken = localStorage.getItem("authToken");
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonColor: "#ff9f43", // Confirm button color (orange)
                cancelButtonColor: "#6c757d", // Cancel button color (gray)
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/deleteProduct/${productId}`, // Adjust based on your route
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                'content') // CSRF token for security
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

                                if (window.productListFetch) {
                                    const page = window.productListGetPage ? window.productListGetPage() : 1;
                                    window.productListFetch(page);
                                } else {
                                    $(`.delete-product[data-id="${productId}"]`).closest('tr')
                                        .remove();
                                }
                            } else {
                                // Use response.error here, fallback to response.message just in case
                                Swal.fire("Error!", response.error || response.message ||
                                    "Unknown error", "error");
                            }
                        },

                        error: function(xhr) {
                            // Parse the JSON error message sent by API in HTTP error response
                            let message = "Something went wrong!";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                message = xhr.responseJSON.error;
                            }
                            // Swal.fire("Error!", message, "error");
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonColor: "#ff9f43", // custom orange color
                                confirmButtonText: "OK"
                            });

                        }
                    });
                }
            });
        });

        $('#exportAllChallan').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            let authToken = localStorage.getItem("authToken");

            let url =
                `/api/export-product?category_id=${selectedCategory}&brand_id=${selectedBrand}`;

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },

                beforeSend: function() {
                    Swal.fire({
                        title: "Exporting Excel...",
                        text: "Please wait while we generate your file.",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },

                success: function(response) {
                    Swal.close();

                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: "success",
                            title: "Excel Exported!",
                            text: "Click the button below to download your Excel file.",
                            showConfirmButton: true,
                            confirmButtonText: "Download File"
                        }).then(() => {
                            let link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || "Products.xlsx";
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Export Failed!",
                            text: "Unable to export Excel file. Please try again."
                        });
                    }
                },

                error: function(xhr, status, error) {
                    Swal.close();
                    console.error("Export failed:", error);

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Export failed. Please try again."
                    });
                }
            });
        });


        $('#exportPdf').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            let authToken = localStorage.getItem("authToken");

            let url =
                `/api/export-product-pdf?category_id=${selectedCategory}&brand_id=${selectedBrand}`;

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },

                beforeSend: function() {
                    Swal.fire({
                        title: "Generating PDF...",
                        text: "Please wait",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },

                success: function(response) {
                    Swal.close();

                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: "success",
                            title: "PDF Generated Successfully!",
                            text: "Your product PDF is ready.",
                            showConfirmButton: true,
                            confirmButtonText: "Download PDF"
                        }).then(() => {
                            let link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || "products.pdf";
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: "Could not generate the PDF. Please try again."
                        });
                    }
                },

                error: function(xhr, status, error) {
                    Swal.close();
                    console.error("Export PDF failed:", error);

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Export PDF failed. Please try again."
                    });
                }
            });
        });

        // ===== Download All Barcodes PDF =====
        $('#downloadAllBarcodes').click(function() {
            let selectedCategory = $('#filterCategory').val() || '';
            let selectedBrand = $('#filterBrand').val() || '';
            let authToken = localStorage.getItem("authToken");

            let url = `/api/export-all-barcodes-pdf?category_id=${selectedCategory}&brand_id=${selectedBrand}`;

            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                beforeSend: function() {
                    Swal.fire({
                        title: "Generating Barcodes PDF...",
                        text: "Please wait",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status && response.file_url) {
                        let link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = response.file_name || "All_Barcodes.pdf";
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: response.message || "Could not generate barcodes PDF.",
                            confirmButtonColor: "#ff9f43"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to generate barcodes PDF. Please try again.",
                        confirmButtonColor: "#ff9f43"
                    });
                }
            });
        });

        // ===== Print & Edit Barcode Modal Logic =====
        var pbBarcodes = [];
        window.currentBarcodeDetails = {};

        function escapeHtmlText(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeHtmlAttr(str) {
            return escapeHtmlText(str);
        }

        function sellingRateLabel(rate, idx, total) {
            let priceText = (rate.price !== undefined && rate.price !== null && rate.price !== '') ? ('₹' + parseFloat(rate.price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : '';
            let mrpText = (rate.mrp !== undefined && rate.mrp !== null && rate.mrp !== '') ? ('MRP: ₹' + parseFloat(rate.mrp).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : '';
            if (mrpText && priceText) {
                return mrpText + ' | Rate: ' + priceText;
            } else if (priceText) {
                return 'Rate: ' + priceText;
            } else if (mrpText) {
                return mrpText;
            }
            return 'Rate ' + (idx + 1);
        }

        function fillRateSelect(rateVal, mrpVal, targetBarcode) {
            let $sel = $('#pbRateSelect');
            $sel.empty().append('<option value="">Select MRP &amp; Rate</option>');
            let allRates = [];

            let priceAttr = (rateVal !== undefined && rateVal !== null && rateVal !== '' && !isNaN(parseFloat(rateVal))) ? parseFloat(rateVal).toFixed(2) : '';
            let mrpAttr = (mrpVal !== undefined && mrpVal !== null && mrpVal !== '' && !isNaN(parseFloat(mrpVal))) ? parseFloat(mrpVal).toFixed(2) : '';

            if (priceAttr || mrpAttr) {
                allRates.push({
                    name: 'Base Rate',
                    price: priceAttr,
                    mrp: mrpAttr || priceAttr,
                    barcode: targetBarcode || ''
                });
            }

            allRates.forEach(function(rate, idx) {
                let barcodeAttr = rate.barcode ? ` data-barcode="${escapeHtmlAttr(rate.barcode)}"` : '';
                $sel.append(`<option value="${idx}" data-price="${rate.price}" data-mrp="${rate.mrp}"${barcodeAttr}>${escapeHtmlText(sellingRateLabel(rate, idx, allRates.length))}</option>`);
            });

            if (allRates.length > 0) {
                $sel.val('0');
                $('#pbPrice').val(allRates[0].price);
                $('#pbMrp').val(allRates[0].mrp);
            }
        }

        function renderPbChips() {
            let container = $('#pbBarcode_tags_container');
            container.empty();
            let currentEdit = $('#pbCurrentEditBarcode').val();
            pbBarcodes.forEach(function(barcode) {
                let isActive = (barcode === currentEdit) ? 'bg-primary text-white' : 'bg-light text-dark';
                let chipHtml = `
                    <div class="barcode-chip ${isActive} pb-chip-selectable" data-barcode="${escapeHtmlAttr(barcode)}" style="cursor: pointer; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <span>${escapeHtmlText(barcode)}</span>
                        <span class="remove-chip pb-remove-chip" data-barcode="${escapeHtmlAttr(barcode)}" style="cursor: pointer; font-size: 14px; font-weight: bold; line-height: 1;">&times;</span>
                    </div>
                `;
                container.append(chipHtml);
            });
            $('#pbBarcode').val(pbBarcodes.join(','));
        }

        function updateRollStatusAndButton() {
            let mode = $('input[name="pb_row_layout"]:checked').val() || 'all';
            let slot = parseInt($('input[name="pb_sticker_slot"]:checked').val(), 10);
            if (isNaN(slot)) slot = 0;
            let slotNames = ['Sticker 1 (Left)', 'Sticker 2 (Middle)', 'Sticker 3 (Right)'];

            if (mode === 'all') {
                $('#pbRollStatusText').text('✓ 3-UP FULL: Printing all 3 stickers across each row.');
                $('#pbPrintBtnText').text('Print Row (All 3 Barcodes)');
            } else {
                let name = slotNames[slot] || 'Sticker 1 (Left)';
                $('#pbRollStatusText').text('✓ 1 STICKER ONLY: Printing on ' + name + '. The other 2 stickers will remain completely CLEAR & BLANK.');
                $('#pbPrintBtnText').text('Print on ' + name + ' (2 Clear)');
            }
        }

        function updateBarcodePreview() {
            let currentBarcode = $('#pbCurrentEditBarcode').val() || $('#pbBarcode_input').val() || '';
            $('#pbBarcodeImagesContainer').empty();

            if (!currentBarcode && pbBarcodes.length > 0) {
                currentBarcode = pbBarcodes[0];
                $('#pbCurrentEditBarcode').val(currentBarcode);
            }

            if (!currentBarcode) {
                currentBarcode = '123456';
            }

            let svgId = 'pbBarcodeImage_preview';
            let compName = {!! json_encode($companyName ?? 'AutoCare Garage & Service Center') !!};
            let compAddr = {!! json_encode($companyAddress ?? '') !!};
            let prodName = $('#pbProductName').text() || 'Product';

            let mrp = '';
            let price = '';
            if ($('#pbRateSelect').val() !== '') {
                mrp = $('#pbMrp').val();
                price = $('#pbPrice').val();
            }

            let blankPad = '______';
            let mrpTxt = 'MRP-' + (mrp ? ('₹' + parseFloat(mrp).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : blankPad);
            let rateTxt = 'RATE-' + (price ? ('₹' + parseFloat(price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : blankPad);
            let barcodeFooter = currentBarcode;
            let format = $('#pbBarcodeTemplate').val() || 'classic';
            let includeTitle = $('#pbIncludeTitle').is(':checked');

            let labelSize = $('#pbLabelSize').val() || '35x25x3';
            let widthMm = 35;
            let heightMm = 25;
            let labelCols = 3;
            if (labelSize === '35x25x1') {
                labelCols = 1;
            } else if (labelSize === '38x25x2') {
                widthMm = 38;
                heightMm = 25;
                labelCols = 2;
            } else if (labelSize === '25x50x2') {
                widthMm = 25;
                heightMm = 50;
                labelCols = 2;
            }

            let scale = 3.78;
            let widthPx = Math.round(widthMm * scale);
            let heightPx = Math.round(heightMm * scale);
            let baseFontSize = 11;
            let verticalGap = '1px';

            let companyHtml = includeTitle
                ? `<div class="company-name" style="font-size: 0.88em; font-weight: bold; line-height: 1.05; text-transform: uppercase; letter-spacing: -0.2px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; color: #000;">${escapeHtmlText(compName)}</div>
                   <div class="address" style="font-size: 0.65em; font-weight: bold; margin-top: ${verticalGap}; line-height: 1; text-transform: uppercase; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; color: #333;">${escapeHtmlText(compAddr)}</div>`
                : '';

            let rowLayout = $('input[name="pb_row_layout"]:checked').val() || 'all';
            let stickerSlot = parseInt($('input[name="pb_sticker_slot"]:checked').val(), 10);
            if (isNaN(stickerSlot)) stickerSlot = 0;
            let slotNames = ['Left (1)', 'Middle (2)', 'Right (3)'];

            updateRollStatusAndButton();

            if (labelCols === 3) {
                $('#pbRowLayoutSection').show();
                $('#pbRollPreviewTitle').text('PHYSICAL THERMAL ROLL PREVIEW (' + (Math.round(widthMm * 3)) + 'mm × ' + Math.round(heightMm) + 'mm • 3-UP • BARCODE)');
                $('#pbRollPreviewBadge').text('3 Die-Cut Stickers').show();
                $('#pbRollStatusAlert').show();
            } else {
                $('#pbRowLayoutSection').hide();
                $('#pbRollPreviewTitle').text('PHYSICAL THERMAL LABEL PREVIEW (' + Math.round(widthMm) + 'mm × ' + Math.round(heightMm) + 'mm)');
                $('#pbRollPreviewBadge').hide();
                $('#pbRollStatusAlert').hide();
            }

            function buildStickerContent(slotIdx) {
                let mrpHtml = mrp ? `MRP-<s>${escapeHtmlText('₹' + parseFloat(mrp).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}))}</s>` : `MRP-${blankPad}`;
                let priceLine = mrpHtml + '&nbsp;&nbsp;' + rateTxt;
                return `
                    ${companyHtml}
                    <div class="product-name" style="font-size: 0.88em; font-weight: bold; margin-top: ${verticalGap}; text-transform: uppercase; line-height: 1.1; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; color: #000;">${escapeHtmlText(prodName)}</div>
                    <div class="price-line" style="font-size: 0.90em; font-weight: bold; margin-top: ${verticalGap}; line-height: 1; text-align: center; white-space: nowrap; color: #000;">${priceLine}</div>
                    <svg id="${svgId}_slot_${slotIdx}" class="barcode-img" style="margin-top: 2px;"></svg>
                    <div style="font-size: 0.74em; font-weight: bold; line-height: 1; margin-top: 1px; white-space: nowrap; color: #000;">${escapeHtmlText(barcodeFooter)}</div>
                `;
            }

            if (labelCols === 3) {
                let slotsHtml = '';
                for (let s = 0; s < 3; s++) {
                    let isPrinting = (rowLayout === 'all') || (rowLayout === 'single' && s === stickerSlot);
                    if (isPrinting) {
                        slotsHtml += `
                        <div class="pb-thermal-sticker-slot" style="flex: 1 1 0; min-width: 0; max-width: 255px; background: #ffffff; border: 2px solid #10b981; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <div class="pb-sticker-top-tag">★ PRINT HERE</div>
                            <div style="padding: 6px 8px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; font-size: ${baseFontSize}px; overflow: hidden;">
                                ${buildStickerContent(s)}
                            </div>
                        </div>`;
                    } else {
                        slotsHtml += `
                        <div class="pb-thermal-sticker-slot blank-slot" data-slot="${s}" style="flex: 1 1 0; min-width: 0; max-width: 255px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer; padding: 22px 8px; text-align: center;" title="Click to select this sticker position">
                            <i class="far fa-square text-muted mb-2" style="font-size: 1.4rem;"></i>
                            <div class="fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">CLEAR / BLANK</div>
                            <div class="text-muted" style="font-size: 0.65rem;">(No print — stays blank)</div>
                            <span class="badge bg-light text-muted border mt-2" style="font-size: 0.65rem;">Sticker ${s + 1} (${slotNames[s]})</span>
                            <div class="text-primary mt-2 small" style="font-size: 0.62rem; font-weight: 600;">Click to select this position</div>
                        </div>`;
                    }
                }

                let rollHtml = `<div class="pb-roll-row d-flex justify-content-center align-items-stretch gap-2 w-100" style="max-width: 820px; margin: 0 auto;">${slotsHtml}</div>`;
                $('#pbBarcodeImagesContainer').append(rollHtml);

                try {
                    let codeStr = String(currentBarcode || '123456').trim();
                    let slotBarH = includeTitle ? 32 : 38;
                    for (let s = 0; s < 3; s++) {
                        if (rowLayout === 'all' || (rowLayout === 'single' && s === stickerSlot)) {
                            let sel = "#" + svgId + "_slot_" + s;
                            JsBarcode(sel, codeStr, {
                                format: "CODE128",
                                lineColor: "#000",
                                background: "#fff",
                                width: 1.5,
                                height: slotBarH,
                                displayValue: false,
                                margin: 2,
                                marginTop: 0,
                                marginBottom: 0
                            });
                            let svgEl = document.querySelector(sel);
                            if (svgEl) {
                                svgEl.style.width = '88%';
                                svgEl.style.maxWidth = '210px';
                                svgEl.style.height = slotBarH + 'px';
                                svgEl.style.display = 'block';
                                svgEl.style.margin = '2px auto 1px auto';
                            }
                        }
                    }
                } catch(e) {
                    console.error("JsBarcode preview error:", e);
                }
            } else {
                let singleContent = buildStickerContent(0);
                let html = `
                <div class="label-box" style="margin: 0 auto; width: ${widthPx}px; height: ${heightPx}px; max-width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 6px 6px 4px 6px; overflow: hidden; font-size: ${baseFontSize}px; box-sizing: border-box; border: 2px solid #10b981; border-radius: 8px; background: #fff;">
                    ${singleContent}
                </div>`;
                $('#pbBarcodeImagesContainer').append(html);

                try {
                    let codeStr = String(currentBarcode || '123456').trim();
                    let singleBarH = includeTitle ? 36 : 42;
                    let sel = "#" + svgId + "_slot_0";
                    JsBarcode(sel, codeStr, {
                        format: "CODE128",
                        lineColor: "#000",
                        background: "#fff",
                        width: 1.5,
                        height: singleBarH,
                        displayValue: false,
                        margin: 2,
                        marginTop: 0,
                        marginBottom: 0
                    });
                    let svgEl = document.querySelector(sel);
                    if (svgEl) {
                        svgEl.style.width = '88%';
                        svgEl.style.maxWidth = '220px';
                        svgEl.style.height = singleBarH + 'px';
                        svgEl.style.display = 'block';
                        svgEl.style.margin = '2px auto 1px auto';
                    }
                } catch(e) {}
            }
        }

        function buildStandardBarcodePrintHtml(format, sizeParts, printCopies, autoPrint, printScope) {
            let widthMm = sizeParts.width || 35;
            let heightMm = sizeParts.height || 25;
            let cols = sizeParts.cols || 3;
            let rowWidthMm = widthMm * cols;
            let includeTitle = $('#pbIncludeTitle').is(':checked');
            let compName = {!! json_encode($companyName ?? 'AutoCare Garage & Service Center') !!};
            let compAddr = {!! json_encode($companyAddress ?? '') !!};
            let prodName = $('#pbProductName').text() || 'Product';

            let mrp = '';
            let price = '';
            if ($('#pbRateSelect').val() !== '') {
                mrp = $('#pbMrp').val();
                price = $('#pbPrice').val();
            }

            let blankPad = '______';
            let mrpTxt = 'MRP-' + (mrp ? ('₹' + parseFloat(mrp).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : blankPad);
            let rateTxt = 'RATE-' + (price ? ('₹' + parseFloat(price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : blankPad);

            let barcodesList = [];
            if (printScope === 'all' && pbBarcodes.length > 0) {
                barcodesList = pbBarcodes;
            } else {
                let currentBarcode = ($('#pbCurrentEditBarcode').val() || $('#pbBarcode_input').val() || '').trim();
                if (currentBarcode) barcodesList = [currentBarcode];
            }
            if (barcodesList.length === 0) barcodesList = ['123456'];

            let totalLabels = [];
            barcodesList.forEach(function(code) {
                for (let c = 0; c < printCopies; c++) {
                    totalLabels.push(code);
                }
            });

            let companyHtml = includeTitle
                ? '<div class="company-name" style="font-size: 7.2pt; font-weight: bold; line-height: 1.05; text-transform: uppercase; letter-spacing: -0.2px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">' + escapeHtmlText(compName) + '</div>' +
                  '<div class="address" style="font-size: 5.2pt; font-weight: bold; margin-top: 0.5px; line-height: 1; text-transform: uppercase; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">' + escapeHtmlText(compAddr) + '</div>'
                : '';

            function createBarcodeSvgTag(code, barH, labelWidthMm) {
                try {
                    let tmpSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    let codeStr = String(code || '123456').trim();
                    let targetH = barH || 32;
                    let maxWMm = (parseFloat(labelWidthMm) || 35) - 2;

                    JsBarcode(tmpSvg, codeStr, {
                        format: "CODE128",
                        lineColor: "#000",
                        background: "#fff",
                        width: 1.5,
                        height: targetH,
                        displayValue: false,
                        margin: 2
                    });

                    let rawW = parseFloat(tmpSvg.getAttribute('width')) || 140;
                    let rawH = parseFloat(tmpSvg.getAttribute('height')) || targetH;
                    tmpSvg.setAttribute('viewBox', '0 0 ' + rawW + ' ' + rawH);
                    tmpSvg.setAttribute('preserveAspectRatio', 'none');
                    tmpSvg.removeAttribute('width');
                    tmpSvg.removeAttribute('height');
                    tmpSvg.setAttribute('style', 'display:block; margin:0.5px auto; width:95%; max-width:' + maxWMm + 'mm; height:' + targetH + 'px;');
                    return tmpSvg.outerHTML;
                } catch(e) {
                    return '<svg style="display:block; width:95%; height:' + (barH || 32) + 'px;"></svg>';
                }
            }

            function renderSingleLabelHtml(code) {
                let footer = code;
                let mrpHtml = mrp ? ('MRP-<s>' + escapeHtmlText('₹' + parseFloat(mrp).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})) + '</s>') : ('MRP-' + blankPad);
                let priceLine = mrpHtml + '&nbsp;&nbsp;' + escapeHtmlText(rateTxt);
                let svgBarH = includeTitle ? 32 : 36;
                let svgHtml = createBarcodeSvgTag(code, svgBarH, widthMm);
                return '<div class="label-box" style="width:' + widthMm + 'mm; height:' + heightMm + 'mm; min-width:' + widthMm + 'mm; max-width:' + widthMm + 'mm; box-sizing:border-box; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding:0.5mm 0.8mm; overflow:hidden; font-family:Arial, sans-serif;">' +
                    companyHtml +
                    '<div class="product-name" style="font-size: 7.2pt; font-weight: bold; margin-top: 0.5px; text-transform: uppercase; line-height: 1.05; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">' + escapeHtmlText(prodName) + '</div>' +
                    '<div class="price-line" style="font-size: 7.5pt; font-weight: bold; margin-top: 0.5px; line-height: 1.05; text-align: center; white-space: nowrap;">' + priceLine + '</div>' +
                    svgHtml +
                    '<div class="barcode-number" style="font-size: 6.2pt; font-weight: bold; line-height: 1; margin-top: 0.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">' + escapeHtmlText(footer) + '</div>' +
                '</div>';
            }

            let rowLayout = $('input[name="pb_row_layout"]:checked').val() || 'all';
            let stickerSlot = parseInt($('input[name="pb_sticker_slot"]:checked').val(), 10);
            if (isNaN(stickerSlot)) stickerSlot = 0;

            let rowsHtml = '';
            if (cols === 3 && rowLayout === 'single') {
                for (let i = 0; i < totalLabels.length; i++) {
                    let code = totalLabels[i];
                    let rowContent = '<div class="print-row" style="display:flex; flex-direction:row; width:' + rowWidthMm + 'mm; height:' + heightMm + 'mm; page-break-after:always; page-break-inside:avoid; overflow:hidden;">';
                    for (let c = 0; c < cols; c++) {
                        if (c === stickerSlot) {
                            rowContent += renderSingleLabelHtml(code);
                        } else {
                            rowContent += '<div style="width:' + widthMm + 'mm; height:' + heightMm + 'mm; box-sizing:border-box;"></div>';
                        }
                    }
                    rowContent += '</div>';
                    rowsHtml += rowContent;
                }
            } else if (cols === 3 && rowLayout === 'all' && printScope === 'selected') {
                for (let cCount = 0; cCount < printCopies; cCount++) {
                    let code = barcodesList[0] || '123456';
                    let rowContent = '<div class="print-row" style="display:flex; flex-direction:row; width:' + rowWidthMm + 'mm; height:' + heightMm + 'mm; page-break-after:always; page-break-inside:avoid; overflow:hidden;">';
                    for (let c = 0; c < cols; c++) {
                        rowContent += renderSingleLabelHtml(code);
                    }
                    rowContent += '</div>';
                    rowsHtml += rowContent;
                }
            } else {
                for (let r = 0; r < totalLabels.length; r += cols) {
                    let rowContent = '<div class="print-row" style="display:flex; flex-direction:row; width:' + rowWidthMm + 'mm; height:' + heightMm + 'mm; page-break-after:always; page-break-inside:avoid; overflow:hidden;">';
                    for (let c = 0; c < cols; c++) {
                        let idx = r + c;
                        if (idx < totalLabels.length) {
                            let code = totalLabels[idx];
                            rowContent += renderSingleLabelHtml(code);
                        } else {
                            rowContent += '<div style="width:' + widthMm + 'mm; height:' + heightMm + 'mm;"></div>';
                        }
                    }
                    rowContent += '</div>';
                    rowsHtml += rowContent;
                }
            }

            return '<!DOCTYPE html>' +
                '<html>' +
                '<head>' +
                '<meta charset="utf-8">' +
                '<title>Print Barcode - ' + escapeHtmlText(prodName) + '</title>' +
                '<style>' +
                '@page { size: ' + rowWidthMm + 'mm ' + heightMm + 'mm; margin: 0; }' +
                '* { box-sizing: border-box; margin: 0; padding: 0; }' +
                'html, body { margin: 0; padding: 0; background: #fff; width: ' + rowWidthMm + 'mm; color: #000; font-family: Arial, Helvetica, sans-serif; overflow: hidden; }' +
                '.print-row { display: flex; flex-direction: row; width: ' + rowWidthMm + 'mm; height:' + heightMm + 'mm; page-break-after: always; break-after: page; page-break-inside: avoid; break-inside: avoid; overflow: hidden; }' +
                '.print-row:last-child { page-break-after: auto; break-after: auto; }' +
                '.company-name, .address, .product-name, .price-line { color: #000; }' +
                '@media print {' +
                '  @page { size: ' + rowWidthMm + 'mm ' + heightMm + 'mm; margin: 0; }' +
                '  html, body { width: ' + rowWidthMm + 'mm !important; margin: 0 !important; padding: 0 !important; background: #fff; }' +
                '  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; color: #000 !important; }' +
                '}' +
                '</style>' +
                '</head>' +
                '<body>' +
                rowsHtml +
                '<script>' +
                'window.onafterprint = function() { window.close(); };' +
                (autoPrint ? 'setTimeout(function() { window.focus(); window.print(); }, 500);' : '') +
                '<\/script>' +
                '</body>' +
                '</html>';
        }

        function openPrintTab(preparingMessage) {
            let features = 'popup=yes,width=920,height=720,scrollbars=yes,resizable=yes';
            let w = window.open('about:blank', '_blank', features);
            if (!w) {
                w = window.open('about:blank', '_blank');
            }
            if (!w) {
                return null;
            }
            try {
                w.document.open();
                w.document.write(
                    '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' +
                    '<title>Print Barcode</title></head><body style="font-family:Arial,sans-serif;padding:24px;text-align:center;">' +
                    (preparingMessage || 'Preparing labels...') + '</body></html>'
                );
                w.document.close();
                w.focus();
            } catch (e) {}
            return w;
        }

        function writePrintTab(w, html) {
            if (!w || w.closed) return false;
            try {
                w.document.open();
                w.document.write(html);
                w.document.close();
                w.focus();
                return true;
            } catch (e) {
                return false;
            }
        }

        // ===== Modal Open Trigger Handler =====
        $(document).on('click', '.print-barcode-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            let $btn = $(this);
            let productId = $btn.data('id');
            let productName = $btn.attr('data-name') || 'Product';
            let rawBarcode = String($btn.attr('data-barcode') || $btn.data('barcode') || $btn.attr('data-sku') || '').trim();
            let priceVal = $btn.attr('data-price') || $btn.data('price') || '';
            let mrpVal = $btn.attr('data-mrp') || $btn.data('mrp') || priceVal;

            $('#pbProductId').val(productId);
            $('#pbProductName').text(productName);
            $('#pbBarcode').val(rawBarcode);
            $('#pbPrice').val(priceVal);
            $('#pbMrp').val(mrpVal);
            $('#pbPrintCopies').val(1);
            $('#pbPrintScope').val('selected');
            $('#pbLabelSize').val('35x25x3');
            $('#pbRowLayoutAll').prop('checked', true).trigger('change');
            $('#pbSlotLeft').prop('checked', true).trigger('change');
            $('#pbIncludeTitle').prop('checked', true);

            // Split barcodes if comma separated
            pbBarcodes = [];
            if (rawBarcode) {
                let parts = rawBarcode.split(',').map(s => s.trim()).filter(Boolean);
                pbBarcodes = parts.length > 0 ? parts : [rawBarcode];
            } else {
                pbBarcodes = ['123456'];
            }

            $('#pbCurrentEditBarcode').val(pbBarcodes[0] || '');
            $('#pbBarcode_input').val('');

            fillRateSelect(priceVal, mrpVal, pbBarcodes[0]);
            renderPbChips();
            updateBarcodePreview();

            $('#printBarcodeModal').modal('show');
        });

        // Add barcode tag in modal
        $('#add_pbBarcode_btn').on('click', function(e) {
            e.preventDefault();
            let val = $('#pbBarcode_input').val().trim();
            if (!val) return;
            if (pbBarcodes.indexOf(val) === -1) {
                pbBarcodes.push(val);
                $('#pbCurrentEditBarcode').val(val);
                renderPbChips();
                updateBarcodePreview();
            }
            $('#pbBarcode_input').val('');
        });

        // Remove barcode tag in modal
        $(document).on('click', '.pb-remove-chip', function(e) {
            e.stopPropagation();
            let code = String($(this).data('barcode'));
            let idx = pbBarcodes.indexOf(code);
            if (idx !== -1) {
                pbBarcodes.splice(idx, 1);
                if ($('#pbCurrentEditBarcode').val() === code) {
                    $('#pbCurrentEditBarcode').val(pbBarcodes[0] || '');
                }
                renderPbChips();
                updateBarcodePreview();
            }
        });

        // Select chip to make it active
        $(document).on('click', '.pb-chip-selectable', function() {
            let barcode = String($(this).data('barcode'));
            $('#pbCurrentEditBarcode').val(barcode);
            renderPbChips();
            updateBarcodePreview();
        });

        // Rate select change
        $('#pbRateSelect').on('change', function() {
            let $opt = $(this).find('option:selected');
            if ($opt.val()) {
                let p = $opt.attr('data-price') || '';
                let m = $opt.attr('data-mrp') || '';
                $('#pbPrice').val(p);
                $('#pbMrp').val(m);
            }
            updateBarcodePreview();
        });

        // Company header toggle
        $('#pbIncludeTitle').on('change', updateBarcodePreview);

        // Template select
        $('#pbBarcodeTemplate').on('change', updateBarcodePreview);

        // Label size change
        $('#pbLabelSize').on('change', updateBarcodePreview);

        // Row layout (All 3 in row vs 1 sticker only)
        $('input[name="pb_row_layout"]').on('change', function() {
            let mode = $(this).val();
            if (mode === 'single') {
                $('#pbStickerSlotWrap').show();
                $('#pbRowLayoutAllIcon').removeClass('fa-check-circle').addClass('fa-circle');
                $('#pbRowLayoutSingleIcon').removeClass('fa-circle').addClass('fa-check-circle');
            } else {
                $('#pbStickerSlotWrap').hide();
                $('#pbRowLayoutAllIcon').removeClass('fa-circle').addClass('fa-check-circle');
                $('#pbRowLayoutSingleIcon').removeClass('fa-check-circle').addClass('fa-circle');
            }
            updateBarcodePreview();
        });

        // Sticker slot selection (Left, Middle, Right)
        $('input[name="pb_sticker_slot"]').on('change', function() {
            let slot = parseInt($(this).val(), 10);
            $('#pbSlotLeftIcon, #pbSlotMiddleIcon, #pbSlotRightIcon').removeClass('fa-check-circle').addClass('fa-circle');
            if (slot === 0) $('#pbSlotLeftIcon').removeClass('fa-circle').addClass('fa-check-circle');
            if (slot === 1) $('#pbSlotMiddleIcon').removeClass('fa-circle').addClass('fa-check-circle');
            if (slot === 2) $('#pbSlotRightIcon').removeClass('fa-circle').addClass('fa-check-circle');
            updateBarcodePreview();
        });

        // Click on blank slot to select that slot
        $(document).on('click', '.pb-thermal-sticker-slot.blank-slot', function() {
            let s = $(this).data('slot');
            if (s !== undefined) {
                $(`input[name="pb_sticker_slot"][value="${s}"]`).prop('checked', true).trigger('change');
            }
        });

        // Print & Save execution
        $('#saveAndPrintBarcodeBtn').off('click').on('click', function() {
            let productId = $('#pbProductId').val();
            let printCopies = Math.max(1, parseInt($('#pbPrintCopies').val(), 10) || 1);
            let printScope = $('#pbPrintScope').val() || 'selected';
            let printLabelSize = $('#pbLabelSize').val() || '35x25x3';
            let printLabelFormat = $('#pbBarcodeTemplate').val() || 'classic';

            let sizeParts = { width: 35, height: 25, cols: 3 };
            if (printLabelSize === '35x25x1') {
                sizeParts = { width: 35, height: 25, cols: 1 };
            } else if (printLabelSize === '38x25x2') {
                sizeParts = { width: 38, height: 25, cols: 2 };
            } else if (printLabelSize === '25x50x2') {
                sizeParts = { width: 25, height: 50, cols: 2 };
            }

            let standardHtml = buildStandardBarcodePrintHtml(printLabelFormat, sizeParts, printCopies, true, printScope);
            let printWindow = openPrintTab('Preparing labels...');

            if (printWindow) {
                writePrintTab(printWindow, standardHtml);
            } else {
                Swal.fire('Error', 'Failed to open print window. Please allow popups and try again.', 'error');
            }

            // Save any updated barcode / price in backend asynchronously
            let authToken = localStorage.getItem("authToken");
            let formData = new FormData($('#printBarcodeForm')[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/api/update-product-barcode-info',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': 'Bearer ' + authToken
                }
            });

            $('#printBarcodeModal').modal('hide');
        });


        $(document).on('click', '.toggle-details', function() {
            let icon = $(this).find('i');
            if (icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle')
                    .addClass('fa-minus-circle')
                    .css('color', 'red'); // or any color you want for minus icon
            } else {
                icon.removeClass('fa-minus-circle')
                    .addClass('fa-plus-circle')
                    .css('color', '#ff9f43'); // your desired orange color
            }
        });
    </script>
@endpush
