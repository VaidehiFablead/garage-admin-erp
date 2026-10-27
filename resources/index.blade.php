@extends('layout.app')

@section('title', 'All Daily Data')

@section('content')
    <style>
        .accordion-arrow {
            display: inline-block;
            transition: transform 0.2s ease;
        }

        tr[aria-expanded="true"] .accordion-arrow {
            transform: rotate(90deg);
        }

        /* Add spacing between nested tables */
        .collapse>td>div>table {
            margin-top: 1rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Give nested tables a subtle background and border */
        .collapse>td>div>table.table-bordered {
            background-color: #fafafa;
            border: 1px solid #dee2e6;
        }

        /* Different background for summary table */
        .collapse>td>div>table:last-of-type {
            background-color: #f1f3f5;
        }

        /* Header styles for clarity */
        .collapse>td>div>table thead th {
            background-color: #e9ecef;
            font-weight: 600;
        }

        /* Extra padding around the div container */
        .collapse>td>div {
            padding: 1rem;
        }

        /* Optional: add rounded corners */
        .collapse>td>div>table {
            border-radius: 4px;
            overflow: hidden;
        }

        /* Space between the modal trigger button and other actions */
        .table td .btn {
            margin-right: 0.25rem;
        }

        /* Smaller font for expense table */
        .collapse>td>div>table:nth-of-type(2) td {
            font-size: 0.9rem;
        }

        /* Add a subtle shadow to separate the nested content visually */
        .collapse>td>div {
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.05);
        }

        .capitalize td {
            text-transform: capitalize;
        }

        #filter-name {
            max-width: 200px;
            font-size: 0.85rem;
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Daily Data</h4>
                <!-- <h6>Manage your purchases</h6> -->
            </div>
            <div class="page-btn">
                <a href="{{ route('daily_data.add-daily-data') }}" class="btn btn-sm btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">New Daily Data
                </a>
            </div>

        </div>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="all-records-tab" href="#">All Records</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="search-daily-tab" href="#">Search Daily Data</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-records-content">
                <div class="card">
                    <div class="table-top mt-3">
                        <div class="row g-2 align-items-end mx-3"> <!-- g-2 for spacing between columns -->
                            <!-- Month Dropdown -->
                            @php
                                $selectedMonth = request('month');
                                $selectedYear = request('year');
                                $selectedDate = request('date');
                            @endphp

                            <div class="col-6 col-md-4">
                                <label for="filter-month" class="form-label mb-1">Month</label>
                                <select id="filter-month" class="form-select form-select-sm">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Year Dropdown -->
                            <div class="col-6 col-md-4">
                                <label for="filter-year" class="form-label mb-1">Year</label>
                                <select id="filter-year" class="form-select form-select-sm">
                                    <option value="">All Years</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Picker -->
                            <div class="col-12 col-md-4">
                                <label for="filter-date" class="form-label mb-1">Date</label>
                                <input type="text" id="filter-date" placeholder="Choose Date"
                                    class="form-control form-control-sm datetimepicker" value="{{ $selectedDate }}">
                            </div>
                        </div>
                    </div>




                    <div class="table-responsive mt-3" style="overflow-x: auto;">
                        <table class="table" id="order-table" style="max-width: 2000px;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Expense</th>
                                    <th>Total Amount</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($jobCards) > 0)
                                
                                    @foreach($jobCards as $date => $cards)
                                        @php
                                            $totalExpense = $cards->sum(fn($card) => $card->expenseStores->sum('amount'));
                                            $totalProfit = $cards->sum('total_subtotal_amount');
                                            $dateSlug = \Illuminate\Support\Str::slug($date);
                                        @endphp
                                        {{-- Parent Row --}}
                                        <tr class="group-row" style="cursor: pointer;"
                                            onclick="handleRowClick(event, '{{ $dateSlug }}')">
                                            <td>
                                                <span class="accordion-arrow me-2">&#9654;</span>
                                                {{ $date }}
                                            </td>
                                            <td>{{ number_format($totalExpense, 2) }}</td>
                                            <td>{{ number_format($totalProfit, 2) }}</td>
                                            <td class="text-center align-middle">
                                                @if($date === \Carbon\Carbon::now()->format('d-m-Y'))
                                                    <a href="{{ route('daily_data.add-daily-data') }}" class="btn btn-sm btn-warning"
                                                        style="color: white; background-color: #ff9f43;">
                                                        Add New Daily Data
                                                    </a>
                                                @endif
                                                {{-- Download CSV Button --}}
                                                <a href="{{ route('jobcards.export', ['date' => $date]) }}"
                                                    class="btn btn-sm btn-success mt-1" style="color: white;">
                                                    Download CSV
                                                </a>
                                            </td>
                                        </tr>
                                        {{-- Collapsible Detail Row --}}
                                        <tr class="collapse" id="collapse-{{ $dateSlug }}">
                                            <td colspan="4" class="p-0">
                                                <div class="p-2">
                                                    {{-- Job Cards Table --}}
                                                    <table class="table table-bordered mb-0 capitalize">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer</th>
                                                                <th>Mobile No.</th>
                                                                <th>Bike</th>
                                                                <th>Bike No.</th>
                                                                <th>Amount</th>
                                                                <th>Received Amount</th>
                                                                <th>Job Done By</th>
                                                                <th>Amount Due/Received</th>
                                                                <th>Start Date</th>
                                                                <th>End Date</th>
                                                                <th>Payment Mode</th>
                                                                <th>Status</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cards as $card)
                                                                <tr>
                                                                    <td>{{ $card->customer_name }}</td>
                                                                    <td>{{ $card->mobile }}</td>
                                                                    <td>{{ $card->autocarmodel->model_name ?? '-' }}</td>
                                                                    <td>{{ $card->vehicle_no }}</td>
                                                                    <td>{{ $card->total_subtotal_amount }}</td>
                                                                    <td>{{ number_format($card->payments->sum('payment_amount'), 2) }}
                                                                    </td>
                                                                    <td>{{ $card->technicianUser->name ?? '-' }}</td>
                                                                    <td>
                                                                        @if ($card->payment_type === 'partial')
                                                                            Due
                                                                        @else
                                                                            Received
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $card->start_date }}</td>
                                                                    <td>{{ $card->end_date }}</td>
                                                                    <td>{{ $card->payment_method }}</td>
                                                                    <td>{{ $card->status }}</td>
                                                                    <td class="text-center">
                                                                        @if ($card->payment_type === 'partial' && $card->remaining_amount > 0)
                                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                                                data-bs-target="#makePaymentModal-{{ $card->id }}">
                                                                                Make Payment
                                                                            </button>
                                                                        @endif

                                                                        <button class="btn btn-sm btn-primary edit-jobcard"
                                                                            data-id="{{ $card->id }}"
                                                                            data-record_date="{{ $card->record_date }}"
                                                                            data-customer_name="{{ $card->customer_name }}"
                                                                            data-phone="{{ $card->mobile }}"
                                                                            data-vehicle_model="{{ $card->model }}"
                                                                            data-vehicle_no="{{ $card->vehicle_no }}"
                                                                            data-technician_name="{{ $card->technician }}"
                                                                            data-start_date="{{ $card->start_date }}"
                                                                            data-end_date="{{ $card->end_date }}"
                                                                            data-status="{{ $card->status }}"
                                                                            data-payment_type="{{ $card->payment_type }}"
                                                                            data-payable_amount="{{ $card->total_subtotal_amount }}"
                                                                            data-payment_mode="{{ $card->payment_method }}"
                                                                            data-comment="{{ $card->comment }}" data-bs-toggle="modal"
                                                                            data-bs-target="#editJobcardModal">
                                                                            Edit
                                                                        </button>
                                                                        <button class="btn btn-sm btn-danger delete-jobcard"
                                                                            data-id="{{ $card->id }}">
                                                                            Delete
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                {{-- Modal for Make Payment --}}
                                                                @if ($card->payment_type === 'partial' && $card->remaining_amount > 0)
                                                                    <div class="modal fade" id="makePaymentModal-{{ $card->id }}"
                                                                        tabindex="-1" aria-labelledby="makePaymentModalLabel{{ $card->id }}"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <form id="makePaymentForm-{{ $card->id }}">
                                                                                @csrf
                                                                                <input type="hidden" id="jobcard_id_{{ $card->id }}"
                                                                                    value="{{ $card->id }}">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title"
                                                                                            id="makePaymentModalLabel{{ $card->id }}">Make
                                                                                            Payment</h5>
                                                                                        <p class="mb-0 small">Payable Amount:
                                                                                            {{ $card->total_subtotal_amount }}
                                                                                        </p>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        {{-- Previous Payments --}}
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label">Previous
                                                                                                Payments</label>
                                                                                            @if ($card->payments && count($card->payments) > 0)
                                                                                                <ul class="list-group">
                                                                                                    @foreach ($card->payments as $payment)
                                                                                                        <li
                                                                                                            class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                            <span>{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') }}</span>
                                                                                                            <span><strong>{{ $payment->payment_amount }}</strong>
                                                                                                                via
                                                                                                                {{ ucfirst($payment->payment_method) }}</span>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                                </ul>
                                                                                            @else
                                                                                                <p class="text-muted">No previous payments
                                                                                                    found.</p>
                                                                                            @endif
                                                                                        </div>

                                                                                        {{-- Remaining Balance --}}
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label">Remaining
                                                                                                Balance</label>
                                                                                            <input type="text" class="form-control bg-light"
                                                                                                value="{{ $card->remaining_amount }}"
                                                                                                readonly>
                                                                                        </div>

                                                                                        {{-- New Payment Input --}}
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label">Payment Amount</label>
                                                                                            <input type="number" name="payment_amount"
                                                                                                id="payment_amount_{{ $card->id }}"
                                                                                                class="form-control"
                                                                                                max="{{ $card->remaining_amount }}"
                                                                                                required>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <label class="form-label">Payment Method</label>
                                                                                            <select name="payment_mode"
                                                                                                id="payment_mode_{{ $card->id }}"
                                                                                                class="form-control" required>
                                                                                                <option value="">Select</option>
                                                                                                <option value="cash">Cash</option>
                                                                                                <option value="upi">UPI</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span id="payment_message_{{ $card->id }}"
                                                                                            class="fw-bold small d-block mt-1"></span>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit" class="btn btn-primary">Submit
                                                                                            Payment</button>
                                                                                        <button type="button" class="btn btn-secondary"
                                                                                            data-bs-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    {{-- Expenses --}}
                                                    @php
                                                        $hasExpenses = false;
                                                        foreach ($cards as $card) {
                                                            if ($card->expenseStores->where('isDeleted', 0)->isNotEmpty()) {
                                                                $hasExpenses = true;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    @if($hasExpenses)
                                                        <h5 class="fw-bold mb-1 mt-3">Expenses Data</h5>

                                                        <table class="table table-bordered mb-4">
                                                            <thead>
                                                                <tr>
                                                                    <th>Vendor Name</th>
                                                                    <th>Item Name</th>
                                                                    <th>Amount (₹)</th>
                                                                    <th>Payment Method</th>
                                                                    <th>Expend By</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cards as $card)
                                                                    @foreach($card->expenseStores->where('isDeleted', 0) as $expense)
                                                                        <tr>
                                                                            <td>{{ $expense->vendor_name ?? 'N/A' }}</td>
                                                                            <td>{{ $expense->items ?? 'N/A' }}</td>
                                                                            <td>{{ number_format($expense->amount, 2)}}</td>
                                                                            <td>{{ $expense->payment_method ?? 'N/A' }}</td>
                                                                            <td>{{ $card->technicianUser->name ?? 'N/A' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                    @endif

                                                    {{-- Summary --}}
                                                    @php
                                                        $summary = $dailySummaries[$date] ?? null;
                                                    @endphp
                                                    @if($summary)
                                                        <h5 class="fw-bold mb-1 mt-3">Overall Total for {{ $date }}</h5>
                                                        <table class="table table-bordered mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cash</th>
                                                                    <th>Online</th>
                                                                    <th>Expenses</th>
                                                                    <th>Total (Cash - Expenses)</th>
                                                                    <th>Cash + Online</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ number_format($summary['cash'], 2) }}</td>
                                                                    <td>{{ number_format($summary['online'], 2) }}</td>
                                                                    <td>{{ number_format($summary['expenses'], 2) }}</td>
                                                                    <td>{{ number_format($summary['total'], 2) }}</td>
                                                                    <td>{{ number_format($summary['combined'], 2) }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No data found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade" id="search-daily-content">
                <div class="card">


                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center my-3 mx-3">
                            <div style="display: flex; flex-direction: column;">
                                <label for="filter-name" class="form-label">Search by Customers</label>
                                <select id="filter-name" class="form-select form-select-sm d-inline-block"
                                    style="width: auto;">
                                    <option value="">All Customers</option>
                                    <!-- JS will populate customers -->
                                </select>
                            </div>
                            <button id="download-csv" class="btn btn-sm btn-success">Download CSV</button>
                        </div>
                        <div class="table-responsive mt-3" style="overflow-x: auto;">
                            <table id="jobcards-table" class="table capitalize">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Record Date</th>
                                        <th>Bike</th>
                                        <th>Bike No.</th>
                                        <th>Amount</th>
                                        <th>Received Amount</th>
                                        <th>Job Done By</th>
                                        <th>Amount Due/Received</th>

                                        <th>Payment Mode</th>


                                    </tr>
                                </thead>
                                <tbody id="jobcards-body">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th id="total-amount">0</th>
                                        <th id="total-received">0</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="editJobcardModal" tabindex="-1" aria-labelledby="editJobcardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJobcardModalLabel">Edit Daily Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editJobcardForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Record Date</label>
                                <input type="date" name="record_date" class="form-control" id="edit_record_date" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" id="edit_customer_name">
                            </div>
                            <div class="col-md-4">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" id="edit_phone">
                            </div>
                        </div>
                        <div class="row mb-3">

                            <div class="col-md-4">
                                <label>Vehicle No.</label>
                                <input type="text" name="vehicle_no" class="form-control" id="edit_vehicle_no">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_technician_name">Technician Name</label>
                                <select name="technician_name" class="form-control" id="edit_technician_name">
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_status">Status</label>
                                <select name="status" class="form-control" id="edit_status">
                                    <option value="">Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>

                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">

                            <div class="col-md-4">
                                <label>Payable Amount</label>
                                <input type="number" name="payable_amount" class="form-control" id="edit_payable_amount">
                            </div>
                            <div class="col-md-4">
                                <label>Comment</label>
                                <textarea name="comment" class="form-control" id="edit_comment"></textarea>
                            </div>
                        </div>



                        <input type="hidden" name="jobcard_id" id="edit_jobcard_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function () {
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            console.log(selectedSubAdminId);
            

            $('.edit-jobcard').on('click', function () {
                const btn = $(this);

                $('#edit_jobcard_id').val(btn.data('id'));
                $('#edit_record_date').val(btn.data('record_date'));
                $('#edit_customer_name').val(btn.data('customer_name'));
                $('#edit_phone').val(btn.data('phone'));
                $('#edit_vehicle_model').val(btn.data('vehicle_model'));
                $('#edit_vehicle_no').val(btn.data('vehicle_no'));
                $('#edit_technician_name').val(btn.data('technician_name'));
                $('#edit_start_date').val(btn.data('start_date'));
                $('#edit_end_date').val(btn.data('end_date'));
                $('#edit_status').val(btn.data('status'));
                $('#edit_vendor_name').val(btn.data('vendor_name'));
                $('#edit_item_name').val(btn.data('item_name'));
                $('#edit_item_price').val(btn.data('item_price'));
                $('#edit_total_expense_amount').val(btn.data('total_expense_amount'));
                $('#edit_payable_amount').val(btn.data('payable_amount'));
                $('#edit_payment_mode').val(btn.data('payment_mode'));
                $('#edit_comment').val(btn.data('comment'));
                const paymentType = btn.data('payment_type'); // get payment type


                $('#edit_payment_type').val(paymentType); // set value

                if (paymentType === 'full') {
                    $('#edit_payable_amount').prop('disabled', true); // disable input
                } else {
                    $('#edit_payable_amount').prop('disabled', false); // enable input
                }
            });

            $('#editJobcardForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/api/jobcards/update', // adjust your route
                    method: 'POST',
                    data: formData,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (res) {
                        location.reload();
                    },
                    error: function (err) {
                        alert('Error saving changes.');
                    }
                });
            });
        });
        $(document).ready(function () {
            let dataTable;
            const authToken = localStorage.getItem("authToken");

            function formatAmount(amount) {
                return Number(amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function getPaymentTotal(payments) {
                return payments.reduce((sum, p) => sum + parseFloat(p.payment_amount || 0), 0);
            }

            // ✅ Recalculate visible totals after search/filter
            function calculateVisibleTotals() {
                let visibleAmount = 0;
                let visibleReceived = 0;

                $('#jobcards-table tbody tr:visible').each(function () {
                    const amount = parseFloat($(this).find('td.amount').text().replace(/,/g, '')) || 0;
                    const received = parseFloat($(this).find('td.received').text().replace(/,/g, '')) || 0;

                    visibleAmount += amount;
                    visibleReceived += received;
                });

                $('#total-amount').text(formatAmount(visibleAmount));
                $('#total-received').text(formatAmount(visibleReceived));
            }

            function loadJobCards() {
                $.ajax({
                    url: '/api/daily-data/ajax',
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (res) {
                        if (res.status && Array.isArray(res.data)) {
                            let rows = '';
                            let totalAmount = 0;
                            let totalReceived = 0;
                            const customerSet = new Set();

                            res.data.forEach(card => {
                                const paymentTotal = getPaymentTotal(card.payments || []);
                                totalAmount += parseFloat(card.total_subtotal_amount || 0);
                                totalReceived += paymentTotal;

                                // Collect unique customer names
                                if (card.customer_name) {
                                    customerSet.add(card.customer_name.trim());
                                }

                                rows += `
                                                                <tr>
                                                                    <td>${card.customer_name || '-'}</td>
                                                                    <td>${card.record_date || '-'}</td>
                                                                    <td>${card.autocarmodel?.model_name || '-'}</td>
                                                                    <td>${card.vehicle_no || '-'}</td>
                                                                    <td class="amount">${formatAmount(card.total_subtotal_amount)}</td>
                                                                    <td class="received">${formatAmount(paymentTotal)}</td>
                                                                    <td>${card.technician_user?.name || '-'}</td>
                                                                    <td>${card.payment_type === 'partial' ? 'Due' : 'Received'}</td>
                                                                    <td>${card.payment_method || '-'}</td>
                                                                </tr>
                                                            `;
                            });

                            // Populate customer filter dropdown
                            let options = '<option value="">All Customers</option>';
                            Array.from(customerSet).sort().forEach(name => {
                                options += `<option value="${name}">${name}</option>`;
                            });
                            $('#filter-name').html(options);

                            $('#jobcards-body').html(rows);

                            if ($.fn.DataTable.isDataTable('#jobcards-table')) {
                                dataTable.clear().destroy();
                            }

                            dataTable = $('#jobcards-table').DataTable();

                            // 🔁 Hook to recalculate totals on draw (search/filter/page)
                            dataTable.on('draw', function () {
                                calculateVisibleTotals();
                            });

                            // Initial calculation (after first load)
                            calculateVisibleTotals();

                            // Trigger filter once to recalculate if filtered
                            $('#filter-name').trigger('change');

                        } else {
                            $('#jobcards-body').html('<tr><td colspan="9">No data available</td></tr>');
                        }
                    },
                    error: function () {
                        alert('Failed to load jobcard data.');
                    }
                });
            }

            // 🔁 Initial load
            loadJobCards();

            // ⛳ Custom filter by customer name
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                const selectedName = $('#filter-name').val().toLowerCase().trim();
                const customerName = data[0].toLowerCase().trim(); // 1st column

                return !selectedName || customerName === selectedName;
            });

            // 🔁 Apply customer filter on change
            $('#filter-name').on('change', function () {
                if (dataTable) {
                    dataTable.draw(); // will also trigger totals
                }
            });

            // ⬇ Download filtered CSV
            $('#download-csv').on('click', function () {
                if (!dataTable) return;

                let csv = [];
                const headers = [];

                $('#jobcards-table thead th').each(function () {
                    headers.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
                });
                csv.push(headers.join(','));

                dataTable.rows({ search: 'applied' }).every(function () {
                    const row = this.node();
                    const rowData = [];

                    $(row).find('td').each(function () {
                        const cell = $(this).text().trim().replace(/"/g, '""');
                        rowData.push('"' + cell + '"');
                    });

                    csv.push(rowData.join(','));
                });

                const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "filtered_daily_data.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });


        $(document).ready(function () {
            // Load selected filters from URL and set inputs
            const urlParams = new URLSearchParams(window.location.search);
            const selectedMonth = urlParams.get('month');
            const selectedYear = urlParams.get('year');
            const selectedDate = urlParams.get('date');

            if (selectedMonth) $('#filter-month').val(selectedMonth);
            if (selectedYear) $('#filter-year').val(selectedYear);
            if (selectedDate) $('#filter-date').val(selectedDate);



            // When date is selected → clear month and year
            $('#filter-date').on('dp.change', function () {
                $('#filter-month').val('');
                $('#filter-year').val('');
                applyFilters('date');
            });

            // When month or year is selected → clear date
            $('#filter-month, #filter-year').on('change', function () {
                $('#filter-date').val('');
                applyFilters('monthYear');
            });

            // Apply filters with specific clearing logic
            function applyFilters(source) {
                const params = new URLSearchParams(window.location.search);

                if (source === 'date') {
                    const date = $('#filter-date').val();
                    if (date) params.set('date', date); else params.delete('date');
                    params.delete('month');
                    params.delete('year');
                } else {
                    const month = $('#filter-month').val();
                    const year = $('#filter-year').val();
                    if (month) params.set('month', month); else params.delete('month');
                    if (year) params.set('year', year); else params.delete('year');
                    params.delete('date');
                }

                window.location.search = params.toString(); // Reload with query
            }
        });



        function handleRowClick(event, collapseId) {
            // If the click was inside the last cell, do nothing
            const target = event.target;
            if (target.closest('.page-btn')) {
                return; // Allow the button click to work normally
            }

            // Toggle the collapse
            const collapseElement = document.getElementById('collapse-' + collapseId);
            const bsCollapse = new bootstrap.Collapse(collapseElement, {
                toggle: true
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.group-row');
            rows.forEach(row => {
                row.addEventListener('click', function () {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', String(!expanded));
                });
            });
        });


        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            $(document).on('submit', '[id^="makePaymentForm-"]', function (e) {
                e.preventDefault();

                const formId = $(this).attr('id');
                const cardId = formId.split('-')[1];

                const messageSpan = $(`#payment_message_${cardId}`);
                messageSpan.removeClass('text-success text-danger').text(''); // reset

                let formData = {
                    payment_amount: $(`#payment_amount_${cardId}`).val(),
                    payment_mode: $(`#payment_mode_${cardId}`).val(),
                    note: $(`#note_${cardId}`).val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '/api/jobcard/' + cardId + '/make-payment',
                    type: 'POST',
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function () {
                        messageSpan
                            .addClass('text-success')
                            .text('Payment recorded successfully.');

                        $(`#makePaymentForm-${cardId}`)[0].reset();

                        // Optionally close modal after delay
                        setTimeout(() => {
                            $(`#makePaymentModal-${cardId}`).modal('hide');
                            messageSpan.text('');
                            location.reload(); // or use AJAX refresh if preferred
                        }, 1500);
                    },
                    error: function (xhr) {
                        let errorText = 'Something went wrong.';
                        if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorText = Object.values(errors).flat().join(' ');
                        }
                        messageSpan
                            .addClass('text-danger')
                            .text(errorText);
                    }
                });
            });
        });


        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            $('.delete-jobcard').click(function (e) {
                e.preventDefault();

                let button = $(this);
                let id = button.data('id');

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the record.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/daily-data/' + id + '/delete',
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function (response) {
                                // Show success SweetAlert
                                Swal.fire(
                                    'Deleted!',
                                    'The record has been deleted.',
                                    'success'
                                );

                                // Optionally remove the row
                                button.closest('tr').remove();
                            },
                            error: function (xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

        $(document).ready(function () {
            // Handle All Records tab click
            $("#all-records-tab").click(function (e) {
                e.preventDefault();

                // Toggle tab active state
                $(this).addClass("active");
                $("#search-daily-tab").removeClass("active");

                // Hide all tab contents
                $(".tab-pane").removeClass("show active");

                // Show only All Records content
                $("#all-records-content").addClass("show active");
            });

            // Handle Search Daily Data tab click
            $("#search-daily-tab").click(function (e) {
                e.preventDefault();

                // Toggle tab active state
                $(this).addClass("active");
                $("#all-records-tab").removeClass("active");

                // Hide all tab contents
                $(".tab-pane").removeClass("show active");

                // Show only Search content
                $("#search-daily-content").addClass("show active");

                // Optional: clear previous results if needed
                // $("#search-daily-content").html("");
            });
            $('#filter-name').on('change', function () {
                const selectedName = $(this).val().toLowerCase().trim();

                $('#jobcards-table tbody tr').each(function () {
                    const rowName = $(this).find('td:first').text().toLowerCase().trim();

                    if (!selectedName || rowName === selectedName) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });


    </script>

@endpush