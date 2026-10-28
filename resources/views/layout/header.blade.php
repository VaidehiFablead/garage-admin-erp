@php
    $settings = App\Models\Setting::first();
    $admin = App\Models\User::where('role', 'admin')->first();
@endphp
<style>
    .web_button {
        width: 25px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
/* .header .user-img img {
    width: 38px;
    border-radius: 50%;
    margin-top:1px !important;
} */
    .bar-icon span {
        display: block;
        height: 3px;
        width: 100%;
        background-color: #333;
        margin: 3px 0;
        transition: all 0.3s ease;
    }

    .search-view {
        width: 465px;
    }

    @media (max-width: 991px) {
        .web_button {
            display: none;
        }
    }

    @media (max-width: 983px) and (min-width: 575px) {
        .logo-view {
            float: left;
            margin-left: 3rem;
        }

        .search-view {
            width: 366px;
        }
    }




    @media screen and (max-width: 991px) {
        .header .header-left .logo img {
            margin-right: 10rem
        }

         .filterbranch {
            /* overflow: hidden; */
            width: 131px;
            position: absolute;
            top: 1px;
            right: 61px;
            font-size: 10px;
        }
 

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            width: auto !important;
        }
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: auto !important;
    }

    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ff9f43;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        display: none;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 1050;
    }

    .mobile-bottom-nav .nav-item {
        text-align: center;
        color: #fff;
        text-decoration: none;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: color 0.3s;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .mobile-bottom-nav .nav-item span {
        font-size: 11px;
        font-weight: 500;
    }

    .mobile-bottom-nav .nav-item.active {
        color: #1b2850;
    }

    @media (max-width: 991px) {
        .mobile-bottom-nav {
            display: flex;
        }

        .header-btn-text {
            display: none;
        }

        .user-menu .btn i {
            margin-right: 0 !important;
        }

        body {
            padding-bottom: 60px !important;
        }

        .sidebar {
            bottom: 60px !important;
        }

        .sidebar-inner,
        .slimScrollDiv {
            height: calc(100vh - 120px) !important;
        }

        /* Hide HR dropdown in mobile view */
        .dropdown.me-3.d-flex.align-items-center {
            display: none !important;
        }        
    }
 @media screen and (min-width: 768px) and (max-width: 912px) {
    .logo-view {
        float: left;
        margin-left: -22rem;
    }
}
    
</style>
@php
    $settings = App\Models\Setting::first();
    $admin = App\Models\User::where('role', 'admin')->first();

    $user = auth()->user();
    $plan = $user && $user->plan_id ? \App\Models\Plan::find($user->plan_id) : null;

    $showBranch = tenant_has_module(['My Branch', 'Branch', 'Branches']);
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="header">

    <div class="header-left">
        <a href="{{ route('auth.dashboard') }}" class="logo">
            <img src="{{ !empty($settings) && !empty($settings->logo) ? env('ImagePath') . '/storage/' . $settings->logo : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}" class="logo-view">
        </a>
        <a href="{{ route('auth.dashboard') }}" class="logo-small">
            <img src="{{ !empty($settings) && !empty($settings->favicon) ? env('ImagePath') . '/storage/' . $settings->favicon : 'https://fableadtechnolabs.com/favicon-192x192.webp' }}" >
        </a>

        <!-- <a id="toggle_btn" href="javascript:void(0);">
        </a> -->
        <div id="toggle_btn1" class="web_button active">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </div>
    </div>

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
    <div style="display: flex; justify-content:end;">

        

        <ul class="nav user-menu">
            <div class="d-flex align-items-center">
                @php
                $activePlan = auth()->check() ? auth()->user()->getActivePlan() : null;
                $canViewSalesBills = app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add');
                $canAddSalesBills = app('hasPermission')(2, 'add');
                $canViewPurchases = app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add');
                $canAddPurchases = app('hasPermission')(3, 'add');
            @endphp
            @if($activePlan && auth()->check() && auth()->user()->role !== 'staff')
                @php
                    $isExpiringSoon = false;
                    if ($activePlan->end_date) {
                        $daysLeft = \Carbon\Carbon::now('Asia/Kolkata')->diffInDays(\Carbon\Carbon::parse($activePlan->end_date), false);
                        if ($daysLeft >= 0 && $daysLeft <= 15) {
                            $isExpiringSoon = true;
                        }
                    }
                @endphp
                <a href="{{ route('plans.myplan') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center border rounded px-2 py-1 me-2 bg-white" style="border-color: #e0e0e0 !important; min-width: max-content; height: 38px;">
                        <i class="fas fa-crown me-2" style="color: #ff9f43; font-size: 16px;"></i>
                        <div class="d-flex flex-column me-2" style="line-height: 1.2;">
                            <span style="font-size: 12px; font-weight: 600; color: #333;">{{ $activePlan->name ?? 'Plan' }}</span>
                            <span style="font-size: 10px; color: #888;">Expires: {{ $activePlan->end_date ? \Carbon\Carbon::parse($activePlan->end_date)->format('d-m-Y') : 'N/A' }}</span>
                        </div>
                        @if($isExpiringSoon)
                            <span class="badge" style="background-color: #ff9f43; color: white; font-size: 9px; padding: 4px 6px; font-weight: 500; border-radius: 4px;">Expiring Soon</span>
                        @endif
                    </div>
                </a>
            @endif
            @if (tenant_has_module(['HR', 'Attendance', 'Payroll', 'Leave', 'Advance Pay']) && in_array(auth()->user()->role, ['admin', 'sub-admin', 'hr']))
            <!-- HR Dropdown -->
            <div class="dropdown me-3 d-flex align-items-center">
                <button class="btn btn-sm dropdown-toggle text-white" style="background-color: #ff9f43; border-radius: 6px; height: 36px;" data-bs-toggle="dropdown">
                    <i class="fa fa-id-badge"></i> HR<i class="fa fa-chevron-down ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    @if (tenant_has_module(['Attendance', 'HR']) && (tenant_has_sub_module('Attendance', 'Attendance') || tenant_has_sub_module('Manage Attendance', 'Attendance')))
                        <li><a class="dropdown-item" href="/staff/attendance/calendar">Attendance</a></li>
                    @endif
                    @if (tenant_has_module(['Payroll', 'HR']) && (tenant_has_sub_module('Payroll View', 'Payroll') || tenant_has_sub_module('Add Payroll', 'Payroll') || tenant_has_sub_module('Group Salary', 'Payroll')))
                        <li><a class="dropdown-item" href="/payrollview">Payroll</a></li>
                    @endif
                    @if (tenant_has_module(['Staff', 'HR']) && (tenant_has_sub_module('All Staff', 'Staff') || tenant_has_sub_module('New Staff', 'Staff')))
                        <li><a class="dropdown-item" href="/staff">Staff</a></li>
                    @endif
                    @if (tenant_has_module(['Leave', 'HR']) && (tenant_has_sub_module('All Leave', 'Leave') || tenant_has_sub_module('New Leave', 'Leave')))
                        <li><a class="dropdown-item" href="/leave-request">Leaves</a></li>
                    @endif
                </ul>
            </div>
        @endif

        @if($showBranch)
            <div class="me-3 filterbranch position-relative" id="subBranchContainer"
                style="z-index:99999; display: none; margin-top: .7rem;">
                <div class="d-flex align-items-center">
                    <select id="subBrandSelect" class="form-select form-select-sm" style="height: 38px; width: 155px;">

                    </select>
                    <div id="currentSelection" class="ms-2 text-muted" style="font-size: 12px;"></div>
                </div>
            </div>
        @endif
                @if (auth()->user() && auth()->user()->role === 'staff')
                    <button class="btn btn-sm align-items-center justify-content-center me-3 btn-check-in" style="height: 36px; background-color: #ff9f43; color: white; display: none; border: none;">
                        <i class="fa fa-sign-in me-2" aria-hidden="true"></i> <span class="header-btn-text">Check In</span>
                    </button>
                    <button class="btn btn-sm align-items-center justify-content-center me-3 btn-check-out" style="height: 36px; background-color: #e74c3c; color: white; display: none; border: none;">
                        <i class="fa fa-sign-out me-2" aria-hidden="true"></i> <span class="header-btn-text">Check Out</span>
                    </button>
                @endif
                <!-- Reports Dropdown -->
                <!-- Simple Sub-Brand Select -->


                <!-- Search Field Container -->


                <!-- New Order Button -->
                @if (tenant_has_module(['Appointment', 'CRM']) && (tenant_has_sub_module('All Appointment', 'Appointment') || tenant_has_sub_module('New Appointment', 'Appointment')) && (app('hasPermission')(17, 'view') || app('hasPermission')(12, 'view')))
                    <a href="/appointments" class="btn btn-sm d-flex align-items-center justify-content-center me-3"
                        style="height: 36px; background-color: #ff9f43; color: white;">
                        <i class="fa fa-calendar-check me-2" aria-hidden="true"></i> <span
                            class="header-btn-text">Appointment</span>
                    </a>
                @endif
                @if (tenant_has_module(['Account Ledger', 'Accounting']) && tenant_has_sub_module('Account Ledger', 'Account Ledger') && (app('hasPermission')(25, 'view') || app('hasPermission')(13, 'view')))
                    <a href="/account-ledger" class="btn btn-sm d-flex align-items-center justify-content-center me-3"
                        style="height: 36px; background-color: #ff9f43; color: white;">
                        <i class="fa fa-book me-2" aria-hidden="true"></i> <span class="header-btn-text">Account
                            Ledger</span>
                    </a>
                @endif

                @php
                    $subAdminId = session('selectedSubAdminId');
                @endphp

                {{-- @if (empty($subAdminId)) --}}

                {{-- Job Cards (Bills) Dropdown --}}
                @if (tenant_has_module(['Job Card', 'ERP']) && (app('hasPermission')(16, 'view') || app('hasPermission')(16, 'add')))
                    <div class="dropdown me-3">
                        <a class="btn btn-sm d-flex align-items-center justify-content-center dropdown-toggle"
                            style="height: 36px; background-color: #ff9f43; color: white;" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-tasks me-2" aria-hidden="true"></i> <span class="header-btn-text">Job cards
                                (Bills)</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if (tenant_has_sub_module('All Job Cards', 'Job Card') && app('hasPermission')(16, 'view'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_card.index') }}">
                                        <img class="me-2"
                                            src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                            alt="Job Cards Icon">
                                        All Job cards (Bills)
                                    </a>
                                </li>
                            @endif
                            @if (tenant_has_sub_module('New Job Card', 'Job Card') && app('hasPermission')(16, 'add'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_card.create') }}">
                                        <img class="me-2"
                                            src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                            alt="Job Cards Icon">
                                        New Job cards (Bills)
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
                {{-- @endif --}}



                {{-- @if ($subAdminId)

                    @if (app('hasPermission')(16, 'view') || app('hasPermission')(16, 'add'))
                        <div class="dropdown me-3">
                            <a class="btn btn-sm d-flex align-items-center justify-content-center dropdown-toggle"
                                style="height: 36px; background-color: #ff9f43; color: white;" href="#"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-tasks me-2" aria-hidden="true"></i>Sales and Orders
                            </a>
                            <ul class="dropdown-menu">
                                @if (app('hasPermission')(16, 'view'))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('sales.list') }}">
                                            <img class="me-2"
                                                src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                                alt="Job Cards Icon">
                                            All Sales and Orders
                                        </a>
                                    </li>
                                @endif
                                @if (app('hasPermission')(16, 'add'))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('sales.add') }}">
                                            <img class="me-2"
                                                src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                                alt="Job Cards Icon">
                                            New Sales
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                @endif --}}

                {{-- Daily Register Dropdown --}}
                @if (tenant_has_module(['Daily Register', 'ERP']) && (app('hasPermission')(24, 'view') || app('hasPermission')(24, 'add')))
                    <div class="dropdown">
                        <a class="btn btn-sm d-flex align-items-center justify-content-center dropdown-toggle me-3"
                            style="height: 36px; background-color: #ff9f43; color: white;" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-calendar me-2" aria-hidden="true"></i> <span class="header-btn-text">Daily
                                Register</span>
                        </a>
                        <ul class="dropdown-menu">
                            @if (tenant_has_sub_module('All Daily Register', 'Daily Register') && app('hasPermission')(24, 'view'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('daily_data.index') }}">
                                        <img class="me-2"
                                            src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                            alt="Daily Register Icon">
                                        All Daily Register
                                    </a>
                                </li>
                            @endif
                            @if (tenant_has_sub_module('New Daily Register', 'Daily Register') && app('hasPermission')(24, 'add'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('daily_data.add-daily-data') }}">
                                        <img class="me-2"
                                            src="{{ env('ImagePath') . '/admin/assets/img/icons/purchase1.svg' }}"
                                            alt="Daily Register Icon">
                                        New Daily Register
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if (tenant_has_module(['Appointment', 'CRM']))
                <li class="nav-item me-3">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#todayAppointmentsModal"
                        class="d-flex align-items-center justify-content-center"
                        style="height: 38px; width: 38px; background-color: #ff9f43; color: white; border-radius: 50%;">
                        <i class="fa fa-bell"></i>
                    </a>
                </li>
                @endif
            </div>

            <li class="nav-item dropdown has-arrow main-drop">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-img"><img
                            src="{{ !empty($admin) && !empty($admin->profile_image) ? env('ImagePath') . 'storage/' . $admin->profile_image : env('ImagePath') . '/admin/assets/img/customer/customer5.jpg' }}"
                            alt="" style="height: 40px;">
                        <span class="status online"></span></span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <div class="profileset">
                            <span class="user-img">
                                <img src="{{ !empty($admin) && !empty($admin->profile_image) ? env('ImagePath') . 'storage/' . $admin->profile_image : env('ImagePath') . '/admin/assets/img/customer/customer5.jpg' }}"
                                    alt="">
                            </span>
                            <span class="status online"></span></span>
                            <div class="profilesets">
                                <h6>{{ auth()->user()->name }}</h6>
                                <h5>{{ auth()->user()->role }}</h5>
                            </div>
                        </div>
                        <hr class="m-0">
                        <a class="dropdown-item" href="{{ route('auth.profile') }}"> <i class="me-2"
                                data-feather="user"></i>
                            My Profile</a>
                        @if (auth()->user()->role === 'admin')
                            @if (tenant_has_module(['Shop Settings', 'Settings', 'Shop Setting']))
                                <a class="dropdown-item setting" href="{{ route('setting.generalsettings') }}"><i
                                        class="me-2" data-feather="settings"></i>Settings</a>
                                <hr class="m-0">
                            @endif
                            @if ($showBranch)
                                <a class="dropdown-item new_branch" href="{{ route('subbranch.list') }}">
                                    <i class="me-2" data-feather="settings"></i>My Branch</a>
                                <hr class="m-0">
                            @endif
                        @endif
                        <a class="dropdown-item logout pb-0" href="{{ route('logout') }}" onclick="localStorage.removeItem('authToken'); localStorage.removeItem('selectedSubAdminId');"><img
                                src="{{ env('ImagePath') . '/admin/assets/img/icons/log-out.svg' }}" class="me-2"
                                alt="img">Logout</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>


    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('auth.profile') }}">My Profile</a>
            @if (auth()->user()->role === 'admin')
                @if (tenant_has_module(['Shop Settings', 'Settings', 'Shop Setting']))
                    <a class="dropdown-item" href="{{ route('setting.generalsettings') }}">Settings</a>
                @endif
                @if ($showBranch)
                    <a class="dropdown-item" href="{{ route('subbranch.list') }}">My Branch</a>
                @endif
            @endif
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="localStorage.removeItem('authToken'); localStorage.removeItem('selectedSubAdminId');">Logout</a>
        </div>
    </div>

