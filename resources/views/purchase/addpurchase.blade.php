@extends('layout.app')

@section('title', 'Add Purchase')

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

        .manage_btn {
            color: #fff;
            background: #1b2850;
        }

        .manage_btn:hover {
            color: #e6e4e4ff;
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
        @media screen and (min-width: 1024px) and (max-width: 1366px) {

            .quantity-input,
            .product-discount-input,
            .product-discount-amount-input {
                min-width: 90px;
            }
        }
        @media screen and (min-width: 992px) and (max-width: 1366px) {
            .quantity-input,
            .product-discount-input,
            .product-discount-amount-input {
                min-width: 90px;
            }

            /* .form-row .col-lg-1 {
                flex: 0 0 11%;
                max-width: 11%;
            } */
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

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add Purchase</h4>
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
                                <select id="vendor_name" name="vendor_id" class="form-control select2 vendor-select w-100">
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
                                <label>Bill No.</label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control" placeholder="Bill No">
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
                            <input type="number" id="vendor_phone" name="phone" class="form-control" placeholder="Enter Phone">
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

                            <span class="error text-danger product-error"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-6 price-col">
                        <div class="form-group">
                            <label>Item Price</label>
                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price"
                                min="0" oninput="this.value = this.value < 0 ? 0 : this.value" inputmode="decimal"
                                step="0.01">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6 qty-col">
                        <div class="form-group">
                            <label>Qty</label>
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
                            <input type="text" name="product_discount_amount[]"
                                class="form-control product-discount-amount-input" placeholder="0.00" value="0"
                                min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-6 total-col">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" name="total[]" class="form-control total-input" placeholder="0"
                                readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 add-row-btn">
                        <button type="button" class="btn btn-success add-row">+</button>
                    </div>
                </div>
                <div id="form-container"></div>
                {{-- </div> --}}

                <div class="row">

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" name="shipping" id="shipping" class="form-control"
                                placeholder="0.00" min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Purchase Status</label>
                            <select name="status" class="form-control purchase-status-select">
                                <option value="">Choose Status</option>
                                <option value="pending">Pending</option>
                                <option value="partially">Partially</option>
                                <option value="completed">Completed</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-2 col-6">
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select name="payment_mode" id="payment_mode" class="form-control payment-mode-select">
                                <option value="">Select Payment Mode</option>
                                <option value="pending">Pending</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cashonline">Cash + Online</option>
                            </select>
                            <div class="text-danger error-payment_mode"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12 col-lg-2 d-none" id="paid_type_container">
                        <div class="form-group">
                            <label>Paid Type</label>
                            <select id="paid_type" name="paid_type" class="form-control paid-type-container">
                                <option value="">Select Paid Type</option>
                                <option value="full">Fully Paid</option>
                                <option value="partial">Partially Paid</option>
                            </select>
                            <div class="text-danger error-paid_type"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12 col-lg-2 d-none" id="cash_amount_input_container">
                        <div class="form-group">
                            <label>Cash Amount</label>
                            <input type="text" id="cash_amount_input" name="cash_amount" class="form-control"
                                placeholder="Enter Cash Amount">
                            <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="amount_input_container">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" id="amount_input" name="amount" class="form-control"
                                placeholder="Enter Amount">
                            <span class="text-danger error-cash-amount" id="amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="upi_amount_input_container">
                        <div class="form-group">
                            <label>Online Amount</label>
                            <input type="text" id="upi_amount_input" name="upi_amount" class="form-control"
                                placeholder="Enter Online Amount">
                            <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="pending_amount_container">
                        <div class="form-group">
                            <label>Pending Amount</label>
                            <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                                readonly>
                            <span id="pending_error" style="color:red; font-size:12px;"></span>
                        </div>
                    </div>

                    {{-- <div class="col-md-2 col-sm-12 col-lg-2 d-none" id="bank_container">
                        <div class="form-group">
                            <label>Select Bank</label>
                            <select name="bank_id" id="bank_id" class="form-control select2">
                                <option value="">Select Bank</option>

                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error-bank_id"></div>
                        </div>
                    </div> --}}
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

            // Initialize Select2
            $(".vendor-select,.product-select,.category-select,#bank_id,.payment-mode-select,.purchase-status-select,.paid-type-container")
                .select2({
                    tags: true,
                    width: '100%',
                });

            // ✅ Common formatting handlers
            // $(document).on("focus", ".price-input, .product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input", function() {
            //     let val = $(this).val();
            //     if (val) {
            //         $(this).val(parseIndianNumber(val) || '');
            //     }
            // });

            // $(document).on("blur", ".price-input, .product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input", function() {
            //     let val = $(this).val();
            //     if (val !== "" && !isNaN(parseIndianNumber(val))) {
            //         $(this).val(formatIndianNumber(parseIndianNumber(val)));
            //     }
            // });
            //  $(".select2, .category-select").select2({
            //     tags: true,
            // });
            let invalid = false;

            $('#upi_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#upi_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    let val = e.target.value;

                    // allow empty while typing
                    if (val === '') return;

                    // prevent negative numbers
                    if (parseIndianNumber(val) < 0) {
                        e.target.value = 0;
                    }
                }
            });
            $('#amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            $('#cash_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#cash_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });

            $('#payment_mode').prop('disabled', true);

            // Watch for changes in the payable amount field
            const target = document.getElementById('grand-total');

            const observer = new MutationObserver(() => {
                let amount = parseIndianNumber($('#grand-total').text());
                if (!isNaN(amount) && amount > 0) {
                    $('#payment_mode').prop('disabled', false);
                } else {
                    $('#payment_mode').val('').prop('disabled', true);
                }
            });

            observer.observe(target, {
                childList: true,
                characterData: true,
                subtree: true
            });

            $("#payment_mode").change(function() {
                const selectedMode = $(this).val();

                // Reset values
                $("#paid_type").val("");
                $("#bank_id").val("").trigger('change');
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");

                // Hide everything first
                $("#paid_type_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #bank_container")
                    .addClass("d-none");

                // ✅ ONLY show Paid Type for real payment modes
                if (selectedMode === "cash" || selectedMode === "online" || selectedMode === "cashonline") {
                    $("#paid_type_container").removeClass("d-none");
                }

                if (selectedMode === "online" || selectedMode === "cashonline") {
                    $("#bank_container").removeClass("d-none");
                }

                // ✅ Pending = no paid type, no amount, no validation
                if (selectedMode === "pending") {
                    return; // stop here
                }
            });


            $("#paid_type").change(function() {
                const type = $(this).val();
                const selectedMode = $("#payment_mode").val();

                // Clear all fields and errors
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#cash_amount_error, #upi_amount_error").text("");

                // Hide all input containers first
                $("#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container")
                    .addClass("d-none");

                // Remove required attributes initially
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").prop("required",
                    false);

                if (type === "full") {
                    invalid1 = false;
                    if (selectedMode === "cash") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "online") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Show both if cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                    }

                } else if (type === "partial") {
                    if (selectedMode === "cash" || selectedMode === "online") {
                        // Normal partial
                        $("#amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Partial with cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);
                    }
                }
            });

            $("#amount_input").on("input", function() {
                const partialPaid = parseIndianNumber($(this).val());
                const payable = parseIndianNumber($("#grand-total").text());
                const pending = payable - partialPaid;

                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");
            });
            let invalid1 = false;
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#cash_amount_input").val());
                const onlinePaid = parseIndianNumber($("#upi_amount_input").val());
                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid + onlinePaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending >= 0 ? formatIndianNumber(pending) : "0.00");


                // Validation
                if (pending < 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text("Enter correct amount cash + online"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            $("#amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#amount_input").val());

                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");

                // Validation
                if (pending <= 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text(
                        "Cannot enter more than payable amount"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            function parseIndianNumber(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            function formatIndianNumber(num) {
                num = parseFloat(num) || 0;
                return num.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function validateFullPayment() {
                const payable = parseIndianNumber($("#grand-total").text());
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const upi = parseIndianNumber($("#upi_amount_input").val());
                const total = cash + upi;

                // Clear previous messages
                $("#cash_amount_error").text("");
                $("#upi_amount_error").text("");

                if ($("#paid_type").val() === "full") {
                    if (total !== payable) {
                        const msg = `Cash + UPI must equal payable amount (₹${payable})`;
                        $("#cash_amount_error").text(msg);
                        $("#upi_amount_error").text(msg);
                        return false;
                    }
                }

                return true;
            }


            // Bind validation on input
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                validateFullPayment();
            });
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
                                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-category="{{ $product->category_id }}" data-gst-option="{{ $product->gst_option }}" data-gst='{!! $product->product_gst !!}'>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="product-gst-info mt-1"></div>
                                                        <span class="error text-danger product-error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-6 col-6 price-col">
                                                    <div class="form-group">
                                                        <label>Item Price</label>
                                                        <input type="text"  inputmode="decimal" name="price[]" class="form-control price-input" placeholder="Enter Price" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6 qty-col">
                                                    <div class="form-group">
                                                        <label>Qty</label>
                                                        <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="1" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6 disc-pct-col">
                                                    <div class="form-group">
                                                        <label>Disc %</label>
                                                        <input type="number" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0" min="0" max="100" oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6 disc-amt-col">
                                                    <div class="form-group">
                                                        <label>Disc Amt</label>
                                                        <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 col-6 total-col">
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
                newRow.find(".select2, .category-select").select2({
                    tags: true
                });

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

            // Update Vendor Phone when Vendor is selected
            $("#vendor_name").on("change", function() {
                let phone = $(this).find(":selected").data("phone");
                $("#vendor_phone").val(phone ? phone : "");
            });



            // Update Product Price when selecting a Product
            $(document).ready(function() {

                $('.category-select').on('change', function() {
                    var selectedCategory = $(this).val();
                    var $row = $(this).closest('.form-row');
                    var $productDropdown = $(this).closest('.col-lg-3, .col-sm-12').siblings().find(
                        '.product-select');

                    // Clear current options
                    let currentValue = $productDropdown.val();
                    $productDropdown.empty();
                    $row.find(".product-gst-info").empty();

                    if (currentValue && isNaN(currentValue)) {
                        // preserve typed product
                        $productDropdown.append(
                            `<option value="${currentValue}" selected>${currentValue}</option>`
                        );
                    }


                    if (selectedCategory) {
                        // Enable dropdown when category is selected
                        $productDropdown.prop('disabled', false);

                        // Add default placeholder
                        $productDropdown.append('<option value="">Item Name</option>');

                        // Filter products by category
                        var filteredProducts = products.filter(p => p.category_id ==
                            selectedCategory);

                        filteredProducts.forEach(function(product) {
                            // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                            let gstVal = (typeof product.product_gst === 'object') ? JSON
                                .stringify(product.product_gst) : product.product_gst;
                            $productDropdown.append(
                                `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                            );
                        });
                    } else {
                        // No category selected, disable product dropdown and add placeholder
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">Item Name</option>');
                    }

                    // Refresh Select2
                    $productDropdown.trigger('change.select2');
                });
            });



            $(document).ready(function() {
                // Delegated change event for category-select
                // $(document).on('change', '.category-select', function() {
                //     var selectedCategory = $(this).val();

                //     var $row = $(this).closest('.form-row'); // find the row of this select
                //     var $productDropdown = $row.find('.product-select');

                //     // Clear current options
                //     let currentValue = $productDropdown.val();
                //     $productDropdown.empty();
                //     $row.find(".product-gst-info").empty();

                //     if (currentValue && isNaN(currentValue)) {
                //         // preserve typed product
                //         $productDropdown.append(
                //             `<option value="${currentValue}" selected>${currentValue}</option>`
                //         );
                //     }

                //     if (selectedCategory) {
                //         $productDropdown.prop('disabled', false);

                //         $productDropdown.append('<option value="">Product Name</option>');

                //         // Filter products by category
                //         var filteredProducts = products.filter(p => p.category_id ==
                //             selectedCategory);

                //         filteredProducts.forEach(function(product) {
                //             // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                //             let gstVal = (typeof product.product_gst === 'object') ? JSON.stringify(product.product_gst) : product.product_gst;
                //             $productDropdown.append(
                //                 `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                //             );
                //         });
                //     } else {
                //         $productDropdown.prop('disabled', true);
                //         $productDropdown.append('<option value="">Product Name</option>');
                //     }

                //     // Refresh Select2
                //     $productDropdown.trigger('change.select2');
                // });
                $(document).on('change', '.category-select', function() {

                    let selectedCategory = $(this).val();
                    let $row = $(this).closest('.form-row');
                    let $productDropdown = $row.find('.product-select');
                    let $error = $row.find('.product-error');

                    // Reset
                    $productDropdown.empty();
                    $error.text('');
                    $row.find(".product-gst-info").empty();

                    // 👉 NEW CATEGORY (typed by user)
                    if (selectedCategory && isNaN(selectedCategory)) {
                        $productDropdown.prop('disabled', false);

                        $productDropdown.append('<option value="">Item Name</option>');
                        $productDropdown.select2({
                            tags: true
                        });

                        return; // stop here
                    }

                    // 👉 NO CATEGORY
                    if (!selectedCategory) {
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">Item Name</option>');
                        return;
                    }

                    // 👉 EXISTING CATEGORY
                    let filteredProducts = products.filter(p => p.category_id == selectedCategory);

                    $productDropdown.prop('disabled', false);
                    $productDropdown.append('<option value="">Item Name</option>');

                    if (filteredProducts.length === 0) {
                        // ❌ Category has NO products
                        $error.text('No Items available for this category');
                        return;
                    }

                    // ✅ Category has products
                    filteredProducts.forEach(product => {
                        let gstVal = typeof product.product_gst === 'object' ?
                            JSON.stringify(product.product_gst) :
                            product.product_gst;

                        $productDropdown.append(
                            `<option value="${product.id}"
                data-price="${product.price}"
                data-gst-option="${product.gst_option}"
                data-gst='${gstVal}'>
                ${product.name}
            </option>`
                        );
                    });

                    $productDropdown.trigger('change.select2');
                });




                // Delegated change event for product-select
                $(document).on('change', '.product-select', function() {

                    let selectedOption = $(this).find("option:selected");
                    let price = selectedOption.data("price") || 0;

                    let $row = $(this).closest('.form-row');

                    // set price
                    $row.find(".price-input").val(price);

                    // get quantity
                    let quantity = parseInt($row.find(".quantity-input").val()) || 0;

                    // calculate base total
                    let baseTotal = price * quantity;

                    // set total immediately
                    $row.find(".total-input").val(baseTotal.toFixed(2));

                    // update gst
                    updateProductGstInfo($row);

                    // recalc discount & gst properly
                    $row.find(".product-discount-input").trigger("input");

                    // update grand total
                    calculateTotal();
                });
                $(document).on("input", ".price-input", function() {
                    let val = this.value;

                    // allow only digits + one dot
                    val = val.replace(/[^0-9.]/g, '');
                    if ((val.match(/\./g) || []).length > 1) {
                        val = val.slice(0, -1);
                    }

                    this.value = val;

                    // calculate totals WITHOUT touching price again
                    let row = $(this).closest(".form-row");
                    let price = parseFloat(val) || 0;
                    let quantity = parseInt(row.find(".quantity-input").val()) || 0;

                    row.find(".total-input").val((price * quantity).toFixed(2));
                    updateProductGstInfo(row);
                    calculateTotal();
                });
                $(document).on("blur", ".price-input", function() {
                    let val = parseFloat(this.value);
                    if (!isNaN(val)) {
                        this.value = val.toFixed(2);
                    }
                });

                $(document).on("input", ".price-input, .quantity-input, .product-discount-input",
                    function() {
                        let row = $(this).closest(".form-row");
                        let price = parseFloat(row.find(".price-input").val()) || 0;
                        let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                        let discountPercent = parseFloat(row.find(".product-discount-input").val()) ||
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
                                    if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                    if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                    if (Array.isArray(taxes)) {
                                        taxes.forEach(tax => {
                                            let taxRate = parseFloat(tax.tax_rate) || 0;
                                            gstAmount += (baseTotal * taxRate) / 100;
                                        });
                                    }
                                } catch (e) {}
                            }
                        }

                        let amountForDiscount = $('#with_gst').is(':checked') ?
                            baseTotal + gstAmount :
                            baseTotal;

                        let discountAmount = (amountForDiscount * discountPercent) / 100;
                        let finalRowTotal = amountForDiscount - discountAmount;

                        row.find(".product-discount-amount-input").val(discountAmount.toFixed(2));
                        row.find(".total-input").val(finalRowTotal.toFixed(2));
                        updateProductGstInfo(row);
                        calculateTotal();
                    });

                $(document).on("input", ".product-discount-amount-input", function() {
                    let row = $(this).closest(".form-row");
                    let price = parseFloat(row.find(".price-input").val()) || 0;
                    let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                    let discountAmount = parseFloat(row.find(".product-discount-amount-input")
                        .val()) || 0;

                    let baseTotal = price * quantity;

                    // Calculate GST first (to match create order logic)
                    let gstAmount = 0;
                    if ($('#with_gst').is(':checked')) {
                        let selectedOption = row.find(".product-select option:selected");
                        let gstData = selectedOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        gstAmount += (baseTotal * taxRate) / 100;
                                    });
                                }
                            } catch (e) {}
                        }
                    }

                    let amountForDiscount = $('#with_gst').is(':checked') ?
                        baseTotal + gstAmount :
                        baseTotal;
                    let discountPercent = 0;
                    if (amountForDiscount > 0) {
                        discountPercent = (discountAmount / amountForDiscount) * 100;
                        // if (discountPercent > 100) discountPercent = 100;
                        // if (discountAmount > totalWithGst) {
                        //     discountAmount = totalWithGst;
                        //     $(this).val(discountAmount.toFixed(2));
                        // }
                    }
                    let finalRowTotal = amountForDiscount - discountAmount;
                    // let finalRowTotal = totalWithGst - discountAmount;

                    row.find(".product-discount-input").val(discountPercent.toFixed(2));
                    row.find(".total-input").val(finalRowTotal.toFixed(2));
                    updateProductGstInfo(row);
                    calculateTotal();
                });
                $('input[name="gst_option"]').on('change', function() {

                    $(".form-row").each(function() {

                        let row = $(this);

                        // GST info update
                        updateProductGstInfo(row);

                        // 🔹 recalc discount percent → amount
                        row.find(".product-discount-input").trigger("input");

                        // 🔹 recalc discount amount → percent
                        row.find(".product-discount-amount-input").trigger("input");

                    });

                    calculateTotal();
                });

                function updateProductGstInfo($row) {
                    let selectedOption = $row.find(".product-select option:selected");
                    let gstOption = selectedOption.data("gst-option");
                    let gstData = selectedOption.data("gst");
                    let price = parseFloat($row.find(".price-input").val()) || 0;
                    let quantity = parseInt($row.find(".quantity-input").val()) || 0;
                    let discountPercent = parseFloat($row.find(".product-discount-input").val()) || 0;

                    let baseTotal = price * quantity;

                    let $gstContainer = $row.find(".product-gst-info");
                    $gstContainer.empty();

                    // Check global toggle first
                    if (!$('#with_gst').is(':checked')) {
                        return;
                    }

                    if (gstOption === 'with_gst' && gstData) {
                        try {
                            let taxes = gstData;
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);

                            if (Array.isArray(taxes)) {
                                let totalTaxAmount = 0;
                                let taxDetails = taxes.map(tax => {
                                    let taxRate = parseFloat(tax.tax_rate) || 0;
                                    let taxAmount = (baseTotal * taxRate) / 100;
                                    totalTaxAmount += taxAmount;
                                    return `${tax.tax_name}: ${tax.tax_rate}%`;
                                }).join(', ');

                                let gstHtml = `<div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 5px; border-radius: 4px; border-left: 3px solid #1b2850; margin-top: 5px;">
                                    <div><strong>Total GST: {{ $currencySymbol }}${totalTaxAmount.toFixed(2)}</strong></div>
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
            });





            // Calculate Total when Price or Quantity is updated


            function calculateTotal() {
                let totalBaseAmount = 0;
                let totalDiscountAmount = 0;
                let totalTaxAmount = 0;
                let products = [];
                let taxTotals = {};

                $(".form-row").each(function() {
                    let productOption = $(this).find(".product-select option:selected");
                    let productId = productOption.val();
                    let categoryId = $(this).find(".category-select").val();
                    let price = parseIndianNumber($(this).find(".price-input").val());
                    let quantity = parseInt($(this).find(".quantity-input").val()) || 0;
                    let discountPercent = parseIndianNumber($(this).find(".product-discount-input").val());

                    let baseTotal = price * quantity;
                    let itemGstAmount = 0;

                    // Product-wise GST calculation
                    if ($('#with_gst').is(':checked')) {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);

                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxName = tax.tax_name;
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        let taxAmount = (baseTotal * taxRate) / 100;
                                        itemGstAmount += taxAmount;

                                        if (!taxTotals[taxName]) {
                                            taxTotals[taxName] = {
                                                id: tax.tax_id,
                                                rate: tax.tax_rate,
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

                    let totalWithGst = baseTotal + itemGstAmount;
                    let itemDiscountAmount = (totalWithGst * discountPercent) / 100;
                    let finalRowTotal = totalWithGst - itemDiscountAmount;

                    if (productId) {
                        products.push({
                            id: productId,
                            category_id: categoryId,
                            price: price,
                            quantity: quantity,
                            discount_percent: discountPercent,
                            discount_amount: itemDiscountAmount,
                            total: finalRowTotal,
                        });
                    }

                    totalBaseAmount += baseTotal;
                    totalTaxAmount += itemGstAmount;
                    totalDiscountAmount += itemDiscountAmount;

                    // $(this).find(".total-input").val(formatIndianNumber(finalRowTotal));
                    $(this).find(".total-input").val(finalRowTotal.toFixed(2));
                });

                // **Update Summary Display**
                $("#total-product-amount").text(formatIndianNumber(totalBaseAmount));

                // let shipping = parseFloat($("#shipping").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val());

                if ($('#with_gst').is(':checked')) {
                    $('#gst-section').show();
                    $("#total-gst-amount").text(formatIndianNumber(totalTaxAmount));
                } else {
                    $('#gst-section').hide();
                    $("#total-gst-amount").text('0.00');
                }

                // Grand Total = Base + GST - ItemDiscounts + Shipping
                let grandTotal = (totalBaseAmount + totalTaxAmount - totalDiscountAmount) + shipping;

                // $("#grand-total").text(formatIndianNumber(grandTotal));
                // $("#shipping-amount").text(formatIndianNumber(shipping));
                $("#grand-total").text(grandTotal.toFixed(2));
                $("#shipping-amount").text(shipping.toFixed(2));
                $("#total-discount-amount").text(formatIndianNumber(totalDiscountAmount));
                $("#price-after-discount").text(formatIndianNumber(totalBaseAmount - totalDiscountAmount));

                return {
                    products: products,
                    totalProductAmount: totalBaseAmount,
                    totalAmount: totalBaseAmount,
                    discount: 0, // General discount removed/not used here
                    discountAmount: totalDiscountAmount,
                    taxAmount: totalTaxAmount,
                    taxTotals: taxTotals,
                    shipping: shipping,
                    grandTotal: grandTotal,
                    gstOption: $('#with_gst').is(':checked') ? 'with' : 'without'
                };
            }

            // Update calculation when any relevant input changes
            $(document).on("input",
                "#discount, #shipping, .price-input, .quantity-input, .product-discount-input, .product-discount-amount-input",
                function() {
                    calculateTotal();
                });

            function validatePaymentSection() {
                $(".error-payment_mode, .error-paid_type").text("");
                $("#amount_error, #cash_amount_error, #upi_amount_error, #pending_error").text("");

                const paymentMode = $("#payment_mode").val();
                const paidType = $("#paid_type").val();
                const grandTotal = parseIndianNumber($("#grand-total").text());
                const amount = parseIndianNumber($("#amount_input").val());
                // const amount = parseFloat($("#amount_input").val()) || 0;
                const cash = parseIndianNumber($("#cash_amount_input"));
                const online = parseIndianNumber($("#upi_amount_input"));
                const totalPaid = cash + online;

                // ❌ Payment mode missing
                if (!paymentMode) {
                    $(".error-payment_mode").text("Payment mode is required");
                    return false;
                }

                // ✅ Pending → skip everything
                if (paymentMode === "pending") {
                    return true;
                }

                // ❌ Paid type required
                if (!paidType) {
                    $(".error-paid_type").text("Paid type is required");
                    return false;
                }

                // 🔸 PARTIAL
                if (paidType === "partial") {

                    if (paymentMode === "cash" || paymentMode === "online") {
                        if (amount <= 0) {
                            $("#amount_error").text("Amount is required");
                            return false;
                        }
                        if (amount >= grandTotal) {
                            $("#amount_error").text("Amount must be less than total");
                            return false;
                        }
                    }

                    if (paymentMode === "cashonline") {
                        if (cash <= 0) {
                            $("#cash_amount_error").text("Cash amount is required");
                            return false;
                        }
                        if (online <= 0) {
                            $("#upi_amount_error").text("Online amount is required");
                            return false;
                        }
                        if (totalPaid >= grandTotal) {
                            $("#pending_error").text("Cash + Online must be less than total");
                            return false;
                        }
                    }
                }

                // 🔸 FULL
                if (paidType === "full" && paymentMode === "cashonline") {
                    if (totalPaid !== grandTotal) {
                        $("#pending_error").text("Cash + Online must equal total");
                        return false;
                    }
                }

                // if ((paymentMode === "online" || paymentMode === "cashonline") && !$("#bank_id").val()) {
                //     $(".error-bank_id").text("Please select a bank");
                //     return false;
                // }

                return true;
            }

            // Form Validation
            function validateForm() {
                let isValid = true;

                // Clear previous errors
                $(".error").text("");

                // Validate Vendor
                if ($("#vendor_name").val() === "") {
                    $("#vendor_name").closest(".form-group").find(".error").text("Vendor is required.");
                    isValid = false;
                }
                if ($("#bill_no").val().trim() === "") {
                    $("#bill_no").closest(".form-group").find(".error").text("Bill number is required.");
                    isValid = false;
                }

                // Validate Product Fields
                $(".form-row").each(function() {
                    let productSelect = $(this).find(".product-select");
                    let categorySelect = $(this).find(".category-select");
                    let priceInput = $(this).find(".price-input");
                    let quantityInput = $(this).find(".quantity-input");
                    let discountInput = $(this).find(".product-discount-input");

                    if (productSelect.val() === "") {
                        productSelect.closest(".form-group").find(".error").text("Product is required.");
                        isValid = false;
                    }
                    if (categorySelect.val() === "") {
                        categorySelect.closest(".form-group").find(".error").text("Category is required.");
                        isValid = false;
                    }
                    if (priceInput.val().trim() === "" || parseFloat(priceInput.val()) <= 0) {
                        priceInput.closest(".form-group").find(".error").text("Valid price is required.");
                        isValid = false;
                    }
                    if (quantityInput.val().trim() === "" || parseInt(quantityInput.val()) <= 0) {
                        quantityInput.closest(".form-group").find(".error").text(
                            "Valid quantity is required.");
                        isValid = false;
                    }
                    if (discountInput.val().trim() !== "" && (parseFloat(discountInput.val()) < 0 ||
                            parseFloat(discountInput.val()) > 100)) {
                        discountInput.closest(".form-group").find(".error").text(
                            "Discount must be 0-100%.");
                        isValid = false;
                    }
                });

                // Validate Discount & Shipping
                let discount = parseFloat($("#discount").val()) || 0;
                // let shipping = parseFloat($("#shipping").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val());

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


            // Submit Form via AJAX
            $(".btn-submit").click(function(e) {
                e.preventDefault();

                let $btn = $(this);
                let originalContent = $btn.html();

                // Show spinner and disable button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).css("pointer-events", "none");

                if (!validateForm()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                if (!validatePaymentSection()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                let formData = calculateTotal();
                formData.vendor_id = $("#vendor_name").val();
                formData.vendor_phone = $("#vendor_phone").val();
                formData.status = $("select[name='status']").val();

                let taxes = [];
                let gstOption = $("input[name='gst_option']:checked").val();

                if (gstOption === "with") {
                    for (let taxName in formData.taxTotals) {
                        let taxData = formData.taxTotals[taxName];
                        taxes.push({
                            id: taxData.id,
                            name: taxName,
                            rate: taxData.rate,
                            amount: taxData.amount
                        });
                    }
                }
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                // Get GST option (radio)
                let gst_option = $("input[name='gst_option']:checked").val();
                // Get payment details
                let bill_no = $("#bill_no").val();
                let payment_mode = $("#payment_mode").val();
                let bank_id = $("#bank_id").val();
                let paid_type = $("#paid_type").val();
                let cash_amount = $("#cash_amount_input").val();
                let upi_amount = $("#upi_amount_input").val();
                let amount = null;

                if (paid_type === 'full') {
                    amount = formData.grandTotal;
                } else {
                    amount = $("#amount_input").val();
                }

                console.log(amount);
                console.log(paid_type);
                console.log(formData.grandTotal);
                let remaining_amount = $("#pending_amount").val();

                $.ajax({
                    url: "/api/purchase_order",
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
                        discount: formData.discount,
                        shipping: formData.shipping,
                        grand_total: formData.grandTotal,
                        products: formData.products,
                        taxes: taxes,
                        gst_option: gst_option,
                        payment_mode: payment_mode,
                        bank_id: bank_id,
                        bill_no: bill_no,
                        paid_type: paid_type,
                        cash_amount: cash_amount,
                        upi_amount: upi_amount,
                        amount: amount,
                        remaining_amount: remaining_amount,
                        selectedSubAdminId: selectedSubAdminId
                    }),
                    success: function(response) {
                        $btn.html(originalContent).css("pointer-events", "auto");

                        const purchaseId = response.purchase_id;

                        if (response.success) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Open the PDF in a new tab
                                    window.open("/purchase/invoice/pdf/" + purchaseId,
                                        "_blank");

                                    // Redirect to purchase view page
                                    window.location.href = "/print-purchase/" +
                                        purchaseId;
                                }
                            });
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.html(originalContent).css("pointer-events", "auto");
                        $(".error").text(""); // Clear previous

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            let message = xhr.responseJSON.message;

                            if (message.includes("phone number")) {
                                $("#vendor_phone").closest(".form-group").find(".error").text(
                                    message);
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: message,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        } else {
                            alert("Something went wrong!");
                        }

                        console.error(xhr.responseText); // For debugging
                    }

                });
            });
        });
    </script>
@endpush
