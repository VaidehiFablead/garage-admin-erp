@extends('layout.app')

@section('title', 'Brand List')

@section('content')
    {{-- <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            min-width: 0 !important;
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

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
            }

            .table-responsive {
                overflow-x: auto;
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

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .toggle-details i {
                font-size: 18px;
            }

            /* Style for brand name wrapping */
            table.datanew tbody td:first-child {
                display: flex !important;
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
            }

            table.datanew tbody td:first-child a {
                align-items: center !important;
                text-align: left !important;
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.3 !important;
            }

            .brand-name {
                display: inline-block !important;
                /* max-width: calc(100% - 60px) !important; */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* Limit to 2 lines with ellipsis */
            .brand-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        /* Tablet specific fixes */
        @media screen and (width: 768px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
            }

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

        /* Brand specific styling */
        .brand-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
        }

        /* Brand name proper wrapping */
        .brand-name {
            display: block;
            flex: 1;
            min-width: 0;
            /* ⭐ REQUIRED for wrapping inside flex */
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.3;
        }

        /* Optional: limit to 2 lines */
        .brand-name.truncated {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style> --}}
    <style>
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

        /* Correct wrapping behaviour */
        .brand-name {
            display: inline-block;
            max-width: calc(100% - 50px);
            margin-left: 8px;
            font-size: 14px;
            line-height: 1.4;

            white-space: normal !important;
            /* allow wrapping */
            word-break: keep-all !important;
            /* DO NOT break words */
            overflow-wrap: normal !important;
            /* wrap only at spaces */
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

            .collapse .card-body .mt-2 {
                display: flex;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                /* gap: 2px; */
                flex-wrap: wrap;
            }

            .collapse .card-body .mt-2 a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 44px;
                /* Better touch target */
                min-height: 44px;
                padding: 8px;
                background-color: #f8f9fa;
                border-radius: 6px;
                transition: background-color 0.2s;
            }

            .collapse .card-body .mt-2 a:hover,
            .collapse .card-body .mt-2 a:active {
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

            /* Style for category name wrapping */
            table.datanew tbody td:first-child {
                display: flex !important;
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

            .brand-name {
                display: inline-block !important;
                max-width: calc(100% - 50px) !important;
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* Limit to 2 lines with ellipsis */
            .brand-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        .brand-details-row {
            display: none;
        }

        .brand-details-row.show {
            display: table-row;
        }

        .brand-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.brand-details-row td.brand-details-content {
            display: table-cell !important;
            width: 100% !important;
            max-width: none !important;
        }

        .brand-details-list {
            margin-bottom: 15px;
        }

        .brand-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .brand-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .brand-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .brand-detail-value-simple {
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
        }

        .mobile-action-buttons-simple a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #1b2850;
            margin-right: 0 !important;
        }

        .mobile-action-buttons-simple img {
            width: 18px;
            height: 18px;
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
            .brand-details-row {
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

            /* Proper wrapping without breaking layout */
            .brand-name {
                max-width: 300px;
                white-space: normal !important;
                word-break: break-word !important;
                overflow-wrap: anywhere;
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

        /* Brand specific styling */
        .brand-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
        }

        /* Brand name proper wrapping */
        .brand-name {
            display: block;
            flex: 1;
            min-width: 0;
            /* ⭐ REQUIRED for wrapping inside flex */
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.3;
        }

        /* Optional: limit to 2 lines */
        .brand-name.truncated {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

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
    @endif

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Brands</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('brand.add') }}" class="btn btn-sm btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New
                    Brand
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
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="card d-none" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Brand Name">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Brand Description">
                                </div>
                            </div>
                            <div class="col-lg-1 col-sm-6 col-12 ms-auto">
                                <div class="form-group">
                                    <a class="btn btn-filters ms-auto"><img
                                            src="{{ env('ImagePath') . 'admin/assets/img/icons/search-whites.svg' }}"
                                            alt="img"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="table-scroll-top">
                    <div></div>
                </div> --}}
                {{-- <div class="table-responsive">
                    <table class="table datanew"> --}}
                <div class="table-container">
                    <table class="table datanew" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Brand Name</th>
                                <th class="details-column">Details</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by JavaScript -->
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
@endsection

@push('js')
    {{-- <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable(); // Initialize DataTable
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            // console.log(selectedSubAdminId);

            let url = "/api/getAllBrand";
            if (selectedSubAdminId) {
                url += `?selectedSubAdminId=${selectedSubAdminId}`;
            }

            fetchBrands(); // Call function to load data

            function fetchBrands() {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let brands = response.data;
                            let tableBody = [];

                            // Function to capitalize first letter of each word
                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }

                            brands.forEach((brand) => {
                                let brandName = capitalizeWords(brand.name);
                                // let createdAt = brand.created_at ? brand.created_at.split("T")[
                                //     0] : "N/A";
                                let createdAt = "N/A";
                                if (brand.created_at) {
                                    let dateParts = brand.created_at.split("T")[0].split("-");
                                    if (dateParts.length === 3) {
                                        createdAt = dateParts[2] + "/" + dateParts[1] + "/" +
                                            dateParts[0];
                                    }
                                }

                                // Details toggle for mobile
                                let detailsToggle = `
                                    <a href="#details-${brand.id}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                    </a>
                                `;

                                // Image URL
                                const imagePath = "{{ env('ImagePath') }}";
                                let imageUrl = brand.logo ?
                                    `${imagePath}/storage/${brand.logo}` :
                                    `${imagePath}admin/assets/img/product/noimage.png`;

                                tableBody.push([
                                    // Column 1: Brand Name with image AND collapsible details
                                    `<div>
                                        <div style="display: flex; align-items: center;">
                                            <a href="javascript:void(0);" class="d-flex align-items-center">
                                                <img src="${imageUrl}"
                                                    alt="brand"
                                                    class="brand-image">
                                                <span class="brand-name truncated">${brandName}</span>
                                            </a>
                                        </div>

                                        <!-- Collapsible Details (visible only on mobile) - AFTER the brand name -->
                                        <div class="collapse mt-2 d-lg-none" id="details-${brand.id}">
                                            <div class="card card-body p-2 bg-light border">
                                                <p class="mb-1"><strong>Created At:</strong> ${createdAt}</p>
                                                <div class="mt-2">
                                                    ${getActionButtons(brand.id)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`,

                                    // Column 2: Details Toggle (only for mobile)
                                    detailsToggle,

                                    createdAt,

                                    // Column 3: Action Buttons (hidden on mobile)
                                    getActionButtons(brand.id)
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
                            $(".datanew tbody").html('<tr><td colspan="3">No brands found</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        // console.log("Error:", xhr);
                    },
                });
            }

            // Helper function to generate action buttons
            function getActionButtons(brandId) {
                return `
                    <a class="me-3" href="/edit-brand/${brandId}">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                    </a>
                    <a class="me-3 confirm-text delete-brand" data-id="${brandId}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                    </a>
                `;
            }

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

            // Delete brand function
            $(document).on('click', '.delete-brand', function() {
                var brandId = $(this).data('id');
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
                            url: `/api/deleteBrand/${brandId}`,
                            type: 'POST',
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
                                    }).then(() => {
                                        fetchBrands(); // Refresh the table
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
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
        });
    </script> --}}
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
                "ordering": false // ✅ Prevent automatic sorting
            });

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            window.brandDataMap = {};

            function buildBrandExpandableRowContent(brand) {
                return `
                    <td colspan="4" class="brand-details-content">
                        <div class="brand-details-list">
                            <div class="brand-detail-row-simple">
                                <span class="brand-detail-label-simple">Created At:</span>
                                <span class="brand-detail-value-simple">${brand.createdAt}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${getActionButtons(brand.id)}
                        </div>
                    </td>
                `;
            }

            window.toggleBrandRowDetails = function(brandId) {
                const btn = $(`.mobile-toggle-btn-table[data-brand-id="${brandId}"]`);
                if (btn.length === 0) {
                    return;
                }

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.brand-details-row[data-brand-id="${brandId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const brandData = window.brandDataMap && window.brandDataMap[brandId];
                    if (!brandData) {
                        return;
                    }

                    detailsRow = $('<tr>')
                        .addClass('brand-details-row')
                        .attr('data-brand-id', brandId)
                        .html(buildBrandExpandableRowContent(brandData));
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
            fetchBrands(currentPage);

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchBrands(1);
            });

            // Fetch brands with pagination and search
            function fetchBrands(page = 1) {
                let url = `/api/getAllBrand?page=${page}&per_page=${perPage}`;
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
                            let brands = response.data;
                            let pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;

                            updatePaginationUI(pagination);

                            let tableBody = [];
                            window.brandDataMap = {};

                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }

                            brands.forEach((brand) => {
                                let brandName = capitalizeWords(brand.name);
                                let createdAt = "N/A";
                                if (brand.created_at) {
                                    let dateParts = brand.created_at.split("T")[0].split("-");
                                    if (dateParts.length === 3) {
                                        createdAt = dateParts[2] + "/" + dateParts[1] + "/" +
                                            dateParts[0];
                                    }
                                }

                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-table" onclick="toggleBrandRowDetails('${brand.id}')" data-brand-id="${brand.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                const imagePath = "{{ env('ImagePath') }}";
                                let imageUrl = brand.logo ?
                                    `${imagePath}/storage/${brand.logo}` :
                                    `${imagePath}admin/assets/img/product/noimage.png`;

                                window.brandDataMap[brand.id] = {
                                    id: brand.id,
                                    createdAt: createdAt
                                };

                                tableBody.push([
                                    `<div>
                                    <div style="display: flex; align-items: center;">
                                        <a href="javascript:void(0);" class="d-flex align-items-center">
                                            <img src="${imageUrl}" alt="brand" class="brand-image">
                                            <span class="brand-name truncated">${brandName}</span>
                                        </a>
                                    </div>
                                </div>`,
                                    detailsToggle,
                                    createdAt,
                                    getActionButtons(brand.id)
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();

                            // Sync top scrollbar (optional)
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
                            $(".datanew tbody").html('<tr><td colspan="4">No brands found</td></tr>');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching brands:", xhr);
                    },
                });
            }

            // Helper: action buttons
            function getActionButtons(brandId) {
                return `
                <a class="me-3" href="/edit-brand/${brandId}">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                </a>
                <a class="me-3 confirm-text delete-brand" data-id="${brandId}" href="javascript:void(0);">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                </a>
            `;
            }

            // Update pagination UI
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

            // Handle pagination clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchBrands(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchBrands(1);
            });

            // Toggle details icon
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
                }
            });

            // Delete brand
            $(document).on('click', '.delete-brand', function() {
                var brandId = $(this).data('id');
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
                            url: `/api/deleteBrand/${brandId}`,
                            type: 'POST',
                            headers: {
                                "Authorization": "Bearer " + authToken
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
                                    }).then(() => {
                                        fetchBrands(
                                            currentPage); // stay on same page
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
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
        });
    </script>
@endpush
