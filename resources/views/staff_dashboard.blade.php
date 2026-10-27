@php use Carbon\Carbon; @endphp
@extends('layout.app')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="content">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">My Dashboard</h4>
                    <p class="text-muted mb-0">Staff activity for {{ $monthName }}.</p>
                </div>
                <div>
                    <a href="{{ route('job_card.index') }}" class="btn btn-sm btn-primary me-2">View Job Cards</a>
                    <a href="{{ route('daily_data.index') }}" class="btn btn-sm btn-secondary">View Daily Data</a>
                </div>
            </div>
        </div>

        <div class="row gx-3 gy-3">
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="text-muted">Today Status</h6>
                                <h3 class="mb-1" id="today-status-value">{{ $todayStatus }}</h3>
                            </div>
                            <button id="btn-dashboard-refresh" class="btn btn-sm btn-outline-primary">Refresh</button>
                        </div>
                        <p class="mb-0 text-muted">Check-in/out summary for today.</p>
                        <small class="text-muted" id="dashboard-last-updated">Last updated: just now</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Checked In</h6>
                        <h3 class="mb-1" id="today-checkin-value">{{ $todayCheckInTime ?? '-' }}</h3>
                        <p class="mb-0 text-muted">First session start time.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Checked Out</h6>
                        <h3 class="mb-1">{{ $todayCheckOutTime ?? '-' }}</h3>
                        <p class="mb-0 text-muted">Last session end time.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Hours Worked</h6>
                        <h3 class="mb-1" id="hours-worked-value">{{ $hoursWorkedFormatted }}</h3>
                        <p class="mb-0 text-muted">Remaining: {{ $remainingHoursFormatted }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-3 gy-3 mt-3">
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">My Job Cards</h6>
                        <h3 class="mb-1" id="job-cards-count-value">{{ $staffJobCardsCount }}</h3>
                        <p class="mb-0 text-muted">Created by you.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">My Daily Data</h6>
                        <h3 class="mb-1" id="daily-data-count-value">{{ $staffDailyDataCount }}</h3>
                        <p class="mb-0 text-muted">Created by you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            @if ($recentJobCards->isNotEmpty())
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">My Recent Job Cards</h5>
                            <a href="{{ route('job_card.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Job Card</th>
                                            <th>Vehicle</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-jobcards-body">
                                        @foreach ($recentJobCards as $job)
                                            <tr>
                                                <td>{{ $job->id }}</td>
                                                <td>{{ $job->job_card_number ?? '-' }}</td>
                                                <td>{{ $job->vehicle_no ?? '-' }}</td>
                                                <td>{{ number_format($job->total_subtotal_amount ?? 0, 2) }}</td>
                                                <td>{{ optional($job->created_at)->format('d-m-Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($recentDailyData->isNotEmpty())
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">My Recent Daily Data</h5>
                            <a href="{{ route('daily_data.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Daily Data</th>
                                            <th>Vehicle</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-daily-data-body">
                                        @foreach ($recentDailyData as $daily)
                                            <tr>
                                                <td>{{ $daily->id }}</td>
                                                <td>{{ $daily->job_card_number ?? '-' }}</td>
                                                <td>{{ $daily->vehicle_no ?? '-' }}</td>
                                                <td>{{ number_format($daily->total_subtotal_amount ?? 0, 2) }}</td>
                                                <td>{{ optional($daily->created_at)->format('d-m-Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
<script>
    (function() {
        var endpoint = "{{ route('auth.staff-dashboard-data') }}";
        var refreshButton = document.getElementById('btn-dashboard-refresh');
        var statusValue = document.getElementById('today-status-value');
        var checkInValue = document.getElementById('today-checkin-value');
        var checkOutValue = document.getElementById('today-checkout-value');
        var hoursWorkedValue = document.getElementById('hours-worked-value');
        var jobCardsCountValue = document.getElementById('job-cards-count-value');
        var dailyDataCountValue = document.getElementById('daily-data-count-value');
        var lastUpdated = document.getElementById('dashboard-last-updated');
        var recentJobCardsBody = document.getElementById('recent-jobcards-body');
        var recentDailyDataBody = document.getElementById('recent-daily-data-body');

        function formatTimestamp(iso) {
            try {
                var date = new Date(iso);
                return date.toLocaleString();
            } catch (e) {
                return 'Updated just now';
            }
        }

        function setButtonLoading(isLoading) {
            if (!refreshButton) return;
            refreshButton.disabled = isLoading;
            refreshButton.innerHTML = isLoading ? '<i class="fa fa-spinner fa-spin"></i> Refreshing' : 'Refresh';
        }

        function renderTableRows(rows, emptyMessage) {
            if (!rows || rows.length === 0) {
                return '<tr><td colspan="5" class="text-center py-4">' + emptyMessage + '</td></tr>';
            }
            return rows.map(function(row) {
                var date = row.created_at ? new Date(row.created_at).toLocaleDateString() : '-';
                return '<tr>' +
                    '<td>' + (row.id || '-') + '</td>' +
                    '<td>' + (row.job_card_number || '-') + '</td>' +
                    '<td>' + (row.vehicle_no || '-') + '</td>' +
                    '<td>' + (row.total_subtotal_amount !== undefined ? parseFloat(row.total_subtotal_amount).toFixed(2) : '0.00') + '</td>' +
                    '<td>' + date + '</td>' +
                '</tr>';
            }).join('');
        }

        function updateDashboard(data) {
            if (statusValue) { statusValue.textContent = data.today_status || '-'; }
            if (checkInValue) { checkInValue.textContent = data.today_check_in_time || '-'; }
            if (checkOutValue) { checkOutValue.textContent = data.today_check_out_time || '-'; }
            if (hoursWorkedValue) { hoursWorkedValue.textContent = data.hours_worked || '-'; }
            if (jobCardsCountValue) { jobCardsCountValue.textContent = data.staff_job_cards_count ?? '-'; }
            if (dailyDataCountValue) { dailyDataCountValue.textContent = data.staff_daily_data_count ?? '-'; }
            if (lastUpdated) { lastUpdated.textContent = 'Last updated: ' + formatTimestamp(data.last_updated); }
            if (recentJobCardsBody) {
                recentJobCardsBody.innerHTML = renderTableRows(data.recent_job_cards, 'No job cards created by you yet.');
            }
            if (recentDailyDataBody) {
                recentDailyDataBody.innerHTML = renderTableRows(data.recent_daily_data, 'No daily data records created by you.');
            }
        }

        function fetchDashboardData() {
            setButtonLoading(true);
            fetch(endpoint, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Unable to refresh dashboard.');
                }
                return response.json();
            })
            .then(function(data) {
                updateDashboard(data);
            })
            .catch(function(error) {
                console.warn(error);
            })
            .finally(function() {
                setButtonLoading(false);
            });
        }

        if (refreshButton) {
            refreshButton.addEventListener('click', function() {
                fetchDashboardData();
            });
        }
    })();
</script>
@endpush
