@extends('layout.app')
@section('title', 'Appointment List')
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

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {
            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td.details-cell {
                display: none !important;
            }

            /* Never show expandable detail rows on desktop */
            .appt-details-row {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {
            table.datanew {
                table-layout: fixed;
                width: 100% !important;
            }

            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            table.datanew thead th:first-child,
            table.datanew tbody td:first-child {
                width: 80% !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td.details-cell {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 20% !important;
            }

            /* Customer name cell */
            table.datanew tbody td:first-child {
                vertical-align: middle;
            }

            .appt-name {
                font-size: 14px;
                word-break: break-word;
                color: #1b2850;
                font-weight: 500;
            }
        }

        @media screen and (max-width: 767px) {
            .table-scroll-top {
                display: block;
            }

            .table-top {
                flex-direction: row;
                margin-bottom: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                display: flex;
                align-items: flex-start;
                gap: 10px;
            }

            .page-btn {
                width: 100%;
                display: flex;
                justify-content: flex-end;
            }

            .page-btn .btn {
                font-size: 12px;
                padding: 6px 10px;
            }

            #export-excel {
                width: 100%;
            }
        }

        /* ── Expandable detail row (same style as product list) ── */
        .appt-details-row {
            display: none;
        }

        .appt-details-row.show {
            display: table-row;
        }

        .appt-details-content {
            padding: 14px 8px !important;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        table.datanew tbody tr.appt-details-row td.appt-details-content {
            display: table-cell !important;
            width: 100% !important;
        }

        .appt-details-list {
            margin-bottom: 15px;
        }

        .appt-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .appt-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .appt-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            white-space: nowrap;
        }

        .appt-detail-value-simple {
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

        /* Toggle button (orange +, red −) */
        .mobile-toggle-btn-appt {
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

        .mobile-toggle-btn-appt:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-appt.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-appt.minus:hover {
            background: #c82333;
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
        /* Only iPad Pro & Nest Hub */
    
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Appointments</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(12, 'add'))
                    <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-added">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New
                        Appointment
                    </a>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-top mb-3">
                    <div class="row g-2 flex-lg-nowrap align-items-end">
                        <!-- Search -->
                        <div class="col-12 col-sm-3 col-md-10">
                            <div class="search-set w-100">
                                <div class="search-path"></div>
                                <div class="search-input d-flex align-items-center">
                                    <a class="btn btn-searchset">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                            alt="img">
                                    </a>
                                    <input type="text" id="search-input" class="form-control"
                                        placeholder="Search...">
                                </div>
                            </div>
                        </div>
                        <!-- Month -->
                        <div class="col-6 col-sm-3 col-md-4">
                            <div class="form-group mb-0">
                                <label for="filter-month" class="form-label">Month</label>
                                <select id="filter-month" class="form-control form-control-sm">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Year -->
                        <div class="col-6 col-sm-3 col-md-4">
                            <div class="form-group mb-0">
                                <label for="filter-year" class="form-label">Year</label>
                                <select id="filter-year" class="form-control form-control-sm">
                                    <option value="">All Years</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Export Button -->
                        <div class="col-12 col-sm-3 col-md-4">
                            <button id="export-excel" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th class="details-column">Details</th>
                                <th>Phone</th>
                                <th>Vehicle Number</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
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
@endsection
@push('js')
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
            });
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let appointmentUrl = "/api/appointments";

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Map to hold appointment data for expandable rows
            window.appointmentDataMap = {};

            // Build the expandable detail row content (same pattern as product list)
            function buildAppointmentExpandableRowContent(appt) {
                let actionBtns = '';

                @if (app('hasPermission')(17, 'view'))
                    actionBtns += `<a class="btn-icon-mobile btn-view" href="/appointments/view/${appt.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </a>`;
                @endif

                @if (app('hasPermission')(17, 'edit'))
                    actionBtns += `<a class="btn-icon-mobile btn-edit" href="/appointments/edit/${appt.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>`;
                @endif

                @if (app('hasPermission')(17, 'delete'))
                    actionBtns += `<a class="btn-icon-mobile btn-delete delete-appointment" data-id="${appt.id}" href="javascript:void(0);" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>`;
                @endif

                return `
                    <td colspan="8" class="appt-details-content">
                        <div class="appt-details-list">
                            <div class="appt-detail-row-simple">
                                <span class="appt-detail-label-simple">Phone:</span>
                                <span class="appt-detail-value-simple">${appt.phone}</span>
                            </div>
                            <div class="appt-detail-row-simple">
                                <span class="appt-detail-label-simple">Vehicle:</span>
                                <span class="appt-detail-value-simple">${appt.vehicle_number}</span>
                            </div>
                            <div class="appt-detail-row-simple">
                                <span class="appt-detail-label-simple">Date:</span>
                                <span class="appt-detail-value-simple">${appt.appointment_date}</span>
                            </div>
                            <div class="appt-detail-row-simple">
                                <span class="appt-detail-label-simple">Time:</span>
                                <span class="appt-detail-value-simple">${appt.appointment_time}</span>
                            </div>
                            <div class="appt-detail-row-simple">
                                <span class="appt-detail-label-simple">Status:</span>
                                <span class="appt-detail-value-simple">${appt.status}</span>
                            </div>
                        </div>
                        <div class="mobile-action-buttons-simple">
                            ${actionBtns}
                        </div>
                    </td>
                `;
            }

            // Toggle expandable row (same pattern as product list)
            window.toggleAppointmentRowDetails = function(apptId) {
                const btn = $(`.mobile-toggle-btn-appt[data-appt-id="${apptId}"]`);
                if (btn.length === 0) return;

                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.appt-details-row[data-appt-id="${apptId}"]`);
                const icon = btn.find('.toggle-icon');

                if (detailsRow.length === 0) {
                    const apptData = window.appointmentDataMap && window.appointmentDataMap[apptId];
                    if (!apptData) return;

                    detailsRow = $('<tr>')
                        .addClass('appt-details-row')
                        .attr('data-appt-id', apptId)
                        .html(buildAppointmentExpandableRowContent(apptData));
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

            fetchAppointments(1);

            function fetchAppointments(page = 1) {
                const filters = {
                    page: page,
                    per_page: perPage,
                    search: searchQuery,
                    selectedSubAdminId: selectedSubAdminId || null,
                    month: $('#filter-month').val() || '',
                    year: $('#filter-year').val() || ''
                };

                $.ajax({
                    url: appointmentUrl,
                    type: "GET",
                    dataType: "json",
                    data: filters,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let appointments = response.data;
                            let tableBodyHtml = "";
                            window.appointmentDataMap = {};

                            if (response.pagination) {
                                currentPage = response.pagination.current_page;
                                lastPage = response.pagination.last_page;
                                updatePaginationUI(response.pagination);
                            }

                            if (!appointments || appointments.length === 0) {
                                $('.pagination-controls').hide();
                            }

                            $.each(appointments, (index, appointment) => {
                                let appointmentName = appointment.name ?
                                    appointment.name.replace(/\b\w/g, c => c.toUpperCase()) : "N/A";
                                let status = appointment.status ?
                                    appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) :
                                    "Pending";

                                // Store data for expandable row
                                window.appointmentDataMap[appointment.id] = {
                                    id: appointment.id,
                                    phone: appointment.phone || "N/A",
                                    vehicle_number: appointment.vehicle_number || "N/A",
                                    appointment_date: appointment.appointment_date || "N/A",
                                    appointment_time: appointment.appointment_time || "N/A",
                                    status: status
                                };

                                // Desktop action buttons (unchanged)
                                let actionButtons = `
                                    @if (app('hasPermission')(17, 'view'))
                                    <a class="me-3" href="/appointments/view/${appointment.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="View">
                                    </a>
                                    @endif
                                    @if (app('hasPermission')(17, 'edit'))
                                    <a class="me-3" href="/appointments/edit/${appointment.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                    </a>
                                    @endif
                                    @if (app('hasPermission')(17, 'delete'))
                                    <a class="me-3 delete-appointment" data-id="${appointment.id}" href="javascript:void(0);">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                    </a>
                                    @endif
                                `;

                                // Mobile toggle button (same style as product list)
                                let detailsToggle = `
                                    <button class="mobile-toggle-btn-appt"
                                        onclick="toggleAppointmentRowDetails('${appointment.id}')"
                                        data-appt-id="${appointment.id}">
                                        <span class="toggle-icon">+</span>
                                    </button>
                                `;

                                tableBodyHtml += `
                                    <tr>
                                        <td>
                                            <a href="/appointments/view/${appointment.id}"
                                               style="color: #1b2850; text-decoration: none;">
                                                <span class="appt-name">${appointmentName}</span>
                                            </a>
                                        </td>
                                        <td class="details-cell">${detailsToggle}</td>
                                        <td>${appointment.phone || "N/A"}</td>
                                        <td>${appointment.vehicle_number || "N/A"}</td>
                                        <td>${appointment.appointment_date || "N/A"}</td>
                                        <td>${appointment.appointment_time || "N/A"}</td>
                                        <td>${status}</td>
                                        <td>${actionButtons}</td>
                                    </tr>
                                `;
                            });

                            if ($.fn.DataTable.isDataTable(".datanew")) {
                                $(".datanew").DataTable().clear().destroy();
                            }

                            $(".datanew tbody").html(tableBodyHtml);

                            table = $(".datanew").DataTable({
                                responsive: true,
                                autoWidth: false,
                                paging: false,
                                searching: false,
                                info: false,
                                dom: 't',
                                ordering: false
                            });

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
                            $(".datanew tbody").html(
                                '<tr><td colspan="8">No appointments found</td></tr>'
                            );
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.log("Error:", xhr);
                    }
                });
            }

            // Handle search input
            let searchTimer;
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchAppointments(1);
                }, 400);
            });

            // Update pagination controls and info
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

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchAppointments(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchAppointments(1);
            });

            // Event listener for Month & Year filters
            $('#filter-month, #filter-year').on('change', function() {
                fetchAppointments(1);
            });

            // Delete appointment
            $(document).on('click', '.delete-appointment', function() {
                var appointmentId = $(this).data('id');
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
                            url: `/api/appointments/${appointmentId}`,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + authToken
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
                                    fetchAppointments(currentPage);
                                } else {
                                    Swal.fire("Error!", response.message, "error");
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

            // Export Excel button
            $("#export-excel").on("click", function() {
                const year = $("#filter-year").val();
                const month = $("#filter-month").val();

                let apiUrl = `/api/export-appointments?selectedSubAdminId=${selectedSubAdminId}`;
                if (year) apiUrl += `&year=${year}`;
                if (month) apiUrl += `&month=${month}`;

                fetch(apiUrl, {
                        method: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error("Export failed");
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement("a");
                        a.href = url;
                        a.download = "appointments_export.xlsx";
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(err => {
                        console.error("Export failed", err);
                        Swal.fire("Appointment!", "No Record Found", "error");
                    });
            });
        });
    </script>
@endpush