</div>


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('customerSearch');
            const resultBox = document.getElementById('searchResults');

            // searchInput.addEventListener('input', function () {
            //     const query = this.value.trim();

            //     if (query.length < 1) {
            //         resultBox.style.display = 'none';
            //         return;
            //     }

            //     fetch(`/search-users?query=${encodeURIComponent(query)}`)
            //         .then(response => response.json())
            //         .then(data => {
            //             resultBox.innerHTML = '';

            //             let hasResults = false;

            //             // Users section
            //             if (data.users && data.users.length > 0) {
            //                 hasResults = true;
            //                 resultBox.innerHTML += `<div class="list-group-item fw-bold">Customers</div>`;
            //                 data.users.forEach(user => {
            //                     const profileUrl = `/${user.role === 'vendor' ? 'vendor-view' : 'customer-view'}/${user.id}`;
            //                     resultBox.innerHTML += `
        //                                                 <a href="${profileUrl}" class="list-group-item d-flex align-items-center">
        //                                                     <img src="${user.profile_image}" alt="User Image" class="rounded-circle me-2" style="width:30px; height:30px;">
        //                                                     <div>
        //                                                         <strong>${user.name ?? 'N/A'}</strong><br>
        //                                                         <small>${user.email ?? 'N/A'}</small>
        //                                                     </div>
        //                                                 </a>
        //                                             `;
            //                 });
            //             }


            //             // Products section
            //             if (data.products && data.products.length > 0) {
            //                 hasResults = true;
            //                 resultBox.innerHTML += `<div class="list-group-item fw-bold mt-2">Products</div>`;
            //                 data.products.forEach(product => {
            //                     resultBox.innerHTML += `
        //                                             <a href="/product-view/${product.id}" class="list-group-item d-flex align-items-center">
        //                                                 <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:30px; height:30px; object-fit: cover;">
        //                                                 <div>
        //                                                     <strong>${product.name ?? 'N/A'}</strong><br>
        //                                                     <small>Price: ${product.price ?? 'N/A'}</small>
        //                                                 </div>
        //                                             </a>
        //                                         `;
            //                 });
            //             }

            //             // Orders section
            //             if (data.orders && data.orders.length > 0) {
            //                 hasResults = true;
            //                 resultBox.innerHTML += `<div class="list-group-item fw-bold mt-2">Orders</div>`;
            //                 data.orders.forEach(order => {
            //                     resultBox.innerHTML += `
        //                                             <a href="/sales-details/${order.id}" class="list-group-item d-flex flex-column">
        //                                                 <div class="d-flex align-items-center mb-1">
        //                                                     <img src="/admin/assets/img/icons/cart.svg" width="20" height="20" class="me-2" alt="cart">
        //                                                     <strong>Order #: ${order.order_number ?? 'N/A'}</strong>
        //                                                 </div>
        //                                                 <div>
        //                                                     <small>Customer Name: ${order.user_name ?? 'N/A'}</small><br>
        //                                                     <small>Total Amount: ${order.total_amount ?? 'N/A'}</small> |
        //                                                     <small>Status: ${order.payment_status ?? 'N/A'}</small>
        //                                                 </div>
        //                                             </a>
        //                                             `;
            //                 });

            //             }


            //             if (!hasResults) {
            //                 resultBox.innerHTML = '<div class="list-group-item">No results found</div>';
            //             }

            //             resultBox.style.display = 'block';
            //         })
            //         .catch(error => {
            //             console.error('Error fetching search results:', error);
            //             resultBox.style.display = 'none';
            //         });
            // });

            // Optional: Hide dropdown on outside click
            // document.addEventListener('click', function (e) {
            //     if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
            //         resultBox.style.display = 'none';
            //     }
            // });




        });

        $(document).on('mouseover', function(e) {
            e.stopPropagation();

            // Check if mini-sidebar mode is active and toggle button is visible
            if ($('body').hasClass('mini-sidebar') && $('#toggle_btn1').is(':visible')) {
                const isInsideSidebar = $(e.target).closest('.sidebar, .header-left').length;

                if (isInsideSidebar) {
                    $('body').addClass('expand-menu');
                    $('.subdrop + ul').slideDown();
                } else {
                    $('body').removeClass('expand-menu');
                    $('.subdrop + ul').slideUp();
                }

                return false;
            }
        });

        $(document).on('click', '#toggle_btn1', function() {
            const body = $('body');

            if (body.hasClass('mini-sidebar')) {
                body.removeClass('mini-sidebar');
                $(this).addClass('active');
                $('.subdrop + ul').slideDown();
                localStorage.setItem('screenModeNightTokenState', 'night');

                setTimeout(function() {
                    body.removeClass('mini-sidebar');
                    $('.header-left').addClass('active');
                }, 100);
            } else {
                body.addClass('mini-sidebar');
                $(this).removeClass('active');
                $('.subdrop + ul').slideUp();
                localStorage.removeItem('screenModeNightTokenState');

                setTimeout(function() {
                    body.addClass('mini-sidebar');
                    $('.header-left').removeClass('active');
                }, 100);
            }

            return false;
        });
        // Fetch Sub Admins and populate select box
        var subAdminAuthToken = localStorage.getItem("authToken");
        var subAdminHeaders = {};
        if (subAdminAuthToken && subAdminAuthToken !== "null" && subAdminAuthToken !== "undefined") {
            subAdminHeaders["Authorization"] = "Bearer " + subAdminAuthToken;
        }

        fetch('/api/get_subadmin', {
                headers: subAdminHeaders
            })
            .then(res => {
                if (!res.ok) {
                    return null;
                }
                return res.json();
            })
            .then(response => {
                if (response && response.status && Array.isArray(response.data)) {
                    const select = document.getElementById("subBrandSelect");
                    const role = response.role;


                    if (role === 'admin') {
                        const subBranchContainer = document.getElementById('subBranchContainer');
                        if (subBranchContainer) {
                            subBranchContainer.style.display = 'block'; // Show the select
                        }
                    }

                    if (select) {
                        if (role === 'admin') {
                            const showAllOption = document.createElement("option");
                            showAllOption.value = "1";
                            showAllOption.textContent = "Main Branch";
                            select.appendChild(showAllOption);
                        }

                        // Append options without images
                        response.data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.id;
                            option.textContent = item.name;
                            select.appendChild(option);
                        });

                        // Handle saved selection
                        const savedId = localStorage.getItem('selectedSubAdminId');
                        if (savedId) {
                            const optionExists = response.data.some(item => item.id == savedId);
                            if (optionExists) {
                                $('#subBrandSelect').val(savedId).trigger('change.select2');
                                setTimeout(() => {
                                    loadFilteredDashboardData(savedId);
                                }, 500);
                            } else {
                                $.post('/clear-subadmin-session', {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                }, function(response) {

                                    console.log('Session cleared');
                                    localStorage.removeItem('selectedSubAdminId');

                                });

                            }
                        }
                    }
                }
                const savedId = localStorage.getItem('selectedSubAdminId');

                if (savedId && response && response.data && document.getElementById("subBrandSelect")) {
                    const optionExists = response.data.some(item => item.id == savedId);
                    if (optionExists) {
                        $('#subBrandSelect').val(savedId).trigger('change.select2');

                        setTimeout(() => {
                            loadFilteredDashboardData(savedId);
                        }, 500);
                    }
                }
            })
            .catch(err => {
                console.warn('get_subadmin notice:', err);
            });



        $('#subBrandSelect').on('change', function() {
            const selectedId = $(this).val();
            const selectedText = $(this).find('option:selected').text();

            // if (selectedId) {
            //     localStorage.setItem('selectedSubAdminId', selectedId);
            //     // $('.content').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            //     loadFilteredDashboardData(selectedId).then(() => {
            //         window.location.href = '/dashboard'; // ✅ Wait until AJAX finishes
            //     });
            // } else {
            //     if (selectedText === "Main Branch") {
            //         $.post('/clear-subadmin-session', {
            //             _token: $('meta[name="csrf-token"]').attr('content')
            //         }, function(response) {

            //             console.log('Session cleared');
            //             localStorage.removeItem('selectedSubAdminId');
            //             window.location.href = '/dashboard';
            //         });


            //     }
            // }
            if (selectedId == "1") {

                $.post('/clear-subadmin-session', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function () {

                    localStorage.removeItem('selectedSubAdminId');
                    window.location.href = '/dashboard';

                });

            } else {

                localStorage.setItem('selectedSubAdminId', selectedId);

                loadFilteredDashboardData(selectedId).then(() => {
                    // window.location.href = '/dashboard';
                    window.location.reload();
                });

            }
        });

        function loadFilteredDashboardData(subAdminId) {
            return $.ajax({
                url: '/api/dashboard-filtered',
                type: 'GET',
                data: {
                    sub_admin_id: subAdminId
                },
                success: function(response) {
                    if (response.status && response.data) {
                        // console.log(response.data);

                        updateDashboardData(response.data);
                    } else {
                        console.error("Failed to load filtered dashboard data:", response.message);
                    }
                },
                error: function(xhr) {
                    console.error("XHR Error Response:", xhr);
                    let message = "Unknown error";
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || xhr.statusText;
                    } catch (e) {
                        message = xhr.statusText;
                    }
                    console.error("Failed to load filtered dashboard data:", message);
                },
                complete: function() {
                    $('#loading-overlay').remove();
                }
            });
        }


        function updateDashboardData(data) {
            const currencySymbol = data.currencySymbol || '₹';
            const currencyPosition = data.currencyPosition || 'left';

            // console.log('Updating dashboard with data:', data);

            $('.counters123[data-count]').each(function() {
                const $counter = $(this);
                const dataCount = parseFloat($counter.attr('data-count'));
                const currentValue = parseFloat(dataCount);

                // console.log('Checking counter:', $counter.text(), 'data-count:', dataCount);

                if (Math.abs(currentValue - data.totalPurchaseAmount) < 0.01) {
                    // console.log('Updating purchase amount counter');
                    updateCounter($counter, data.totalPurchaseAmount, currencySymbol, currencyPosition);
                } else if (Math.abs(currentValue - data.totalSalesAmount) < 0.01) {
                    // console.log('Updating sales amount counter');
                    updateCounter($counter, data.totalSalesAmount, currencySymbol, currencyPosition);
                } else if (Math.abs(currentValue - data.totalExpenseAmount) < 0.01) {
                    // console.log('Updating expense amount counter');
                    updateCounter($counter, data.totalExpenseAmount, currencySymbol, currencyPosition);
                }
            });

            // Also try to update by looking at the parent elements for context
            $('.dash-widget, .dash-count').each(function() {
                const $widget = $(this);
                const $counter = $widget.find('.counters123[data-count]');

                if ($counter.length > 0) {
                    const widgetText = $widget.text().toLowerCase();
                    // console.log('Widget text:', widgetText);


                    if (widgetText.includes('collections')) {
                        // console.log('asdasd');
                        // console.log(data.totalCollection);

                        // console.log('Found purchase amount widget');
                        updateCounter($counter, data.totalCollection, currencySymbol, currencyPosition);
                    } else if (widgetText.includes('expense')) {
                        // console.log('Found sales amount widget');
                        updateCounter($counter, data.totalExpenseAmount, currencySymbol, currencyPosition);
                    } else if (widgetText.includes('pending')) {
                        // console.log('Found expense amount widget');
                        updateCounter($counter, data.pendingAmount, currencySymbol, currencyPosition);
                    }
                }
            });

            if (data.totalJobCards !== undefined) {
                let tempid = localStorage.getItem('selectedSubAdminId');


                if (tempid !== null && tempid !== undefined && tempid !== '') {
                    $('[data-customer-count]').text(data.totalOrdersCount);
                } else {
                    $('[data-customer-count]').text(data.totalJobCards);
                }
            }
            if (data.totalDailyData !== undefined) {
                $('[data-vendor-count]').text(data.totalDailyData);
                // console.log('Updated vendor count:', data.vendorCount);
            }
            if (data.purchaseInvoiceCount !== undefined) {
                // $('[data-purchase-invoice-count]').text(data.purchaseInvoiceCount);
                $('[data-purchase-invoice-count]').html(
                    `P ${data.totalPresent} / AB ${data.totalAbsent}`
                );
                // console.log(data);

                // console.log('Updated purchase invoice count:', data.purchaseInvoiceCount);
            }
            if (data.totalAppointments !== undefined) {
                $('[data-sales-invoice-count]').text(data.totalAppointments);
                // console.log('Updated sales invoice count:', data.salesInvoiceCount);
            }

            if (data.salesChartThisMonth && window.salesChartData && document.querySelector('#saleschart')) {
                window.salesChartData.thisMonth = data.salesChartThisMonth;
                if (window.currentSalesYear) {
                    renderChart('#saleschart', 'Job Card', window.salesChartData[window.currentSalesYear], '#44c4fa', window
                        .currentSalesYear === 'thisMonth');
                }
            }
            if (data.purchaseChartThisMonth && window.purchaseChartData && document.querySelector('#purchasechart')) {
                window.purchaseChartData.thisMonth = data.purchaseChartThisMonth;
                if (window.currentPurchaseYear) {
                    renderChart('#purchasechart', 'Daily Data', window.purchaseChartData[window.currentPurchaseYear],
                        '#fa6c7c', window.currentPurchaseYear === 'thisMonth');
                }
            }
            if (data.salesChartthisyear && window.salesChartData && document.querySelector('#saleschart')) {
                window.salesChartData[new Date().getFullYear()] = data.salesChartthisyear;
                if (window.currentSalesYear) {
                    renderChart('#saleschart', 'Job Card', window.salesChartData[window.currentSalesYear], '#44c4fa', window
                        .currentSalesYear === 'thisMonth');
                }
            }
            if (data.purchaseChartthisyear && window.purchaseChartData && document.querySelector('#purchasechart')) {
                window.purchaseChartData[new Date().getFullYear()] = data.purchaseChartthisyear;
                if (window.currentPurchaseYear) {
                    renderChart('#purchasechart', 'Daily Data', window.purchaseChartData[window.currentPurchaseYear],
                        '#fa6c7c', window.currentPurchaseYear === 'thisMonth');
                }
            }


            // Update latest sales table
            if (data.latestSales) {
                const currencySymbol = data.currencySymbol || '₹';
                const currencyPosition = data.currencyPosition || 'left';
                updateLatestSalesTable(data.latestSales, currencySymbol, currencyPosition);
            }
            // console.log(data);

            // Update latest purchases table
            if (data.latestPurchases) {
                const currencySymbol = data.currencySymbol || '₹';
                const currencyPosition = data.currencyPosition || 'left';
                updateLatestPurchasesTable(data.latestPurchases, currencySymbol, currencyPosition);
            }

            // console.log("Dashboard data updated successfully");
        }

        // Function to update counter with currency formatting
        function updateCounter($element, value, currencySymbol, currencyPosition) {
            const formattedValue = new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);

            // Only update the number part, not the entire content

            $element.attr('data-count', value);
            // Update the data-count attribute
            $element.text(formattedValue);

            // console.log('Counter updated to:', $element.html());
        }

        // Function to update latest sales table
        // Capitalize helper
        // console.log(currencySymbol);

        function capitalizeFirstLetter(str) {
            if (!str || str === 'N/A') return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function updateLatestSalesTable(salesData, currencySymbol = '', currencyPosition = 'left') {
            const tableBody = $('.latest-sales-table tbody');

            if (tableBody.length > 0) {
                tableBody.empty();

                if (!salesData || salesData.length === 0) {
                    tableBody.append('<tr><td colspan="4">No Job Cards Found</td></tr>');
                    return;
                }

                salesData.forEach((sale, index) => {
                    console.log(sale);
                    const jobCardNumber = capitalizeFirstLetter(sale.job_card_number || sale.order_number);
                    const vehicleNo = capitalizeFirstLetter(sale.vehicle_no || (sale.user && sale.user.name) || 'N/A');
                    const customerName = capitalizeFirstLetter((sale.customer && sale.customer.name) || sale
                        .payment_status);
                    const amountRaw = parseFloat(sale.total_subtotal_amount || sale.total_amount || 0).toFixed(2);
                    const totalAmount =
                        `${currencyPosition === 'left' ? currencySymbol : ''}${amountRaw}${currencyPosition === 'right' ? currencySymbol : ''}`;
                    const url = sale.job_card_number ?
                        `/job-card/${sale.id}/view-pdf` :
                        `/sales-details/${sale.id}`;
                    const detailsRowId = `sales-details-ajax-${sale.id || index}`;
                    const row = `
                <tr>
                    <td><a href="${url}">${jobCardNumber}</a></td>
                    <td>${vehicleNo}</td>
                    <td>${customerName}</td>
                    <td>${totalAmount}</td>
                    <td class="action-cell">
                        <a href="javascript:void(0);" class="toggle-details"
                            data-bs-target="#${detailsRowId}"
                            aria-expanded="false">
                            <i class="fa fa-plus"></i>
                        </a>
                    </td>
                </tr>
                <tr class="sales-details-row" id="${detailsRowId}" style="display:none;">
                    <td colspan="5">
                        <div class="collapse-content">
                            <p class="mb-1"><strong>Vehicle No:</strong> ${vehicleNo}</p>
                            <p class="mb-1"><strong>Customer Name:</strong> ${customerName}</p>
                            <p class="mb-0"><strong>Grand Total:</strong> ${totalAmount}</p>
                        </div>
                    </td>
                </tr>
            `;
                    tableBody.append(row);
                });
            }
        }

        function updateLatestPurchasesTable(latestPurchases, currencySymbol = '₹', currencyPosition = 'left') {
            const tableBody = $('.latest-purchases-table tbody');

            if (tableBody.length > 0) {
                tableBody.empty();

                if (!latestPurchases || latestPurchases.length === 0) {
                    tableBody.append('<tr><td colspan="4">No Job Card Found</td></tr>');
                    return;
                }

                latestPurchases.forEach((item, index) => {
                    const vehicleNo = capitalizeFirstLetter(item.vehicle_no || 'N/A');
                    const customerName = capitalizeFirstLetter((item.customer && item.customer.name) || 'N/A');
                    const technicianName = capitalizeFirstLetter((item.technician_user && item.technician_user
                        .name) || 'N/A');

                    let totalAmount = Number(item.total_subtotal_amount || 0).toFixed(2);
                    if (currencyPosition === 'left') {
                        totalAmount = `${currencySymbol}${totalAmount}`;
                    } else if (currencyPosition === 'right') {
                        totalAmount = `${totalAmount}${currencySymbol}`;
                    }

                    const detailsRowId = `purchase-details-ajax-${index}`;
                    const row = `
                <tr>
                    <td>${vehicleNo}</td>
                    <td>${customerName}</td>
                    <td class="details-cell">
                        <a href="javascript:void(0);" class="toggle-details"
                            data-bs-target="#${detailsRowId}"
                            aria-expanded="false">
                            <i class="fa fa-plus"></i>
                        </a>
                    </td>
                    <td>${technicianName}</td>
                    <td>${totalAmount}</td>
                </tr>
                <tr class="purchase-details-row" id="${detailsRowId}" style="display:none;">
                    <td colspan="5">
                        <div class="collapse-content">
                            <p class="mb-1"><strong>Job Done By:</strong> ${technicianName}</p>
                            <p class="mb-1"><strong>Work Amount:</strong> ${totalAmount}</p>
                        </div>
                    </td>
                </tr>
            `;
                    tableBody.append(row);
                });
            }
        }

        // $('#subBrandSelect').select2({
        //     placeholder: "Select Sub Brand",
        //     allowClear: true,
        //     width: 'resolve' // Ensures it respects the inline width
        // });
    </script>

    <style>
        @media only screen and (max-width: 767px) {
            .select2-container {
                min-width: unset !important;
            }
        }

        /* iPhone SE */
        @media screen and (max-width: 340px) {
            .select2-container {
                width: 123px !important;
            }
        }

        @media screen and (min-width: 341px) and (max-width: 374px) {
            .select2-container {
                width: 140px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 375px) and (max-width: 390px) {
            .select2-container {
                width: 145px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 391px) and (max-width: 420px) {
            .select2-container {
                width: 165px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 430px) and (max-width: 480px) {
            .select2-container {
                width: 175px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 819px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 340px !important;
            }
        }

        @media screen and (min-width: 820px) and (max-width: 1023px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 368px !important;
            }

            .header-new-order-button {
                display: none !important;
            }

            #toggle_btn1 {
                display: none !important;
            }


            .header-search {
                display: none !important;
            }

            .header-search-container {
                display: none !important;
            }

            .logo-view {
                float: none;
            }
        }

        @media screen and (width: 768px) and (height: 1024px) {
            .header-new-order-button {
                display: none !important;
            }

            .header-search {
                display: none !important;
            }

            .header-search-container {
                display: none !important;
            }

            .logo-view {
                float: none;
            }

            #toggle_btn1 {
                display: none !important;
            }
        }


        @media screen and (width: 1024px) and (height: 1366px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 151px !important;
            }

            /* Hide button text labels on iPad Pro, show icons only */
            .header-btn-text {
                display: none !important;
            }

            .user-menu .btn i,
            .user-menu .dropdown > a i {
                margin-right: 0 !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */
            /* .web_button{
                position: absolute;
                z-index: 9999;
               }


               #toggle_btn1  {
                display: none !important;
               }
               .header .mobile_btn {
                display: block !important;
               }
        */
            /* .header-search{
                display: none !important;
               }
               .header-search-container{
                display: none !important;
               } */
            .search-view {
                width: 285px !important;
            }

            .logo-view {
                float: none;
                /* display: none !important; */
            }

            /* .header-new-order-button{
                display: none !important;
               } */
            .mini-sidebar .header-left .logo-small {
                display: none !important;
            }
        }

        @media screen and (width: 540px) and (height: 720px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 225px !important;
            }
        }

        @media screen and (width: 1024px) and (height: 600px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 155px !important;
            }

            /* Hide button text labels on Nest Hub, show icons only */
            .header-btn-text {
                display: none !important;
            }

            .user-menu .btn i,
            .user-menu .dropdown > a i {
                margin-right: 0 !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */
            .web_button {
                position: absolute;
                z-index: 9999;
            }

            .search-view {
                width: 285px !important;
            }

            .logo-view {
                float: none;
                /* display: none !important; */
            }

            .mini-sidebar .header-left .logo-small {
                display: none !important;
            }

            div#toggle_btn1 {
                position: relative !important;
            }
        }

        @media screen and (width: 1280px) and (height: 800px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 215px !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */

            .web_button {
                position: absolute;
                z-index: 9999;
            }
        }

        .search-view {
            width: 285px !important;
        }

        .logo-view {
            float: none;
            /* display: none !important; */
        }

        .mini-sidebar .header-left .logo-small {
            display: none !important;
        }

        div#toggle_btn1 {
            position: relative !important;
        }
    </style>
