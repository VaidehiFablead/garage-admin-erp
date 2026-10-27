<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <!-- Search bar at the top of the sidebar -->
        <div class="sidebar-search-container">
            <i class="fa fa-search"></i>
            <input type="text" id="sidebar-search-input" placeholder="Search...">
        </div>

        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Dashboard -->
                @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                    <li class="menu-dashboard">
                        <a href="{{ route('auth.dashboard') }}">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @elseif (Auth::check() && strtolower(Auth::user()->role) === 'staff')
                    <li class="menu-dashboard">
                        <a href="{{ route('auth.staff-dashboard') }}">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endif

                @php
                    $subAdminId = session('selectedSubAdminId');
                    $role = Auth::user()->role;
                    $canViewStaff = app('hasPermission')(8, 'view');
                    $canAddStaff = app('hasPermission')(8, 'add');
                @endphp

                <!-- ERP -->
                @if (app('hasPermission')(18, 'view') ||
                        app('hasPermission')(1, 'view') ||
                        app('hasPermission')(19, 'view') ||
                        app('hasPermission')(20, 'view') ||
                        app('hasPermission')(21, 'view') ||
                        app('hasPermission')(22, 'view') ||
                        app('hasPermission')(16, 'view') ||
                        app('hasPermission')(24, 'view') ||
                        app('hasPermission')(3, 'view') ||
                        app('hasPermission')(15, 'view') ||
                        app('hasPermission')(13, 'view') ||
                        app('hasPermission')(5, 'view'))
                    <li class="submenu menu-erp">
                        <a href="javascript:void(0);">
                            <i class="fa fa-database"></i>
                            <span>ERP</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- All Masters Submenu -->
                            @if (app('hasPermission')(18, 'view') ||
                                    app('hasPermission')(1, 'view') ||
                                    app('hasPermission')(19, 'view') ||
                                    app('hasPermission')(20, 'view') ||
                                    app('hasPermission')(21, 'view') ||
                                    app('hasPermission')(22, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-folder-open"></i>
                                        <span>All Masters</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(1, 'view'))
                                            <li><a href="{{ route('product.list') }}"><i class="fa fa-box"></i><span>All
                                                        Items</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(18, 'view'))
                                            <li><a href="{{ route('category.list') }}"><i
                                                        class="fa fa-tags"></i><span>All Categories</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(19, 'view'))
                                            <li><a href="{{ route('brand.list') }}"><i
                                                        class="fa fa-copyright"></i><span>All Brands</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(20, 'view'))
                                            <li><a href="{{ route('labour.list') }}"><i
                                                        class="fa fa-wrench"></i><span>All Labour Services</span></a>
                                            </li>
                                        @endif
                                        @if (app('hasPermission')(21, 'view'))
                                            <li><a href="{{ route('autocar.category.list') }}"><i
                                                        class="fa fa-cogs"></i><span>All Service Category</span></a>
                                            </li>
                                        @endif
                                        @if (app('hasPermission')(22, 'view'))
                                            <li><a href="{{ route('autocar.model.list') }}"><i
                                                        class="fa fa-car"></i><span>Make Model</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(22, 'view'))
                                            <li><a href="{{ route('unit.list') }}"><i
                                                        class="fa fa-balance-scale"></i><span>All Units</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(22, 'view'))
                                            <li><a href="{{ route('product.import') }}"><i
                                                        class="fa fa-file-import"></i><span>Import Items</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Job Card -->
                            @if (app('hasPermission')(16, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-file-invoice"></i>
                                        <span>Job Card</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(16, 'view'))
                                            <li><a href="{{ route('job_card.index') }}"><i
                                                        class="fa fa-list"></i><span>All Job Cards</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(16, 'add'))
                                            <li><a href="{{ route('job_card.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Job Card</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Daily Register -->
                            @if (app('hasPermission')(24, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-book-open"></i>
                                        <span>Daily Register</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(24, 'view'))
                                            <li><a href="{{ route('daily_data.index') }}"><i
                                                        class="fa fa-list"></i><span>All Daily Register</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(24, 'add'))
                                            <li><a href="{{ route('daily_data.add-daily-data') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Daily
                                                        Register</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Purchases -->
                            @if (app('hasPermission')(3, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Purchase</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(3, 'view'))
                                            <li><a href="{{ route('purchase.lists') }}"><i
                                                        class="fa fa-list"></i><span>All Purchases</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(3, 'add'))
                                            <li><a href="{{ route('purchase.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Purchase</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
 <!-- Vendor -->
                            @if (app('hasPermission')(5, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-truck"></i>
                                        <span>Vendor</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(5, 'view'))
                                            <li><a href="{{ route('vendor.list') }}"><i
                                                        class="fa fa-list"></i><span>All Vendors</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(5, 'add'))
                                            <li><a href="{{ route('vendor.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Vendor</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            <!-- Inventory -->
                            @if (app('hasPermission')(15, 'view'))
                                <li>
                                    <a href="{{ route('inventory.list') }}">
                                        <i class="fa fa-warehouse"></i>
                                        <span>Inventory</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif

                <!-- CRM -->
                @if (app('hasPermission')(12, 'view') || app('hasPermission')(4, 'view') || app('hasPermission')(5, 'view'))
                    <li class="submenu menu-crm">
                        <a href="javascript:void(0);">
                            <i class="fa fa-users"></i>
                            <span>CRM</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Appointment -->
                            @if (app('hasPermission')(12, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar-check"></i>
                                        <span>Appointment</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(12, 'view'))
                                            <li><a href="{{ route('appointments.index') }}"><i
                                                        class="fa fa-list"></i><span>All Appointment</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(12, 'add'))
                                            <li><a href="{{ route('appointments.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Appointment</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Customer -->
                            @if (app('hasPermission')(4, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-user"></i>
                                        <span>Customer</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(4, 'view'))
                                            <li><a href="{{ route('customer.list') }}"><i
                                                        class="fa fa-list"></i><span>All Customers</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(4, 'add'))
                                            <li><a href="{{ route('customer.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Customer</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                           
                        </ul>
                    </li>
                @endif

                <!-- Accounting -->
                @if (app('hasPermission')(23, 'view') ||
                        (Auth::check() && strtolower(Auth::user()->role) === 'staff') ||
                        app('hasPermission')(25, 'view') ||
                        app('hasPermission')(30, 'view') ||
                        app('hasPermission')(6, 'view'))
                    <li class="submenu menu-accounting">
                        <a href="javascript:void(0);">
                            <i class="fa fa-calculator"></i>
                            <span>Accounting</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Advance Pay -->


                            <!-- Credit/Debit Notes -->
                            @if (app('hasPermission')(25, 'view'))


                                <!-- Account Ledger -->
                                @if (app('hasPermission')(13, 'view'))
                                    <li>
                                        <a href="{{ route('account_ledger.add') }}">
                                            <i class="fa fa-book"></i>
                                            <span>Account Ledger</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-credit-card"></i>
                                        <span>Credit/Debit Notes</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(25, 'list'))
                                            <li><a href="{{ route('credit-notes-items.index') }}"><i
                                                        class="fa fa-arrow-down"></i><span>Credit Notes</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(26, 'list'))
                                            <li><a href="{{ route('debit-notes-items.index') }}"><i
                                                        class="fa fa-arrow-up"></i><span>Debit Notes</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(25, 'list'))
                                            <li><a href="{{ route('credit-notes.index') }}"><i
                                                        class="fa fa-cog"></i><span>Credit/Debit Notes Type</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- GST Reports -->
                            @if (app('hasPermission')(30, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-chart-line"></i>
                                        <span>GST Reports</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(30, 'view'))
                                            <li><a href="{{ route('gst.sales_list') }}"><i
                                                        class="fa fa-file-excel"></i><span>GST Report</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Expense -->
                            @if (app('hasPermission')(6, 'view'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-receipt"></i>
                                        <span>Expense</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(6, 'view'))
                                            <li><a href="{{ route('expense.list') }}"><i
                                                        class="fa fa-list"></i><span>All Expenses</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(6, 'add'))
                                            <li><a href="{{ route('expense.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Expense</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(6, 'view'))
                                            <li><a href="{{ route('expensetype.list') }}"><i
                                                        class="fa fa-cog"></i><span>All Expense Type</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!-- HR -->
                @if (
                    $canViewStaff ||
                        $canAddStaff ||
                        app('hasPermission')(9, 'view') ||
                        app('hasPermission')(9, 'add') ||
                        (Auth::check() && strtolower(Auth::user()->role) === 'staff') ||
                        app('hasPermission')(10, 'view') ||
                        app('hasPermission')(10, 'add') ||
                        app('hasPermission')(11, 'view') ||
                        app('hasPermission')(11, 'add'))
                    <li class="submenu menu-hr">
                        <a href="javascript:void(0);">
                            <i class="fa fa-id-card"></i>
                            <span>HR</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Staff -->
                            @if ($canViewStaff || $canAddStaff)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-users"></i>
                                        <span>Staff</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canViewStaff)
                                            <li><a href="{{ route('staff.list') }}"><i
                                                        class="fa fa-list"></i><span>All Staff</span></a></li>
                                        @endif
                                        @if ($canAddStaff)
                                            <li><a href="{{ route('staff.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Staff</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Attendance -->
                            @if (app('hasPermission')(9, 'view') ||
                                    app('hasPermission')(9, 'add') ||
                                    (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-clock"></i>
                                        <span>Attendance</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(9, 'add') ||
                                                app('hasPermission')(9, 'view') ||
                                                (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                            <li><a href="{{ route('attendence.calendar') }}"><i
                                                        class="fa fa-calendar-alt"></i><span>Manage
                                                        Attendance</span></a></li>
                                            <li><a href="{{ route('attendence.summary') }}"><i
                                                        class="fa fa-list"></i><span>Attendance</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Payroll -->
                            @if (app('hasPermission')(10, 'view') ||
                                    app('hasPermission')(10, 'add') ||
                                    (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-check-alt"></i>
                                        <span>Payroll</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(10, 'view') || (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                            <li><a href="{{ route('payroll.list') }}"><i
                                                        class="fa fa-eye"></i><span>Payroll View</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(10, 'add'))
                                            <li><a href="{{ route('payroll.list') }}"><i
                                                        class="fa fa-plus-circle"></i><span>Add Payroll</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(10, 'view'))
                                            <li><a href="{{ route('payroll.salary-details') }}"><i
                                                        class="fa fa-users-cog"></i><span>Group Salary</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            <!-- advance pay -->
                            @if (app('hasPermission')(23, 'view') || (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-bill-wave"></i>
                                        <span>Advance Pay</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(23, 'view') || (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                            <li><a href="{{ route('advance_pay.index') }}"><i
                                                        class="fa fa-list"></i><span>All Advance Pay</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(23, 'add'))
                                            <li><a href="{{ route('advance_pay.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Advance Pay</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            <!-- Leave -->
                            @if (app('hasPermission')(11, 'view') ||
                                    app('hasPermission')(11, 'add') ||
                                    (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar-times"></i>
                                        <span>Leave</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if (app('hasPermission')(11, 'view'))
                                            <li><a href="{{ route('leave.view') }}"><i
                                                        class="fa fa-list"></i><span>All Leave</span></a></li>
                                        @endif
                                        @if (app('hasPermission')(11, 'view') || (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                            <li><a href="{{ route('leave.request') }}"><i
                                                        class="fa fa-paper-plane"></i><span>Leave Request</span></a>
                                            </li>
                                        @endif
                                        @if (app('hasPermission')(11, 'add') || (Auth::check() && strtolower(Auth::user()->role) === 'staff'))
                                            <li><a href="{{ route('leave.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>Add Leave</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!-- Settings -->
                @if (app('hasPermission')(14, 'view'))
                    <li class="submenu menu-settings">
                        <a href="javascript:void(0);">
                            <i class="fa fa-cog"></i>
                            <span>Settings</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            {{-- @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <li><a href="{{ route('plans.planlist') }}"
                                        class="{{ request()->routeIs('plans.planlist') ? 'active' : '' }}">Plans</a>
                                </li>
                                <!-- <li><a href="{{ route('plans.details') }}" class="{{ request()->routeIs('plans.details') ? 'active' : '' }}">Plan Details</a></li> -->
                            @endif --}}
                            @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <li><a href="{{ route('plans.myplan') }}"><i class="fa fa-list-alt"></i> My Plan Details</a></li>
                                @endif @if (app('hasPermission')(14, 'view'))
                                    <li><a href="{{ route('setting.generalsettings') }}"><i
                                                class="fa fa-store"></i><span>Shop Settings</span></a></li>
                                @endif
                                <li><a href="{{ route('auth.taxrates') }}"><i class="fa fa-percent"></i><span>Tax
                                            Rates</span></a></li>
                                <li><a href="{{ route('department.view') }}"><i
                                            class="fa fa-building"></i><span>Departments</span></a></li>
                                <li><a href="{{ route('designation.view') }}"><i
                                            class="fa fa-id-badge"></i><span>Designations</span></a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<style>
    /* Fonts Override */
    .sidebar {
        font-family: 'Outfit', sans-serif !important;
    }

    /* Live Search Styling */
    .sidebar-search-container {
        padding: 15px 15px 0px 15px;
        position: relative;
    }

    .sidebar-search-container input {
        width: 100%;
        padding: 10px 10px 10px 42px;
        border: 1px solid #dcdde1;
        border-radius: 6px;
        font-size: 13px;
        outline: none;
        transition: all 0.3s ease;
        background-color: #fcfcfc;
        color: #2f3640;
    }

    .sidebar-search-container input:focus {
        border-color: #1b2850;
        background-color: #fff;
        box-shadow: 0 0 4px rgba(27, 40, 80, 0.15);
    }

    .sidebar-search-container i {
        position: absolute;
        left: 31px;
        top: 65%;
        transform: translateY(-50%);
        color: #a4b0be;
        font-size: 13px;
        pointer-events: none;
    }

    /* Reset default styling */
    .sidebar .sidebar-menu > ul > li {
        margin-bottom: 6px !important;
    }

    .sidebar .sidebar-menu>ul>li>a {
        display: flex !important;
        align-items: center;
        padding: 10px 15px !important;
        border: 1px solid transparent !important;
        border-left: 3px solid var(--accent-color) !important;
        border-radius: 8px !important;
        background-color: var(--bg-color) !important;
        transition: all 0.2s ease-in-out;
        position: relative;
        font-weight: 600 !important;
    }

    /* Individual Color Themes */
    .menu-dashboard {
        --accent-color: #ea5455;
        --bg-color: #f9dede99;
        --icon-color: #ea5455;
    }

    .menu-erp {
        --accent-color: #4b6584;
        --bg-color: #f1f2f6;
        --icon-color: #4b6584;
    }

    .menu-crm {
        --accent-color: #9b59b6;
        --bg-color: #f5f0fa;
        --icon-color: #9b59b6;
    }

    .menu-accounting {
        --accent-color: #2ecc71;
        --bg-color: #ebfbf2;
        --icon-color: #2ecc71;
    }

    .menu-hr {
        --accent-color: #e67e22;
        --bg-color: #fef5ec;
        --icon-color: #e67e22;
    }

    .menu-settings {
        --accent-color: #3498db;
        --bg-color: #edf7fc;
        --icon-color: #3498db;
    }

    /* Icon & Text Styling */
    .sidebar .sidebar-menu>ul>li>a i {
        color: var(--icon-color) !important;
        font-size: 16px !important;
        width: 20px;
        text-align: center;
        margin-right: 5px;
        transition: color 0.2s ease;
    }

    .sidebar .sidebar-menu>ul>li>a span {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #2f3640 !important;
        margin-left: 5px !important;
        transition: color 0.2s ease;
    }

    .sidebar .sidebar-menu>ul>li>a .menu-arrow {
        color: #7f8c8d !important;
        font-size: 14px !important;
        right: 15px !important;
        top: 15px !important;
    }

    /* Active State (Solid Dark Navy Blue Background) */
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a.active {
        background-color: #1b2850 !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a.active span {
        color: #ffffff !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a.active i {
        color: #ffffff !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a.active .menu-arrow {
        color: #ffffff !important;
    }

    /* Hover State */
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a:hover {
        background-color: #1b2850 !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a:hover span {
        color: #ffffff !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a:hover i {
        color: #ffffff !important;
    }
    .sidebar .sidebar-menu > ul > li:not(.submenu) > a:hover .menu-arrow {
        color: #ffffff !important;
    }

    /* Force submenu parents to keep original colors on active/hover/subdrop */
    #sidebar-menu > ul > li.submenu.active > a i,
    #sidebar-menu > ul > li.submenu.active > a i.fa,
    #sidebar-menu > ul > li.submenu > a.active i,
    #sidebar-menu > ul > li.submenu > a.active i.fa,
    #sidebar-menu > ul > li.submenu > a.subdrop i,
    #sidebar-menu > ul > li.submenu > a.subdrop i.fa,
    #sidebar-menu > ul > li.submenu > a:hover i,
    #sidebar-menu > ul > li.submenu > a:hover i.fa {
        color: var(--accent-color) !important;
    }
    #sidebar-menu > ul > li.submenu.active > a span,
    #sidebar-menu > ul > li.submenu > a.active span,
    #sidebar-menu > ul > li.submenu > a.subdrop span,
    #sidebar-menu > ul > li.submenu > a:hover span {
        color: #2f3640 !important;
    }
    #sidebar-menu > ul > li.submenu.active > a .menu-arrow,
    #sidebar-menu > ul > li.submenu > a.active .menu-arrow,
    #sidebar-menu > ul > li.submenu > a.subdrop .menu-arrow,
    #sidebar-menu > ul > li.submenu > a:hover .menu-arrow {
        color: #7f8c8d !important;
    }

    /* Submenus (Level 2) */
    .sidebar .sidebar-menu>ul>li.submenu ul {
        background-color: #fafbfe !important;
        border-radius: 8px !important;
        /* border-left: 1px dashed #dcdde1 !important; */
        /* margin: 5px 0 10px 22px !important; */
        padding: 5px 0 5px 10px !important;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li {
        margin-bottom: 2px;
        position: relative;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #57606f !important;
        padding: 8px 10px 8px 10px !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        transition: all 0.2s ease;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a::after {
        display: none !important;
        /* Hide defaults */
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a i {
        font-size: 12px !important;
        width: 16px;
        text-align: center;
        margin-right: 8px;
        color: #7f8c8d;
        transition: color 0.2s ease;
        filter: none !important;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a span {
        margin-left: 0 !important;
        color: #57606f !important;
        font-weight: 500 !important;
        font-size: 15px !important;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a:hover {
        color: #ffffff !important;
        background-color: #1b2850 !important;
        border-left: 3px solid #ea5455 !important;
        border-radius: 6px;
    }

    #sidebar-menu ul li.submenu ul li a:hover i,
    #sidebar-menu ul li.submenu ul li a:hover i.fa {
        color: #ffffff !important;
        filter: none !important;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li a:hover span {
        color: #ffffff !important;
    }
    #sidebar-menu ul li.submenu ul li a.active {
        background-color: #1b2850 !important;
        border-left: 3px solid #ea5455 !important;
        border-radius: 6px;
        color: #ffffff !important;
    }

    #sidebar-menu ul li.submenu ul li a.active i,
    #sidebar-menu ul li.submenu ul li a.active i.fa {
        color: #ffffff !important;
        filter: none !important;
    }
    .sidebar .sidebar-menu > ul > li.submenu ul li a.active span {
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    /* Submenus (Level 3 - Nested dropdown inside Level 2) */
    .sidebar .sidebar-menu>ul>li.submenu ul li.submenu ul {
        margin: 4px 0 4px 10px !important;
        padding: 2px 0 2px 5px !important;
        /* border-left: 1px dashed #dcdde1 !important; */
        background-color: transparent !important;
    }

    .sidebar .sidebar-menu>ul>li.submenu ul li.submenu ul li a {
        font-size: 12px !important;
        padding: 6px 10px 6px 20px !important;
    }
    
    /* Level 3 Active state overriding Level 2 */
    #sidebar-menu ul li.submenu ul li.submenu ul li a.active {
        background-color: transparent !important;
        border-left: none !important;
        color: #1b2850 !important;
        font-weight: 700 !important;
        position: relative;
    }
    #sidebar-menu ul li.submenu ul li.submenu ul li a.active::before {
        content: "";
        position: absolute;
        left: 0px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background-color: #1b2850;
        border-radius: 50%;
    }
    #sidebar-menu ul li.submenu ul li.submenu ul li a.active i,
    #sidebar-menu ul li.submenu ul li.submenu ul li a.active i.fa {
        color: #1b2850 !important;
    }
    #sidebar-menu ul li.submenu ul li.submenu ul li a.active span {
        color: #1b2850 !important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ensure sidebar state is persisted on larger screens
        if (window.innerWidth > 991) {
            if (localStorage.getItem('screenModeNightTokenState') === null) {
                localStorage.setItem('screenModeNightTokenState', 'night');
            }
            const isNight = localStorage.getItem('screenModeNightTokenState') === 'night';
            const headerLeft = document.querySelector('.header-left');
            const toggleBtn = document.getElementById('toggle_btn1');
            
            if (isNight) {
                document.body.classList.remove('mini-sidebar');
                if (headerLeft) headerLeft.classList.add('active');
                if (toggleBtn) toggleBtn.classList.add('active');
            } else {
                document.body.classList.add('mini-sidebar');
                if (headerLeft) headerLeft.classList.remove('active');
                if (toggleBtn) toggleBtn.classList.remove('active');
            }
        }

        // Live Search feature implementation
        const searchInput = document.getElementById('sidebar-search-input');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase().trim();
                const mainMenuItems = document.querySelectorAll('#sidebar-menu > ul > li');

                mainMenuItems.forEach(function(item) {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.setProperty('display', '', 'important');

                        // Expand menus when searching
                        if (query.length > 0) {
                            const childUl = item.querySelector('ul');
                            if (childUl) {
                                childUl.style.setProperty('display', 'block', 'important');
                                const toggleLink = item.querySelector('a');
                                if (toggleLink) toggleLink.classList.add('subdrop');
                            }

                            // Show matching submenus and expand them too if needed
                            const submenus = item.querySelectorAll('li.submenu');
                            submenus.forEach(function(sub) {
                                const subText = sub.textContent.toLowerCase();
                                if (subText.includes(query)) {
                                    sub.style.setProperty('display', '', 'important');
                                    const subUl = sub.querySelector('ul');
                                    if (subUl) {
                                        subUl.style.setProperty('display', 'block',
                                            'important');
                                        const subLink = sub.querySelector('a');
                                        if (subLink) subLink.classList.add('subdrop');
                                    }
                                } else {
                                    sub.style.setProperty('display', 'none',
                                        'important');
                                }
                            });
                        } else {
                            // Collapse back to normal when search is cleared
                            const childUl = item.querySelector('ul');
                            if (childUl && !item.classList.contains('active')) {
                                childUl.style.setProperty('display', 'none', 'important');
                                const toggleLink = item.querySelector('a');
                                if (toggleLink) toggleLink.classList.remove('subdrop');
                            }

                            const submenus = item.querySelectorAll('li.submenu');
                            submenus.forEach(function(sub) {
                                sub.style.setProperty('display', '', 'important');
                                const subUl = sub.querySelector('ul');
                                if (subUl) {
                                    subUl.style.setProperty('display', 'none',
                                        'important');
                                    const subLink = sub.querySelector('a');
                                    if (subLink) subLink.classList.remove('subdrop');
                                }
                            });
                        }
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }
    });
</script>
