<div class="modal fade" id="todayAppointmentsModal" tabindex="-1" aria-labelledby="todayAppointmentsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="todayAppointmentsLabel">
                    📅 Today's Schedule - {{ now()->format('d M Y') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified bg-light" id="todayScheduleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 border-0 rounded-0" id="appointments-tab" data-bs-toggle="tab"
                            data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments"
                            aria-selected="true">
                            <i class="fas fa-calendar-check me-1"></i> Appointments
                            <span class="badge rounded-pill bg-primary ms-1">{{ $appointments->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 border-0 rounded-0" id="emis-tab" data-bs-toggle="tab"
                            data-bs-target="#emis" type="button" role="tab" aria-controls="emis"
                            aria-selected="false">
                            <i class="fas fa-money-bill-wave me-1"></i> EMIs
                            <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $upcomingEmis->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 border-0 rounded-0" id="payments-tab" data-bs-toggle="tab"
                            data-bs-target="#payments" type="button" role="tab" aria-controls="payments"
                            aria-selected="false">
                            <i class="fas fa-credit-card me-1"></i> Payments
                            <span class="badge rounded-pill bg-info text-dark ms-1">{{ $upcomingPayments->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 border-0 rounded-0" id="sixmonths-tab" data-bs-toggle="tab"
                            data-bs-target="#sixmonths" type="button" role="tab" aria-controls="sixmonths"
                            aria-selected="false">
                            <i class="fas fa-history me-1"></i> 6-Month
                            <span class="badge rounded-pill bg-success ms-1">{{ $customers6Months->count() }}</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-4" id="todayScheduleTabsContent">
                    {{-- Today's Appointments Pane --}}
                    <div class="tab-pane fade show active" id="appointments" role="tabpanel"
                        aria-labelledby="appointments-tab">
                        <h6 class="mb-3 text-dark fw-bold border-bottom pb-2">📌 Today's Appointments</h6>
                        @if ($appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Vehicle</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($appointments as $appointment)
                                            <tr>
                                                <td class="fw-semibold">{{ ucfirst($appointment->name ?? 'N/A') }}</td>
                                                <td>{{ $appointment->phone ?? 'N/A' }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $appointment->vehicle_number ?? 'N/A' }}</span></td>
                                                <td>{{ $appointment->appointment_time ?? 'N/A' }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ ($appointment->status ?? '') == 'Confirmed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ ucfirst($appointment->status ?? 'N/A') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                {{-- <img src="{{ asset('assets/img/icons/no-data.svg') }}" alt="No data" width="50" class="mb-2 opacity-50"> --}}
                                <p class="text-muted">No appointments today.</p>
                            </div>
                        @endif
                    </div>   

                    {{-- Upcoming EMIs Pane --}}
                    <div class="tab-pane fade" id="emis" role="tabpanel" aria-labelledby="emis-tab">
                        <h6 class="mb-3 text-dark fw-bold border-bottom pb-2">💰 Upcoming EMIs (Next 7 Days)</h6>
                        @if ($upcomingEmis->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Vehicle No</th>
                                            <th>Model</th>
                                            <th>Due Date</th>
                                            <th>Pending Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingEmis as $emi)
                                            <tr>
                                                <td class="fw-semibold">{{ ucfirst($emi->customer_name ?? 'N/A') }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $emi->vehicle_no ?? 'N/A' }}</span></td>
                                                <td>{{ ucfirst($emi->model_name ?? 'N/A') }}</td>
                                                <td>{{ $emi->emi_due_date ?? 'N/A' }}</td>
                                                <td class="text-danger fw-bold">
                                                    {{ isset($emi->total_subtotal_amount, $emi->payment_amount)
                                                        ? number_format($emi->total_subtotal_amount - $emi->payment_amount, 2)
                                                        : '0.00' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                {{-- <img src="{{ asset('assets/img/icons/no-data.svg') }}" alt="No data" width="50" class="mb-2 opacity-50"> --}}
                                <p class="text-muted">No EMIs due in the next 7 days.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Upcoming Payments Pane --}}
                    <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                        <h6 class="mb-3 text-dark fw-bold border-bottom pb-2">💳 Upcoming Cash/Online Payments (Next 7 Days)</h6>
                        @if ($upcomingPayments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Vehicle No</th>
                                            <th>Model</th>
                                            <th>Next Pending Date</th>
                                            <th>Remaining Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingPayments as $pay)
                                            <tr>
                                                <td class="fw-semibold">{{ ucfirst($pay->customer_name ?? 'N/A') }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $pay->vehicle_no ?? 'N/A' }}</span></td>
                                                <td>{{ ucfirst($pay->model_name ?? 'N/A') }}</td>
                                                <td>{{ $pay->emi_due_date ?? 'N/A' }}</td>
                                                <td class="text-danger fw-bold">{{ number_format($pay->remaining_amount ?? 0, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                {{-- <img src="{{ asset('assets/img/icons/no-data.svg') }}" alt="No data" width="50" class="mb-2 opacity-50"> --}}
                                <p class="text-muted">No Cash/Online payments due in the next 7 days.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Customers Completing 6 Months Pane --}}
                    <div class="tab-pane fade" id="sixmonths" role="tabpanel" aria-labelledby="sixmonths-tab">
                        <h6 class="mb-3 text-dark fw-bold border-bottom pb-2">🎉 Customers Completing 6 Months (Today)</h6>
                        @if ($customers6Months->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Vehicle No</th>
                                            <th>Model</th>
                                            <th>Record Date</th>
                                            <th>6-Month Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers6Months as $cust)
                                            <tr>
                                                <td class="fw-semibold">{{ ucfirst($cust->customer_name ?? 'N/A') }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $cust->vehicle_no ?? 'N/A' }}</span></td>
                                                <td>{{ ucfirst($cust->model_name ?? 'N/A') }}</td>
                                                <td>{{ $cust->record_date ? \Carbon\Carbon::parse($cust->record_date)->format('d-m-Y') : 'N/A' }}</td>
                                                <td class="text-primary fw-bold">{{ $cust->six_month_due_date ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                {{-- <img src="{{ asset('assets/img/icons/no-data.svg') }}" alt="No data" width="50" class="mb-2 opacity-50"> --}}
                                <p class="text-muted">No customers completing 6 months today.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    #todayAppointmentsModal .nav-tabs .nav-link {
        color: #555;
        font-weight: 500;
        transition: all 0.2s;
    }
    #todayAppointmentsModal .nav-tabs .nav-link.active {
        background-color: #FF9F43 !important;
        color: #fff !important;
        font-weight: 600;
        border: none !important;
    }
    #todayAppointmentsModal .nav-tabs .nav-link.active .badge {
        background-color: #fff !important;
        color: #FF9F43 !important;
    }
    #todayAppointmentsModal .nav-tabs .nav-link:hover:not(.active) {
        background-color: #e9ecef;
    }
    #todayAppointmentsModal .table th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    #todayAppointmentsModal .table td {
        font-size: 0.9rem;
    }
</style>
