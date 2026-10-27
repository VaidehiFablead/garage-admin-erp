@extends('layout.app')

@section('title', 'Add Sale')

@section('content')
<style>
    .disabled-product {
        opacity: 0.6;
        pointer-events: none;
        /* disables clicking inside */
    }

    .paymentmethod.active {
        /* background-color: #1b2850; */
        color: white;
        /* border-radius: 5px; */
    }

    .paymentmethod.active:hover {
        /* background-color: white; */
        color: white;
        /* Dark text for contrast */
        /* border: 1px solid #1b2850; */
        /* Optional: border to define shape */
    }

    .tabs_wrapper ul.tabs {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding: 0;
        margin: 0;
        list-style: none;
        scrollbar-width: thin;
        width: auto;
        scrollbar-color: #ff9f43 #f1f1f1;
    }

    .tabs_wrapper ul.tabs::-webkit-scrollbar {
        display: none;
    }

    .tabs_wrapper ul.tabs li {
        flex: 0 0 auto;
        cursor: pointer;
        white-space: nowrap;
        width: auto;
        /* ðŸ‘ˆ Prevent tab name from wrapping */
        padding: 0;
    }

    .product-details {
        background: #fff;
        padding: 10px 16px;
        box-shadow: none;
        border: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: 0.3s ease;
        min-width: max-content;
        /* ðŸ‘ˆ Ensure box width fits content */
    }

    .product-details h6 {
        font-size: 14px;
        color: #000;
        margin: 0;
        text-align: center;
        white-space: nowrap;
        /* ðŸ‘ˆ Prevent text wrap */
        overflow: hidden;
        text-overflow: ellipsis;
        /* ðŸ‘ˆ Optional: show ... if too long */
        max-width: 100%;
        /* ðŸ‘ˆ Avoid overflow outside parent */
    }

    .tabs_wrapper ul.tabs li.active .product-details {
        background: transparent;
        /* border-bottom: 2px solid #ff9f43 !important; */
    }

    .tabs_wrapper ul.tabs li.active .product-details h6 {
        color: #ff9f43;
    }

    .payment_panel {
        position: fixed;
        bottom: 0;
        background-color: white;
        width: 32%;
    }

    .body_space {
        /*margin-bottom: 2rem;*/
        padding: 9px !important;
    }

    .paymentmethod.active {
        background-color: #1b2850;
        color: #ffffff;
    }

    .paymentmethod.active svg {
        fill: #ffffff;
    }

    .productsetimgin {
        height: 100px;
        object-fit: contain;
        margin: 1rem auto 0;
        display: block;
    }

    .productsetbtn button {
        display: none;
    }

    .search_custom_product {
        width: 100%;
    }

    .payment_panel {
        z-index: 999;
    }

    .bill-generate {
        color: #fff;
        background: #1b2850;
        border-radius: 5px;
        margin: 0 0 15px;
        padding: 12px;
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        .payment_panel {
            position: fixed;
            bottom: 50px;
            background-color: white;
            width: 80%;
            margin-left: -6px;
        }

        .productset {
            display: flex;
        }

        .productset .productsetimg {

            width: 30%;
        }

        .productsetimgin {
            object-fit: cover;
            height: 90px;
            margin: 0;
            max-width: 110px;
        }

        .productsetcontent {
            text-align: left !important;
        }



        .productsetbtn {
            position: absolute;
            right: 10px;
            top: 43px;
        }

        .productsetbtn button {
            display: block;
            width: 100%;
            height: 33px;
            background-color: #1b2850;
            color: white !important;
            border: none;
            border-radius: 5px;
        }

        .productsetbtn button:active {
            color: white;
        }

        .product-lists {
            flex-direction: row !important;
        }

        .body_space_two {
            margin-bottom: 4rem;
        }

        a.confirm-text.remove-item {
            /* font-size: 116px; */
            margin: 6px;
        }




    }
