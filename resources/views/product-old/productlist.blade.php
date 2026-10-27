@extends('layout.app')

@section('title', 'Items List')

@section('content')
    <style>
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

        .table-scroll-top {
            display: none;
        }

        .quantity-box {
            cursor: pointer !important;
            border: 1px solid #ececec !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            display: inline-block !important;
            /* background: #f9f9f9; optional light background */
            font-size: 14px !important;
        }

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td.details-cell {
                display: none !important;
            }
        }

        @media screen and (max-width: 768px) {

            /* Mobile: hide non-essential columns, show Details toggle */
            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td.details-cell {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .toggle-details i {
                font-size: 18px;
            }

            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #ff9f43;
            }

            .toggle-details i {
                color: #fff;
                font-size: 12px;
                line-height: 1;
            }

            .toggle-details.is-open {
                background: #dc3545;
            }

            /* Style for item name wrapping */
            table.datanew tbody td:first-child {
                display: block !important;
                max-width: calc(100vw - 100px) !important;
            }

            /* Keep name row horizontal, details stacked below */
            table.datanew tbody td:first-child>a {
                display: flex !important;
                align-items: center !important;
                gap: 10px !important;
                width: 100% !important;
            }

            table.datanew tbody td:first-child .collapse,
            table.datanew tbody td:first-child .collapse .card {
                width: 100% !important;
            }

            .product-name {
                display: inline-block !important;
                max-width: 100% !important;
                font-size: 14px !important;
                word-break: break-word !important;
                hyphens: auto !important;
                -webkit-hyphens: auto !important;
                -ms-hyphens: auto !important;
            }

            /* Limit to 2 lines with ellipsis */
            .product-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .table-scroll-top {
                display: block;
                -webkit-overflow-scrolling: touch !important;
                /* smooth scrolling on iOS */
            }

            select#filterCategory {
                font-size: 12px !important;
            }

            select#filterBrand {
                font-size: 12px !important;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }

            .form-control-sm {
                min-height: calc(1.5em + .5rem + 2px);
                padding: .25rem .5rem;
                font-size: 13px !important;
                border-radius: .2rem;
            }

            .search-set {
                margin-right: 1rem !important;
            }
        }
    </style>
    @if (session('error'))
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

    <!-- Quantity History Modal -->
    <div class="modal fade" id="quantityHistoryModal" tabindex="-1" aria-labelledby="quantityHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityHistoryLabel">Quantity History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="quantityHistoryContent">
                    <!-- History content will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Items</h4>
                <!--<h6>Manage your products</h6>-->
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(1, 'add'))
                    <a href="{{ route('product.add') }}" class="btn btn-sm btn-added"><img
                            src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1">New
                        Item</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- <div class="search-set d-flex mt-2 mx-2">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                        </div>
                    </div> -->
                    <!-- Right: Filters (Grouped) -->
                    <div class="row">
                        <!-- Sub-Branch Filter (Admin Only) -->
                        <div class="col-6 col-md-6 mb-3 px-1" id="subBranchContainer" style="display: none;">
                            <div class="form-group mb-0">
                                <label for="filterSubBranch" class="form-label mb-1">Sub-Branch</label>
                                <select id="filterSubBranch" class="form-control form-control-sm">
                                    <option value="">All Sub-Branches</option>
                                </select>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-6 col-md-6 mb-3 px-1">
                            <div class="form-group mb-0">
                                <label for="filterCategory" class="form-label mb-1">Category</label>
                                <select id="filterCategory" class="form-control form-control-sm">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="col-6 col-md-6 mb-3 px-1">
                            <div class="form-group mb-0">
                                <label for="filterBrand" class="form-label mb-1">Brand</label>
                                <select id="filterBrand" class="form-control form-control-sm">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-scroll-top">
                    <div></div>
                </div>
                <div class="table-responsive">
                    <table class="table  datanew">
                        <thead>
                            <tr>
                                <!-- <th>
                                                        <label class="checkboxs">
                                                            <input type="checkbox" id="select-all">
                                                            <span class="checkmarks"></span>
                                                        </label>
                                                    </th> -->
                                <th>Item Name</th>
                                <th class="details-column">Details</th>
                                <th>SKU</th>
                                <th>Category </th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Created At</th>
                                <th>Action</th>
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
            var table = $('.datanew').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                ordering: false,
                searching: true,
                paging: true,
                destroy: true,
                columnDefs: [{
                    className: "details-cell",
                    targets: 1
                }],
                language: {
                    searchPlaceholder: " ",
                    search: "Search:",
                    emptyTable: "No products found.",
                    zeroRecords: "No products record found.",
                },
            });

            // Fetch sub-admins for admin users
            $.ajax({
                    url: "/api/get_subadmin",
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status) {
                        const select = document.getElementById("filterSubBranch");
                        const selectDiv = select.parentElement;
                        const role = response.role;

                        if (role === 'admin') {
                            document.getElementById('subBranchContainer').style.display =
                                'block'; // Show the select
                        }

                        // Add "Show All" option for admin
                        if (role === 'admin') {
                            const showAllOption = document.createElement("option");
                            showAllOption.value = "";
                            showAllOption.textContent = "All Sub-Branches";
                            select.appendChild(showAllOption);
                        }

                        // Populate options
                        response.data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.id;
                            option.textContent = item.name;
                            select.appendChild(option);
                        });

                        // Set previous selected ID from localStorage
                        const savedId = localStorage.getItem('selectedSubAdminId');
                        if (savedId && role === 'admin') {
                            const optionExists = response.data.some(item => item.id == savedId);
                            if (optionExists) {
                                $('#filterSubBranch').val(savedId);
                            }
                        }
                    }
                });

            // Define the function FIRST
            function fetchProducts() {
                var categoryId = $('#filterCategory').val();
                var brandId = $('#filterBrand').val();
                // var subBranchId = $('#filterSubBranch').val();
                const subBranchId = localStorage.getItem('selectedSubAdminId');
                $(".datanew tbody").html(
                        '<tr><td colspan="9" class="text-center text-primary">Please wait, loading products...</td></tr>'
                    );
                $.ajax({
                    url: "/api/getAllProduct",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        category_id: categoryId,
                        brand_id: brandId,
                        sub_branch_id: subBranchId
                    },
                    success: function(response) {
                        if (response.status) {
                            let products = response.data;
                            let currencySymbol = response.currencySymbol || '₹';
                            let currencyPosition = response.currencyPosition || 'left';
                            let tableBody = [];

                            let lowStockThreshold = response.lowStockThreshold || 0;


                            // Function to capitalize first letter of each word
                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }
                            $.each(products, function(index, product) {
                                let productName = capitalizeWords(product.name || 'N/A');
                                let sku = product.SKU || 'N/A';
                                let quantity = product.quantity ?? 'N/A';
                                let categoryName = capitalizeWords(product.category ? product
                                    .category.name : "N/A");
                                let brandName = capitalizeWords(product.brand ? product.brand
                                    .name : "N/A");

                                let createdAt = "N/A";
                                if (product.created_at) {
                                    let date = new Date(product.created_at);
                                    let day = String(date.getDate()).padStart(2, '0');
                                    let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    let month = monthNames[date.getMonth()];
                                    let year = date.getFullYear();
                                    let hours = date.getHours();
                                    let minutes = String(date.getMinutes()).padStart(2, '0');
                                    let ampm = hours >= 12 ? 'PM' : 'AM';
                                    hours = hours % 12 || 12;
                                    createdAt =
                                        `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
                                }

                                let productImage =
                                    '{{ env('ImagePath') . 'admin/assets/img/product/noimage.png' }}';
                                const ImagePath = "{{ env('ImagePath') . 'storage/' }}";
                                // if (product.images) {
                                //     let imagesArray = typeof product.images === "string" ? JSON
                                //         .parse(product.images) : product.images;
                                //     if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                //         productImage = `${ImagePath}${imagesArray[0]}`;
                                //     }
                                // }
                                let imagesArray = [];
                                if (product.images) {
                                    try {
                                        imagesArray = JSON.parse(product.images);
                                    } catch (e) {
                                        // If it's not JSON, assume it's comma-separated string or single image
                                        imagesArray = product.images.includes(',') ?
                                            product.images.split(',') :
                                            [product.images];
                                    }
                                }

                                let formattedPrice = 'N/A';
                                if (product.price !== undefined && product.price !== null &&
                                    product.price !== '') {
                                    if (currencyPosition === 'left') {
                                        formattedPrice = currencySymbol + product.price;
                                    } else {
                                        formattedPrice = product.price + currencySymbol;
                                    }
                                }

                                // let quantityDisplay =
                                //     `<span class="quantity-history" data-id="${product.id}" style="cursor:pointer ; border: 1px solid #ececec; padding: 4px 8px; border-radius: 4px; display: inline-block;">${quantity}</span>`;
                                // if (quantity !== 'N/A' && parseInt(quantity) <=
                                //     lowStockThreshold) {
                                //     quantityDisplay +=
                                //         ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                // }
                                let quantityDisplay =
                                    `<span class="quantity-history quantity-box" data-id="${product.id}">${quantity}</span>`;

                                if (quantity !== 'N/A' && parseInt(quantity) <=
                                    lowStockThreshold) {
                                    quantityDisplay +=
                                        ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                }

                                let actionButtons = `
                                    @if (app('hasPermission')(1, 'view'))
                                        <a class="me-3" href="/product-view/${product.id}">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/eye.svg' }}" alt="img">
                                        </a>
                                    @endif
                                    @if (app('hasPermission')(1, 'edit'))
                                        <a class="me-3" href="/edit-product/${product.id}">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="img">
                                        </a>
                                    @endif
                                    @if (app('hasPermission')(4, 'delete'))
                                        <a class="confirm-text delete-product" data-id="${product.id}" href="javascript:void(0);">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="img">
                                        </a>
                                    @endif
                                `;

                                tableBody.push([
                                    `<div style="display: flex; align-items: center;">
                                        <a href="/product-view/${product.id}" class="product-img mb-1">
                                            <img src="${productImage}" alt="product" style="max-width: 60px;">
                                        </a>
                                        <a href="/product-view/${product.id}" style="color: #1b2850; font-weight: 500; text-align: center; margin-left: 8px;" class="product-name truncated">
                                            ${productName}
                                        </a>
                                    </div>
                                    <!-- Collapsible Details (visible only on mobile) -->
                                    <div class="collapse mt-2 d-lg-none" id="details-${product.id}">
                                        <div class="card card-body p-2 bg-light border">
                                            <p class="mb-1"><strong>SKU:</strong> ${sku}</p>
                                            <p class="mb-1"><strong>Category:</strong> ${categoryName}</p>
                                            <p class="mb-1"><strong>Brand:</strong> ${brandName}</p>
                                            <p class="mb-1"><strong>Price:</strong> ${formattedPrice}</p>
                                            <p class="mb-1"><strong>Quantity:</strong> ${quantityDisplay}</p>
                                            <p class="mb-1"><strong>Created At:</strong> ${createdAt}</p>
                                            <div class="mt-2">
                                                ${actionButtons}
                                            </div>
                                        </div>
                                    </div>`,
                                    `<a href="#details-${product.id}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus"></i>
                                    </a>`,
                                    sku,
                                    categoryName,
                                    brandName,
                                    formattedPrice,
                                    quantityDisplay,
                                    createdAt,
                                    actionButtons
                                ]);
                            });
                            table.clear().rows.add(tableBody).draw();

                            // Sync top scrollbar
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

                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html('<tr><td colspan="9">No products found</td></tr>');
                        }
                    },
                    error: function() {
                        alert("Error fetching products.");
                    }
                });
            }
            $(document).on('click', '.quantity-history', function() {
                const productId = $(this).data('id');

                $.ajax({
                    url: `/api/product-quantity-history/${productId}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(res) {
                        if (res.status) {
                            let content = `<h5>${res.product}</h5><ul class="list-group">`;
                            res.history.forEach(item => {
                                content += `
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${item.type}</strong> - ${item.note}<br>
                                                <small>${item.date}</small>
                                            </div>
                                        <span class="badge bg-${item.type === 'Added' ? 'success' : 'danger'} px-4 py-2 small">${item.quantity}</span>
                                        </li>
                                    `;
                            });
                            content += `</ul>`;
                            $('#quantityHistoryContent').html(content);
                            $('#quantityHistoryModal').modal('show');
                        } else {
                            alert(res.message || "Failed to load quantity history.");
                        }
                    },
                    error: function() {
                        alert("Error fetching quantity history.");
                    }
                });
            });

            // Initial fetch
            fetchProducts();

            // Filters: bind change events AFTER function is defined
            $('#filterCategory, #filterBrand, #filterSubBranch').on('change', function() {
                fetchProducts();
            });
        })

        // Toggle +/− icon for mobile details
        $(document).on("click", ".toggle-details", function() {
            const $toggle = $(this);
            const $icon = $toggle.find("i");
            $icon.toggleClass("fa-plus fa-minus");
            $toggle.toggleClass("is-open");
        });


        $(document).on('click', '.delete-product', function() {
            var productId = $(this).data('id'); // Get product ID
            var authToken = localStorage.getItem("authToken");
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonColor: "#ff9f43", // Confirm button color (orange)
                cancelButtonColor: "#6c757d", // Cancel button color (gray)
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/deleteProduct/${productId}`, // Adjust based on your route
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                'content') // CSRF token for security
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });

                                $(`.delete-product[data-id="${productId}"]`).closest('tr')
                                    .remove();
                            } else {
                                // Use response.error here, fallback to response.message just in case
                                Swal.fire("Error!", response.error || response.message ||
                                    "Unknown error", "error");
                            }
                        },

                        error: function(xhr) {
                            // Parse the JSON error message sent by API in HTTP error response
                            let message = "Something went wrong!";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                message = xhr.responseJSON.error;
                            }
                            // Swal.fire("Error!", message, "error");
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonColor: "#ff9f43", // custom orange color
                                confirmButtonText: "OK"
                            });

                        }
                    });
                }
            });
        });
    </script>
@endpush
