@extends('layout.app')
@section('title', 'Staff Leave Requests')

@section('content')
    @php
        $canManageLeaveRequests = in_array(optional(auth()->user())->role, ['admin', 'sub-admin', 'hr'], true);
    @endphp

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Staff Leave Requests</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('leave.add') }}" class="btn btn-added" style="background: #FF9F43; color: #fff;">
                    <i class="fa fa-plus me-1"></i> Add Leave
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input">
                            <a class="btn btn-searchset"><img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img"></a>
                        </div>
                    </div>
                    <div class="wordset">
                        <div class="d-flex align-items-center">
                            <select id="department_filter" class="form-select me-2" style="width: 200px;">
                                <option value="">All Departments</option>
                            </select>
                            <select id="employee_filter" class="form-select" style="width: 200px;">
                                <option value="">All Staff</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr style="background: #000; color: #fff;">
                                <th style="color: #fff;">Name</th>
                                <th style="color: #fff;">Start Date</th>
                                <th style="color: #fff;">End Date</th>
                                <th style="color: #fff;">Days</th>
                                <th style="color: #fff;">Reason</th>
                                <th style="color: #fff;">Status</th>
                                @if ($canManageLeaveRequests)
                                    <th style="color: #fff;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($leaves as $leave)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $profileImage = $leave->user && $leave->user->profile_image ? asset('storage/' . $leave->user->profile_image) : asset('public/admin/assets/img/customer/customer5.jpg');
                                            @endphp
                                            <img src="{{ $profileImage }}" alt="" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                            <span>{{ $leave->user ? $leave->user->name : $leave->firstname }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $leave->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $leave->end_date->format('Y-m-d') }}</td>
                                    <td>{{ $leave->no_of_day }}</td>
                                    <td>{{ $leave->reason }}</td>
                                    <td>
                                        @php
                                            $status = strtolower($leave->status);
                                            $bgColor = '#FF9F43'; // Default to pending color
                                            if ($status == 'approved') {
                                                $bgColor = '#28a745';
                                            } elseif ($status == 'rejected') {
                                                $bgColor = '#dc3545';
                                            }
                                        @endphp
                                        <span class="badge" style="background: {{ $bgColor }}; color: #fff; padding: 10px 15px; border-radius: 20px;">{{ ucfirst($leave->status) }}</span>
                                    </td>
                                    @if ($canManageLeaveRequests)
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <form action="{{ route('leave.request.status', $leave->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn p-0 border-0 bg-transparent" title="Approve leave" {{ $status === 'approved' ? 'disabled' : '' }}>
                                                        <i class="fa fa-check-circle" style="color: #28a745; font-size: 20px; opacity: {{ $status === 'approved' ? '0.5' : '1' }};"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('leave.request.status', $leave->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn p-0 border-0 bg-transparent" title="Reject leave" {{ $status === 'rejected' ? 'disabled' : '' }}>
                                                        <i class="fa fa-times-circle" style="color: #dc3545; font-size: 20px; opacity: {{ $status === 'rejected' ? '0.5' : '1' }};"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
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
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';
            const leaveRequestParams = new URLSearchParams(window.location.search);
            const currentSelectedSubAdminId = leaveRequestParams.get('selectedSubAdminId') || '';

            if (selectedSubAdminId !== currentSelectedSubAdminId) {
                if (selectedSubAdminId) {
                    leaveRequestParams.set('selectedSubAdminId', selectedSubAdminId);
                } else {
                    leaveRequestParams.delete('selectedSubAdminId');
                }

                const queryString = leaveRequestParams.toString();
                window.location.replace(`${window.location.pathname}${queryString ? `?${queryString}` : ''}`);
            }
        });
    </script>
@endpush
