@extends('layout.app')

@section('title', 'Branch List')

@section('content')
<style>
    #DataTables_Table_0_info {
        float: left;
    }

    .table-scroll-top {
        display: none;
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

    /* Desktop: show all columns normally */
    @media (min-width: 769px) {

        #subbranchTable thead th,
        #subbranchTable tbody td {
            display: table-cell !important;
        }

        /* Hide the Details toggle column on desktop */
        #subbranchTable thead th.details-column,
        #subbranchTable tbody td.details-cell {
            display: none !important;
        }
    }

    /* Mobile: hide non-essential columns, show Details toggle */
    @media (max-width: 768px) {

        #subbranchTable thead th:nth-child(n+3),
        #subbranchTable tbody td:nth-child(n+3) {
            display: none !important;
        }

        #subbranchTable thead th.details-column,
        #subbranchTable tbody td.details-cell {
            display: table-cell !important;
            text-align: center;
            vertical-align: middle;
            width: 50px;
        }

        .toggle-details i {
            font-size: 18px;
        }

        .toggle-details {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ff9f43;
        }

        .toggle-details i {
            color: #fff;
            font-size: 12px;
            line-height: 1;
        }

        .toggle-details.is-open {
            background: #dc3545;
        }

        /* Style for staff name wrapping */
        #subbranchTable tbody td:first-child {
            display: block !important;
            max-width: calc(100vw - 100px) !important;
        }

        /* Keep name row horizontal, details stacked below */
        #subbranchTable tbody td:first-child>a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            width: 100% !important;
        }

        #subbranchTable tbody td:first-child .collapse,
        #subbranchTable tbody td:first-child .collapse .card {
            width: 100% !important;
        }

        .staff-name {
            display: inline-block !important;
            max-width: 100% !important;
            font-size: 14px !important;
            word-break: break-word !important;
            hyphens: auto !important;
            -webkit-hyphens: auto !important;
            -ms-hyphens: auto !important;
        }

        /* Limit to 2 lines with ellipsis */
        .staff-name.truncated {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            flex: 1;
            min-width: 0;
        }
    }

    @media screen and (max-width: 767px) {
        .table-scroll-top {
            display: block;
        }

        div#DataTables_Table_0_filter {
            margin-top: 10px !important;
        }

        .table-top {
            flex-direction: row;
            margin-bottom: 0 !important;
        }

        .table-top .wordset {
            margin-top: 0 !important;
        }

        .dataTables_length {
            margin-left: .8rem !important;
            margin-bottom: .1rem !important;
        }

        .dataTables_filter {
            text-align: left !important;
        }

        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter,
        div.dataTables_wrapper div.dataTables_info,
        div.dataTables_wrapper div.dataTables_paginate {
            text-align: start;
        }
    }
    .staff-name.truncated {
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: anywhere;
    max-width: 750px;
}
        /* Custom Pagination styles */
        .table-pagination-btn {
            background-color: #f8f9fa;
            border-color: #ddd;
            color: #333;
            min-width: 32px;
            padding: 0.25rem 0.5rem;
        }

        .table-pagination-btn:not(:disabled):hover,
        .table-pagination-btn.active {
            background-color: #FF9F43;
            border-color: #FF9F43;
            color: #fff;
        }
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>All Branches</h4>
        </div>
        <div class="page-btn">
                <a href="{{ route('subbranch.add') }}" class="btn btn-added btn-sm" id="new-branch-btn">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">
                    New Branch
                </a>
            </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-search-wrap mb-3" style="max-width: 280px;">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" class="form-control" id="customSearch" placeholder="Search...">
                </div>
            </div>

            <div class="table-scroll-top">
                <div></div>
            </div>
            <div class="table-responsive">
                <table class="table" id="subbranchTable">
                    <thead>
                        <tr>
                            <th>Branch Name</th>
                            <th class="details-column">Details</th>
                            <th>Branch Role</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3" id="paginationWrapper">
                <div class="d-flex align-items-center gap-2">
                    <label for="perPage" class="mb-0 text-muted" style="font-size: 14px;">Show per page :</label>
                    <select id="perPage" class="form-select form-select-sm" style="width: auto;">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span id="pageInfo" class="ms-3 text-muted" style="font-size: 14px;"></span>
                </div>
                <div class="d-flex align-items-center gap-2" id="paginationControls">
                    <!-- Pagination buttons generated via JS -->
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        const imagePath = "{{ env('ImagePath') }}";
        fetchCustomers(); // Call function on page load

        var allCustomers = [];
        var filteredCustomers = [];
        var currentPage = 1;
        var perPage = 10;

        $('#perPage').on('change', function() {
            perPage = Number($(this).val()) || 10;
            currentPage = 1;
            renderTable();
        });

        $('#customSearch').on('input', function() {
            const query = $(this).val().toLowerCase();
            filteredCustomers = allCustomers.filter(c => {
                return (c.name || '').toLowerCase().includes(query) ||
                       (c.email || '').toLowerCase().includes(query) ||
                       (c.phone || '').toLowerCase().includes(query) ||
                       (c.role || '').toLowerCase().includes(query) ||
                       (c.user_detail?.city || '').toLowerCase().includes(query) ||
                       (c.user_detail?.country || '').toLowerCase().includes(query);
            });
            currentPage = 1;
            renderTable();
        });

        function fetchCustomers() {
            $.ajax({
                url: "/api/getAllSubbranch",
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $("#subbranchTable tbody").html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status && response.data.length > 0) {
                        allCustomers = response.data;
                        filteredCustomers = [...allCustomers];

                        // ✅ Check sub-admin count and disable "New Branch" button if >= 2
                        let subAdminCount = allCustomers.filter(c => c.role === 'sub-admin').length;
                        if (subAdminCount >= 2) {
                            $('#new-branch-btn')
                                .attr('href', 'javascript:void(0);')
                                .css({'opacity': '0.5', 'cursor': 'not-allowed', 'pointer-events': 'none'})
                                .attr('title', 'Maximum 2 sub-branches limit reached');
                        } else {
                            $('#new-branch-btn')
                                .attr('href', "{{ route('subbranch.add') }}")
                                .css({'opacity': '', 'cursor': '', 'pointer-events': ''})
                                .removeAttr('title');
                        }

                        renderTable();
                    } else {
                        allCustomers = [];
                        filteredCustomers = [];
                        renderTable();
                        $('#new-branch-btn')
                            .attr('href', "{{ route('subbranch.add') }}")
                            .css({'opacity': '', 'cursor': '', 'pointer-events': ''})
                            .removeAttr('title');
                    }
                },
                error: function() {
                    $("#subbranchTable tbody").html('<tr><td colspan="8" class="text-center text-danger">Error loading data.</td></tr>');
                },
            });
        }

        function renderTable() {
            if (filteredCustomers.length === 0) {
                $("#subbranchTable tbody").html('<tr><td colspan="8" class="text-center">No branches found.</td></tr>');
                $('#pageInfo').text('');
                $('#paginationControls').empty();
                return;
            }

            const total = filteredCustomers.length;
            const totalPages = Math.ceil(total / perPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const currentData = filteredCustomers.slice(start, end);

            let tableBody = "";

            function capitalizeFirstLetter(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            $.each(currentData, function(index, customer) {
                let isAdmin = customer.role === 'admin';

                let deleteBtn = isAdmin
                    ? `<a class="me-3" href="javascript:void(0);" title="Admin cannot be deleted" style="opacity:0.4; cursor:not-allowed;">
                            <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg" alt="Delete">
                       </a>`
                    : `<a class="me-3 confirm-text delete-btn" href="javascript:void(0);" data-id="${customer.id}">
                            <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg" alt="Delete">
                       </a>`;

                let actionButtons = `
                    <a class="me-3" href="/view-subbranch/${customer.id}">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/eye.svg" alt="View">
                    </a>
                    <a class="me-3" href="/edit-subbranch/${customer.id}">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/edit.svg" alt="Edit">
                    </a>
                    ${deleteBtn}
                `;

                tableBody += `
                <tr>
                    <td class="productimgname">
                        <a href="/view-subbranch/${customer.id}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                            <p class="product-img mb-0" style="margin: 0;">
                                <img src="${customer.profile_image ? imagePath + '/storage/' + customer.profile_image : imagePath + 'admin/assets/img/customer/customer5.jpg'}"
                                    alt="Product"
                                    style="width: 50px; height: auto;"
                                >
                            </p>
                            <p class="staff-name truncated" style="margin: 0;">${capitalizeFirstLetter(customer.name)}</p>
                        </a>

                        <!-- Collapsible Details (visible only on mobile) -->
                        <div class="collapse mt-2 d-lg-none" id="details-${customer.id}">
                            <div class="card card-body p-2 bg-light border">
                                <p class="mb-1"><strong>Role:</strong> ${capitalizeFirstLetter(customer.role) || 'N/A'}</p>
                                <p class="mb-1"><strong>Phone:</strong> ${customer.phone || 'N/A'}</p>
                                <p class="mb-1"><strong>Email:</strong> ${customer.email || 'N/A'}</p>
                                <p class="mb-1"><strong>City:</strong> ${capitalizeFirstLetter(customer.user_detail?.city) || 'N/A'}</p>
                                <p class="mb-1"><strong>Country:</strong> ${capitalizeFirstLetter(customer.user_detail?.country) || 'N/A'}</p>
                                <div class="mt-2">
                                    ${actionButtons}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="details-cell">
                        <a href="#details-${customer.id}" class="toggle-details" data-bs-toggle="collapse">
                            <i class="fas fa-plus"></i>
                        </a>
                    </td>
                    <td>${capitalizeFirstLetter(customer.role) || 'N/A'}</td>
                    <td>${customer.phone || 'N/A'}</td>
                    <td>${customer.email || 'N/A'}</td>
                    <td>${capitalizeFirstLetter(customer.user_detail?.city) || 'N/A'}</td>
                    <td>${capitalizeFirstLetter(customer.user_detail?.country) || 'N/A'}</td>
                    <td>
                        ${actionButtons}
                    </td>
                </tr>
                `;
            });

            $("#subbranchTable tbody").html(tableBody);

            // Update Pagination UI
            const currentEnd = Math.min(start + perPage, total);
            $('#pageInfo').text(`${start + 1} - ${currentEnd} of ${total} items`);

            let paginationHtml = '';
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    paginationHtml += `<button type="button" class="btn btn-sm btn-outline-secondary table-pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    paginationHtml += `<span class="px-1 text-muted">...</span>`;
                }
            }
            $('#paginationControls').html(paginationHtml);

            // Top scrollbar sync
            const topScroll = document.querySelector('.table-scroll-top');
            const tableResponsive = document.querySelector('.table-responsive');
            const tableElement = document.querySelector('#subbranchTable');

            if (topScroll && tableResponsive && tableElement) {
                const topInnerDiv = topScroll.querySelector('div');
                topInnerDiv.style.width = tableElement.scrollWidth + 'px';

                topScroll.onscroll = () => { tableResponsive.scrollLeft = topScroll.scrollLeft; };
                tableResponsive.onscroll = () => { topScroll.scrollLeft = tableResponsive.scrollLeft; };
            }
        }

        window.goToPage = function(page) {
            currentPage = page;
            renderTable();
        };


        // Handle Delete
        $(document).on("click", ".delete-btn", function() {
            let customerId = $(this).data("id");

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
                        url: `/api/deleteSubbranch/${customerId}`,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            // Swal.fire("Deleted!", response.message, "success");
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#ff9f43", // Custom OK button color
                                confirmButtonText: "OK"
                            });
                            fetchCustomers(); // Refresh the table after deletion
                        },
                        error: function(xhr) {
                            let errorMessage = "Failed to delete branch.";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                            Swal.fire({
                                title: "Error!",
                                text: errorMessage,
                                icon: "error",
                                confirmButtonColor: "#ff9f43", // custom orange color
                                confirmButtonText: "OK"
                            });
                        },
                    });
                }
            });
        });

        // Toggle +/− icon for mobile details
        $(document).on("click", ".toggle-details", function() {
            const $toggle = $(this);
            const $icon = $toggle.find("i");
            $icon.toggleClass("fa-plus fa-minus");
            $toggle.toggleClass("is-open");
        });

    });
</script>
@endpush
