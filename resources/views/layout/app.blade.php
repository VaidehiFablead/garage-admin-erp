@php
    $settings = App\Models\Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"> -->

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fablead Inventory-Billing Software">
    <meta name="keywords"
        content="inventory management, billing system, invoice generator, purchase orders, inventory control, POS system, admin dashboard">
    <meta name="author" content="Fablead Developers Technolab">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Default Title')</title>

    <link rel="shortcut icon" type="image/x-icon"
        href="{{ optional($settings)->favicon ? env('ImagePath') . '/storage/' . $settings->favicon : 'https://fableadtechnolabs.com/favicon-192x192.webp' }}">

    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/bootstrap.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/plugins/owlcarousel/owl.carousel.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/plugins/owlcarousel/owl.theme.default.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/animate.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/plugins/select2/css/select2.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/bootstrap-datetimepicker.min.css' }}">

    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/dataTables.bootstrap4.min.css' }}">

    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/plugins/fontawesome/css/fontawesome.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/plugins/fontawesome/css/all.min.css' }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/style.css' }}"> -->
    <!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @php
        $cssPath = public_path('admin/assets/css/style.css');
        $version = file_exists($cssPath) ? filemtime($cssPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ env('ImagePath') . '/admin/assets/css/style.css?v=' . $version }}">

    <style>
        /* Global SweetAlert2 Button Colors */
        .swal2-confirm {
            background-color: #ff9f43 !important;
            border-color: #ff9f43 !important;
        }
        .swal2-confirm:hover {
            background-color: #e58f3c !important;
            border-color: #e58f3c !important;
        }
    </style>
</head>

<body style="min-height: 100vh; display: flex; flex-direction: column;">

    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>

    <div class="main-wrapper" style="flex: 1;">
        @include('layout.header')
        @include('layout.sidebar')

        <!-- Main Content Section -->
        <div class="page-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Footer with copyright -->
    <footer style="text-align: center; padding: 10px 0; background-color: #f4f4f4; height: 50px;">
        <h1 style="font-size: 14px; font-weight: 600;">© <?= date('Y') ?> Copyright - Fablead Developers Technolab</h1>
    </footer>
    @include('partials.todayAppointmentsModal')
    @include('layout.footer')
    <div class="mobile-bottom-nav">
        <a href="{{ route('auth.dashboard') }}"
            class="nav-item {{ request()->routeIs('auth.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('product.list') }}"
            class="nav-item {{ request()->routeIs('product.list') ? 'active' : '' }}">
            <i class="fas fa-cubes"></i>
            <span>Items</span>
        </a>
        <a href="{{ route('job_card.index') }}" class="nav-item {{ request()->routeIs('sales.list') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Job Card</span>
        </a>
        <a href="{{ route('purchase.lists') }}"
            class="nav-item {{ request()->routeIs('purchase.lists') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i>
            <span>Purchase</span>
        </a>
        <a href="{{ route('customer.list') }}"
            class="nav-item {{ request()->routeIs('customer.list') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Customer</span>
        </a>
    </div>
    @stack('js')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to add red asterisks to required fields
            function markRequiredFields() {
                var requiredNames = [
                    'name', 'supplier_name', 'customer_name', 'staff_name', 'email', 'phone', 
                    'password', 'unit_id', 'category_id', 'hsn_code', 'quantity', 'price', 
                    'supplier_id', 'customer_id', 'date', 'SKU', 'unitname', 'vendor_id', 
                    'vendor_name', 'bill_no', 'bill_number', 'purchase_status', 'payment_mode', 
                    'product_id', 'product_name', 'item_name', 'item_price', 'qty', 
                    'status', 'payment_method', 'tax_name', 'tax_rate', 
                    'rate', 'unit_name', 'category_name', 'payment_mode',
                    'amount', 'type', 'model', 'reason', 'invoice', 'job_card', 'method', 'staff', 'vehicle',
                    'leave', 'user_id', 'day', 'days', 'no_of_day'
                ];

                var selector = 'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="file"]), select, textarea';
                document.querySelectorAll(selector).forEach(function(el) {
                    var name = el.getAttribute('name');
                    var id = el.getAttribute('id');
                    var placeholder = el.getAttribute('placeholder');
                    
                    // Skip search inputs and explicitly opted-out fields
                    if (el.classList.contains('no-asterisk') ||
                        (name && name.toLowerCase().includes('search')) || 
                        (id && id.toLowerCase().includes('search')) ||
                        (placeholder && placeholder.toLowerCase().includes('search'))) {
                        return;
                    }

                    var isRequired = el.hasAttribute('required');

                    if (!isRequired) {
                        if (name) {
                            requiredNames.forEach(function(req) {
                                if (name.toLowerCase() === req.toLowerCase() || name.toLowerCase().includes(req.toLowerCase())) {
                                    isRequired = true;
                                }
                            });
                        }
                        if (id && !isRequired) {
                            requiredNames.forEach(function(req) {
                                if (id.toLowerCase() === req.toLowerCase() || id.toLowerCase().includes(req.toLowerCase())) {
                                    isRequired = true;
                                }
                            });
                        }
                    }

                    if (isRequired) {
                        var group = el.closest('.form-group') || el.closest('[class*="col-"]') || el.closest('.mb-3') || el.parentElement;
                        if (group) {
                            var label = group.querySelector('label');
                            if (label) {
                                var html = label.innerHTML;
                                if (!html.includes('*') && !html.includes('Add Category') && !html.includes('Add Brand') && !html.includes('Add Unit') && !html.includes('Add Tax') && !html.includes('Add Vendor') && !html.includes('Add Product') && !html.includes('Add Leave')) {
                                    var span = label.querySelector('span');
                                    if (span) {
                                        if (!span.innerHTML.includes('*')) {
                                            span.insertAdjacentHTML('beforeend', ' <span class="text-danger">*</span>');
                                        }
                                    } else {
                                        label.insertAdjacentHTML('beforeend', ' <span class="text-danger">*</span>');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Run on load
            markRequiredFields();

            // Run after AJAX calls complete (to handle dynamic forms/modals)
            if (typeof jQuery !== 'undefined') {
                $(document).ajaxComplete(function() {
                    setTimeout(markRequiredFields, 150);
                });
            }
        });
    </script>
</body>

</html>
