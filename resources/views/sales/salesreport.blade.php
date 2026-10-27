@extends('layout.app')

@section('title', 'Sales Report')

@section('content')
    <style>
        .dataTables_info {
            float: left;
            padding-right: 15px;
            font-size: 12px;
            color: #5e5873;
            font-weight: 600;
        }

        tbody#purchase-data tr td:nth-child(2) {
            display: flex;
            align-items: center;
        }

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
        

        @media screen and (max-width: 767px) {
            .table-scroll-top {
                display: block;
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
                margin-bottom: 1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Sales Report</h4>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <select id="date-filter" class="form-select form-select-sm">
                            <option value="">All Time</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="this_year">This Year</option>
                            <option value="previous_year">Previous Year</option>
                        </select>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li>
                                <a id="generate-pdf" href="javascript:void(0);" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Download PDF">

                                    <button class="btn btn-primary btn-sm">View PDF</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                 <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Sold amount</th>
                                <th>Sold qty</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


@endsection
@push('js')
    <script>
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
             const ImagePath = "{{ env('ImagePath') }}";
             const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            fetchOrderReport(); // initial load

            $('#date-filter').on('change', function () {
                const selectedFilter = $(this).val();
                fetchOrderReport(selectedFilter);
            });

            function fetchOrderReport(dateFilter = "") {
                $.ajax({
                    url: "/api/order-report",
                    type: "GET",
                    data: {
                        filter: dateFilter,
                        selectedSubAdminId: selectedSubAdminId 
                    }, // send filter in query params
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (response) {
                        let tbody = "";
                        $.each(response, function (index, item) {
                            tbody += `
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox" name="item_ids[]" value="${item.id}">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="productimgname">
                                <div style="display: flex; align-items: center; gap: 10px; white-space: normal;">
                                    <a href="/product-view/${item.product_id}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                                        <img src="${ImagePath}${item.image}" alt="${item.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                        <span style="margin-left: 10px;">${item.name}</span>
                                    </a>
                                </div>
                            </td>
                            <td>${item.SKU}</td>
                            <td>${item.category}</td>
                            <td>${item.brand ?? 'N/A'}</td>
                            <td>${item.sold_amount}</td>
                            <td>${item.sold_qty}</td>
                        </tr>
                    `;
                        });

                        let table = $('.datanew').DataTable();
                        table.destroy();
                        $(".datanew tbody").html(tbody);
                        $('.datanew').DataTable({
                            destroy: true,
                            responsive: true,
                            language: {
                                emptyTable: "No orders found.",
                                zeroRecords: "No order record found.",
                                search: "Search:",
                                searchPlaceholder: ""
                            }
                        });
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
                    },
                    error: function () {
                        alert("Failed to load order report.");
                    }
                });
            }


            document.getElementById('generate-pdf').addEventListener('click', function () {
                let selectedIds = [];
                document.querySelectorAll('input[name="item_ids[]"]:checked').forEach(function (checkbox) {
                    console.log("Checked ID:", checkbox.value); // ✅ Check this

                    selectedIds.push(checkbox.value);
                    console.log("Selected IDs:", selectedIds);

                });

                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one product to generate the PDF.',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Construct the URL using slashes
                const url = `/sales/report/${selectedIds.join(',')}`;
                window.open(url, '_blank');
            });
        });
    </script>
@endpush