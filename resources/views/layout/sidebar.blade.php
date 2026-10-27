<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <!-- Search bar at the top of the sidebar -->
        <div class="sidebar-search-container">
            <i class="fa fa-search"></i>
            <input type="text" id="sidebar-search-input" placeholder="Search...">
        </div>

        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @php
                    $subAdminId = session('selectedSubAdminId');
                    $user = Auth::user();
                    $role = $user ? $user->role : '';
                    $isStaff = Auth::check() && strtolower($role) === 'staff';

                    // Dashboard
                    $showDashboard = tenant_has_module('Dashboard') && (
                        in_array($role, ['admin', 'sub-admin']) || $isStaff
                    );

                    // ERP Submodules
                    $canAllItems = tenant_has_sub_module('All Items', 'All Masters') && app('hasPermission')(1, 'view');
                    $canAllCategories = tenant_has_sub_module('All Categories', 'All Masters') && app('hasPermission')(18, 'view');
                    $canAllBrands = tenant_has_sub_module('All Brands', 'All Masters') && app('hasPermission')(19, 'view');
                    $canAllLabourServices = tenant_has_sub_module('All Labour Services', 'All Masters') && app('hasPermission')(20, 'view');
                    $canAllServiceCategory = tenant_has_sub_module('All Service Category', 'All Masters') && app('hasPermission')(21, 'view');
                    $canMakeModel = tenant_has_sub_module('Make Model', 'All Masters') && app('hasPermission')(22, 'view');
                    $canAllUnits = tenant_has_sub_module('All Units', 'All Masters') && app('hasPermission')(22, 'view');
                    $canImportItems = tenant_has_sub_module('Import Items', 'All Masters') && app('hasPermission')(22, 'view');

                    $canAllMasters = tenant_has_module(['All Masters', 'ERP']) && (
                        $canAllItems || $canAllCategories || $canAllBrands || $canAllLabourServices ||
                        $canAllServiceCategory || $canMakeModel || $canAllUnits || $canImportItems
                    );

                    $canAllJobCards = tenant_has_sub_module('All Job Cards', 'Job Card') && app('hasPermission')(16, 'view');
                    $canNewJobCard = tenant_has_sub_module('New Job Card', 'Job Card') && app('hasPermission')(16, 'add');
                    $canJobCard = tenant_has_module(['Job Card', 'ERP']) && ($canAllJobCards || $canNewJobCard);

                    $canAllDailyRegister = tenant_has_sub_module('All Daily Register', 'Daily Register') && app('hasPermission')(24, 'view');
                    $canNewDailyRegister = tenant_has_sub_module('New Daily Register', 'Daily Register') && app('hasPermission')(24, 'add');
                    $canDailyRegister = tenant_has_module(['Daily Register', 'ERP']) && ($canAllDailyRegister || $canNewDailyRegister);

                    $canAllPurchases = tenant_has_sub_module('All Purchases', 'Purchase') && app('hasPermission')(3, 'view');
                    $canNewPurchase = tenant_has_sub_module('New Purchase', 'Purchase') && app('hasPermission')(3, 'add');
                    $canPurchase = tenant_has_module(['Purchase', 'ERP']) && ($canAllPurchases || $canNewPurchase);

                    $canAllVendors = tenant_has_sub_module('All Vendors', 'Vendor') && app('hasPermission')(5, 'view');
                    $canNewVendor = tenant_has_sub_module('New Vendor', 'Vendor') && app('hasPermission')(5, 'add');
                    $canVendor = tenant_has_module(['Vendor', 'ERP']) && ($canAllVendors || $canNewVendor);

                    $canInventory = tenant_has_module(['Inventory', 'ERP']) && tenant_has_sub_module('Inventory', 'Inventory') && app('hasPermission')(15, 'view');

                    $showErpSection = tenant_has_module(['ERP', 'All Masters', 'Job Card', 'Daily Register', 'Purchase', 'Vendor', 'Inventory']) && (
                        $canAllMasters || $canJobCard || $canDailyRegister || $canPurchase || $canVendor || $canInventory
                    );

                    // CRM Submodules
                    $canAllAppointment = tenant_has_sub_module('All Appointment', 'Appointment') && app('hasPermission')(12, 'view');
                    $canNewAppointment = tenant_has_sub_module('New Appointment', 'Appointment') && app('hasPermission')(12, 'add');
                    $canAppointment = tenant_has_module(['Appointment', 'CRM']) && ($canAllAppointment || $canNewAppointment);

                    $canAllCustomer = tenant_has_sub_module('All Customers', 'Customer') && app('hasPermission')(4, 'view');
                    $canNewCustomer = tenant_has_sub_module('New Customer', 'Customer') && app('hasPermission')(4, 'add');
                    $canCustomer = tenant_has_module(['Customer', 'CRM']) && ($canAllCustomer || $canNewCustomer);

                    $showCrmSection = tenant_has_module(['CRM', 'Appointment', 'Customer']) && ($canAppointment || $canCustomer);

                    // Accounting Submodules
                    $canAccountLedger = tenant_has_module(['Account Ledger', 'Accounting']) && tenant_has_sub_module('Account Ledger', 'Account Ledger') && app('hasPermission')(13, 'view');

                    $canCreditNotes = tenant_has_sub_module('Credit Notes', 'Credit/Debit Notes') && app('hasPermission')(25, 'list');
                    $canDebitNotes = tenant_has_sub_module('Debit Notes', 'Credit/Debit Notes') && app('hasPermission')(26, 'list');
                    $canCreditDebitNotesType = tenant_has_sub_module('Credit/Debit Notes Type', 'Credit/Debit Notes') && app('hasPermission')(25, 'list');
                    $canCreditDebitNotes = tenant_has_module(['Credit/Debit Notes', 'Accounting']) && app('hasPermission')(25, 'view') && (
                        $canCreditNotes || $canDebitNotes || $canCreditDebitNotesType
                    );

                    $canGstReport = tenant_has_sub_module('GST Report', 'GST Reports') && app('hasPermission')(30, 'view');
                    $canGstReports = tenant_has_module(['GST Reports', 'Accounting']) && app('hasPermission')(30, 'view') && $canGstReport;

                    $canAllExpense = tenant_has_sub_module('All Expenses', 'Expense') && app('hasPermission')(6, 'view');
                    $canNewExpense = tenant_has_sub_module('New Expense', 'Expense') && app('hasPermission')(6, 'add');
                    $canAllExpenseType = tenant_has_sub_module('All Expense Type', 'Expense') && app('hasPermission')(6, 'view');
                    $canExpense = tenant_has_module(['Expense', 'Accounting']) && app('hasPermission')(6, 'view') && (
                        $canAllExpense || $canNewExpense || $canAllExpenseType
                    );

                    $showAccountingSection = tenant_has_module(['Accounting', 'Account Ledger', 'Credit/Debit Notes', 'GST Reports', 'Expense']) && (
                        $canAccountLedger || $canCreditDebitNotes || $canGstReports || $canExpense
                    );

                    // HR Submodules
                    $canViewStaff = app('hasPermission')(8, 'view');
                    $canAddStaff = app('hasPermission')(8, 'add');
                    $canAllStaff = tenant_has_sub_module('All Staff', 'Staff') && $canViewStaff;
                    $canNewStaff = tenant_has_sub_module('New Staff', 'Staff') && $canAddStaff;
                    $canStaff = tenant_has_module(['Staff', 'HR']) && ($canAllStaff || $canNewStaff);

                    $canManageAttendance = tenant_has_sub_module('Manage Attendance', 'Attendance') && (app('hasPermission')(9, 'add') || app('hasPermission')(9, 'view') || $isStaff);
                    $canAttendanceSummary = tenant_has_sub_module('Attendance', 'Attendance') && (app('hasPermission')(9, 'add') || app('hasPermission')(9, 'view') || $isStaff);
                    $canAttendance = tenant_has_module(['Attendance', 'HR']) && (app('hasPermission')(9, 'view') || app('hasPermission')(9, 'add') || $isStaff) && (
                        $canManageAttendance || $canAttendanceSummary
                    );

                    $canPayrollView = tenant_has_sub_module('Payroll View', 'Payroll') && (app('hasPermission')(10, 'view') || $isStaff);
                    $canAddPayroll = tenant_has_sub_module('Add Payroll', 'Payroll') && app('hasPermission')(10, 'add');
                    $canGroupSalary = tenant_has_sub_module('Group Salary', 'Payroll') && app('hasPermission')(10, 'view');
                    $canPayroll = tenant_has_module(['Payroll', 'HR']) && (app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add') || $isStaff) && (
                        $canPayrollView || $canAddPayroll || $canGroupSalary
                    );

                    $canAllAdvancePay = tenant_has_sub_module('All Advance Pay', 'Advance Pay') && (app('hasPermission')(23, 'view') || $isStaff);
                    $canNewAdvancePay = tenant_has_sub_module('New Advance Pay', 'Advance Pay') && app('hasPermission')(23, 'add');
                    $canAdvancePay = tenant_has_module(['Advance Pay', 'HR']) && (app('hasPermission')(23, 'view') || $isStaff) && (
                        $canAllAdvancePay || $canNewAdvancePay
                    );

                    $canAllLeave = tenant_has_sub_module('All Leave', 'Leave') && app('hasPermission')(11, 'view');
                    $canLeaveRequest = tenant_has_sub_module('Leave Request', 'Leave') && (app('hasPermission')(11, 'view') || $isStaff);
                    $canAddLeave = tenant_has_sub_module('Add Leave', 'Leave') && (app('hasPermission')(11, 'add') || $isStaff);
                    $canLeave = tenant_has_module(['Leave', 'HR']) && (app('hasPermission')(11, 'view') || app('hasPermission')(11, 'add') || $isStaff) && (
                        $canAllLeave || $canLeaveRequest || $canAddLeave
                    );

                    $showHrSection = tenant_has_module(['HR', 'Staff', 'Attendance', 'Payroll', 'Advance Pay', 'Leave']) && (
                        $canStaff || $canAttendance || $canPayroll || $canAdvancePay || $canLeave
                    );

                    // Settings Submodules
                    $canMyPlanDetails = in_array($role, ['admin', 'sub-admin']) && tenant_has_module(['My Plan Details', 'Settings']) && tenant_has_sub_module('My Plan Details', 'My Plan Details');
                    $canShopSettings = app('hasPermission')(14, 'view') && tenant_has_module(['Shop Settings', 'Settings']) && tenant_has_sub_module('Shop Settings', 'Shop Settings');
                    $canTaxRates = tenant_has_module(['Tax Rates', 'Settings']) && tenant_has_sub_module('Tax Rates', 'Tax Rates');
                    $canDepartments = tenant_has_module(['Departments', 'Settings']) && tenant_has_sub_module('Departments', 'Departments');
                    $canDesignations = tenant_has_module(['Designations', 'Settings']) && tenant_has_sub_module('Designations', 'Designations');

                    $showSettingsSection = tenant_has_module(['Settings', 'My Plan Details', 'Shop Settings', 'Tax Rates', 'Departments', 'Designations']) && (
                        $canMyPlanDetails || $canShopSettings || $canTaxRates || $canDepartments || $canDesignations
                    );
                @endphp

                <!-- Dashboard -->
                @if ($showDashboard)
                    <li class="menu-dashboard">
                        @if (in_array($role, ['admin', 'sub-admin']))
                            <a href="{{ route('auth.dashboard') }}">
                                <i class="fa fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('auth.staff-dashboard') }}">
                                <i class="fa fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        @endif
                    </li>
                @endif

                <!-- ERP -->
                @if ($showErpSection)
                    <li class="submenu menu-erp">
                        <a href="javascript:void(0);">
                            <i class="fa fa-database"></i>
                            <span>ERP</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- All Masters Submenu -->
                            @if ($canAllMasters)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-folder-open"></i>
                                        <span>All Masters</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllItems)
                                            <li><a href="{{ route('product.list') }}"><i class="fa fa-box"></i><span>All
                                                        Items</span></a></li>
                                        @endif
                                        @if ($canAllCategories)
                                            <li><a href="{{ route('category.list') }}"><i
                                                        class="fa fa-tags"></i><span>All Categories</span></a></li>
                                        @endif
                                        @if ($canAllBrands)
                                            <li><a href="{{ route('brand.list') }}"><i
                                                        class="fa fa-copyright"></i><span>All Brands</span></a></li>
                                        @endif
                                        @if ($canAllLabourServices)
                                            <li><a href="{{ route('labour.list') }}"><i
                                                        class="fa fa-wrench"></i><span>All Labour Services</span></a>
                                            </li>
                                        @endif
                                        @if ($canAllServiceCategory)
                                            <li><a href="{{ route('autocar.category.list') }}"><i
                                                        class="fa fa-cogs"></i><span>All Service Category</span></a>
                                            </li>
                                        @endif
                                        @if ($canMakeModel)
                                            <li><a href="{{ route('autocar.model.list') }}"><i
                                                        class="fa fa-car"></i><span>Make Model</span></a></li>
                                        @endif
                                        @if ($canAllUnits)
                                            <li><a href="{{ route('unit.list') }}"><i
                                                        class="fa fa-balance-scale"></i><span>All Units</span></a></li>
                                        @endif
                                        @if ($canImportItems)
                                            <li><a href="{{ route('product.import') }}"><i
                                                        class="fa fa-file-import"></i><span>Import Items</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Job Card -->
                            @if ($canJobCard)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-file-invoice"></i>
                                        <span>Job Card</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllJobCards)
                                            <li><a href="{{ route('job_card.index') }}"><i
                                                        class="fa fa-list"></i><span>All Job Cards</span></a></li>
                                        @endif
                                        @if ($canNewJobCard)
                                            <li><a href="{{ route('job_card.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Job Card</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Daily Register -->
                            @if ($canDailyRegister)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-book-open"></i>
                                        <span>Daily Register</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllDailyRegister)
                                            <li><a href="{{ route('daily_data.index') }}"><i
                                                        class="fa fa-list"></i><span>All Daily Register</span></a></li>
                                        @endif
                                        @if ($canNewDailyRegister)
                                            <li><a href="{{ route('daily_data.add-daily-data') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Daily
                                                        Register</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Purchases -->
                            @if ($canPurchase)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span>Purchase</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllPurchases)
                                            <li><a href="{{ route('purchase.lists') }}"><i
                                                        class="fa fa-list"></i><span>All Purchases</span></a></li>
                                        @endif
                                        @if ($canNewPurchase)
                                            <li><a href="{{ route('purchase.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Purchase</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Vendor -->
                            @if ($canVendor)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-truck"></i>
                                        <span>Vendor</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllVendors)
                                            <li><a href="{{ route('vendor.list') }}"><i
                                                        class="fa fa-list"></i><span>All Vendors</span></a></li>
                                        @endif
                                        @if ($canNewVendor)
                                            <li><a href="{{ route('vendor.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Vendor</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Inventory -->
                            @if ($canInventory)
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
                @if ($showCrmSection)
                    <li class="submenu menu-crm">
                        <a href="javascript:void(0);">
                            <i class="fa fa-users"></i>
                            <span>CRM</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Appointment -->
                            @if ($canAppointment)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar-check"></i>
                                        <span>Appointment</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllAppointment)
                                            <li><a href="{{ route('appointments.index') }}"><i
                                                        class="fa fa-list"></i><span>All Appointment</span></a></li>
                                        @endif
                                        @if ($canNewAppointment)
                                            <li><a href="{{ route('appointments.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Appointment</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Customer -->
                            @if ($canCustomer)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-user"></i>
                                        <span>Customer</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllCustomer)
                                            <li><a href="{{ route('customer.list') }}"><i
                                                        class="fa fa-list"></i><span>All Customers</span></a></li>
                                        @endif
                                        @if ($canNewCustomer)
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
                @if ($showAccountingSection)
                    <li class="submenu menu-accounting">
                        <a href="javascript:void(0);">
                            <i class="fa fa-calculator"></i>
                            <span>Accounting</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Account Ledger -->
                            @if ($canAccountLedger)
                                <li>
                                    <a href="{{ route('account_ledger.add') }}">
                                        <i class="fa fa-book"></i>
                                        <span>Account Ledger</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Credit/Debit Notes -->
                            @if ($canCreditDebitNotes)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-credit-card"></i>
                                        <span>Credit/Debit Notes</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canCreditNotes)
                                            <li><a href="{{ route('credit-notes-items.index') }}"><i
                                                        class="fa fa-arrow-down"></i><span>Credit Notes</span></a></li>
                                        @endif
                                        @if ($canDebitNotes)
                                            <li><a href="{{ route('debit-notes-items.index') }}"><i
                                                        class="fa fa-arrow-up"></i><span>Debit Notes</span></a></li>
                                        @endif
                                        @if ($canCreditDebitNotesType)
                                            <li><a href="{{ route('credit-notes.index') }}"><i
                                                        class="fa fa-cog"></i><span>Credit/Debit Notes Type</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- GST Reports -->
                            @if ($canGstReports)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-chart-line"></i>
                                        <span>GST Reports</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canGstReport)
                                            <li><a href="{{ route('gst.sales_list') }}"><i
                                                        class="fa fa-file-excel"></i><span>GST Report</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Expense -->
                            @if ($canExpense)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-receipt"></i>
                                        <span>Expense</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllExpense)
                                            <li><a href="{{ route('expense.list') }}"><i
                                                        class="fa fa-list"></i><span>All Expenses</span></a></li>
                                        @endif
                                        @if ($canNewExpense)
                                            <li><a href="{{ route('expense.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Expense</span></a></li>
                                        @endif
                                        @if ($canAllExpenseType)
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
                @if ($showHrSection)
                    <li class="submenu menu-hr">
                        <a href="javascript:void(0);">
                            <i class="fa fa-id-card"></i>
                            <span>HR</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Staff -->
                            @if ($canStaff)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-users"></i>
                                        <span>Staff</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllStaff)
                                            <li><a href="{{ route('staff.list') }}"><i
                                                        class="fa fa-list"></i><span>All Staff</span></a></li>
                                        @endif
                                        @if ($canNewStaff)
                                            <li><a href="{{ route('staff.add') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Staff</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Attendance -->
                            @if ($canAttendance)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-clock"></i>
                                        <span>Attendance</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canManageAttendance)
                                            <li><a href="{{ route('attendence.calendar') }}"><i
                                                        class="fa fa-calendar-alt"></i><span>Manage
                                                        Attendance</span></a></li>
                                        @endif
                                        @if ($canAttendanceSummary)
                                            <li><a href="{{ route('attendence.summary') }}"><i
                                                        class="fa fa-list"></i><span>Attendance</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Payroll -->
                            @if ($canPayroll)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-check-alt"></i>
                                        <span>Payroll</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canPayrollView)
                                            <li><a href="{{ route('payroll.list') }}"><i
                                                        class="fa fa-eye"></i><span>Payroll View</span></a></li>
                                        @endif
                                        @if ($canAddPayroll)
                                            <li><a href="{{ route('payroll.list') }}"><i
                                                        class="fa fa-plus-circle"></i><span>Add Payroll</span></a></li>
                                        @endif
                                        @if ($canGroupSalary)
                                            <li><a href="{{ route('payroll.salary-details') }}"><i
                                                        class="fa fa-users-cog"></i><span>Group Salary</span></a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Advance Pay -->
                            @if ($canAdvancePay)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-money-bill-wave"></i>
                                        <span>Advance Pay</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllAdvancePay)
                                            <li><a href="{{ route('advance_pay.index') }}"><i
                                                        class="fa fa-list"></i><span>All Advance Pay</span></a></li>
                                        @endif
                                        @if ($canNewAdvancePay)
                                            <li><a href="{{ route('advance_pay.create') }}"><i
                                                        class="fa fa-plus-circle"></i><span>New Advance Pay</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <!-- Leave -->
                            @if ($canLeave)
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar-times"></i>
                                        <span>Leave</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        @if ($canAllLeave)
                                            <li><a href="{{ route('leave.view') }}"><i
                                                        class="fa fa-list"></i><span>All Leave</span></a></li>
                                        @endif
                                        @if ($canLeaveRequest)
                                            <li><a href="{{ route('leave.request') }}"><i
                                                        class="fa fa-paper-plane"></i><span>Leave Request</span></a>
                                            </li>
                                        @endif
                                        @if ($canAddLeave)
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
                @if ($showSettingsSection)
                    <li class="submenu menu-settings">
                        <a href="javascript:void(0);">
                            <i class="fa fa-cog"></i>
                            <span>Settings</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if ($canMyPlanDetails)
                                <li><a href="{{ route('plans.myplan') }}"><i class="fa fa-list-alt"></i> My Plan Details</a></li>
                            @endif
                            @if ($canShopSettings)
                                <li><a href="{{ route('setting.generalsettings') }}"><i
                                            class="fa fa-store"></i><span>Shop Settings</span></a></li>
                            @endif
                            @if ($canTaxRates)
                                <li><a href="{{ route('auth.taxrates') }}"><i class="fa fa-percent"></i><span>Tax
                                            Rates</span></a></li>
                            @endif
                            @if ($canDepartments)
                                <li><a href="{{ route('department.view') }}"><i
                                            class="fa fa-building"></i><span>Departments</span></a></li>
                            @endif
                            @if ($canDesignations)
                                <li><a href="{{ route('designation.view') }}"><i
                                            class="fa fa-id-badge"></i><span>Designations</span></a></li>
                            @endif
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
