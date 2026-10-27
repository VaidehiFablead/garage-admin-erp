@extends('layout.app')

@section('title', 'Expense Report')

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

        @media screen and (max-width: 768px) {
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
@if(session('error'))
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
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Expense Report</h4>
            <!-- <h6>Manage your Expense Report</h6> -->
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
                            <a id="generate-pdf" href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Download PDF">
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
                            <th>Expense Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Expense For</th>
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
    $(document).ready(function() {
         var authToken = localStorage.getItem("authToken");
         const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        var table = $('.datanew').DataTable({
                destroy: true,
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                language: {
                    emptyTable: "No expenses found.",
                    zeroRecords: "No expense record found.",
                    search: "Search:",
                    searchPlaceholder: ""
                }
            });

        // Initial fetch
        fetchExpenses();

        // Listen for filter changes
        $('#date-filter').change(function() {
            const selectedFilter = $(this).val();
            fetchExpenses(selectedFilter);
        });

        function fetchExpenses(filter = '') {
            $.ajax({
                url: '/api/expenses/report',
                method: 'GET',
                data: {
                    filter: filter, // Send filter to backend
                    selectedSubAdminId: selectedSubAdminId 
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    table.clear().draw();
                    $.each(response, function(index, expense) {
                        table.row.add([
                            `<label class="checkboxs">
                            <input type="checkbox" class="expense-check" name="item_ids[]" value="${expense.id}">
                            <span class="checkmarks"></span>
                        </label>`,
                            // expense.expense_name,
                            `<a href="{{ route('expense.list') }}" style="text-decoration: none;">${expense.expense_name}</a>`,
                            expense.formatted_amount,
                            formatDate(expense.expense_date),
                            `<div style="white-space: normal; word-break: break-word; max-width: 400px;">${expense.description ?? ''}</div>`,
                        ]).draw(false);
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
                error: function() {
                    alert('Failed to fetch expenses.');
                }
            });
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-GB', options);
        }


        document.getElementById('generate-pdf').addEventListener('click', function() {
            let selectedIds = [];
            document.querySelectorAll('input[name="item_ids[]"]:checked').forEach(function(checkbox) {
                console.log("Checked ID:", checkbox.value); // ✅ Check this

                selectedIds.push(checkbox.value);
                console.log("Selected IDs:", selectedIds);

            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one expense to generate the PDF.',
                    confirmButtonColor: '#ff9f43',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Construct the URL using slashes
            const url = `/expense/report/${selectedIds.join(',')}`;
            window.open(url, '_blank');
        });
    });
</script>


@endpush