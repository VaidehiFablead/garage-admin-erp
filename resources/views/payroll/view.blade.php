@extends('layout.app')
@section('title', 'Payroll List')
@section('content')
<style>
        /* .sorting_1 {
                    display: flex !important;
                    align-items: center !important;
                    gap: 5px !important;
                } */

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

        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table.datanew {
            width: 100%;
        }

        table.datanew td.sorting_1 {
            display: table-cell !important;
        }


        /* Desktop and general rules for the first column */
        /* table.datanew tbody td:first-child {
            max-width: 260px;
            width: 260px;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
        } */

        /* The anchor containing image and name */
        table.datanew tbody td:nth-child(2) a.d-flex {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .staff-name {
            display: block;
            flex: 1;
            min-width: 80px;
            max-width: 200px;
            word-break: break-word;
            line-height: 1.3;
        }

          /* Add styles for search input and pagination (from category list) */
        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .btn-searchset {            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
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
            table.datanew {
                min-width: 1100px;
            }

            .staff-name {
                display: block !important;
                max-width: 250px;
                /* adjust as needed */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
            }

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(9) {
                display: none !important;
            }

            .staff-name {
                display: block !important;
                max-width: 250px;
                /* Adjust based on your layout */
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {

            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            /* Show only Checkbox, Staff Name and Details columns on mobile */
            table.datanew thead th:first-child,
            table.datanew tbody td:first-child,
            table.datanew thead th:nth-child(2),
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(9) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .toggle-details i {
                font-size: 24px;
            }

            /* Second column (Staff Name column) */
            table.datanew tbody td:nth-child(2) {
                max-width: 260px;
                /* control column width */
                white-space: normal !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
            }

            /* Image + name container */
            table.datanew tbody td:nth-child(2) a.d-flex {
                display: flex;
                align-items: center;
                gap: 2px;
                width: 100%;
            }

            /* Staff name text */
            table.datanew tbody td:nth-child(2) .staff-name {
                display: block;
                flex: 1;
                min-width: 0;
                white-space: normal;
                word-break: break-word;
                overflow-wrap: anywhere;
                line-height: 1.3;
                font-size: 14px;
            }

            /* Limit to 2 lines with ellipsis */
            /* .staff-name.truncated {
                            display: -webkit-box !important;
                            -webkit-line-clamp: 2 !important;
                            -webkit-box-orient: vertical !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        } */
        }

        /* Tablet specific fixes */
        @media screen and (width: 768px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(9) {
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

        /* Staff specific styling */
        .staff-image {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;

        }

        /* Collapsible details styling */
        .collapse-details {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #ff9f43;
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

        .mobile-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .mobile-actions a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .mobile-actions a:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .mobile-actions svg {
            width: 18px;
            height: 18px;
        }
    /* DataTables specific styling for the exact look */
    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 10px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 8px;
        margin-left: 5px;
    }
    .dataTables_wrapper .dataTables_info {
        float: left;
        padding-top: 15px;
        color: #555;
    }
    .dataTables_wrapper .dataTables_paginate {
        float: right;
        padding-top: 15px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        background: #f8f9fa;
        color: #555 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #ff9f43 !important;
        color: #fff !important;
        border: 1px solid #ff9f43;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e2e6ea;
    }
    table.dataTable thead th {
        background-color: #1f2937 !important;
        color: #fff !important;
        text-transform: capitalize;
        font-weight: 600;
        font-size: 14px;
        border-bottom: none;
        padding: 12px 10px;
    }
    table.dataTable tbody td {
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .hr-btnbg {
        background: #ff9f43 !important;
        color: #fff !important;
        border-radius: 4px;
        font-weight: 500;
        padding: 6px 12px;
        font-size: 13px;
        transition: all 0.25s ease;
        border: none;
        white-space: nowrap;
    }

    .hr-btnbg:hover {
        background: #e88c30 !important;
        color: #fff !important;
    }

    .capitalize-text {
        text-transform: capitalize;
    }

    /* ── Payroll toolbar ── */
    .payroll-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
        border-radius: 8px 8px 0 0;
    }

    .payroll-toolbar h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #000;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .toolbar-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .toolbar-actions input[type="month"] {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        color: #495057;
        height: 36px;
        background: #f9fafb;
        cursor: pointer;
        outline: none;
        transition: border 0.2s;
    }

    .toolbar-actions input[type="month"]:focus {
        border-color: #ff9f43;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(255,159,67,0.15);
    }

    /* ── Search bar ── */
    .payroll-search-wrap {
        position: relative;
        max-width: 280px;
        width: 100%;
    }

    .payroll-search-wrap .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 15px;
        pointer-events: none;
    }

    .payroll-search-wrap input {
        padding-left: 34px !important;
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
        background: #f9fafb !important;
        font-size: 13px !important;
        height: 36px !important;
        transition: all 0.2s;
    }

    .payroll-search-wrap input:focus {
        border-color: #ff9f43 !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(255,159,67,0.12) !important;
        outline: none !important;
    }

    @media (max-width: 767px) {
        .payroll-toolbar {
            padding: 10px 12px;
        }
        .toolbar-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .payroll-search-wrap {
            max-width: 100%;
        }
    }
    </style>


<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1" aria-labelledby="addSalaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryLabel">Add Employee Salaries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Month Select -->
                <div class="mb-3">
                    <label for="salaryMonth" class="form-label">Select Month</label>
                    <input type="month" id="salaryMonth" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn hr-btnbg" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn hr-btnbg" id="openSalaryPage">OK</button>
            </div>

        </div>
    </div>
</div>
 <div class="content">
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card" style="border-radius:4px;border:none;">

            <!-- ══ Toolbar ══ -->
            <div class="payroll-toolbar">
                <h4>
                    Manage Payrolls
                </h4>

                @if (in_array(auth()->user()->role, ['hr', 'admin']))
                <div class="toolbar-actions">
                    @php
                        $now = new DateTime();
                        $currentMonth = date('Y-m');
                    @endphp

                    <input type="month" id="payrollMonthFilter" name="payrollMonth"
                        value="{{ request('month', $currentMonth) }}"
                        max="{{ $currentMonth }}" />

                    <button type="button" id="downloadMultipleBtn" class="btn hr-btnbg">
                        <i class="mdi mdi-download me-1"></i> Download Selected
                    </button>

                    <a href="{{ route('payroll.salary-details') }}" class="btn hr-btnbg">
                        + Group Salary
                    </a>

                    <button type="button" id="yearlySlipBtn" class="btn hr-btnbg">
                        <i class="mdi mdi-download me-1"></i> Yearly Slip
                    </button>

                    <button type="button" class="btn hr-btnbg">
                        Salary Sheet
                    </button>

                    <a href="/payroll" class="btn hr-btnbg">
                        + Add Payroll
                    </a>
                </div>
                @endif
            </div>

            <div class="card-body pt-3">
                <div class="table-container table-responsive">
                    <table class="table datanew w-100" id="payroll-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th>EMPLOYEE NAME</th>
                                <th class="desktop-only-col">BASE SALARY</th>
                                <th class="desktop-only-col">MONTH & YEAR</th>
                                <th class="desktop-only-col">NET SALARY</th>
                                <th class="desktop-only-col">PAYMENT DATE</th>
                                <th style="display: none;">CREATED AT</th>
                                <th class="desktop-only-col">ACTION</th>
                                <th class="details-column">DETAILS</th>
                            </tr>
                        </thead>
                        <tbody id="payroll-table-tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deduction breakdown modal (same as Add Payroll / Profile) -->
<div class="modal fade" id="listDeductionBreakdownModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listDeductionBreakdownModalLabel"><i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="listDeductionBreakdownLoading" style="display:block;">Loading...</div>
                <div id="listDeductionBreakdownContent" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>
 </div>

 <div class="loginIdName" id="userRole" data-role="{{ auth()->user()->role }}">
</div>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Get current month as default
        const currentMonth = new Date().toISOString().slice(0, 7); // "YYYY-MM" format

        // Get month from URL or use current month as default
        const urlParams = new URLSearchParams(window.location.search);
        const selectedMonth = urlParams.get('month') || currentMonth;

        // Set the month filter value
        const monthFilter = document.getElementById('payrollMonthFilter');
        if (monthFilter) {
            monthFilter.value = selectedMonth;
        }

        $('#groupsalary').on('click', function() {
            // Use selected month as default for Group Salary
            window.location.href = `/payroll/salary-details?month=${selectedMonth}`;
        });

        $('#yearlySlipBtn').on('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Yearly Slip',
                text: 'Select a year to download slips for that year.',
                showCancelButton: true,
                confirmButtonText: 'Download',
                cancelButtonText: 'Cancel'
            });
        });

        // Month filter change event
        if (monthFilter) {
            monthFilter.addEventListener('change', function() {
                const month = this.value;
                if (month) {
                    window.location.href = `/payrollview?month=${month}`;
                }
            });
        }


        const token = localStorage.getItem('authToken'); // JWT token

     function getUserRole() {
            if (!token || !token.includes('.')) return 'staff';
            try {
                let payload = JSON.parse(atob(token.split('.')[1]));
                return payload.role || 'staff';
            } catch (e) {
                return 'staff';
            }
}

        function fetchPayroll() {
            // Build URL with month filter
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            let apiUrl = '{{ url("/api/payroll/getAll") }}?month=' + selectedMonth;

            if (selectedSubAdminId) {
                apiUrl += '&selectedSubAdminId=' + selectedSubAdminId;
            }

            $.ajax({
                url: apiUrl,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const payrolls = response.data;
                        let tableRows = '';
                        let userRole = getUserRole(); // Get role from JWT
                        var baseImagePath = "{{ env('ImagePath', '') }}";
                        
                        payrolls.forEach((payroll) => {
                            // let imageUrl = payroll.profile_image ?
                            //     `/upload/${payroll.profile_image}` :
                            //     `${baseImagePath}upload/default-profile.jpg`;
                            let imageUrl = payroll.profile_image 
                    ? "{{ env('ImagePath') }}/storage/" + payroll.profile_image 
                    : "/public/admin/assets/img/customer/customer5.jpg";
                            let actionButtons = '';
                            let mobileActionsHtml = '';

                            const employeeId = payroll.employee_id || payroll.user_id;
                            const monthYear = payroll.month_year || '';
                            const empName = (payroll.username || '').replace(/"/g, '&quot;');
                            const infoIcon = `<a href="javascript:void(0)" role="button" class="text-info-custom action-icons payroll-deduction-info" title="Why was this amount deducted?" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="mdi mdi-information-outline"></i></a>`;
                            
                            if (userRole !== 'staff') {
                               actionButtons = `
                                    <a href="/payroll/profile/${payroll.id}" class="text-primary me-2">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="/payroll/${payroll.id}" class="text-warning me-2">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a href="#" class="text-danger delete-payroll me-2" data-id="${payroll.id}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a href="/payroll/download-slip/${payroll.id}" class="text-success me-2">
                                        <i class="fa fa-download"></i>
                                    </a>
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/payroll/profile/${payroll.id}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
                                        <a href="javascript:void(0)" role="button" class="btn btn-sm btn-info payroll-deduction-info" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="fa fa-information-circle"></i> Deduction</a>
                                        <a href="/payroll/${payroll.id}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-payroll" data-id="${payroll.id}"><i class="fa fa-trash"></i> Delete</a>
                                        <a href="/payroll/download-slip/${payroll.id}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                `;
                            } else {
                                actionButtons = `
                                    <a href="/payroll/profile/${payroll.id}" class="text-primary me-2" title="View"><i class="fa fa-eye"></i></a>
                                    <a href="/payroll/${payroll.id}" class="text-warning me-2" title="Edit"><i class="fa fa-pen"></i></a>
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/payroll/profile/${payroll.id}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
                                        <a href="/payroll/${payroll.id}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i> Edit</a>
                                        <a href="javascript:void(0)" role="button" class="btn btn-sm btn-info payroll-deduction-info" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="fa fa-information-circle"></i> Deduction</a>
                                    </div>
                                `;
                            }

                            const detailsToggle = `
                                <a href="#details-${payroll.id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                </a>
                            `;

                            const staffNameColumn = `
                                <div>
                                    <a href="javascript:void(0);" class="d-flex align-items-center">
                                        <img src="${imageUrl}" alt="staff" class="staff-image">
                                        <span class="staff-name capitalize-text">${payroll.username}</span>
                                    </a>
                                    <div class="collapse mt-2 d-lg-none" id="details-${payroll.id}">
                                        <div class="collapse-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Base Salary:</span>
                                                <span class="detail-value capitalize-text">${payroll.salary_amount}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Month & Year:</span>
                                                <span class="detail-value capitalize-text">${payroll.month_year}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Net Salary:</span>
                                                <span class="detail-value capitalize-text">${payroll.net_salary}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Payment Date:</span>
                                                <span class="detail-value capitalize-text">${payroll.payment_date}</span>
                                            </div>
                                            <div class="mobile-actions">
                                                ${mobileActionsHtml}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            tableRows += `
                                <tr data-id="${payroll.id}" data-employee-id="${payroll.employee_id || payroll.id}">
                                    <td>
                                        <input type="checkbox" class="payroll-checkbox form-check-input"
                                               data-id="${payroll.id}"
                                               data-employee-id="${payroll.employee_id || payroll.id}">
                                    </td>
                                    <td>${staffNameColumn}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.salary_amount}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.month_year}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.net_salary}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.payment_date}</td>
                                    <td class="capitalize-text" style="display: none;">${payroll.created_at}</td>
                                    <td class="desktop-only-col">
                                        <div class="d-flex align-items-center gap-2">
                                            ${actionButtons}
                                        </div>
                                    </td>
                                    <td class="text-center">${detailsToggle}</td>
                                </tr>
                            `;
                        });

                        // $('#payroll-table tbody').html(tableRows); // Update the table body
                        
                        if ($.fn.DataTable.isDataTable('#payroll-table')) {
                                $('#payroll-table').DataTable().destroy();
                            }

                            $('#payroll-table tbody').html(tableRows);

                            const table = $('#payroll-table').DataTable({
                            order: [
                                [6, 'desc']
                            ],
                            columnDefs: [{
                                    targets: 0, // checkbox column
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    targets: 6,
                                    visible: false
                                },
                                {
                                    targets: 8, // details column
                                    visible: false, // ❌ hide on desktop
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            },
                            pageLength: 10
                        });

                        // Initialize checkbox functionality after DataTable
                        initCheckboxFunctionality();
                        
                    } else {
                        Swal.fire('Error', 'Failed to load payroll records', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch payroll records', 'error');
                }
            });
        }

        // Toggle details icon
        $(document).on('click', '.toggle-details', function() {
            let icon = $(this).find('i');
            if (icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
            } else {
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
            }
        });

        // Checkbox management for multiple downloads
        function initCheckboxFunctionality() {
            const checkAllCheckbox = document.getElementById('checkAll');
            const downloadMultipleBtn = document.getElementById('downloadMultipleBtn');

            function updateDownloadButtonVisibility() {
                const checkedCount = document.querySelectorAll('.payroll-checkbox:checked').length;
                if (downloadMultipleBtn) {
                    // downloadMultipleBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                    // Keep visible as per design
                }
            }

            // Check all functionality
            if (checkAllCheckbox) {
                checkAllCheckbox.addEventListener('change', function() {
                    document.querySelectorAll('.payroll-checkbox').forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateDownloadButtonVisibility();
                });
            }

            // Individual checkbox logic
            $(document).on('change', '.payroll-checkbox', function() {
                const allCheckboxes = document.querySelectorAll('.payroll-checkbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);

                if (checkAllCheckbox) {
                    checkAllCheckbox.checked = allChecked;
                    checkAllCheckbox.indeterminate = someChecked && !allChecked;
                }
                updateDownloadButtonVisibility();
            });

            // Download multiple slips functionality
            if (downloadMultipleBtn) {
                downloadMultipleBtn.addEventListener('click', function() {
                    const selectedCheckboxes = document.querySelectorAll('.payroll-checkbox:checked');
                    const selectedPayrollIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.id);

                    if (selectedPayrollIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No payrolls selected',
                            text: 'Please select at least one payroll record'
                        });
                        return;
                    }

                    // Show loading state
                    Swal.fire({
                        title: 'Preparing download...',
                        html: 'Generating salary slips for ' + selectedPayrollIds.length + ' record(s)',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send request to download multiple slips
                    fetch('{{ url("/api/payroll/downloadMultipleByIds") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${token}`
                            },
                            body: JSON.stringify({
                                payroll_ids: selectedPayrollIds,
                                month: selectedMonth
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to download salary slips');
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            Swal.close();

                            // Create download link
                            const url = window.URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = `salary-slips-${selectedMonth}.pdf`;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(url);

                            Swal.fire({
                                icon: 'success',
                                title: 'Downloaded successfully',
                                text: selectedPayrollIds.length + ' salary slip(s) combined in one PDF',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Download failed',
                                text: error.message
                            });
                        });
                });
            }
        }

        // Call the fetchPayroll function on page load
        fetchPayroll();

        // Deduction breakdown popup – open modal (shared for click and touchend)
        function openListDeductionModal($el) {
            const userId = $el.data('user-id');
            const month = $el.data('month-year');
            const name = $el.data('name') || 'Employee';
            if (!userId || !month) return;
            const modal = document.getElementById('listDeductionBreakdownModal');
            const loading = document.getElementById('listDeductionBreakdownLoading');
            const content = document.getElementById('listDeductionBreakdownContent');
            if (!modal) return;
            document.getElementById('listDeductionBreakdownModalLabel').innerHTML = '<i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted? – ' + name;
            loading.style.display = 'block';
            content.style.display = 'none';
            content.innerHTML = '';
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            $.ajax({
                url: '{{ url("api/payroll/get-deduction-breakdown") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: { user_id: userId, month: month },
                dataType: 'json',
                success: function(res) {
                    loading.style.display = 'none';
                    if (res.status !== 'success' || !res.data) {
                        content.innerHTML = '<p class="text-muted">No breakdown available.</p>';
                        content.style.display = 'block';
                        return;
                    }
                    const d = res.data;
                    let html = '';
                    if (d.leaves && d.leaves.count > 0) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Leaves</strong>';
                        if (d.leaves.dates && d.leaves.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.leaves.dates.forEach(function(l) { html += '<li>' + (l.label || l.date) + '</li>'; });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.leaves.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.absent && d.absent.dates && d.absent.dates.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Absent</strong><ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                        d.absent.dates.forEach(function(a) { html += '<li>' + (a.label || a.date) + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.absent.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.half_day && d.half_day.count > 0) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Half-day</strong>';
                        if (d.half_day.dates && d.half_day.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.half_day.dates.forEach(function(h) {
                                var baseLabel = (h.label || h.date);
                                var worked = h.worked_text ? (' – Worked: ' + h.worked_text) : '';
                                var missing = h.missing_text ? (' – Deduct: ' + h.missing_text) : '';
                                html += '<li>' + baseLabel + worked + missing + '</li>';
                            });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.half_day.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.late && d.late.list && d.late.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Late arrival</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.late.list.forEach(function(l) { html += '<li>' + (l.label || l.date) + ' – ' + (l.late_text || l.late_minutes + ' min') + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.late.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.overtime && d.overtime.list && d.overtime.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-success">Overtime</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.overtime.list.forEach(function(o) { html += '<li>' + (o.label || o.date) + ' – ' + (o.overtime_text || o.overtime_hours + 'h') + '</li>'; });
                        html += '</ul><span class="text-success">Added to salary: ₹' + (d.overtime.pay_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.summary) {
                        html += '<hr><div class="fw-bold"><span>Total deduction (leaves + half-day + late):</span> <span class="text-danger">₹' + (d.summary.total_deduction || 0).toFixed(2) + '</span></div>';
                        if (d.summary.overtime_added > 0) html += '<div class="fw-bold"><span>Overtime added:</span> <span class="text-success">₹' + d.summary.overtime_added.toFixed(2) + '</span></div>';
                    }
                    if (!d.leaves?.count && !d.absent?.dates?.length && !d.half_day?.count && !d.late?.list?.length && !d.overtime?.list?.length) {
                        html += '<p class="text-muted">No leaves, absent, late, or overtime in this month.</p>';
                    }
                    content.innerHTML = html;
                    content.style.display = 'block';
                },
                error: function() {
                    loading.style.display = 'none';
                    content.innerHTML = '<p class="text-danger">Failed to load details.</p>';
                    content.style.display = 'block';
                }
            });
        }

        // Deduction: handle both touchend (mobile) and click (desktop), avoid double-open
        var listDeductionLastTouch = 0;
        $(document).on('touchend', '.payroll-deduction-info', function(e) {
            e.preventDefault();
            e.stopPropagation();
            listDeductionLastTouch = Date.now();
            var $el = $(e.target).closest('.payroll-deduction-info');
            if ($el.length) openListDeductionModal($el);
        });
        $(document).on('click', '.payroll-deduction-info', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (Date.now() - listDeductionLastTouch < 400) return; // already opened by touchend
            openListDeductionModal($(this));
        });

        // Handle delete action
        $(document).on('click', '.delete-payroll', function(e) {
            e.preventDefault();
            const payrollId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                    cancelButton: 'hr-btnbg',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/payroll/${payrollId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', 'The payroll record has been deleted.', 'success').then(() => {
                                    $(`tr[data-id="${payrollId}"]`).remove();
                                });
                            } else {
                                Swal.fire('Error!', 'Failed to delete the payroll record.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'There was an error deleting the payroll record.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush



@endsection
