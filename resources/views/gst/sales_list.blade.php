@extends('layout.app')

@section('title', 'GST Reports')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

<style>
    /* ── Desktop: show all columns, hide toggle ── */
    @media (min-width: 768px) {
        .gst-table th.col-toggle,
        .gst-table td.col-toggle {
            display: none !important;
        }
        .gst-table tr.gst-detail-row {
            display: none !important;
        }
    }

    /* ── Mobile: hide Download column, show toggle ── */
    @media (max-width: 767px) {
        .gst-table th.col-download,
        .gst-table td.col-download {
            display: none !important;
        }

        .gst-table th.col-toggle,
        .gst-table td.col-toggle {
            display: table-cell !important;
            width: 44px;
            text-align: center;
            vertical-align: middle;
        }

        .gst-table th.col-no,
        .gst-table td.col-no {
            width: 36px;
        }
    }

    /* Toggle button style */
    .gst-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #ff9f43;
        border: none;
        cursor: pointer;
        color: #fff;
        font-size: 13px;
        line-height: 1;
    }

    .gst-toggle-btn.is-open {
        background: #dc3545;
    }

    /* Expanded detail row */
    .gst-detail-row td {
        padding: 8px 12px !important;
        background: #f8f9fa;
    }

    .gst-detail-row .detail-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>GST Reports</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-2 col-4">
                        <label for="from_date" class="form-label fw-bold">Start Date</label>
                        <input type="date" id="from_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-4">
                        <label for="to_date" class="form-label fw-bold">End Date</label>
                        <input type="date" id="to_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-4">
                        <label for="year_select" class="form-label fw-bold">Year</label>
                        <select id="year_select" class="form-control form-control-sm">
                            <option value="">-- Select Year --</option>
                            @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="table-responsive">
                    <table class="table gst-table">
                        <thead>
                            <tr>
                                <th class="col-no" style="width: 50px;">No</th>
                                <th class="col-name">GST Reports Name</th>
                                <th class="col-download text-end">Download PDF/Excel</th>
                                <th class="col-toggle"></th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- Row 1: GSTR-2B --}}
                            <tr>
                                <td class="col-no">1</td>
                                <td class="col-name">Goods and Services Tax - GSTR-2B</td>
                                <td class="col-download text-end">
                                    <a class="btn btn-success btn-sm" id="exportGSTR2B">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>
                                </td>
                                <td class="col-toggle">
                                    <button type="button" class="gst-toggle-btn" data-target="#gst-detail-1">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="gst-detail-row" id="gst-detail-1" style="display:none;">
                                <td colspan="4">
                                    <div class="detail-label">Download PDF/Excel</div>
                                    <a class="btn btn-success btn-sm" id="exportGSTR2B_mob">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>
                                </td>
                            </tr>

                            {{-- Row 2: GSTR-1 --}}
                            <tr>
                                <td class="col-no">2</td>
                                <td class="col-name">Goods and Services Tax - GSTR-1</td>
                                <td class="col-download text-end">
                                    <a class="btn btn-success btn-sm" id="exportExcel">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>
                                </td>
                                <td class="col-toggle">
                                    <button type="button" class="gst-toggle-btn" data-target="#gst-detail-2">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="gst-detail-row" id="gst-detail-2" style="display:none;">
                                <td colspan="4">
                                    <div class="detail-label">Download PDF/Excel</div>
                                    <a class="btn btn-success btn-sm" id="exportExcel_mob">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </a>
                                </td>
                            </tr>

                            {{-- Row 3: GSTR-3B --}}
                            <tr>
                                <td class="col-no">3</td>
                                <td class="col-name">GSTR-3B Summary</td>
                                <td class="col-download text-end">
                                    <a href="#" id="exportGstr3bPdf" class="btn btn-primary btn-sm py-1">
                                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </a>
                                </td>
                                <td class="col-toggle">
                                    <button type="button" class="gst-toggle-btn" data-target="#gst-detail-3">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="gst-detail-row" id="gst-detail-3" style="display:none;">
                                <td colspan="4">
                                    <div class="detail-label">Download PDF/Excel</div>
                                    <a href="#" id="exportGstr3bPdf_mob" class="btn btn-primary btn-sm py-1">
                                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <style>
                .table tbody tr td a {
                    color: #ffffff !important;
                }
            </style>
            @endsection
            @push('js')
            <script>
                // Toggle expand/collapse for mobile detail rows
                $(document).on('click', '.gst-toggle-btn', function () {
                    var $btn = $(this);
                    var target = $btn.data('target');
                    var $row = $(target);
                    var isOpen = $btn.hasClass('is-open');

                    if (isOpen) {
                        $row.hide();
                        $btn.find('i').removeClass('fa-minus').addClass('fa-plus');
                        $btn.removeClass('is-open');
                    } else {
                        $row.show();
                        $btn.find('i').removeClass('fa-plus').addClass('fa-minus');
                        $btn.addClass('is-open');
                    }
                });

                // Mirror mobile export buttons to desktop handlers
                $('#exportGSTR2B_mob').on('click', function(e) { $('#exportGSTR2B').trigger('click'); e.preventDefault(); });
                $('#exportExcel_mob').on('click', function(e) { $('#exportExcel').trigger('click'); e.preventDefault(); });
                $('#exportGstr3bPdf_mob').on('click', function(e) { $('#exportGstr3bPdf').trigger('click'); e.preventDefault(); });

                $('#exportGSTR2B, #exportGSTR2B_mob').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $('#exportGSTR2B');
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gstr3b/export-excel',
                        method: 'GET',
                        headers: { "Authorization": "Bearer " + authToken },
                        data: { from_date, to_date, year, selectedSubAdminId },
                        xhrFields: { responseType: 'blob' },
                        success: function(blob) {
                            if (!(blob instanceof Blob)) { alert('Export failed: Invalid response.'); btn.prop('disabled', false).html(originalHtml); return; }
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url; a.download = 'GSTR-2B.xlsx';
                            document.body.appendChild(a); a.click(); a.remove();
                            window.URL.revokeObjectURL(url);
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function() { alert('Failed to export Excel!'); btn.prop('disabled', false).html(originalHtml); }
                    });
                });

                $('#exportExcel').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $(this);
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gstr1/export-excel',
                        method: 'GET',
                        data: { from_date, to_date, year, selectedSubAdminId },
                        headers: { "Authorization": "Bearer " + authToken },
                        xhrFields: { responseType: 'blob' },
                        success: function(blob) {
                            if (!(blob instanceof Blob)) { alert('Export failed: Invalid response format.'); btn.prop('disabled', false).html(originalHtml); return; }
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url; a.download = 'GSTR1_Multiple_Sheets.xlsx';
                            document.body.appendChild(a); a.click(); a.remove();
                            window.URL.revokeObjectURL(url);
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function() { alert('Failed to export Excel!'); btn.prop('disabled', false).html(originalHtml); }
                    });
                });

                $('#exportGstr3bPdf').click(function(e) {
                    e.preventDefault();

                    const authToken = localStorage.getItem("authToken");
                    const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                    const from_date = $('#from_date').val();
                    const to_date = $('#to_date').val();
                    const year = $('#year_select').val();

                    const btn = $(this);
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                    $.ajax({
                        url: '/api/gst/gstr-3b/export',
                        method: 'GET',
                        data: { from_date, to_date, year, selectedSubAdminId },
                        headers: { "Authorization": "Bearer " + authToken },
                        success: function(response) {
                            if (response.status && response.file_url) {
                                const a = document.createElement('a');
                                a.href = response.file_url;
                                a.download = response.file_name || 'GSTR-3B-Summary.pdf';
                                document.body.appendChild(a); a.click(); a.remove();
                            } else {
                                alert('Export failed: ' + (response.message || 'Unknown error'));
                            }
                            btn.prop('disabled', false).html(originalHtml);
                        },
                        error: function() { alert('Failed to export PDF!'); btn.prop('disabled', false).html(originalHtml); }
                    });
                });
            </script>
            @endpush