</style>
<style>
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
</style>
<div class="content">

    <div class="row">

        <div class="gst-header" style="display: flex; align-items: center; justify-content: space-between;">

            <div class="d-flex">
                <label class="custom-radio-label" style="margin-right: 1rem;">
                    <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                    Without GST
                </label>

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="with_gst" value="with" />
                    With GST
                </label>
            </div>

            <a href="{{ route('product.add') }}" class="btn btn-primary py-1"><i class="fas fa-plus me-1"></i> Add Items</a>

        </div>






        <div class="col-lg-7 col-sm-12 tabs_wrapper">

            <ul class="tabs border-0 mb-4">
                @php
                $hasProducts = false;
                @endphp

                @foreach($categories as $cat)
                @php
                $availableProducts = $cat->products->where('quantity', '>', 0);
                @endphp

                @if($availableProducts->count())
                @php
                $hasProducts = true;
                @endphp
                <li id="{{ $cat->id }}">
                    <div class="product-details">
                        <h6 style="text-transform: capitalize;">{{ $cat->name }}</h6>
                    </div>
                </li>
                @endif
                @endforeach
            </ul>

            @if(!$hasProducts)
            <div class="text-center my-4">
                <h6 class="text-muted mb-3">No data available</h6>
                <a href="{{ route('product.add') }}" class="btn btn-primary py-1">
                    <i class="fas fa-plus me-1"></i> Add Items
                </a>
            </div>
            @endif

            <div class="tabs_container">

            </div>
        </div>
        <div class="col-lg-5 col-sm-12 ">

            <div class="card card-order">
                <div class="card-body body_space">
                    <div class="row" style="padding: 20px;">
                        <div class="col-lg-12">
                            <div
                                class="header-search d-flex align-items-center position-relative me-3 mb-3 search_custom_product">
                                <!-- Search Icon -->
                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/search.svg' }}" alt="Search"
                                    style="position: absolute; left: 12px; width: 18px; height: 18px; z-index: 10; opacity: 0.6;">

                                <!-- Input Field -->
                                <input type="text" id="customerSearch1"
                                    class="form-control form-control-sm rounded px-3 ps-5" placeholder="Search..."
                                    autocomplete="off" style="height: 38px; font-size: 14px;">

                                <!-- Search Results -->
                                <div id="searchResults1"
                                    class="list-group bg-white position-absolute rounded shadow mt-1 w-100"
                                    style="z-index: 1050; max-height: 300px; overflow-y: auto; display: none; top: 100%; left: 0;">
                                </div>
                            </div>
                            <div class="row">





                                <!-- Customer Section -->
                                <div class="col-md-6" id="customerSection" style="display:none;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label>Customer Name</label>
                                            <select id="customer_name" name="customer_name"
                                                class="form-control select2">
                                                <option value="">Select Customer</option>
                                                @foreach ($customers as $username)
                                                <option value="{{ $username->id }}" data-phone="{{ $username->phone }}"
                                                    data-group="{{ $username->group_by_price }}">
                                                    {{ $username->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="error_customername text-danger"></span>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-6" id="customerPhoneSection" style="display:none;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label>Customer Phone</label>
                                            <input type="tel" id="customer_phone" class="form-control"
                                                placeholder="Customer number" name="customer_phone">
                                            <span class="error_customerphone text-danger"></span>
                                        </div>
                                    </div>
                                </div>


                                <!-- Vendor Section -->
                                <div class="col-lg-6 col-sm-6 col-6" id="vendorDropdown" style="display: none;">
                                    <div class="form-group">
                                        <label>Vendor Name</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                                            <option value="">Select Vendor</option>
                                            @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}"
                                                data-type="{{ $vendor->group_by_price }}"> <!-- 👈 added vendor type -->
                                                {{ $vendor->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-vendor_id"></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-6" id="vendorPhoneSection" style="display: none;">
                                    <div class="form-group">
                                        <label>Vendor Phone</label>
                                        <input type="tel" id="vendor_phone" class="form-control"
                                            placeholder="Vendor number" name="vendor_phone">
                                        <span class="text-danger error-vendorphone"></span>
                                    </div>
                                </div>
                                 @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <div class="col-md-12 col-lg-4" id="staffSection"  style="overflow: hidden;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label>Staff Name</label>
                                            <select id="staff_name" name="staff_name" class="form-control select2">
                                                <option value="">Select Staff</option>
                                                @foreach ($staff as $username)
                                                <option value="{{ $username->id }}" data-phone="{{ $username->phone }}"
                                                    data-group="{{ $username->group_by_price }}">
                                                    {{ $username->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="error_staff text-danger"></span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <div class="col-md-12 col-lg-4" style="overflow: hidden;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label>Model</label>
                                            <select id="car_model" name="car_model" class="form-control select2">
                                                <option value="">Select Model</option>
                                                @foreach ($model as $username)
                                                <option value="{{ $username->id }}">
                                                    {{ $username->model_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="error_car_model text-danger"></span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <div class="col-md-12 col-lg-4" style="overflow: hidden;">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <label>Vehicle No.</label>
                                            <input type="text" id="vehicle_no" class="form-control"
                                            placeholder="Vehicle No." name="vehicle_no">
                                            <span class="error_vehicle_no text-danger"></span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group" style="display: none;">
                                        <label>User Type</label>
                                        <select name="user_type" id="user_type" class="form-control">
                                            <option value="customer">Customer</option>
                                            <option value="vendor">Vendor</option>
                                        </select>
                                        <span class="text-danger error-usertype"></span>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="split-card">

                    </div>
                    <div class="card-body pt-0">
                        <div class="totalitem">
                            <h4>Total items : 4</h4>
                            <a href="javascript:void(0);" class="clear_items">Clear all</a>
                        </div>
                        <span class="error_product text-danger"></span>
                        <div class="product-table">
                        </div>
                    </div>
                    <div class="split-card">
                    </div>
                    <div class="card-body pt-0 pb-2 body_space_two">

                        <div class="col-lg-12">
                            <div class="select-split ">
                                <div class="select-group w-100">
                                    <label for="discount">Discount (₹)</label>
                                    <input type="number" class="form-control" placeholder="Discount ₹" name="discount"
                                        id="discount">
                                    <span class="error_discount"></span>
                                </div>
                            </div>
                        </div>

                        <div class="setvalue">
                            <ul>
                                <li>

                                    <h5>Subtotal</h5>
                                    <h6 style="color: green;">
                                        @if($currency_position === 'right')
                                        <span class="subtotal-value">0.00</span>{{ $currency_symbol }}
                                        @else

                                        {{ $currency_symbol }}<span class="subtotal-value">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="subtotal" value="" class="tax-hidden">
                                </li>

                                <li>
                                    <h5>Discount Amount</h5>
                                    <h6 class="discount-amount">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="discount_amount" value="0.00" class="tax-hidden">
                                </li>

                                <li>
                                    <h5>Price After Discount</h5>
                                    <h6 class="price-after-discount">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="price_after_discount" value="0.00" class="tax-hidden">
                                </li>

                                @foreach($taxRates as $tax)
                                <li class="taxList">
                                    <h5>{{ $tax->tax_name }} ({{ $tax->tax_rate }}%) Tax</h5>
                                    <h6 class="tax-value" data-rate="{{ $tax->tax_rate }}"
                                        data-symbol="{{ $currency_symbol }}" data-position="{{ $currency_position }}">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>
                                @endforeach

                                <li class="total-value">
                                    <h5>Total</h5>
                                    <h6>
                                        @if($currency_position === 'right')
                                        <span class="total-amount">0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span class="total-amount">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="total" value="" class="tax-hidden">
                                </li>

                                <li class="error-message-container">
                                    <span class="error_total" style="color:red; display:none;"></span>
                                </li>
                            </ul>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="payment_date">Date</label>
                                    <input type="date" name="payment_date" id="payment_date" class="form-control"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="warranty_date">Warranty Date</label>
                                    <input type="date" name="warranty_date" id="warranty_date" class="form-control"
                                        value="{{ date('Y-m-d', strtotime('+3 years')) }}">
                                </div>
                            </div>
                            <!-- Paid By -->
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="paid_by">Paid By</label>
                                    <select name="paid_by" id="paid_by" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Online">Online</option>
                                        <option value="Cash_online">Cash + Online</option>
                                        <option value="EMI">EMI</option>
                                    </select>
                                </div>
                            </div>



                            <!-- Cash Payment Type -->
                            <div class="col-md-4" id="cash_type_container" style="display: none;">
                                <div class="form-group mb-2">
                                    <label for="cash_type">Cash Payment</label>
                                    <select name="cash_type" id="cash_type" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="cash_Fully">Fully</option>
                                        <option value="cash_partially">Partially</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Cash + Online Payment Type -->
                            <div class="col-md-4" id="cash_online_type_container" style="display: none;">
                                <div class="form-group mb-2">
                                    <label for="cash_online_type">Cash + Online</label>
                                    <select name="cash_online_type" id="cash_online_type" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="cash_online_Fully">Fully</option>
                                        <option value="cash_online_Partially">Partially</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fully -->
                            <div class="col-12" id="cash_online_fully_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cash_online_cash_amount">Cash Amount</label>
                                            <input type="number" name="cash_online_cash_amount"
                                                id="cash_online_cash_amount" class="form-control"
                                                placeholder="Enter cash amount">
                                            <span
                                                class="text-danger">{{ $errors->first('cash_online_cash_amount') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cash_online_upi_amount">Online Amount</label>
                                            <input type="number" name="cash_online_upi_amount"
                                                id="cash_online_upi_amount" class="form-control"
                                                placeholder="Enter online amount" readonly>
                                            <span
                                                class="text-danger">{{ $errors->first('cash_online_upi_amount') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Partially -->
                            <div class="col-12" id="cash_online_partial_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="cash_online_partial_cash">Cash Amount</label>
                                            <input type="number" name="cash_online_partial_cash"
                                                id="cash_online_partial_cash" class="form-control"
                                                placeholder="Enter cash amount">
                                            <span
                                                class="text-danger">{{ $errors->first('cash_online_partial_cash') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="cash_online_partial_upi">Online Amount</label>
                                            <input type="number" name="cash_online_partial_upi"
                                                id="cash_online_partial_upi" class="form-control"
                                                placeholder="Enter online amount">
                                            <span
                                                class="text-danger">{{ $errors->first('cash_online_partial_upi') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="cash_online_remaining">Remaining Amount</label>
                                            <input type="number" name="cash_online_remaining" id="cash_online_remaining"
                                                class="form-control" placeholder="Remaining amount" readonly>
                                            <span
                                                class="text-danger">{{ $errors->first('cash_online_remaining') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Cash Fully Payment Fields -->
                            <div class="col-12" id="cash_fully_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cash_amount">Cash Amount</label>
                                            <input type="number" name="cash_amount" id="cash_amount"
                                                class="form-control" placeholder="Enter cash amount">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="upi_amount">Online Amount</label>
                                            <input type="float" name="upi_amount" id="upi_amount" class="form-control"
                                                placeholder="Enter online amount" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash Partially Payment Fields -->
                            <div class="col-12" id="cash_partially_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cash_partial_amount">Cash Amount</label>
                                            <input type="number" name="cash_partial_amount" id="cash_partial_amount"
                                                class="form-control" placeholder="Enter cash amount" min="0">
                                            <span class="text-danger cash_partial_error d-none"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cash_remaining_amount">Remaining Amount</label>
                                            <input type="number" name="cash_remaining_amount" id="cash_remaining_amount"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>





                            <!-- Online Payment Type -->
                            <div class="col-md-4" id="online_type_container" style="display: none;">
                                <div class="form-group mb-2">
                                    <label for="online_type">Online Payment</label>
                                    <select name="online_type" id="online_type" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="Fully">Fully</option>
                                        <option value="Partially">Partially</option>
                                    </select>
                                </div>
                            </div>


                            <!-- This Payment (Grand Total) -->
                            <div class="col-md-4" id="full_payment_block">
                                <div class="form-group mb-2">
                                    <label for="grand_total_form" class="font-weight-bold">Total</label>
                                    <input type="number" step="0.01" name="grand_total" id="grand_total_form"
                                        class="form-control" value="0.00" readonly>
                                </div>
                            </div>
                        </div>


                        <!-- Partial & Remaining Amounts Together -->
                        <div class="row" id="partial_remaining_container" style="display: none;">
                            <!-- Partial Amount -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="partial_amount">Partial Amount</label>
                                    <input type="number" step="0.01" name="partial_amount" id="partial_amount"
                                        class="form-control" value="0.00" min="0">
                                </div>
                            </div>

                            <!-- Remaining Amount -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="remaining_amount">Remaining Amount</label>
                                    <input type="number" step="0.01" name="remaining_amount" id="remaining_amount"
                                        class="form-control" value="0.00" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- EMI Details (hidden by default) -->
                        <div class="row" id="emi_container" style="display: none;">
                            <!-- EMI Amount -->
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="emi_amount">EMI Amount</label>
                                    <input type="number" step="0.01" name="emi_amount" id="emi_amount"
                                        class="form-control" value="0.00">
                                </div>
                            </div>

                            <!-- EMI Duration -->
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="emi_duration">EMI Duration (Months)</label>
                                    <input type="number" id="emi_duration" name="emi_duration" class="form-control"
                                        min="1" max="12">
                                </div>
                            </div>

                            <!-- Calculated EMI -->
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="calculated_emi">Monthly Installment</label>
                                    <input type="number" step="0.01" name="calculated_emi" id="calculated_emi"
                                        class="form-control" value="0.00" readonly>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group mb-1">
                                <label for="remark">Remark</label>
                                <textarea name="remark" id="remark" class="form-control" rows="2"
                                    placeholder="Enter remark"></textarea>
                            </div>
                        </div>


                        <div class="payment_panel">


                            <div class="row align-items-center mb-2">
                                 <div class="col-auto">
                                    <div class="bill-generate py-2">
                                        <input type="hidden" id="bill_generate" name="bill_generate" value="bill_generate">
                                        <button type="button" class="btn py-1" style=" background: #1b2850; color: white; ">
                                            Generate Bill
                                        </button>
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="btn-totallabel">
                                        <h6>Total Amount : 60.00$</h6>
                                        <h5>Pay Now <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"
                                                style="margin-bottom: 0.1rem;">
                                                <path fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                            </svg></h5>
                                    </div>
                                </div>
                               
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
    @push('js')

    <script>
        // let selectedItems = new Map();
        document.querySelectorAll('.paymentmethod').forEach(el => {
            el.addEventListener('click', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => radio.checked = false);
                this.querySelector('input[type="radio"]').checked = true;
                document.querySelectorAll('.paymentmethod').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });

        let groupPrices = [];
        let selectedItems = new Map();



        // Fetch group prices when customer/vendor changes
        $(document).on('change', '#customer_name, #vendor_name', function() {
            let authToken = localStorage.getItem("authToken");
            let selectedOption = $(this).find(":selected");
            let name = selectedOption.data('group');
            let userType = $("#user_type").val() || "";

            if (name) {
                $.ajax({
                    url: '/api/getproductpricebygroup',
                    type: 'GET',
                    data: {
                        name: name,
                        userType: userType
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            groupPrices = response.data; // save group prices

                            // Update selected items
                            selectedItems.forEach((item, key) => {
                                const groupProduct = groupPrices.find(p => p.product_id == String(item.productId));
                                // Use group price if available, otherwise keep original price
                                item.price = groupProduct ? parseFloat(groupProduct.price) : parseFloat(item.price || 0);
                            });

                            // Recalculate totals and update UI
                            calculateTotals();
                            renderSelectedItems();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                // If no customer selected, fallback: use each item's original price
                selectedItems.forEach((item, key) => {
                    item.price = parseFloat(item.originalPrice || 0);
                });

                // Recalculate totals and update UI
                calculateTotals();
                renderSelectedItems();
            }
        });


        function addOrUpdateItem(item) {
            // Find latest price from groupPrices
            let product = groupPrices.find(p => p.product_id == item.product_id);
            if (product) {
                item.price = parseFloat(product.price); // ensure numeric
            }

            selectedItems.set(item.product_id, item);
            updateTotalItems(); // recalc totals
            renderSelectedItems(); // update UI3
            printSelectedItems();
        }

        function printSelectedItems() {
            const arr = Array.from(selectedItems, ([id, item]) => ({
                productId: id,
                ...item
            }));
            console.log("Selected Items:", arr);
        }



        function calculateTotals() {
            const firstTaxElement = $(".tax-value").first();
            const currencySymbol = firstTaxElement.data("symbol") || '{{ $currency_symbol }}';
            const currencyPosition = firstTaxElement.data("position") || '{{ $currency_position }}';

            let subtotal = 0;

            selectedItems.forEach(item => {
                // Try to find group price for this exact product
                let groupProduct = groupPrices.find(gp => gp.product_id == String(item.productId) && gp.id == item.id);

                // Use group price if match found, else fallback to item's original price
                let price = groupProduct ? parseFloat(groupProduct.price) : parseFloat(item.price || 0);
                let quantity = parseInt(item.quantity) || 0;

                subtotal += price * quantity;
            });

            $(".setvalue li:nth-child(1) h6").html(formatCurrency(subtotal.toFixed(2), currencySymbol, currencyPosition));
            $("input[name='subtotal']").val(subtotal.toFixed(2));

            if (subtotal > 0) {
                let totalTax = 0;
                let gstOption = $("input[name='gst_option']:checked").val();

                let discountAmount = 0;
                const selectedGroup = $("#discount_group option:selected");
                if (selectedGroup.val()) {
                    const discountType = selectedGroup.data("type");
                    const discountValue = parseFloat(selectedGroup.data("value")) || 0;
                    discountAmount = discountType === "Percentage" ? subtotal * (discountValue / 100) : discountValue;
                } else {
                    // discountAmount = subtotal * (parseFloat($("#discount").val()) || 0) / 100;
                    discountAmount = parseFloat($("#discount").val()) || 0;
                }
                if (discountAmount > subtotal) discountAmount = subtotal;

                subtotal = subtotal - discountAmount;
                if (gstOption === "with") {
                    $(".taxList").each(function() {
                        const taxRate = parseFloat($(this).find(".tax-value").data("rate")) / 100;
                        const taxAmount = subtotal * taxRate;
                        const taxEl = $(this).find(".tax-value");
                        const symbol = taxEl.data("symbol") || currencySymbol;
                        const position = taxEl.data("position") || currencyPosition;
                        taxEl.html(formatCurrency(taxAmount.toFixed(2), symbol, position));
                        $(this).show();
                        totalTax += taxAmount;
                    });
                } else {
                    $(".taxList").hide().find(".tax-value").html(formatCurrency("0.00", currencySymbol, currencyPosition));
                    totalTax = 0;
                }

                
                

                const priceAfterDiscount = subtotal;
                const total = subtotal + totalTax;

                $(".discount-amount").html(formatCurrency(discountAmount.toFixed(2), currencySymbol, currencyPosition));
                $("input[name='discount_amount']").val(discountAmount.toFixed(2));
                $(".price-after-discount").html(formatCurrency(priceAfterDiscount.toFixed(2), currencySymbol, currencyPosition));
                $("input[name='price_after_discount']").val(priceAfterDiscount.toFixed(2));
                $(".total-value h6").html(formatCurrency(total.toFixed(2), currencySymbol, currencyPosition));
                $(".btn-totallabel h6").html('Total Amount : ' + formatCurrency(total.toFixed(2), currencySymbol, currencyPosition));
                $("input[name='total']").val(total.toFixed(2));
                $("input[name='grand_total']").val(total.toFixed(2));

            } else {
                $(".taxList").hide().find(".tax-value").html(formatCurrency("0.00", currencySymbol, currencyPosition));
                $(".discount-amount, .price-after-discount, .total-value h6, .btn-totallabel h6").html(formatCurrency("0.00", currencySymbol, currencyPosition));
                $("input[name='discount_amount'], input[name='price_after_discount'], input[name='total'], input[name='grand_total']").val("0.00");
            }
        }

        function renderSelectedItems() {
            let $productTable = $(".product-table");

            let productHtml = "";


            selectedItems.forEach(item => {
                productHtml += `
                                <ul class="product-lists">
                                    <li>
                                        <div class="productimg">
                                            <div class="productimgs">
                                                <img src="${item.image}" alt="${item.name}">
                                            </div>
                                            <div class="productcontet">
                                                <h4 style="text-transform: capitalize;">${item.name}
                                                    <a href="javascript:void(0);" class="ms-2 edit-item" data-id="${item.id}">
                                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit-5.svg' }}" alt="edit">
                                                    </a>
                                                </h4>
                                                <div class="increment-decrement">
                                                    <div class="input-groups">
                                                        <input type="button" value="-" class="button-minus dec button" data-id="${item.id}">
                                                        <input type="text" name="quantity" value="${item.quantity}" class="quantity-field" data-id="${item.id}" data-stock="${item.stock}">
                                                        <input type="button" value="+" class="button-plus inc button" data-id="${item.id}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="price">${item.price}</li>
                                    <li>
                                        <a class="confirm-text remove-item" href="javascript:void(0);" data-id="${item.id}">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete-2.svg' }}" alt="delete">
                                        </a>
                                    </li>
                                </ul>`;
            });

            $productTable.html(productHtml);
        }

        function formatCurrency(amount, symbol, position) {
            return position === 'right' ? amount + symbol : symbol + amount;
        }

        $(document).ready(function() {
            selectedItems = new Map();
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            // Initialize tax rows as hidden on page load
            $(".taxList").hide();

            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            $(document).on("change", "#discount_group", function() {
                calculateTotals();
            });

            $(document).on("change", "input[name='gst_option']", function() {
                calculateTotals();
            });


            function updateItemPrice(productId) {
                // Find the product in your groupPrices
                let product = groupPrices.find(p => p.product_id == productId);

                console.log("Updating price for product ID:", productId, "Found:", product);


                if (product) {
                    // Update the price in selectedItems
                    let item = selectedItems.find(i => i.product_id == productId);
                    if (item) {
                        item.price = product.price; // set correct price
                    }
                }

                calculateTotals(); // recalc totals
            }









            window.updateTotalItems = function() {
                $(".totalitem h4").text("Total items : " + selectedItems.size);
                renderSelectedItems();
                calculateTotals();
            }



            $(document).on("click", ".productset", function() {
                var $product = $(this);
                var productId = $product.data("id");
                var productName = $product.find("h4").text().trim();
                var productPrice = $product.find(".productsetcontent h6").text().trim();
                var categoryId = $product.find(".productsetcontent h1").text().trim();
                var productImage = $product.find("img").attr("src");
                var productCode = "PT001";
                var $checkIcon = $product.find(".check-product i");
                var productStock = parseInt($product.data("stock")) || 0;

                // Override price if group price exists
                let groupProduct = groupPrices.find(p => p.product_id == String(productId));

                // console.log("Clicked productId:", productId);
                // console.log("Searching in groupPrices:", groupPrices);
                // console.log("Matched groupProduct:", groupProduct);



                if (groupProduct) {
                    productPrice = parseFloat(groupProduct.price);
                } else {
                    productPrice = parseFloat(productPrice.replace(/[^0-9.]/g, '')) || 0; // remove any symbols and parse
                }

                var uniqueId = productId;
                selectedItems.set(uniqueId, {
                    id: uniqueId,
                    productId: productId,
                    categoryId: categoryId,
                    name: productName,
                    price: productPrice,
                    originalPrice: productPrice,
                    image: productImage,
                    code: productCode,
                    quantity: 1,
                    stock: productStock
                });

                $checkIcon.addClass("selected");
                updateTotalItems();
            });


            function updateTotalItems() {
                $(".totalitem h4").text("Total items : " + selectedItems.size);
                renderSelectedItems();
                calculateTotals();
            }

            // Format currency helper
            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            $(document).on("click", ".button-plus", function() {
                var itemId = $(this).data("id");
                if (selectedItems.has(itemId)) {
                    var item = selectedItems.get(itemId);
                    if (item.quantity < item.stock) {
                        item.quantity++;
                        selectedItems.set(itemId, item);
                        updateTotalItems();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Limit Reached',
                            text: 'Cannot exceed available stock (' + item.stock + ')',
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });

            $(document).on("change", ".quantity-field", function() {
                var itemId = $(this).data("id");
                var newQty = parseInt($(this).val());

                if (selectedItems.has(itemId)) {
                    var item = selectedItems.get(itemId);

                    if (newQty > item.stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Quantity Exceeded',
                            text: 'Only ' + item.stock + ' quantity are available.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        newQty = item.stock;
                    } else if (newQty < 1 || isNaN(newQty)) {
                        newQty = 1;
                    }

                    item.quantity = newQty;
                    selectedItems.set(itemId, item);
                    updateTotalItems();
                    $(this).val(newQty); // update input box with valid value
                }
            });

            $(document).on("click", ".button-minus", function() {
                var itemId = $(this).data("id");
                if (selectedItems.has(itemId) && selectedItems.get(itemId).quantity > 1) {
                    selectedItems.get(itemId).quantity--;
                    updateTotalItems();
                }
            });

            $(document).on("click", ".remove-item", function() {
                var itemId = $(this).data("id");
                var item = selectedItems.get(itemId);
                if (item) {
                    $(".productset[data-id='" + item.productId + "'] .check-product i").removeClass("selected");
                    selectedItems.delete(itemId);
                    updateTotalItems();
                }
            });

            $(".clear_items").click(function() {
                selectedItems.clear();
                $(".check-product i").removeClass("selected");
                updateTotalItems();
            });

            $("#discount").on("input", function() {
                const discountValue = parseFloat($(this).val());

                if ($(this).val() === "") {
                    // If empty, treat as 0 and remove error
                    $(".error_discount").text("");
                    $("#discount").val(""); // keep it blank for UX
                    calculateTotals(); // recalculate with 0 discount
                    return;
                }

                if (discountValue < 0) {
                    $(".error_discount").text("Discount must be greater 0").css("color", "red");
                    $(this).val("");
                } else if (isNaN(discountValue)) {
                    $(".error_discount").text("");
                } else {
                    $(".error_discount").text("Discount Applied").css("color", "green");
                    calculateTotals();
                }
            });

            $(".btn-totallabel").click(function(event) {

                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();


                // Reset errors
                $(".error_customername, .error_customerphone, .error_peymentmethod, .error_discount").text("");
                $(".total-value .error_total").remove();


                let userType = $("#user_type").val() || "";

                if (userType === "customer") {
                    let customerId = $("#customer_name").val();
                    let customerName = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = customerId ? customerId : customerName;


                } else if (userType === "vendor") {
                    let vendorId = $("#vendor_id").val();
                    let vendorName = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = vendorId ? vendorId : vendorName;
                }

                let selectedPayment = $("input[name='payment_method']:checked").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount = parseFloat($("#discount").val()) || 0;
                let grandTotal = parseFloat($("#grand_total_form").val()) || 0;
                let paidBy = $("#paid_by").val() || "";
                let cashType = $("#cash_type").val() || "";
                let onlineType = $("#online_type").val() || "";
                let cashPartial = $("#cash_partial_amount").val() || "";
                let partialAmount = $("#partial_amount").val() || "";
                let cashOnlineType = $("#cash_online_type").val() || "";
                let cashOnlineCash = $("#cash_online_cash_amount").val() || "";
                let cashOnlinePartialCash = $("#cash_online_partial_cash").val() || "";
                let cashOnlinePartialUpi = $("#cash_online_partial_upi").val() || "";
                let emiDuration = $("#emi_duration").val() || "";
                let remark = $("#remark").val() || "";
                let discount_group = $("#discount_group").val() || "";
                let gstOption = $('input[name="gst_option"]:checked').val() || "without";


                let isValid = true;

                // Reset errors
                $(
                    "#paid_by, #cash_type, #cash_partial_amount, #online_type, #partial_amount, #cash_online_type, #cash_online_cash_amount, #cash_online_partial_cash, #cash_online_partial_upi"
                ).removeClass("is-invalid");
                $(
                    ".paid_by_error, .cash_type_error, .cash_partial_error, .online_type_error, .partial_amount_error, .cash_online_type_error, .cash_online_cash_error, .cash_online_partial_cash_error, .cash_online_partial_upi_error"
                ).remove();

                $("#emi_duration").removeClass("is-invalid");
                $(".emi_duration_error").remove();

                $(".error_customername").text('');
                $(".error_customerphone").text('');


                // Reset previous validation
                $("#customer_name, #vendor_id, #customer_phone, #vendor_phone, #user_type").removeClass("is-invalid");
                $(".error_customername, .error-vendor_id, .error_customerphone, .error-vendorphone, .error-usertype").text("");

                if (userType === "customer") {
                    // Get values
                    name = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // Validate Customer Name
                    if (!$("#customer_name").val()) {
                        isValid = false;
                        $("#customer_name").addClass("is-invalid");
                        $(".error_customername").text("Please select a customer");
                    }

                    // Validate Customer Phone
                    if (!phone) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Please enter customer phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Enter a valid 10-digit phone number");
                    }

                } else if (userType === "vendor") {
                    // Get values
                    name = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // Validate Vendor Name
                    if (!$("#vendor_id").val()) {
                        isValid = false;
                        $("#vendor_id").addClass("is-invalid");
                        $(".error-vendor_id").text("Please select a vendor");
                    }

                    // Validate Vendor Phone
                    if (!phone) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Please enter vendor phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Enter a valid 10-digit phone number");
                    }

                } else {
                    isValid = false;
                    $("#user_type").addClass("is-invalid");
                    $(".error-usertype").text("Please select a user type (Customer or Vendor)");
                }



                $(".error_product").text('');

                // Check if at least one product exists
                let totalProducts = $(".product-table").children().length;
                if (totalProducts === 0) {
                    isValid = false;
                    $(".error_product").text("Please select at least one product.");
                }

                // ✅ Validate Paid By
                if (!paidBy) {
                    isValid = false;
                    $("#paid_by").focus().addClass("is-invalid")
                        .after('<span class="text-danger paid_by_error">Please select method</span>');
                }

                // ✅ Validate Cash
                if (paidBy === "Cash") {
                    if (cashType === "cash_fully") {
                        if (parseFloat(cashPartial) !== grandTotal) {
                            isValid = false;
                            $("#cash_partial_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_partial_error">Amount must equal total</span>');
                            $("#cash_partial_amount").focus();
                        }
                    } else if (cashType === "cash_partially") {
                        if (!cashPartial || cashPartial.trim() === "" || parseFloat(cashPartial) <= 0 || parseFloat(cashPartial) > grandTotal) {
                            isValid = false;
                            $("#cash_partial_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_partial_error">Please enter a valid partial amount</span>');
                            $("#cash_partial_amount").focus();
                        }
                    }
                }


                // ✅ Validate Online
                // if (paidBy === "Online") {
                //     if (!onlineType) {
                //         isValid = false;
                //         $("#online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger online_type_error">Select type</span>');
                //         $("#online_type").focus();
                //     } else if (onlineType === "Fully") {
                //         if (parseFloat(partialAmount) !== grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Amount must equal total</span>');
                //             $("#partial_amount").focus();
                //         }
                //     } else if (onlineType === "Partially") {
                //         if (!partialAmount || parseFloat(partialAmount) <= 0 || parseFloat(partialAmount) > grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Enter valid partial amount</span>');
                //             $("#partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Cash + Online
                if ($("#cash_online_type_container").is(":visible")) {
                    if (!cashOnlineType) {
                        isValid = false;
                        $("#cash_online_type")
                            .addClass("is-invalid")
                            .closest(".form-group")
                            .append('<span class="text-danger cash_online_type_error">Select type</span>');
                        $("#cash_online_type").focus();
                    } else if (cashOnlineType === "cash_online_Fully") {
                        if (parseFloat(cashOnlineCash) !== grandTotal) {
                            isValid = false;
                            $("#cash_online_cash_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_online_cash_error">Amount must equal total</span>');
                            $("#cash_online_cash_amount").focus();
                        }
                    } else if (cashOnlineType === "cash_online_Partially") {
                        let totalSplit = (parseFloat(cashOnlinePartialCash) || 0) + (parseFloat(cashOnlinePartialUpi) || 0);

                        if (totalSplit <= 0 || totalSplit > grandTotal) {
                            isValid = false;
                            $("#cash_online_partial_cash").addClass("is-invalid");
                            $("#cash_online_partial_upi").addClass("is-invalid");
                            $("#cash_online_partial_upi").closest(".form-group")
                                .append('<span class="text-danger cash_online_partial_upi_error">Split must be > 0 and ≤ total</span>');
                        }
                    }
                }

                // ✅ EMI validation
                if ($("#emi_container").is(":visible")) {
                    if (!emiDuration || parseInt(emiDuration) < 1 || parseInt(emiDuration) > 12) {
                        $("#emi_duration")
                            .addClass("is-invalid")
                            .closest(".form-group")
                            .append('<span class="text-danger emi_duration_error">1-12 months only</span>');
                        $("#emi_duration").focus();
                        return false;
                    }
                }

                if (!isValid) return false;

                // Show loading text and disable the button
                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...').prop('disabled', true);


                if (!selectedPayment) {
                    $(".error_peymentmethod").text("Please select a payment method").css("color", "red");
                    isValid = false;
                }



                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }





                // if (!isValid) return;
                // if (!isValid) {
                //     $btn.html(originalText).prop('disabled', false); // <-- important line
                //     return;
                // }




                // Prepare order items data
                let orderItems = [];
                selectedItems.forEach(function(item) {


                    let price = typeof item.price === "string" ?
                        parseFloat(item.price.replace(/[^0-9.-]+/g, "")) :
                        parseFloat(item.price) || 0;
                    orderItems.push({
                        product_id: item.productId,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        total: price * item.quantity
                    });
                });



                // Prepare tax data
                let taxes = [];
                $(".taxList").each(function() {

                    const taxName = $(this).find("h5").text().replace(" Tax", "");
                    const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                    const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                    taxes.push({
                        tax_name: taxName,
                        rate: taxRate,
                        amount: taxAmount
                    });
                });



                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                const remaining_amount = parseFloat($("#cash_online_remaining").val()) ||
                    parseFloat($("#cash_remaining_amount").val()) ||
                    parseFloat($("#remaining_amount").val()) || 0;


                let type; 

                if (cashType === "cash_partially" || onlineType === "Partially" || cashOnlineType === "cash_online_partially") {
                    type = "partially";
                } else if (cashType === "cash_Fully" || onlineType === "Fully" || cashOnlineType === "cash_online_Fully") {
                    type = "fully";
                } else {
                    type = "emi";
                }

                let orderData = {
                    user_id: name_or_id,
                    user_phone: phone,
                    payment_method: selectedPayment,
                    cash_type: $("#cash_type").val() || "",
                    car_model: $("#car_model").val() || "",
                    vehicle_no: $("#vehicle_no").val() || "",
                    warranty_date: $("#warranty_date").val() || "",
                    payment_date: $("#payment_date").val() || "",
                    staff_name: $("#staff_name").val() || "",
                    cash_partial_amount: $("#cash_partial_amount").val() || "",
                    online_type: $("#online_type").val() || "",
                    partial_amount: $("#partial_amount").val() || "",
                    cash_online_type: $("#cash_online_type").val() || "",
                    cash_online_cash_amount: $("#cash_online_cash_amount").val() || "",
                    cash_online_upi_amount: $("#cash_online_upi_amount").val() || "",
                    cash_online_partial_cash: $("#cash_online_partial_cash").val() || "",
                    cash_online_partial_upi: $("#cash_online_partial_upi").val() || "",
                    emi_amount: $("#emi_amount").val() || "",
                    emi_duration: $("#emi_duration").val() || "",
                    calculated_emi: $("#calculated_emi").val() || "",
                    method: paidBy,
                    type: type,
                    subtotal: subtotal,
                    discount: discount,
                    discount_amount: subtotal * (discount / 100),
                    remaining_amount: remaining_amount,
                    tax: taxes,
                    total: total,
                    items: orderItems,
                    selectedSubAdminId: selectedSubAdminId,
                    remark: remark,
                    gst_option: gstOption,
                    userType: userType

                };



                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open("/sales/invoice/pdf/" + orderId, "_blank");
                                    window.location.href = "/sales-invoice/" + orderId;
                                }
                            });
                            // Reset form
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']").val("");
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            $("#discount").val("");
                            updateTotalItems();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        // SweetAlert2 error popup
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error placing order. Please try again.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }

                });
            });

            $(".bill-generate").click(function(event) {
                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();


                // Reset errors
                $(".error_customername, .error_customerphone, .error_peymentmethod, .error_discount").text("");
                $(".total-value .error_total").remove();


                let userType = $("#user_type").val() || "";

                if (userType === "customer") {
                    let customerId = $("#customer_name").val();
                    let customerName = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = customerId ? customerId : customerName;


                } else if (userType === "vendor") {
                    let vendorId = $("#vendor_id").val();
                    let vendorName = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = vendorId ? vendorId : vendorName;
                }

                let selectedPayment = $("input[name='payment_method']:checked").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount = parseFloat($("#discount").val()) || 0;
                let grandTotal = parseFloat($("#grand_total_form").val()) || 0;
                let paidBy = $("#paid_by").val() || "";
                let cashType = $("#cash_type").val() || "";
                let onlineType = $("#online_type").val() || "";
                let cashPartial = $("#cash_partial_amount").val() || "";
                let partialAmount = $("#partial_amount").val() || "";
                let cashOnlineType = $("#cash_online_type").val() || "";
                let cashOnlineCash = $("#cash_online_cash_amount").val() || "";
                let cashOnlinePartialCash = $("#cash_online_partial_cash").val() || "";
                let cashOnlinePartialUpi = $("#cash_online_partial_upi").val() || "";
                let emiDuration = $("#emi_duration").val() || "";
                let remark = $("#remark").val() || "";
                let discount_group = $("#discount_group").val() || "";
                let gstOption = $('input[name="gst_option"]:checked').val() || "without";

                let bill_generate = $("#bill_generate").val() || "";




                let isValid = true;

                // Reset errors
                $(
                    "#paid_by, #cash_type, #cash_partial_amount, #online_type, #partial_amount, #cash_online_type, #cash_online_cash_amount, #cash_online_partial_cash, #cash_online_partial_upi"
                ).removeClass("is-invalid");
                $(
                    ".paid_by_error, .cash_type_error, .cash_partial_error, .online_type_error, .partial_amount_error, .cash_online_type_error, .cash_online_cash_error, .cash_online_partial_cash_error, .cash_online_partial_upi_error"
                ).remove();

                $("#emi_duration").removeClass("is-invalid");
                $(".emi_duration_error").remove();

                $(".error_customername").text('');
                $(".error_customerphone").text('');


                // Reset previous validation
                $("#customer_name, #vendor_id, #customer_phone, #vendor_phone, #user_type").removeClass("is-invalid");
                $(".error_customername, .error-vendor_id, .error_customerphone, .error-vendorphone, .error-usertype").text("");

                if (userType === "customer") {
                    // Get values
                    name = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // Validate Customer Name
                    if (!$("#customer_name").val()) {
                        isValid = false;
                        $("#customer_name").addClass("is-invalid");
                        $(".error_customername").text("Please select a customer");
                    }

                    // Validate Customer Phone
                    if (!phone) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Please enter customer phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Enter a valid 10-digit phone number");
                    }

                } else if (userType === "vendor") {
                    // Get values
                    name = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // Validate Vendor Name
                    if (!$("#vendor_id").val()) {
                        isValid = false;
                        $("#vendor_id").addClass("is-invalid");
                        $(".error-vendor_id").text("Please select a vendor");
                    }

                    // Validate Vendor Phone
                    if (!phone) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Please enter vendor phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Enter a valid 10-digit phone number");
                    }

                } else {
                    isValid = false;
                    $("#user_type").addClass("is-invalid");
                    $(".error-usertype").text("Please select a user type (Customer or Vendor)");
                }



                $(".error_product").text('');

                // Check if at least one product exists
                let totalProducts = $(".product-table").children().length;
                if (totalProducts === 0) {
                    isValid = false;
                    $(".error_product").text("Please select at least one product.");
                }

                // ✅ Validate Paid By
                if (!paidBy) {
                    isValid = false;
                    $("#paid_by").focus().addClass("is-invalid")
                        .after('<span class="text-danger paid_by_error">Please select method</span>');
                }

                // ✅ Validate Cash
                // if (paidBy === "Cash") {
                //     if (cashType === "cash_fully") {
                //         if (parseFloat(cashPartial) !== grandTotal) {
                //             isValid = false;
                //             $("#cash_partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_partial_error">Amount must equal total</span>');
                //             $("#cash_partial_amount").focus();
                //         }
                //     } else if (cashType === "cash_partially") {
                //         if (!cashPartial || cashPartial.trim() === "" || parseFloat(cashPartial) <= 0 || parseFloat(cashPartial) > grandTotal) {
                //             isValid = false;
                //             $("#cash_partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_partial_error">Please enter a valid partial amount</span>');
                //             $("#cash_partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Online
                // if (paidBy === "Online") {
                //     if (!onlineType) {
                //         isValid = false;
                //         $("#online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger online_type_error">Select type</span>');
                //         $("#online_type").focus();
                //     } else if (onlineType === "Fully") {
                //         if (parseFloat(partialAmount) !== grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Amount must equal total</span>');
                //             $("#partial_amount").focus();
                //         }
                //     } else if (onlineType === "Partially") {
                //         if (!partialAmount || parseFloat(partialAmount) <= 0 || parseFloat(partialAmount) > grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Enter valid partial amount</span>');
                //             $("#partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Cash + Online
                // if ($("#cash_online_type_container").is(":visible")) {
                //     if (!cashOnlineType) {
                //         isValid = false;
                //         $("#cash_online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger cash_online_type_error">Select type</span>');
                //         $("#cash_online_type").focus();
                //     } else if (cashOnlineType === "cash_online_Fully") {
                //         if (parseFloat(cashOnlineCash) !== grandTotal) {
                //             isValid = false;
                //             $("#cash_online_cash_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_online_cash_error">Amount must equal total</span>');
                //             $("#cash_online_cash_amount").focus();
                //         }
                //     } else if (cashOnlineType === "cash_online_Partially") {
                //         let totalSplit = (parseFloat(cashOnlinePartialCash) || 0) + (parseFloat(cashOnlinePartialUpi) || 0);

                //         if (totalSplit <= 0 || totalSplit > grandTotal) {
                //             isValid = false;
                //             $("#cash_online_partial_cash").addClass("is-invalid");
                //             $("#cash_online_partial_upi").addClass("is-invalid");
                //             $("#cash_online_partial_upi").closest(".form-group")
                //                 .append('<span class="text-danger cash_online_partial_upi_error">Split must be > 0 and ≤ total</span>');
                //         }
                //     }
                // }

                // ✅ EMI validation
                // if ($("#emi_container").is(":visible")) {
                //     if (!emiDuration || parseInt(emiDuration) < 1 || parseInt(emiDuration) > 12) {
                //         $("#emi_duration")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger emi_duration_error">1-12 months only</span>');
                //         $("#emi_duration").focus();
                //         return false;
                //     }
                // }

                if (!isValid) return false;

                // Show loading text and disable the button
                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...').prop('disabled', true);


                if (!selectedPayment) {
                    $(".error_peymentmethod").text("Please select a payment method").css("color", "red");
                    isValid = false;
                }



                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }





                // if (!isValid) return;
                // if (!isValid) {
                //     $btn.html(originalText).prop('disabled', false); // <-- important line
                //     return;
                // }




                // Prepare order items data
                let orderItems = [];
                selectedItems.forEach(function(item) {


                    let price = typeof item.price === "string" ?
                        parseFloat(item.price.replace(/[^0-9.-]+/g, "")) :
                        parseFloat(item.price) || 0;
                    orderItems.push({
                        product_id: item.productId,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        total: price * item.quantity
                    });
                });



                // Prepare tax data
                let taxes = [];
                $(".taxList").each(function() {

                    const taxName = $(this).find("h5").text().replace(" Tax", "");
                    const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                    const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                    taxes.push({
                        tax_name: taxName,
                        rate: taxRate,
                        amount: taxAmount
                    });
                });



                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                const remaining_amount = parseFloat($("#cash_online_remaining").val()) ||
                    parseFloat($("#cash_remaining_amount").val()) ||
                    parseFloat($("#remaining_amount").val()) || 0;

                    

                let type; // declare once

                if (cashType === "cash_partially" || onlineType === "Partially" || cashOnlineType === "cash_online_partially") {
                    type = "partially";
                } else if (cashType === "cash_Fully" || onlineType === "Fully" || cashOnlineType === "cash_online_Fully") {
                    type = "fully";
                } else {
                    type = "emi";
                }

                let orderData = {
                    user_id: name_or_id,
                    user_phone: phone,
                    payment_method: selectedPayment,
                    car_model: $("#car_model").val() || "",
                    vehicle_no: $("#vehicle_no").val() || "",
                    cash_type: $("#cash_type").val() || "",
                    warranty_date: $("#warranty_date").val() || "",
                    payment_date: $("#payment_date").val() || "",
                    staff_name: $("#staff_name").val() || "",
                    cash_partial_amount: $("#cash_partial_amount").val() || "",
                    online_type: $("#online_type").val() || "",
                    partial_amount: $("#partial_amount").val() || "",
                    cash_online_type: $("#cash_online_type").val() || "",
                    cash_online_cash_amount: $("#cash_online_cash_amount").val() || "",
                    cash_online_upi_amount: $("#cash_online_upi_amount").val() || "",
                    cash_online_partial_cash: $("#cash_online_partial_cash").val() || "",
                    cash_online_partial_upi: $("#cash_online_partial_upi").val() || "",
                    emi_amount: $("#emi_amount").val() || "",
                    emi_duration: $("#emi_duration").val() || "",
                    calculated_emi: $("#calculated_emi").val() || "",
                    method: paidBy,
                    type: type,
                    subtotal: subtotal,
                    discount: discount,
                    discount_amount: subtotal * (discount / 100),
                    remaining_amount: remaining_amount,
                    tax: taxes,
                    total: total,
                    items: orderItems,
                    selectedSubAdminId: selectedSubAdminId,
                    remark: remark,
                    gst_option: gstOption,
                    userType: userType,
                    bill_generate: bill_generate

                };


                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open("/sales/invoice/pdf/" + orderId, "_blank");
                                    window.location.href = "/sales-invoice/" + orderId;
                                }
                            });
                            // Reset form
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']").val("");
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            $("#discount").val("");
                            updateTotalItems();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        // SweetAlert2 error popup
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error placing order. Please try again.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }

                });
            });

            $(".owl-carousel").owlCarousel({
                items: 5,
                loop: false,
                nav: true,
                dots: false,
                margin: 10
            });

            // Handle category click
            $(".tabs li").click(function() {
                var categoryId = $(this).attr('id');


                // Remove active class from all tabs and tab contents
                $(".tabs li").removeClass('active');
                $(".tab_content").removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Find the corresponding tab content
                var tabContentSelector = '.tab_content[data-tab="' + categoryId + '"]';
                var $tabContent = $(tabContentSelector);

                if ($tabContent.length) {
                    $tabContent.addClass('active');


                    // Check if content already loaded
                    if ($tabContent.find('.productset').length === 0) {
                        loadProductsByCategory(categoryId);
                    }
                } else {

                    // Create new tab content if not found
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + categoryId + '"></div>');
                    loadProductsByCategory(categoryId);
                }
            });

            // Function to load products by category
            function loadProductsByCategory(categoryId) {
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let url = `/api/getProductsByCategory/${categoryId}`;

                if (selectedSubAdminId) {
                    url += `?sub_admin_id=${encodeURIComponent(selectedSubAdminId)}`;
                }

                $.ajax({
                    // url: "/api/getProductsByCategory/" + categoryId,
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        const currencySymbol = response.currency_symbol || 'Ã¢â€šÂ¹';
                        const currencyPosition = response.currency_position || 'left';


                        if (response.status) {
                            renderProducts(response.data, categoryId, currencySymbol, currencyPosition);
                        } else {
                            renderProducts([], categoryId, currencySymbol, currencyPosition);
                        }
                    },
                    error: function(xhr, status, error) {

                        renderProducts([], categoryId);
                    }
                });
            }

            // Function to render products
            function renderProducts(products, categoryId, currencySymbol, currencyPosition) {

                var productsHtml = '';

                if (products.length === 0) {
                    productsHtml = '<div class="col-12"><p>No products found in this category</p></div>';
                } else {
                    products.forEach(function(product) {
                        // Default image
                        let productImage = "{{ env('ImagePath') }}/admin/assets/img/product/noimage.png";
                        // console.log('ImagePath' + productImage);


                        // Handle different image formats
                        if (product.image) {
                            try {
                                // Clean the image string
                                let cleanImageString = product.image.replace(/\\/g, '');
                                cleanImageString = cleanImageString.replace(/^"(.*)"$/, '$1');

                                let imageBasePath = "{{ env('ImagePath') }}"; // or config('app.url') if you prefer

                                if (cleanImageString.startsWith('[')) {
                                    let imagesArray = JSON.parse(cleanImageString);
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        productImage = `${imageBasePath}/storage/${imagesArray[0]}`;
                                    }
                                } else {
                                    productImage = `${imageBasePath}/storage/${cleanImageString}`;
                                }
                            } catch (e) {

                            }
                        }

                        // Format price
                        let price = parseFloat(product.price || 0).toFixed(2);
                        let displayPrice = currencyPosition === 'right' ? `${price}${currencySymbol}` : `${currencySymbol}${price}`;

                        // Check if product is out of stock
                        let outOfStockBadge = '';
                        let disabledClass = '';
                        let addButtonDisabled = '';

                        if (Number(product.quantity) === 0) {
                            outOfStockBadge = `<div style="
                                                        position: absolute;
                                                        top: 10px;
                                                        left: 10px;
                                                        background: rgba(255,0,0,0.85);
                                                        color: white;
                                                        font-weight: 700;
                                                        padding: 2px 8px;
                                                        border-radius: 4px;
                                                        font-size: 12px;
                                                        z-index: 10;
                                                        ">Out of Stock</div>`;

                            disabledClass = 'disabled-product'; // optional styling class
                            addButtonDisabled = 'disabled'; // disable button
                        }

                        productsHtml += `
                                                <div class="col-lg-3 col-sm-6 d-flex position-relative">
                                                    <div class="productset flex-fill text-center ${disabledClass}" data-id="${product.id}" data-stock="${product.quantity}" style="position: relative;">
                                                        ${outOfStockBadge}
                                                        <div class="productsetimg">
                                                            <img src="${productImage}" alt="${product.name}" class="productsetimgin">
                                                            <h6>Qty: ${product.quantity || '0'}</h6>
                                                        </div>
                                                        <div class="productsetcontent" style="padding: 0.5rem;">
                                                            <h4 style="font-size: 14px; font-weight: 600; margin: 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                ${product.name}
                                                            </h4>
                                                            <h1 class="d-none">${product.categoryId || 'Uncategorized'}</h5>
                                                            <h5 style="text-transform: capitalize;">${product.category_name || 'Uncategorized'}</h5>
                                                            <h6>${displayPrice}</h6>        
                                                        </div>
                                                        <div class="productsetbtn">
                                                            <button class="btn btn-added" ${addButtonDisabled}>+ Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                    });
                }

                // Update the tab content
                var $tabContent = $('.tab_content[data-tab="' + categoryId + '"]');
                if ($tabContent.length) {
                    $tabContent.html('<div class="row">' + productsHtml + '</div>');

                } else {

                }
            }



            // Load products for the first category by default
            var firstCategory = $(".tabs li:first");
            if (firstCategory.length) {
                var firstCategoryId = firstCategory.attr('id');
                firstCategory.addClass('active');
                var firstTabContent = $('.tab_content[data-tab="' + firstCategoryId + '"]');

                if (firstTabContent.length) {
                    firstTabContent.addClass('active');
                } else {
                    // Create tab content if it doesn't exist
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + firstCategoryId + '"></div>');
                }

                // Load data for first category
                loadProductsByCategory(firstCategoryId);
            }


            // Initialize with empty state
            updateTotalItems();
        });
        $(".select2").select2({
            tags: true,
        });
        // $(document).ready(function() {
        $('#customer_name').on('change', function() {
            var phone = $(this).find(':selected').data('phone');
            $('#customer_phone').val(phone || '');
        });
        // });
        $(document).ready(function() {
            const $searchInput = $('#customerSearch1');
            const $resultBox = $('#searchResults1');
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            $searchInput.on('input', function() {
                const query = $(this).val().trim();

                if (query.length < 1) {
                    $resultBox.hide();
                    return;
                }

                $.ajax({
                    url: `/search-users`,
                    method: 'GET',
                    data: {
                        query: query,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $resultBox.empty();

                        let hasResults = false;

                        // Users section
                        // (add user search results here if needed)

                        // Products section
                        if (data.products && data.products.length > 0) {
                            hasResults = true;
                            $resultBox.append(`<div class="list-group-item fw-bold mt-2">Products</div>`);
                            data.products.forEach(product => {
                                $resultBox.append(`
                                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:30px; height:30px; object-fit: cover;">
                                                                <div>
                                                                    <strong>${product.name ?? 'N/A'}</strong><br>
                                                                    <small>Price: ${product.price ?? 'N/A'}</small>
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-sm btn-primary add-product" 
                                                                data-id="${product.id}" 
                                                                data-name="${product.name}" 
                                                                data-price="${product.price}" 
                                                                data-image="${product.image}" 
                                                                data-stock="${product.stock ?? 10}" 
                                                                data-category="${product.category_id ?? 0}">+ Add</button>
                                                        </div>
                                                    `);
                            });
                        }

                        // Orders section
                        // (add orders section if needed)

                        if (!hasResults) {
                            $resultBox.html('<div class="list-group-item">No results found</div>');
                        }

                        $resultBox.show();
                    },
                    error: function(xhr, status, error) {

                        $resultBox.hide();
                    }
                });
            });

            $(document).on("click", ".add-product", function() {
                const $btn = $(this);
                const productId = $btn.data("id");
                const productName = $btn.data("name");
                let productPrice = $btn.data("price"); // raw price (may be string)
                const productImage = $btn.data("image");
                const productStock = parseInt($btn.data("stock")) || 0;
                const categoryId = $btn.data("category");

                // 🔹 Ensure price is numeric (remove currency symbols / text)
                productPrice = parseFloat(productPrice?.toString().replace(/[^0-9.]/g, "")) || 0;

                // 🔹 Override price if group price exists
                const groupProduct = groupPrices.find(p => p.product_id == String(productId));
                if (groupProduct) {
                    productPrice = parseFloat(groupProduct.price) || 0;
                }

                // Check if product is already selected
                if (!selectedItems.has(productId)) {
                    selectedItems.set(productId, {
                        id: productId,
                        productId: productId,
                        categoryId: categoryId,
                        name: productName,
                        price: productPrice,
                        originalPrice: productPrice, // keep original safe copy
                        image: productImage,
                        code: "PT001", // Default code
                        quantity: 1,
                        stock: productStock
                    });

                    // Disable button & update label
                    $btn.prop("disabled", true).text("Added");

                    // Update UI & totals
                    updateTotalItems();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Added',
                        text: 'This product is already in the list!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Optional: Hide dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest($searchInput).length && !$(e.target).closest($resultBox).length) {
                    $resultBox.hide();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#full_payment_block').hide();

            // Master controller: Paid By selection
            $(document).ready(function() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                // ✅ Show Cash container & set default
                $('#cash_type_container').show();
                $('#cash_type').val('cash_Fully'); // default 'Fully'

                // Set Paid By dropdown to Cash
                $('#paid_by').val('Cash');

                // Show full payment block & set total
                $('#full_payment_block').show();
                $('#grand_total_form').val(total.toFixed(2));

                // Hidden input
                $('input[name="type"]').remove();
                $('<input>', {
                    type: 'hidden',
                    name: 'type',
                    id: 'payment_type_hidden'
                }).appendTo('#jobCardForm');

                // Existing change handler
                $('#paid_by').on('change', function() {
                    const paidBy = $(this).val();

                    // Hide all sections
                    $('#online_type_container, #cash_type_container, #cash_fully_fields, #cash_partially_fields, #emi_container, #partial_remaining_container, #full_payment_block, #cash_online_type_container, #cash_online_fully_fields, #cash_online_partial_fields').hide();

                    const total = parseFloat($("input[name='total']").val()) || 0;

                    if (paidBy === 'Cash') {
                        $('#cash_type_container').show();
                        $('#cash_type').val('cash_Fully'); // default again on change

                        $('#full_payment_block').show();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'Online') {
                        $('#online_type_container').show();
                        $('#online_type').trigger('change');

                        $('#full_payment_block').hide();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'Cash_online') {
                        $('#cash_online_type_container').show();
                        $('#cash_online_type').trigger('change');

                        $('#full_payment_block').hide();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'EMI') {
                        $('#emi_container').show();
                        $('#full_payment_block').hide();

                        $('#emi_amount').val(total.toFixed(2));
                        calculateMonthlyEMI();

                        $('input[name="type"]').remove();
                    }
                });
            });



            // Cash type change logic
            $('#cash_type').on('change', function() {
                const type = $(this).val();
                $('#cash_fully_fields').hide();
                $('#cash_partially_fields').hide();
                $('#partial_remaining_container').hide();
                $('#full_payment_block').hide();

                if (type === 'cash_Fully') {
                    $('#cash_fully_fields').hide();
                    $('#full_payment_block').show();

                    // ✅ Set "This Payment" to grand total immediately
                    const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                    $('#grand_total_form').val(grandTotal.toFixed(2));
                    $('#payment_type_hidden').val('cash_Fully');
                } else if (type === 'cash_partially') {
                    $('#full_payment_block').hide();
                    $('#cash_partially_fields').show();
                    $('#payment_type_hidden').val('cash_partially');
                }
            });

            // Online type logic
            $('#online_type').on('change', function() {
                const type = $(this).val();
                $('#partial_remaining_container').hide();
                $('#full_payment_block').hide();

                if (type === 'Fully') {
                    $('#full_payment_block').show();
                    $('#payment_type_hidden').val('Fully');
                } else if (type === 'Partially') {
                    $('#partial_remaining_container').show();
                    $('#payment_type_hidden').val('Partially');
                }
            });

            $('#cash_online_type').on('change', function() {
                const type = $(this).val();
                $('#cash_online_fully_fields, #cash_online_partial_fields').hide();

                if (type === 'cash_online_Fully') {
                    $('#cash_online_fully_fields').show();
                    const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                    $('#cash_online_cash_amount').val(grandTotal.toFixed(2));
                    $('#cash_online_upi_amount').val('0.00');
                    $('#full_payment_block').hide();
                    $('#payment_type_hidden').val('cash_online_Fully');

                } else if (type === 'cash_online_Partially') {
                    $('#cash_online_partial_fields').show();
                    $('#full_payment_block').hide();
                    $('#payment_type_hidden').val('cash_online_Partially');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            // ✅ Cash partially calculation
            $(document).on('input', '#cash_partial_amount', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;
                let partial = parseFloat($(this).val()) || 0;

                // ❌ Negative value allow mat karo
                if (partial < 0) {
                    partial = 0;
                    $(this).val(" ");
                }

                if (partial > total) {
                    $(this).val(total.toFixed(2));
                    $('#cash_remaining_amount').val(" ");
                } else {
                    const remaining = total - partial;
                    $('#cash_remaining_amount').val(remaining.toFixed(2));
                }
            });


            // ✅ Online partially calculation
            $(document).on('input', '#partial_amount', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;
                let partial = parseFloat($(this).val()) || 0;

                // ❌ Negative value allow mat kar
                if (partial < 0) {
                    partial = 0;
                    $(this).val("");
                }

                if (partial > total) {
                    $(this).val(total.toFixed(2));
                    $('#remaining_amount').val("");
                } else {
                    const remaining = total - partial;
                    $('#remaining_amount').val(remaining.toFixed(2));
                }
            });


            function calcCashOnlineFully() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                let cash = parseFloat($("#cash_online_cash_amount").val()) || 0;

                // ❌ Ab turant overwrite nahi karunga while typing
                // ✅ Sirf agar cash > total tabhi adjust karunga
                if (cash > total) {
                    cash = total;
                    $("#cash_online_cash_amount").val(cash.toFixed(2));
                } else if (cash < 0 || isNaN(cash)) {
                    cash = 0;
                    $("#cash_online_cash_amount").val("");
                }

                // Online = total - cash
                const online = total - cash;
                $("#cash_online_upi_amount").val(online.toFixed(2));
                $("#cash_online_upi_amount_hidden").val(online.toFixed(2));
            }

            // Trigger on typing + blur
            $(document).on('input change blur', '#cash_online_cash_amount, input[name="total"]', calcCashOnlineFully);

            function calcEMI() {
                let amount = parseFloat($("#emi_amount").val()) || 0;
                let duration = parseInt($("#emi_duration").val()) || 0;

                let monthly = 0;

                // ✅ Prevent negative amount
                if (amount < 0) {
                    amount = 0;
                    $("#emi_amount").val(""); // reset field
                }

                // ✅ Ensure duration is between 1 and 12
                if (duration < 1 || duration > 12) {
                    duration = 0;
                    $("#emi_duration").val(""); // reset invalid value
                }

                // ✅ Calculate EMI only if amount > 0 and duration valid
                if (amount > 0 && duration > 0) {
                    monthly = amount / duration;
                }

                // ✅ Prevent negative EMI
                if (monthly < 0) {
                    monthly = 0;
                }

                $("#calculated_emi").val(monthly.toFixed(2));
            }


            // 🔗 Trigger calc on amount or duration change
            $(document).on('input change', '#emi_amount, #emi_duration', calcEMI);





            // ✅ Cash + Online — PARTIAL calculation
            $(document).on('input', '#cash_online_partial_cash, #cash_online_partial_upi', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                let cash = parseFloat($("#cash_online_partial_cash").val()) || 0;
                let upi = parseFloat($("#cash_online_partial_upi").val()) || 0;

                // ❌ Prevent negative values
                if (cash < 0) {
                    cash = 0;
                    $("#cash_online_partial_cash").val(" ");
                }
                if (upi < 0) {
                    upi = 0;
                    $("#cash_online_partial_upi").val(" ");
                }

                let combined = cash + upi;

                if (combined > total) {
                    // If combined > total → reset latest field
                    if ($(this).attr("id") === "cash_online_partial_cash") {
                        cash = total - upi;
                        $("#cash_online_partial_cash").val(cash >= 0 ? cash.toFixed(2) : "");
                    } else {
                        upi = total - cash;
                        $("#cash_online_partial_upi").val(upi >= 0 ? upi.toFixed(2) : "");
                    }
                    combined = total;
                }

                const remaining = total - combined;
                $("#cash_online_remaining").val(remaining.toFixed(2));
            });


        });
    </script>
    <script>
        $(document).ready(function() {

            $("#user_type").change(function() {
                const type = $(this).val();
                if (type === "customer") {
                    $("#customerSection, #customerPhoneSection").show();
                    $("#vendorDropdown, #vendorPhoneSection").hide();
                } else if (type === "vendor") {
                    $("#vendorDropdown, #vendorPhoneSection").show();
                    $("#customerSection, #customerPhoneSection").hide();
                } else {
                    $("#customerSection, #customerPhoneSection, #vendorDropdown, #vendorPhoneSection").hide();
                }
            });
            $("#user_type").val("customer").trigger("change");


            // Populate customer phone
            $("#customer_name").change(function() {
                const phone = $(this).find(':selected').data('phone') || '';
                $("#customer_phone").val(phone);

                if (phone === "") {
                    $(".error_customerphone").text("");
                } else if (!/^\d+$/.test(phone)) {
                    $(".error_customerphone").text("Enter a valid number (digits only)").css("color", "red");
                } else {
                    $(".error_customerphone").text("");
                }
            });

            // Populate vendor phone
            $("#vendor_id").change(function() {
                const phone = $(this).find(':selected').data('phone') || '';
                $("#vendor_phone").val(phone);

                if (phone === "") {
                    $(".error-vendorphone").text("");
                } else if (!/^\d+$/.test(phone)) {
                    $(".error-vendorphone").text("Enter a valid number (digits only)").css("color", "red");
                } else {
                    $(".error-vendorphone").text("");
                }
            });

        });
    </script>


    @endpush