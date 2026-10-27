@extends('layout.app')

@section('title', 'Edit Purchase')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .add-row-btn {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .add-row {
                width: 100%;
                text-align: center;
            }

            .remove-row {
                width: 100%;
                text-align: center;
            }

            .form-group {
                margin-bottom: 10px !important
            }
        }

        .gst-header {
            display: flex;
            align-items: center;
            gap: 25px;
            padding: 12px 20px;
        }

        .gst-header .header-title {
            min-width: 140px;
            font-weight: bold;
            margin: 0;
            font-size: 18px;
            color: #444;
        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            user-select: none;
            position: relative;
            padding-left: 28px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: color 0.2s ease;
        }

        .custom-radio-label input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2.5px solid #888;
            border-radius: 50%;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .custom-radio-label input[type="radio"]:checked {
            border-color: #0056b3;
            background-color: #0056b3;
        }

        .custom-radio-label input[type="radio"]:checked::after {
            content: "";
            display: block;
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-radio-label:hover input[type="radio"] {
            border-color: #5a50cc;
        }

        .custom-radio-label:hover {
            color: #0056b3;
        }

        .form-row {
            border: 1px solid #fdc794;
            padding: 20px 10px 10px 10px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #fdfdfd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .add-row-btn {
            margin-top: 28px;
        }

        @media screen and (min-width: 992px) and (max-width: 1366px) {
            .quantity-input,
            .product-discount-input,
            .product-discount-amount-input {
                min-width: 75px;
            }

            .form-row .col-lg-1 {
                flex: 0 0 9%;
                max-width: 9%;
            }
        }

        /* iPad Pro (1024px), Nest Hub (1024px), Nest Hub Max (1280px) */
        @media screen and (min-width: 1024px) and (max-width: 1280px) {
            /* Row 1: Category | Item Name | Item Price | Qty — 4 equal columns */
            .form-row .category-col,
            .form-row .product-col,
            .form-row .price-col,
            .form-row .qty-col {
                flex: 0 0 25%;
                max-width: 25%;
            }

            /* Row 2: Disc% | Disc-Amt | Total Amount | + button */
            .form-row .disc-pct-col,
            .form-row .disc-amt-col {
                flex: 0 0 20%;
                max-width: 20%;
            }

            .form-row .total-col {
                flex: 0 0 40%;
                max-width: 40%;
            }

            .form-row .add-row-btn {
                flex: 0 0 20%;
                max-width: 20%;
                margin-top: 28px;
            }
        }

        #search_results_dropdown {
            position: absolute;
            width: 100%;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 9999;
            max-height: 350px;
            overflow-y: auto;
        }
        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            background: #fdfdfd;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .search-header h6 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        .search-close {
            font-size: 20px;
            color: #999;
            cursor: pointer;
            line-height: 1;
        }
        .search-close:hover {
            color: #333;
        }
        .search-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #f9f9f9;
            transition: background 0.2s;
        }
        .search-item-row:hover {
            background: #fcfcfc;
        }
        .search-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .search-item-img {
            width: 45px;
            height: 45px;
            border-radius: 6px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bbb;
            font-size: 18px;
            border: 1px solid #eaeaea;
            object-fit: cover;
        }
        .search-item-details {
            display: flex;
            flex-direction: column;
        }
        .search-item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }
        .search-item-price {
            font-size: 13px;
            color: #666;
        }
        .search-item-gst {
            font-size: 11px;
            font-weight: 500;
            margin-top: 2px;
        }
        .gst-with {
            color: #28a745;
        }
        .gst-without {
            color: #6c757d;
        }
        .search-add-btn {
            background: #ff9f43;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-add-btn:hover {
            background: #e08b35;
        }
    </style>
    @php
        $user = Auth()->user();
        $branchId = $user->id ?? null;
        $userRole = $user->role ?? '';
        $subAdminId = session('selectedSubAdminId');
        if ($userRole === 'sub-admin') {
            $branchId = $user->id; // sub-admin uses own id
        } elseif ($userRole === 'admin' && !empty($subAdminId)) {
            $branchId = $subAdminId; // admin chooses sub-admin
        } elseif ($userRole === 'staff') {
            $branchId = $user->branch_id; // admin with no sub-admins
        } else {
            $branchId = $user->id; // for other roles, use own id
        }
        $vendors = App\Models\User::where('role', 'vendor')
            ->where('isDeleted', 0) // Only non-deleted vendors
            ->where('branch_id', $branchId) // Filter by branch_id
            ->get();
        $taxes = App\Models\TaxRate::where('status', 'active')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        $products = App\Models\Product::where('isDeleted', 0)->where('branch_id', $branchId)->get();

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        $productsArray = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'gst_option' => $product->gst_option,
                'product_gst' => $product->product_gst,
                'image_url' => $product->image_url[0] ?? asset('admin/assets/img/product/noimage.png'),
            ];
        });
        $productsJson = json_encode($productsArray);
    @endphp
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Purchase</h4>
            </div>
            <div class="gst-header">

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                    Without GST
                </label>

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="with_gst" value="with" />
                    With GST
                </label>
                <a href="{{ route('purchase.lists') }}" class="btn btn-added">Back</a>
            </div>
            
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="row d-flex mb-3">
                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="mb-0">Vendor Name</label>
                                    <button type="button" class="btn btn-sm text-white" style="background: #ff9f43; padding: 2px 8px; font-size: 12px; border-radius: 4px;" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                                        <i class="fa fa-plus"></i> Add Vendor
                                    </button>
                                </div>
                                <select id="vendor_name" name="vendor_id" class="form-control select2 vendor-select" disabled>
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Bill No</label>
                                <input type="text" id="bill_no" name="bill_no" class="form-control" placeholder="Bill No">
                                <span class="error text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <input type="text" id="purchase_date" class="form-control" value="{{ date('d/m/Y') }}" readonly style="background-color: #f9f9f9; color: #666;">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-flex align-items-end position-relative">
                        <div class="col-lg-10 col-md-9 col-12 mb-2">
                            <div class="form-group mb-0 position-relative">
                                <input type="text" id="search_product" class="form-control" placeholder="Search product by name..." style="height: 40px;">
                                <div id="search_results_dropdown" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-12 mb-2">
                            <button type="button" class="btn text-white w-100" style="background-color: #ff9f43; border-color: #ff9f43; height: 40px;" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fa fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12 d-none">
                        <div class="form-group">
                            <label>Vendor Phone</label>
                            <input type="number" id="vendor_phone" name="phone" class="form-control" placeholder="Enter Phone" readonly>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div>
                {{-- <div class="mt-2"> --}}
                <div class="row form-row">
                    <div class="col-lg-2 col-sm-6 col-6 category-col">
                        <div class="form-group">
                            <label>Category Name</label>
                            <select id="category_name" name="category_name[]" class="form-control select2 category-select">
                                <option value="">Category Name</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-price="{{ $category->price }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-6 product-col">
                        <div class="form-group">
                            <label>Item Name</label>
                            <select id="product_name" name="product_name[]" class="form-control select2 product-select"
                                disabled>
                                <option value="">Item Name</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-category="{{ $product->category_id }}"
                                        data-gst-option="{{ $product->gst_option }}" data-gst='{!! $product->product_gst !!}'>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="product-gst-info mt-1"></div>

                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-6 price-col">
                        <div class="form-group">
                            <label>Item Price</label>
                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6 qty-col">
                        <div class="form-group">
                            <label> Qty</label>
                            <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Qty"
                                value="1" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6 disc-pct-col">
                        <div class="form-group">
                            <label>Disc%</label>
                            <input type="number" name="product_discount[]" class="form-control product-discount-input"
                                placeholder="0.00" value="0" min="0" max="100"
                                oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6 disc-amt-col">
                        <div class="form-group">
                            <label>Disc-Amt</label>
                            <input type="number" name="product_discount_amount[]"
                                class="form-control product-discount-amount-input" placeholder="0.00" value="0"
                                min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-12 total-col">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" name="total[]" class="form-control total-input" placeholder="0"
                                min="0" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 add-row-btn">
                        <button type="button" class="btn btn-success add-row">+</button>
                    </div>
                </div>

                <!-- Placeholder for additional rows -->
                <div id="form-container"></div>
                {{-- </div> --}}

                <div class="row">
                    <!-- <div class="col-lg-3 col-sm-6 col-12">
                                                                                                                                                            <div class="form-group">
                                                                                                                                                                <label>Discount</label>
                                                                                                                                                                <input type="number" name="discount" id="discount" class="form-control" placeholder="0.00%">
                                                                                                                                                                <span class="error text-danger"></span>
                                                                                                                                                            </div>
                                                                                                                                                        </div> -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" name="shipping" id="shipping" class="form-control"
                                placeholder="0.00">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Purchase Status</label>
                            <select name="status" class="form-control" disabled>
                                <option value="">Choose Status</option>
                                <option value="completed">Completed</option>
                                <option value="partially">Partially</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control payment_status" disabled>
                                <option value="">Choose Status</option>
                                <option value="completed">Completed</option>
                                <option value="partially">Partially</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 float-md-right">
                        <div class="total-order">
                            <ul>
                                <li>
                                    <h4>Total Item Amount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-product-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-product-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>


                                <li>
                                    <h4>Discount Amount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-discount-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-discount-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li>
                                    <h4>Price after Discount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="price-after-discount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="price-after-discount">0.00</span>
                                        @endif
                                    </h5>
                                </li>
                                <li id="gst-section" style="display:none;">
                                    <h4>Total GST</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-gst-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-gst-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li>
                                    <h4>Shipping</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="shipping-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="shipping-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="total">
                                    <h4>Grand Total</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="grand-total">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="grand-total">0.00</span>
                                        @endif
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <a href="javascript:void(0);" class="btn btn-submit me-2">Submit</a>
                    <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickAddVendorForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Vendor Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter 10-Digit Phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">GST Number</label>
                                <input type="text" name="gst_num" class="form-control" placeholder="Enter GST Number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" placeholder="Enter City">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" placeholder="Enter Country" value="India">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Enter Address"></textarea>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #ff9f43; border-color: #ff9f43;">Save Vendor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickAddProductForm" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Product Name">
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control select2-modal">
                                    <option value="">Choose Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" id="modal_brand_id" class="form-control select2-modal">
                                    <option value="">Choose Brand</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <select name="unit_id" id="modal_unit_id" class="form-control select2-modal">
                                    <option value="">Choose Unit</option>
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" placeholder="0.00" min="0" step="0.01">
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" value="0" min="0">
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="SKU" id="modal_sku" class="form-control" placeholder="Enter SKU">
                                <span class="error text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control" placeholder="Enter Barcode">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">GST Option</label>
                                <select name="gst_option" id="modal_gst_option" class="form-control select2-modal">
                                    <option value="without_gst">Without GST</option>
                                    <option value="with_gst">With GST</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="modal_gst_dropdown_container" style="display: none;">
                                <label class="form-label">Product GST</label>
                                <select name="product_gst[]" id="modal_product_gst" class="form-control select2-modal" multiple>
                                    <!-- Will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Enter Description"></textarea>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #ff9f43; border-color: #ff9f43;">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const products = @json($productsArray);
    </script>
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");

            // Product search dropdown logic
            // Product search dropdown logic
            $(document).on('input', '#search_product', function() {
                const query = $(this).val().toLowerCase().trim();
                const $dropdown = $('#search_results_dropdown');
                $dropdown.empty();
                
                if (query.length < 1) {
                    $dropdown.hide();
                    return;
                }
                
                const filtered = products.filter(p => p.name.toLowerCase().includes(query) || (p.SKU && p.SKU.toString().toLowerCase().includes(query)));
                
                // Append Header
                $dropdown.append('<div class="search-header"><h6>Products</h6><span class="search-close">&times;</span></div>');
                
                if (filtered.length === 0) {
                    $dropdown.append('<div class="p-3 text-muted">No products found</div>');
                } else {
                    filtered.forEach(p => {
                        let imgUrl = p.image_url || '/admin/assets/img/product/noimage.png';
                        let gstLabel = p.gst_option === 'with_gst' ? 'With GST' : 'Without GST';
                        let gstClass = p.gst_option === 'with_gst' ? 'gst-with' : 'gst-without';
                        
                        $dropdown.append(`
                            <div class="search-item-row">
                                <div class="search-item-left">
                                    <img src="${imgUrl}" class="search-item-img" onerror="this.src='/admin/assets/img/product/noimage.png';">
                                    <div class="search-item-details">
                                        <div class="search-item-name">${p.name}</div>
                                        <div class="search-item-price">Price: ₹${parseFloat(p.price).toFixed(2)}</div>
                                        <div class="search-item-gst ${gstClass}">${gstLabel}</div>
                                    </div>
                                </div>
                                <button type="button" class="search-add-btn search-item" data-id="${p.id}" data-category="${p.category_id}">+ Add</button>
                            </div>
                        `);
                    });
                }
                $dropdown.show();
            });
            
            // Hide dropdown when clicking close icon or clicking outside
            $(document).on('click', '.search-close', function(e) {
                e.stopPropagation();
                $('#search_results_dropdown').hide();
            });
            
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search_product, #search_results_dropdown').length) {
                    $('#search_results_dropdown').hide();
                }
            });
            
            // Handle clicking a product from search results
            $(document).on('click', '.search-item', function() {
                const productId = $(this).data('id');
                const categoryId = $(this).data('category');
                
                let $targetRow = null;
                const $rows = $('.form-row');
                
                // Find first row with no product selected
                $rows.each(function() {
                    if ($(this).find('.product-select').val() === "") {
                        $targetRow = $(this);
                        return false; // break loop
                    }
                });
                
                if (!$targetRow) {
                    // Click "+" button to create a new row
                    $('.add-row').click();
                    $targetRow = $('.form-row').last();
                }
                
                // Populate category first and trigger change
                const $catSelect = $targetRow.find('.category-select');
                $catSelect.val(categoryId).trigger('change');
                
                // Then populate product (delaying slightly to allow category select event list to run)
                setTimeout(() => {
                    const $prodSelect = $targetRow.find('.product-select');
                    $prodSelect.val(productId).trigger('change');
                    
                    const productData = products.find(p => p.id == productId);
                    if (productData) {
                        $targetRow.find('.price-input').val(parseFloat(productData.price)).trigger('input');
                        $targetRow.find('.quantity-input').val(1).trigger('input');
                    }
                }, 150);
                
                $('#search_results_dropdown').hide();
                $('#search_product').val('');
            });

            // Modal elements dynamic loading & actions
            let modalDataLoaded = false;
            function loadModalDropdowns() {
                if (modalDataLoaded) return;
                
                // Load Brands
                $.ajax({
                    url: '/api/get-brand',
                    type: 'GET',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(resp) {
                        if (resp.status && resp.data) {
                            const $brandSelect = $('#modal_brand_id');
                            resp.data.forEach(function(brand) {
                                $brandSelect.append(`<option value="${brand.id}">${brand.name}</option>`);
                            });
                            $brandSelect.trigger('change.select2');
                        }
                    }
                });
                
                // Load Units
                $.ajax({
                    url: '/api/get-units',
                    type: 'GET',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(resp) {
                        if (resp.status && resp.data) {
                            const $unitSelect = $('#modal_unit_id');
                            resp.data.forEach(function(unit) {
                                $unitSelect.append(`<option value="${unit.id}">${unit.unit_name}</option>`);
                            });
                            $unitSelect.trigger('change.select2');
                        }
                    }
                });
                
                // Load Tax Rates
                $.ajax({
                    url: '/api/get-tax-rates',
                    type: 'GET',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(resp) {
                        if (resp.status && resp.data) {
                            const $gstSelect = $('#modal_product_gst');
                            resp.data.forEach(function(tax) {
                                $gstSelect.append(`<option value="${tax.id}" data-rate="${tax.tax_rate}">${tax.tax_name} (${tax.tax_rate}%)</option>`);
                            });
                            $gstSelect.trigger('change.select2');
                        }
                    }
                });
                
                modalDataLoaded = true;
            }
            
            // Auto-generate a random SKU when the modal is opened
            $(document).on('show.bs.modal', '#addProductModal', function() {
                loadModalDropdowns();
                $('#modal_sku').val('SKU-' + Math.floor(100000 + Math.random() * 900000));
                $('.select2-modal').select2({
                    dropdownParent: $('#addProductModal'),
                    width: '100%'
                });
            });
            
            // Toggle GST dropdown inside modal
            $(document).on('change', '#modal_gst_option', function() {
                if ($(this).val() === 'with_gst') {
                    $('#modal_gst_dropdown_container').show();
                } else {
                    $('#modal_gst_dropdown_container').hide();
                    $('#modal_product_gst').val([]).trigger('change.select2');
                }
            });

            // Track active product row
            let activeProductRow = null;
            $(document).on('click', '.btn-add-product-trigger', function() {
                activeProductRow = $(this).closest('.form-row');
            });

            // Quick Add Product Form Submit
            $(document).on('submit', '#quickAddProductForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                
                // Clear previous errors
                $form.find('.error').text('');
                
                let isValid = true;
                
                // Validate fields
                const name = $form.find('input[name="name"]').val().trim();
                const categoryId = $form.find('select[name="category_id"]').val();
                const unitId = $form.find('select[name="unit_id"]').val();
                const price = $form.find('input[name="price"]').val().trim();
                const quantity = $form.find('input[name="quantity"]').val().trim();
                const sku = $form.find('input[name="SKU"]').val().trim();
                
                if (name === "") {
                    $form.find('input[name="name"]').siblings('.error').text('Product name is required.');
                    isValid = false;
                }
                if (categoryId === "") {
                    $form.find('select[name="category_id"]').siblings('.error').text('Category is required.');
                    isValid = false;
                }
                if (unitId === "") {
                    $form.find('select[name="unit_id"]').siblings('.error').text('Unit is required.');
                    isValid = false;
                }
                if (price === "" || parseFloat(price) < 0) {
                    $form.find('input[name="price"]').siblings('.error').text('Valid price is required.');
                    isValid = false;
                }
                if (quantity === "" || parseInt(quantity) < 0) {
                    $form.find('input[name="quantity"]').siblings('.error').text('Valid quantity is required.');
                    isValid = false;
                }
                if (sku === "") {
                    $form.find('input[name="SKU"]').siblings('.error').text('SKU is required.');
                    isValid = false;
                }
                
                if (!isValid) {
                    return false;
                }
                
                const $submitBtn = $form.find('button[type="submit"]');
                const originalText = $submitBtn.text();
                
                $submitBtn.prop('disabled', true).text('Saving...');
                
                $.ajax({
                    url: '/api/createProduct',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken
                    },
                    data: $form.serialize(),
                    success: function(response) {
                        $submitBtn.prop('disabled', false).text(originalText);
                        if (response.status && response.product) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43'
                            });
                            
                            const newProd = response.product;
                            
                            // Parse GST if exists
                            let gstVal = newProd.product_gst;
                            if (typeof gstVal === 'string') {
                                try {
                                    gstVal = JSON.parse(gstVal);
                                } catch(e) {}
                            }
                            
                            // Format product for global products array
                            const formattedProduct = {
                                id: newProd.id,
                                name: newProd.name,
                                category_id: newProd.category_id,
                                price: parseFloat(newProd.price) || 0,
                                product_gst: gstVal,
                                gst_option: newProd.gst_option
                            };
                            
                            // Push to global products array
                            products.push(formattedProduct);
                            
                            let $targetRow = activeProductRow;
                            if (!$targetRow) {
                                // Find first row with no product selected
                                const $rows = $('.form-row');
                                $rows.each(function() {
                                    if ($(this).find('.product-select').val() === "") {
                                        $targetRow = $(this);
                                        return false; // break loop
                                    }
                                });
                                
                                if (!$targetRow) {
                                    // Click "+" button to create a new row
                                    $('.add-row').click();
                                    $targetRow = $('.form-row').last();
                                }
                            }
                            
                            // Select category first
                            const $catSelect = $targetRow.find('.category-select');
                            $catSelect.val(newProd.category_id).trigger('change');
                            
                            // Then select product and fill details
                            setTimeout(() => {
                                const $prodSelect = $targetRow.find('.product-select');
                                
                                // Ensure option exists
                                if ($prodSelect.find(`option[value="${newProd.id}"]`).length === 0) {
                                    $prodSelect.append(`<option value="${newProd.id}" data-price="${newProd.price}" data-category="${newProd.category_id}" data-gst-option="${newProd.gst_option}" data-gst='${JSON.stringify(gstVal)}'>${newProd.name}</option>`);
                                }
                                
                                $prodSelect.val(newProd.id).trigger('change');
                                $targetRow.find('.price-input').val(parseFloat(newProd.price)).trigger('input');
                                $targetRow.find('.quantity-input').val(1).trigger('input');
                            }, 150);
                            
                            activeProductRow = null; // Reset tracker
                            
                            $('#addProductModal').modal('hide');
                            $form[0].reset();
                            $('#modal_gst_dropdown_container').hide();
                        } else {
                            Swal.fire('Error', response.message || 'Failed to save product', 'error');
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false).text(originalText);
                        let errMsg = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errMsg = Object.values(xhr.responseJSON.errors).map(err => err.join('<br>')).join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error',
                            html: errMsg,
                            icon: 'error'
                        });
                    }
                });
            });

            // Quick Add Vendor Form Submit
            $(document).on('submit', '#quickAddVendorForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');
                const originalText = $submitBtn.text();
                
                $submitBtn.prop('disabled', true).text('Saving...');
                
                $.ajax({
                    url: '/api/createSupplier',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken
                    },
                    data: $form.serialize(),
                    success: function(response) {
                        $submitBtn.prop('disabled', false).text(originalText);
                        if (response.status && response.vendor) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43'
                            });
                            
                            // Append newly created vendor option and select it
                            const newVendor = response.vendor;
                            const $select = $('#vendor_name');
                            const optExists = $select.find(`option[value="${newVendor.id}"]`).length > 0;
                            if (!optExists) {
                                $select.append(`<option value="${newVendor.id}" data-phone="${newVendor.phone}">${newVendor.name}</option>`);
                            }
                            $select.val(newVendor.id).trigger('change.select2');
                            
                            $('#addVendorModal').modal('hide');
                            $form[0].reset();
                        } else {
                            Swal.fire('Error', response.error || 'Failed to save vendor', 'error');
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false).text(originalText);
                        let errMsg = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errMsg = Object.values(xhr.responseJSON.errors).map(err => err.join('<br>')).join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errMsg = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            title: 'Error',
                            html: errMsg,
                            icon: 'error'
                        });
                    }
                });
            });

            // ✅ Extract invoice ID from URL
            let urlSegments = window.location.pathname.split("/");
            let invoiceId = urlSegments[urlSegments.length - 1]; // Get last segment

            if (!isNaN(invoiceId) && invoiceId > 0) {
                loadPurchaseData(invoiceId); // Fetch purchase details
            }


            // Initialize Select2
            // $(".select2, .category-select").select2({
            //     tags: true,
            // });
            $(".vendor-select,.product-select,.category-select").select2({});
            // Add new form row dynamically
            $(document).on("click", ".add-row", function() {
                let row = `
                                                                                <div class="row form-row">

                                                                                    <div class="col-lg-2 col-sm-6 col-6 category-col">
                                                                                        <div class="form-group">
                                                                                            <label>Category Name</label>
                                                                                            <select name="category_name[]" class="form-control category-select">
                                                                                                <option value="">Category Name</option>
                                                                                                @foreach ($categories as $category)
                                                                                                    <option value="{{ $category->id }}" data-price="{{ $category->price }}">{{ $category->name }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-sm-6 col-6 product-col">
                                                                                        <div class="form-group">
                                                                                            <label>Item Name</label>
                                                                                            <select name="product_name[]" class="form-control select2 product-select" disabled>
                                                                                                <option value="">Item Name</option>
                                                                                                @foreach ($products as $product)
                                                                                                    <option value="{{ $product->id }}"
                                                                                                        data-price="{{ $product->price }}"
                                                                                                        data-category="{{ $product->category_id }}"
                                                                                                        data-gst-option="{{ $product->gst_option }}"
                                                                                                        data-gst='{!! $product->product_gst !!}'>
                                                                                                        {{ $product->name }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                            <div class="product-gst-info mt-1"></div>
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-sm-6 col-6 price-col">
                                                                                        <div class="form-group">
                                                                                            <label>Item Price</label>
                                                                                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price">
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-1 col-sm-6 col-6 qty-col">
                                                                                        <div class="form-group">
                                                                                            <label> Qty</label>
                                                                                            <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="1" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-1 col-sm-6 col-6 disc-pct-col">
                                                                                        <div class="form-group">
                                                                                            <label>Disc%</label>
                                                                                            <input type="number" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0" min="0" max="100" oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-1 col-sm-6 col-6 disc-amt-col">
                                                                                        <div class="form-group">
                                                                                            <label>Disc Amt</label>
                                                                                            <input type="number" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                                                                            <span class="error text-danger"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-sm-12 col-12 total-col">
                                                                                        <div class="form-group">
                                                                                            <label>Total Amount</label>
                                                                                            <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-1 col-sm-12 add-row-btn">
                                                                                        <button type="button" class="btn btn-danger remove-row">-</button>
                                                                                    </div>
                                                                                </div>`;

                let newRow = $(row).appendTo("#form-container");

                // Reinitialize Select2 for dynamically added elements
                newRow.find(".select2, .category-select").select2({});

                // Scroll to the new row
                newRow[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            // Remove form row dynamically
            $(document).on("click", ".remove-row", function() {
                $(this).closest(".form-row").remove();
                calculateTotal();
            });

            // Event Listeners
            $(document).on('change', '.category-select', function() {
                var selectedCategory = $(this).val();

                var $row = $(this).closest('.form-row'); // find the row of this select
                var $productDropdown = $row.find('.product-select');

                // Clear current options
                $productDropdown.empty();

                if (selectedCategory) {
                    $productDropdown.prop('disabled', false);

                    $productDropdown.append('<option value="">Item Name</option>');

                    // Filter products by category
                    var filteredProducts = products.filter(p => p.category_id ==
                        selectedCategory);

                    filteredProducts.forEach(function(product) {
                        $productDropdown.append(
                            `<option value="${product.id}"
                                    data-price="${product.price}"
                                    data-gst-option="${product.gst_option}"
                                    data-gst='${JSON.stringify(product.product_gst)}'>
                                    ${product.name}
                                </option>`
                        );
                    });
                } else {
                    $productDropdown.prop('disabled', true);
                    $productDropdown.append('<option value="">Item Name</option>');
                }

                // Refresh Select2
                $productDropdown.select2('destroy').select2();
            });

            // Delegated change event for product-select
            $(document).on('change', '.product-select', function() {
                let selectedOption = $(this).find("option:selected");
                let price = selectedOption.data("price") || 0;

                let $row = $(this).closest('.form-row');
                $row.find(".price-input").val(parseFloat(price)).trigger("input");
                updateProductGstInfo($row);
            });

            $(document).on('change', 'input[name="gst_option"]', function() {

                $(".form-row").each(function() {

                    let row = $(this);

                    // update GST display
                    updateProductGstInfo(row);

                    // 🔹 recalc discount percent → amount
                    row.find(".product-discount-input").trigger("input");

                    // 🔹 recalc discount amount → percent
                    row.find(".product-discount-amount-input").trigger("input");

                });

                calculateTotal();
            });

            function formatIndianNumber(num) {
                if (isNaN(num) || num === null) return "0.00";
                return parseFloat(num).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function parseIndianNumber(str) {
                if (typeof str === 'number') return str;
                if (!str) return 0;
                return parseFloat(str.toString().replace(/,/g, '')) || 0;
            }

            function updateProductGstInfo($row) {
                let selectedOption = $row.find(".product-select option:selected");
                let gstOption = selectedOption.data("gst-option");
                let gstData = selectedOption.data("gst");
                let price = parseIndianNumber($row.find(".price-input").val()) || 0;
                let quantity = parseInt($row.find(".quantity-input").val()) || 0;
                let total = price * quantity;

                let $gstContainer = $row.find(".product-gst-info");
                $gstContainer.empty();

                // Check global toggle first
                if (!$('#with_gst').is(':checked')) {
                    return;
                }

                if (gstOption === 'with_gst' && gstData) {
                    try {
                        let taxes = gstData;
                        if (typeof taxes === "string") {
                            if (taxes === "undefined" || taxes === "") taxes = "[]";
                            taxes = JSON.parse(taxes);
                        }
                        if (typeof taxes === "string") taxes = JSON.parse(taxes);

                        if (Array.isArray(taxes)) {
                            let totalTaxAmount = 0;
                            let taxDetails = taxes.map(tax => {
                                let taxName = tax.tax_name || tax.name;
                                let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                let taxAmount = (total * taxRate) / 100;
                                totalTaxAmount += taxAmount;
                                return `${taxName}: ${taxRate}%`;
                            }).join(', ');

                            let gstHtml = `<div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 5px; border-radius: 4px; border-left: 3px solid #1b2850; margin-top: 5px;">
                                        <div><strong>Total GST: {{ $currencySymbol }}${formatIndianNumber(totalTaxAmount)}</strong></div>
                                        <div style="font-size: 10px;">(${taxDetails})</div>
                                    </div>`;
                            $gstContainer.html(gstHtml);
                        }
                    } catch (e) {
                        console.error("Error parsing GST data", e);
                    }
                } else if (gstOption === 'without_gst') {
                    $gstContainer.html(
                        '<small class="text-muted" style="font-size: 11px;">No GST for this Item</small>'
                    );
                }
            }

            $(document).on("input", ".price-input, .quantity-input, .product-discount-input", function() {
                let row = $(this).closest(".form-row");
                let price = parseIndianNumber(row.find(".price-input").val()) || 0;
                let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                let discountPercent = parseFloat(row.find(".product-discount-input").val()) || 0;

                let baseTotal = price * quantity;

                // Calculate GST first (to match create order logic)
                let gstAmount = 0;
                if ($('#with_gst').is(':checked')) {
                    let selectedOption = row.find(".product-select option:selected");
                    let gstData = selectedOption.data("gst");
                    if (gstData) {
                        try {
                            let taxes = gstData;
                            if (typeof taxes === "string") {
                                if (taxes === "undefined" || taxes === "") taxes = "[]";
                                taxes = JSON.parse(taxes);
                            }
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);

                            if (Array.isArray(taxes)) {
                                taxes.forEach(tax => {
                                    let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                    gstAmount += (baseTotal * taxRate) / 100;
                                });
                            }
                        } catch (e) {}
                    }
                }

                let totalWithGst = baseTotal + gstAmount;
                let discountAmount = (totalWithGst * discountPercent) / 100;
                let finalRowTotal = totalWithGst - discountAmount;

                row.find(".product-discount-amount-input").val(formatIndianNumber(discountAmount));
                row.find(".total-input").val(formatIndianNumber(finalRowTotal));
                updateProductGstInfo(row);
                calculateTotal();
            });

            $(document).on("input", ".product-discount-amount-input", function() {
                let row = $(this).closest(".form-row");
                let price = parseIndianNumber(row.find(".price-input").val()) || 0;
                let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                let discountAmount = parseIndianNumber(row.find(".product-discount-amount-input").val()) ||
                    0;

                let baseTotal = price * quantity;

                // Calculate GST first (to match create order logic)
                let gstAmount = 0;
                if ($('#with_gst').is(':checked')) {
                    let selectedOption = row.find(".product-select option:selected");
                    let gstData = selectedOption.data("gst");
                    if (gstData) {
                        try {
                            let taxes = gstData;
                            if (typeof taxes === "string") {
                                if (taxes === "undefined" || taxes === "") taxes = "[]";
                                taxes = JSON.parse(taxes);
                            }
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);

                            if (Array.isArray(taxes)) {
                                taxes.forEach(tax => {
                                    let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                    gstAmount += (baseTotal * taxRate) / 100;
                                });
                            }
                        } catch (e) {}
                    }
                }

                let totalWithGst = baseTotal + gstAmount;

                // Avoid division by zero
                let discountPercent = 0;
                if (totalWithGst > 0) {
                    discountPercent = (discountAmount / totalWithGst) * 100;
                    if (discountPercent > 100) discountPercent = 100;
                    if (discountAmount > totalWithGst) {
                        discountAmount = totalWithGst;
                        $(this).val(formatIndianNumber(discountAmount));
                    }
                }

                let finalRowTotal = totalWithGst - discountAmount;

                row.find(".product-discount-input").val(discountPercent.toFixed(2));
                row.find(".total-input").val(formatIndianNumber(finalRowTotal));
                updateProductGstInfo(row);
                calculateTotal();
            });

            function calculateTotal() {
                let totalAmount = 0;
                let products = [];
                let taxTotals = {};
                let totalTaxAmount = 0;

                $(".form-row").each(function() {
                    let productOption = $(this).find(".product-select option:selected");
                    let productId = productOption.val();
                    let categoryId = $(this).find(".category-select").val();
                    let price = parseIndianNumber($(this).find(".price-input").val()) || 0;
                    let quantity = parseInt($(this).find(".quantity-input").val()) || 0;
                    let discountPercent = parseFloat($(this).find(".product-discount-input").val()) || 0;

                    let subTotal = price * quantity;
                    let rowGstAmount = 0;

                    // Product-wise GST calculation
                    if ($('#with_gst').is(':checked')) {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") {
                                    if (taxes === "undefined" || taxes === "") taxes = "[]";
                                    taxes = JSON.parse(taxes);
                                }
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);

                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxName = tax.tax_name || tax.name;
                                        let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                        let taxAmount = (subTotal * taxRate) / 100;
                                        rowGstAmount += taxAmount;
                                        totalTaxAmount += taxAmount;

                                        if (!taxTotals[taxName]) {
                                            taxTotals[taxName] = {
                                                id: tax.tax_id || tax.id,
                                                rate: taxRate,
                                                amount: 0
                                            };
                                        }
                                        taxTotals[taxName].amount += taxAmount;
                                    });
                                }
                            } catch (e) {
                                console.error("Error parsing GST data in calculation", e);
                            }
                        }
                    }

                    let rowTotalWithGst = subTotal + rowGstAmount;
                    let discountAmount = (rowTotalWithGst * discountPercent) / 100;
                    let finalRowTotal = rowTotalWithGst - discountAmount;

                    if (productId) {
                        products.push({
                            id: productId,
                            category_id: categoryId,
                            price: price,
                            quantity: quantity,
                            discount_percent: discountPercent,
                            discount_amount: discountAmount,
                            total: finalRowTotal,
                        });
                    }

                    totalAmount += subTotal;
                    $(this).find(".total-input").val(formatIndianNumber(finalRowTotal));
                });

                // ✅ Update Total Product Amount
                $("#total-product-amount").text(formatIndianNumber(totalAmount));

                let shipping = parseIndianNumber($("#shipping").val()) || 0;

                if ($('#with_gst').is(':checked')) {
                    $('#gst-section').show();
                    // $("#total-gst-amount").text(totalTaxAmount.toFixed(2));
                    $("#total-gst-amount").text(formatIndianNumber(totalTaxAmount));
                } else {
                    $('#gst-section').hide();
                    $("#total-gst-amount").text('0.00');
                }

                let grandTotal = totalAmount + shipping + totalTaxAmount;

                // Subtract individual item discounts from grand total
                let totalDiscountAmount = 0;
                products.forEach(p => {
                    totalDiscountAmount += p.discount_amount;
                });
                grandTotal -= totalDiscountAmount;

                // $("#grand-total").text(grandTotal.toFixed(2));
                // $("#shipping-amount").text(shipping.toFixed(2));
                // $("#total-discount-amount").text(totalDiscountAmount.toFixed(2));
                // $("#price-after-discount").text((totalAmount - totalDiscountAmount).toFixed(2));

                $("#grand-total").text(formatIndianNumber(grandTotal));
                $("#shipping-amount").text(formatIndianNumber(shipping));
                $("#total-discount-amount").text(formatIndianNumber(totalDiscountAmount));
                $("#price-after-discount").text(formatIndianNumber(totalAmount - totalDiscountAmount));

                return {
                    products: products,
                    totalProductAmount: totalAmount,
                    totalAmount: totalAmount,
                    taxAmount: totalTaxAmount,
                    taxTotals: taxTotals,
                    shipping: shipping,
                    grandTotal: grandTotal,
                    gstOption: $('#with_gst').is(':checked') ? 'with' : 'without'
                };
            }


            // ✅ Format price only when user leaves the field
            $(document).on("focus", ".price-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", ".price-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            $(document).on("focus", ".product-discount-amount-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", ".product-discount-amount-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            $(document).on("focus", "#shipping", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", "#shipping", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            // ✅ Recalculate when inputs change
            $(document).on("input", "#shipping", function() {
                calculateTotal();
            });
            calculateTotal();


            function validateForm() {
                let isValid = true;

                // Clear previous errors
                $(".error").text("");

                // Validate Vendor
                if ($("#vendor_name").val() === "") {
                    $("#vendor_name").closest(".form-group").find(".error").text("Vendor is required.");
                    isValid = false;
                }
                  if ($("#bill_no").val() === "") {
                    $("#bill_no").closest(".form-group").find(".error").text("Bill Number is required.");
                    isValid = false;
                }

                // Validate Product Fields only if category is selected
                $(".form-row").each(function() {
                    let productSelect = $(this).find(".product-select");
                    let categorySelect = $(this).find(".category-select");
                    let priceInput = $(this).find(".price-input");
                    let quantityInput = $(this).find(".quantity-input");

                    let categoryVal = categorySelect.val();

                    // 🌟 If category is selected, validate product, price, and quantity
                    if (categoryVal !== "") {
                        if (productSelect.val() === "") {
                            productSelect.closest(".form-group").find(".error").text(
                                "Product is required.");
                            isValid = false;
                        }
                        if (priceInput.val().trim() === "" || parseIndianNumber(priceInput.val()) <= 0) {
                            priceInput.closest(".form-group").find(".error").text(
                                "Valid price is required.");
                            isValid = false;
                        }
                        if (quantityInput.val().trim() === "" || parseInt(quantityInput.val()) <= 0) {
                            quantityInput.closest(".form-group").find(".error").text(
                                "Valid quantity is required.");
                            isValid = false;
                        }
                    }
                });

                // Validate Discount & Shipping
                let discount = parseFloat($("#discount").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val()) || 0;

                if (discount < 0 || discount > 100) {
                    $("#discount").closest(".form-group").find(".error").text("Discount must be between 0-100%.");
                    isValid = false;
                }
                if (shipping < 0) {
                    $("#shipping").closest(".form-group").find(".error").text("Shipping cost cannot be negative.");
                    isValid = false;
                }

                // Validate Status
                if ($("select[name='status']").val() === "") {
                    $("select[name='status']").closest(".form-group").find(".error").text(
                        "Please select a status.");
                    isValid = false;
                }


                return isValid;
            }
            // ✅ Function to load purchase data
            function loadPurchaseData(invoiceId) {
                var authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                $.ajax({
                    url: "/api/purchase_get/" + invoiceId,
                    type: "GET",
                    data: {
                        selectedSubAdminId: selectedSubAdminId
                    },
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.success) {
                            let taxes = response.taxes; // ✅ Get taxes from API response
                            let data = response.data;
                            let invoice = response.invoice;

                            // console.log('xfdsf',invoice.bill_no);

                            $("#vendor_phone").val(data.vendor_phone);
                            $("#bill_no").val(invoice.bill_no);
                            // ✅ Set Shipping Cost
                            $("#shipping").val(formatIndianNumber(data.shipping));
                            $("#shipping-amount").text(formatIndianNumber(data.shipping));

                            // ✅ Set Purchase Status
                            $("select[name='status']").val(data.status);

                            // ✅ Set Total Product Amount
                            $("#total-product-amount").text(formatIndianNumber(data.total_amount));

                            // ✅ Populate vendor details
                            $("#vendor_name").val(data.vendor_id).trigger("change");
                            $(".payment_status").val(data.payment_status).trigger("change");
                            $("#vendor_phone").val(data.vendor_phone);

                            if (data.gst_option) {
                                let val = (data.gst_option === 'with_gst' || data.gst_option ===
                                    'with') ? 'with' : 'without';
                                $("input[name='gst_option'][value='" + val + "']")
                                    .prop("checked", true)
                                    .trigger("change"); // 🔥 This triggers GST toggle
                            }

                            $("#total-product-amount").text(parseFloat(data.total_amount).toFixed(2));

                            // ✅ Show Tax Amounts from DB
                            // ✅ Show Tax Amounts from DB
                            if (data.gst_option === "with_gst") {
                                $(".tax-section").show();

                                taxes.forEach(function(tax) {
                                    let $el = $("#tax-" + tax.id);
                                    if ($el.length) {
                                        $el.text(parseFloat(tax.amount).toFixed(2));
                                        $el.attr("data-rate", tax.rate); // keep rate synced
                                    }
                                });
                            } else {
                                $(".tax-section").hide();
                            }


                            // ✅ Show Grand Total from DB (already includes taxes)
                            $("#grand-total").text(parseFloat(data.grand_total).toFixed(2));


                            // ✅ Split product data
                            let productIds = data.product_ids.split(", ");
                            let productNames = data.product_names.split(", ");
                            let productPrices = data.product_prices.split(", ");
                            let productQuantities = data.product_quantities.split(", ");
                            let discountPercents = data.discount_percents ? data.discount_percents
                                .split(", ") : [];
                            let discountAmounts = data.discount_amounts ? data.discount_amounts.split(
                                ", ") : [];
                            let categoryIds = data.category_ids.split(", ");
                            let categoryNames = data.category_names.split(", ");
                            let productGstDetails = data.product_gst_details ? data.product_gst_details
                                .split("|||") : [];

                            // ✅ Clear previous product rows
                            $(".form-row").remove();
                            $("#form-container").empty();

                            // ✅ Loop through products & add rows
                            for (let i = 0; i < productIds.length; i++) {
                                let productObj = products.find(p => p.id == productIds[i]);
                                let gstOption = productObj ? productObj.gst_option : "";
                                let discountPercent = discountPercents[i] || 0;
                                let discountAmount = discountAmounts[i] || 0;

                                // Priority: 1. Saved GST details from purchase, 2. Master product GST
                                let gstData = "[]";
                                let itemGstTotal = 0;
                                if (productGstDetails[i] && productGstDetails[i] !== "null" &&
                                    productGstDetails[i] !== "[]") {
                                    gstData = productGstDetails[i];
                                    try {
                                        let parsedGst = JSON.parse(gstData);
                                        if (Array.isArray(parsedGst)) {
                                            let subTotal = parseFloat(productPrices[i]) * parseFloat(
                                                productQuantities[i]);
                                            parsedGst.forEach(tg => {
                                                itemGstTotal += (subTotal * parseFloat(tg
                                                    .tax_rate || tg.rate || 0)) / 100;
                                            });
                                        }
                                    } catch (e) {}
                                } else if (productObj && productObj.product_gst) {
                                    gstData = typeof productObj.product_gst === 'string' ?
                                        productObj.product_gst :
                                        JSON.stringify(productObj.product_gst);
                                    try {
                                        let parsedGst = JSON.parse(gstData);
                                        if (Array.isArray(parsedGst)) {
                                            let subTotal = parseFloat(productPrices[i]) * parseFloat(
                                                productQuantities[i]);
                                            parsedGst.forEach(tg => {
                                                itemGstTotal += (subTotal * parseFloat(tg
                                                    .tax_rate || tg.rate || 0)) / 100;
                                            });
                                        }
                                    } catch (e) {}
                                }

                                // Get all products for this category to populate dropdown
                                let categoryProducts = products.filter(p => p.category_id ==
                                    categoryIds[i]);
                                let productOptions = `<option value="">Item Name</option>`;

                                // If current product is not in master list (e.g. deleted or custom), add it manually
                                if (!categoryProducts.find(p => p.id == productIds[i])) {
                                    productOptions +=
                                        `<option value="${productIds[i]}" selected data-price="${productPrices[i]}" data-gst-option="${gstOption}" data-gst='${gstData}'>${productNames[i]}</option>`;
                                }

                                categoryProducts.forEach(p => {
                                    let selected = p.id == productIds[i] ? 'selected' : '';
                                    let pGst = typeof p.product_gst === 'string' ? p
                                        .product_gst : JSON.stringify(p.product_gst);
                                    productOptions +=
                                        `<option value="${p.id}" ${selected} data-price="${p.price}" data-gst-option="${p.gst_option}" data-gst='${pGst}'>${p.name}</option>`;
                                });

                                let buttonHtml =
                                    i === 0 ?
                                    `<button type="button" class="btn btn-success add-row">+</button>` :
                                    `<button type="button" class="btn btn-danger remove-row">-</button>`;

                                let subTotal = parseFloat(productPrices[i]) * parseFloat(
                                    productQuantities[i]);
                                let rowTotalWithGst = subTotal + itemGstTotal;
                                let discountAmt = (rowTotalWithGst * parseFloat(discountPercent)) / 100;
                                let finalRowTotal = rowTotalWithGst - discountAmt;

                                let rowHtml = `
                                    <div class="row form-row">
                                        <div class="col-lg-2 col-sm-6 col-6 category-col">
                                            <div class="form-group">
                                                <label>Category Name</label>
                                                <select name="category_name[]" class="form-control select2 category-select">
                                                    <option value="">Category Name</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" data-price="{{ $category->price }}">
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6 col-6 product-col">
                                            <div class="form-group">
                                                <label>Item Name</label>
                                                <select name="product_name[]" class="form-control select2 product-select">
                                                    ${productOptions}
                                                </select>
                                                <div class="product-gst-info mt-1"></div>
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6 col-6 price-col">
                                            <div class="form-group">
                                                <label>Item Price</label>
                                                <input type="text" name="price[]" class="form-control price-input"
                                                    value="${formatIndianNumber(productPrices[i])}">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-6 col-6 qty-col">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" name="quantity[]" class="form-control quantity-input"
                                                    value="${productQuantities[i]}" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-6 col-6 disc-pct-col">
                                            <div class="form-group">
                                                <label>Disc%</label>
                                                <input type="number" name="product_discount[]" class="form-control product-discount-input"
                                                    value="${discountPercent}" min="0" max="100" oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-6 col-6 disc-amt-col">
                                            <div class="form-group">
                                                <label>Disc-Amt</label>
                                                <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input"
                                                    value="${formatIndianNumber(discountAmount)}">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-12 col-12 total-col">
                                            <div class="form-group">
                                                <label>Total Amount</label>
                                                <input type="text" name="total[]" class="form-control total-input"
                                                    value="${formatIndianNumber(finalRowTotal)}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 add-row-btn">
                                            ${buttonHtml}
                                        </div>
                                    </div>`;

                                let $newRow = $(rowHtml).appendTo("#form-container");

                                // ✅ Set the category value
                                $newRow.find(".category-select").val(categoryIds[i]);

                                // ✅ Reinitialize Select2 for the new row
                                $newRow.find(".select2, .category-select").select2({});
                            }

                            $(".form-row").each(function() {
                                updateProductGstInfo($(this));
                            });
                            calculateTotal();

                        } else {
                            Swal.fire("Error!", "Purchase not found!", "error");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", "Purchase not found!", "error");
                        // window.location.href = "{{ route('purchase.lists') }}";
                    }
                });
            }

            $(document).on("click", ".btn-submit", function(e) {
                e.preventDefault();

                const $btn = $(this);
                const originalText = $btn.html();

                // Show loader
                $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                    )
                    .prop('disabled', true);

                if (!validateForm()) {
                    $btn.html(originalText).prop('disabled', false);
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                let formData = calculateTotal();
                formData.vendor_id = $("#vendor_name").val();
                formData.vendor_phone = $("#vendor_phone").val();
                formData.status = $("select[name='status']").val();
                formData.payment_status = $("select[name='payment_status']").val();
                formData.bill_no =$("#bill_no").val();

                // ✅ Collect Tax Data
                let taxArray = [];
                for (let taxName in formData.taxTotals) {
                    let taxData = formData.taxTotals[taxName];
                    taxArray.push({
                        name: taxName,
                        id: taxData.id,
                        rate: taxData.rate,
                        amount: taxData.amount,
                    });
                }

                formData.taxes = taxArray;

                $.ajax({
                    url: "/api/purchase_update/" + invoiceId,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken,
                    },
                    contentType: "application/json",
                    data: JSON.stringify({
                        vendor_id: formData.vendor_id,
                        vendor_phone: formData.vendor_phone,
                        status: formData.status,
                        payment_status: formData.payment_status,
                        discount: formData.discount,
                        shipping: formData.shipping,
                        bill_no:formData.bill_no,
                        grand_total: formData.grandTotal,
                        taxes: formData.taxes,
                        products: formData.products,
                        gst_option: formData.gstOption,
                        selectedSubAdminId: selectedSubAdminId
                    }),
                    success: function(response) {
                        // Reset button
                        $btn.html(originalText).prop('disabled', false);

                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: "Purchase updated successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // your custom color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('purchase.lists') }}";
                                }
                            });
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.html(originalText).prop('disabled', false);


                        console.error(xhr.responseText);
                    }
                });
            });



            $(document).on("input", ".quantity-input", function() {
                let value = parseFloat($(this).val()) || 0;
                if (value < 0) value = 0; // no negatives
                $(this).val(value);
            });




            calculateTotal();
        });
    </script>
@endpush