@endpush


<script>
    var currentUserRole = "{{ auth()->check() ? auth()->user()->role : '' }}";
</script>
{{-- ==================== ATTENDANCE CHECK IN/OUT (same as omsai-ERP) ==================== --}}
    <script>
        (function() {
            // Guard: only run for staff role
            console.log('[Attendance] currentUserRole =', typeof currentUserRole !== 'undefined' ? currentUserRole : 'UNDEFINED');
            if (typeof currentUserRole === 'undefined' || currentUserRole !== 'staff') {
                console.log('[Attendance] Skipping — user is not staff.');
                return;
            }

            var btnIns  = document.querySelectorAll('.btn-check-in');
            var btnOuts = document.querySelectorAll('.btn-check-out');

            console.log('[Attendance] check-in buttons found:', btnIns.length, '| check-out buttons found:', btnOuts.length);

            if (btnIns.length === 0 || btnOuts.length === 0) return;

            var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // ── State helpers ──────────────────────────────────────────────
            function showCheckIn() {
                btnIns.forEach(function(btn) { btn.style.display = 'flex'; });
                btnOuts.forEach(function(btn) { btn.style.display = 'none'; });
                console.log('[Attendance] → Showing Check In');
            }
            function showCheckOut() {
                btnIns.forEach(function(btn) { btn.style.display = 'none'; });
                btnOuts.forEach(function(btn) { btn.style.display = 'flex'; });
                console.log('[Attendance] → Showing Check Out');
            }

            // ── Fetch today's status on page load ──────────────────────────
            function fetchStatus() {
                fetch("{{ route('staff.checkstatus', [], false) }}", {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(function(r) {
                    console.log('[Attendance] status response HTTP:', r.status);
                    return r.ok ? r.json() : Promise.reject('HTTP ' + r.status);
                })
                .then(function(data) {
                    console.log('[Attendance] status data:', data);
                    if (data.status === 'checked_in') {
                        showCheckOut();
                    } else {
                        showCheckIn();
                    }
                })
                .catch(function(err) {
                    console.warn('[Attendance] status fetch failed:', err);
                    showCheckIn(); // safe fallback
                });
            }

            fetchStatus();

            // ── Check In click ─────────────────────────────────────────────
            btnIns.forEach(function(btn) {
                btn.addEventListener('click', function() {

                    // Disable all check-in buttons and show spinner
                    btnIns.forEach(function(b) {
                        b.disabled = true;
                        b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting GPS...';
                    });

                    function resetBtnIns() {
                        btnIns.forEach(function(b) {
                            b.disabled = false;
                            b.innerHTML = '<i class="fa fa-sign-in-alt"></i>&nbsp;Check In';
                        });
                    }

                    async function doCheckIn(latitude, longitude) {
                        btnIns.forEach(function(b) {
                            b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking In...';
                        });

                        var body = {};
                        if (latitude !== undefined && longitude !== undefined) {
                            body.check_in_latitude  = latitude;
                            body.check_in_longitude = longitude;
                            
                            try {
                                btnIns.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting Address...'; });
                                let geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                                let geoData = await geoRes.json();
                                if (geoData && geoData.display_name) {
                                    body.check_in_location_name = geoData.display_name;
                                }
                            } catch (e) {
                                console.warn('[CheckIn] Geocoding failed:', e);
                            }
                            
                            btnIns.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking In...'; });
                        }

                        fetch("{{ route('staff.checkin', [], false) }}", {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        })
                        .then(async function(r) {
                            var text = await r.text();
                            try {
                                var data = JSON.parse(text);
                                return { ok: r.ok, status: r.status, data: data };
                            } catch (e) {
                                console.error('[CheckIn] Server returned non-JSON:', text);
                                throw new Error('Server returned an invalid response (HTTP ' + r.status + ')');
                            }
                        })
                        .then(function(res) {
                            var data = res.data;
                            if (res.ok || data.success) {
                                Swal.fire({ toast:true, position:'top', icon:'success', title: data.message || 'Checked In successfully.', showConfirmButton:false, timer:2000, timerProgressBar:true })
                                    .then(function() { window.location.reload(); });
                                showCheckOut();
                            } else {
                                Swal.fire({ toast:true, position:'top', icon:'error', title: data.error || 'Check In failed.', showConfirmButton:false, timer:5000 });
                            }
                        })
                        .catch(function(err) {
                            console.error('[CheckIn] Request failed:', err);
                            Swal.fire({ toast:true, position:'top', icon:'error', title: err.message || 'Check In failed. Please try again.', showConfirmButton:false, timer:5000 });
                        })
                        .finally(resetBtnIns);
                    }

                    // Get GPS then check in
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                var lat      = position.coords.latitude;
                                var lon      = position.coords.longitude;
                                var accuracy = position.coords.accuracy;
                                console.log('[CheckIn] GPS: ' + lat + ', ' + lon + ' | Accuracy: ' + accuracy + 'm');
                                doCheckIn(lat, lon);
                            },
                            function(error) {
                                console.warn('[CheckIn] GPS error (code ' + error.code + '):', error.message);
                                if (error.code === 1) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Location Access Required',
                                        html: 'Please allow location access in your browser and try again.<br><br><small>On iPhone: Settings → Safari → Location → Allow</small>',
                                        confirmButtonText: 'OK'
                                    });
                                    resetBtnIns();
                                } else {
                                    // GPS timeout / unavailable — proceed without coords
                                    doCheckIn();
                                }
                            },
                            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                        );
                    } else {
                        doCheckIn();
                    }

                }); // end click listener
            }); // end btnIns.forEach

            // ── Check Out click ────────────────────────────────────────────
            btnOuts.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    btnOuts.forEach(function(b) {
                        b.disabled = true;
                        b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting GPS...';
                    });

                    async function doCheckOut(latitude, longitude) {
                        btnOuts.forEach(function(b) {
                            b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking Out...';
                        });

                        var body = {};
                        if (latitude !== undefined && longitude !== undefined) {
                            body.check_out_latitude  = latitude;
                            body.check_out_longitude = longitude;
                            
                            try {
                                btnOuts.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting Address...'; });
                                let geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                                let geoData = await geoRes.json();
                                if (geoData && geoData.display_name) {
                                    body.check_out_location_name = geoData.display_name;
                                }
                            } catch (e) {
                                console.warn('[CheckOut] Geocoding failed:', e);
                            }
                            
                            btnOuts.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking Out...'; });
                        }

                        fetch("{{ route('staff.checkout', [], false) }}", {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        })
                        .then(async function(r) {
                            var text = await r.text();
                            try {
                                var data = JSON.parse(text);
                                return { ok: r.ok, status: r.status, data: data };
                            } catch (e) {
                                console.error('[CheckOut] Server returned non-JSON:', text);
                                throw new Error('Server returned an invalid response (HTTP ' + r.status + ')');
                            }
                        })
                        .then(function(res) {
                            var data = res.data;
                            if (res.ok || data.success) {
                                Swal.fire({ toast:true, position:'top', icon:'success', title: data.message || 'Checked Out successfully.', showConfirmButton:false, timer:2000, timerProgressBar:true })
                                    .then(function() { window.location.reload(); });
                                showCheckIn();
                            } else {
                                Swal.fire({ toast:true, position:'top', icon:'error', title: data.error || 'Check Out failed.', showConfirmButton:false, timer:5000 });
                            }
                        })
                        .catch(function(err) {
                            console.error('[CheckOut] Request failed:', err);
                            Swal.fire({ toast:true, position:'top', icon:'error', title: err.message || 'Check Out failed. Please try again.', showConfirmButton:false, timer:5000 });
                        })
                        .finally(function() {
                            btnOuts.forEach(function(b) { 
                                b.disabled = false; 
                                b.innerHTML = '<i class="fa fa-sign-out-alt"></i>&nbsp;Check Out';
                            });
                        });
                    }

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                var lat = position.coords.latitude;
                                var lon = position.coords.longitude;
                                doCheckOut(lat, lon);
                            },
                            function(error) {
                                console.warn('[CheckOut] GPS error:', error.message);
                                if (error.code === 1) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Location Access Required',
                                        html: 'Please allow location access in your browser and try again.',
                                        confirmButtonText: 'OK'
                                    });
                                    btnOuts.forEach(function(b) { 
                                        b.disabled = false; 
                                        b.innerHTML = '<i class="fa fa-sign-out-alt"></i>&nbsp;Check Out';
                                    });
                                } else {
                                    doCheckOut();
                                }
                            },
                            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                        );
                    } else {
                        doCheckOut();
                    }
                });
            });

        })();
    </script>
