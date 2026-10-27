@extends('layout.app')

@section('title', 'Edit Sales')

@section('content')
<style>
    .d-none {
        display: none !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding-top: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .img-flag {
        vertical-align: middle;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px !important;
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
</style>
<div class="content">
    <div class="page-header">

        <div class="gst-header">
            <h4>Edit Sales</h4>

            <label class="custom-radio-label">
                <input type="radio" name="gst_option" id="without_gst" value="without"
                    {{ $sales->gst_option == 'without' ? 'checked' : '' }} />
                Without GST
            </label>

            <label class="custom-radio-label">
                <input type="radio" name="gst_option" id="with_gst" value="with"
                    {{ $sales->gst_option == 'with' ? 'checked' : '' }} />
                With GST
            </label>
        </div>
                    <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-6">
                    <input type="hidden" name="update_selse_id" id="update_selse_id" value="{{ $update_id }}">
                    <div class="form-group">
                        <label>{{ in_array($sales->user->role ?? '', ['customer','vendor']) ? ucfirst($sales->user->role) : 'Customer/Vendor' }}</label>
                        <div class="col-lg-12 col-sm-10 col-12">
                            <select name="user_id" class="form-control" disabled>
                                <option value="">Select Customer/Vendor</option>
                                @foreach($usernames as $user)
                                <option value="{{ $user->id }}" {{ $sales->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-6">
                    <div class="form-group">
                        <label>{{ in_array($sales->user->role ?? '', ['customer','vendor']) ? ucfirst($sales->user->role) : 'Customer/Vendor' }} Number</label>
                        <div class="input-groupicon">
                            <input type="tel" id="customer_phone" class="form-control"
                                placeholder="Customer/Vendor number" name="customer_phone"
                                value="{{ $sales->user->phone ?? '' }}" readonly>
                            <span class="error_customerphone"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-6" style="overflow: hidden;">
                    <div class="select-split">
                        <div class="select-group w-100">
                            <label>Model</label>
                            <select id="car_model" name="car_model" class="form-control select2">
                                <option value="">Select Model</option>
                                @foreach ($model as $username)
                                    <option value="{{ $username->id }}" 
                                        {{ $sales->car_model == $username->id ? 'selected' : '' }}>
                                        {{ $username->model_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error_car_model text-danger"></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 col-6" style="overflow: hidden;">
                    <div class="select-split">
                        <div class="select-group w-100">
                            <label>Vehicle No.</label>
                            <input type="text" id="vehicle_no" class="form-control"
                            placeholder="Vehicle No." name="vehicle_no" value="{{ $sales->vehicle_no }}">
                            <span class="error_vehicle_no text-danger"></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Product Name</label>
                        <div class="input-groupicon">
                            <!-- Product Select Dropdown -->

                            <select name="product_id[]" class="form-control product-select" multiple="multiple">
                                @foreach($products as $product)
                                @php
                                $settings = \DB::table('settings')->first();
                                $currencySymbol = $settings->currency_symbol ?? '₹';
                                $currencyPosition = $settings->currency_position ?? 'left';
                                $images = json_decode($product->images ?? '', true);
                                $imageUrl = !empty($images) && isset($images[0])
                                ? env('ImagePath').'storage/' . $images[0]
                                : env('ImagePath').'/admin/assets/img/product/noimage.png';

                                // Check if this product is in the order items
                                $isSelected = false;
                                foreach ($sales->order_items as $item) {
                                if ($item->product_id == $product->id) {
                                $isSelected = true;
                                break;
                                }
                                }
                                @endphp
                                @php
                                $priceFormatted = number_format($product->price, 2);
                                $displayPrice = $currencyPosition === 'right'
                                ? $priceFormatted . ' ' . $currencySymbol
                                : $currencySymbol . $priceFormatted;
                                @endphp
                                <option value="{{ $product->id }}"
                                    data-image="{{ $imageUrl }}"
                                    data-price="{{ $product->price }}"
                                    data-name="{{ $product->name }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $product->name }} - {{ $displayPrice }}
                                </option>
                                @endforeach
                            </select>
                            <div class="addonset">
                                <img src="{{ env('ImagePath').'admin/assets/img/icons/scanner.svg'}}" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive mb-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>QTY</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="product-table-body">
                            <!-- Existing items (if any) -->
                            @forelse ($sales->order_items as $index => $item)
                            <tr data-product-id="{{ $item->product_id }}">
                                <td>{{ $index + 1 }}</td>
                                <td class="productimgname">
                                    @php
                                    $images = json_decode($item->product->images ?? '', true);
                                    @endphp
                                    <a class="product-img">
                                        @if (!empty($images) && isset($images[0]))
                                        <img src="{{ env('ImagePath').'storage/' . $images[0] }}" alt="product" width="40">
                                        @else
                                        <img src="{{ env('ImagePath').'/admin/assets/img/product/noimage.png' }}" alt="No image" width="40">
                                        @endif
                                    </a>
                                    <a href="javascript:void(0);">{{ $item->product->name ?? 'N/A' }}</a>
                                </td>
                                <td>
                                    <input type="number"
                                        name="quantities[{{ $item->product_id }}]"
                                        class="form-control quantity-input"
                                        value="{{ number_format($item->quantity, 2, '.', '') }}"
                                        step="1"
                                        min="0"
                                        data-price="{{ $item->price }}"
                                        style="width: 80px;">
                                </td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>
                                    <span class="total-amount">{{ number_format($item->total_amount, 2) }}</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="delete-set">
                                        <img src="{{ env('ImagePath').'admin/assets/img/icons/delete.svg' }}" alt="svg">
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr id="no-products-row">
                                <td colspan="6" class="text-center">No products selected</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <!-- <tr>
                                <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                <td colspan="2">
                                    <span id="order-subtotal">{{ number_format($sales->order_items->sum('total_amount'), 2) }}</span>
                                </td>
                            </tr> -->
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-6 col-6">
                    
                </div>

                <!-- <div class="col-lg-4 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select class="select" name="status">
                            <option value="completed" {{ $sales->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ $sales->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ $sales->status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div> -->




                <!-- <div class="col-lg-4 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="select" name="payment_method">
                            <option value="cash" {{ $sales->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="debit" {{ $sales->payment_method == 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="scan" {{ $sales->payment_method == 'scan' ? 'selected' : '' }}>Scan</option>
                        </select>
                    </div>
                </div> -->
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Discount(₹)</label>
                            <input type="number" class="form-control" name="discount" id="discount-input" value="{{ $sales->discount ?? 0 }}" min="0">
                        </div>
                    </div>

                    <div class="col-md-3" style="display: none;">
                        <div class="form-group mb-2">
                            <label for="paid_by">Paid By</label>
                            <select name="paid_by" id="paid_by" class="form-control">
                                <option value="Cash" {{ isset($sales) && $sales->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online" {{ isset($sales) && $sales->payment_method == 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Cash_online" {{ isset($sales) && $sales->payment_method == 'Cash_online' ? 'selected' : '' }}>Cash + Online</option>
                                <option value="EMI" {{ isset($sales) && $sales->payment_method == 'EMI' ? 'selected' : '' }}>EMI</option>
                            </select>
                        </div>
                    </div>

                    <!-- <div class="col-md-3" id="cash_type_container" style="display: none;"> -->
                    <div class="col-md-3" id="cash_type_container1" style="display: none;">
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
                    <div class="col-md-3" id="cash_online_type_container" style="display: none;">
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
                                    <input type="number" name="cash_online_cash_amount" id="cash_online_cash_amount" class="form-control" placeholder="Enter cash amount">
                                    <span class="text-danger">{{ $errors->first('cash_online_cash_amount') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="cash_online_upi_amount">Online Amount</label>
                                    <input type="number" name="cash_online_upi_amount" id="cash_online_upi_amount" class="form-control" placeholder="Enter online amount" readonly>
                                    <span class="text-danger">{{ $errors->first('cash_online_upi_amount') }}</span>
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
                                    <input type="number" name="cash_online_partial_cash" id="cash_online_partial_cash" class="form-control" placeholder="Enter cash amount">
                                    <span class="text-danger">{{ $errors->first('cash_online_partial_cash') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="cash_online_partial_upi">Online Amount</label>
                                    <input type="number" name="cash_online_partial_upi" id="cash_online_partial_upi" class="form-control" placeholder="Enter online amount">
                                    <span class="text-danger">{{ $errors->first('cash_online_partial_upi') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="cash_online_remaining">Remaining Amount</label>
                                    <input type="number" name="cash_online_remaining" id="cash_online_remaining" class="form-control" placeholder="Remaining amount" readonly>
                                    <span class="text-danger">{{ $errors->first('cash_online_remaining') }}</span>
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
                                    <input type="number" name="cash_amount" id="cash_amount" class="form-control" placeholder="Enter cash amount">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="upi_amount">Online Amount</label>
                                    <input type="float" name="upi_amount" id="upi_amount" class="form-control" placeholder="Enter online amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Partially Payment Fields -->
                    <div class="col-6" id="cash_partially_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="cash_partial_amount">Cash Amount</label>
                                    <input type="number" name="cash_partial_amount" id="cash_partial_amount" class="form-control" placeholder="Enter cash amount" min="0">
                                    <span class="text-danger cash_partial_error d-none"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="cash_remaining_amount">Remaining Amount</label>
                                    <input type="number" name="cash_remaining_amount" id="cash_remaining_amount" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>





                    <!-- Online Payment Type -->
                    <div class="col-md-3" id="online_type_container" style="display: none;">
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
                    <!-- <div class="col-md-3" id="full_payment_block" style="display: none;"> -->
                    <div class="col-md-3" id="full_payment_block1" style="display: none;">
                        <div class="form-group mb-2">
                            <label for="grand_total_form" class="font-weight-bold">Total</label>
                            <input type="number" step="0.01" name="grand_total" id="grand_total_form" class="form-control" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Partial & Remaining Amounts Together -->
                    <div class="col-6" id="partial_remaining_container" style="display: none;">
                        <div class="row">
                            <!-- Partial Amount -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="partial_amount">Partial Amount</label>
                                    <input type="number" step="0.01" name="partial_amount" id="partial_amount" class="form-control" value="0.00" min="0">
                                </div>
                            </div>

                            <!-- Remaining Amount -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="remaining_amount">Remaining Amount</label>
                                    <input type="number" step="0.01" name="remaining_amount" id="remaining_amount" class="form-control" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- EMI Details (hidden by default) -->
                <div class="row" id="emi_container" style="display: none;">
                    <!-- EMI Amount -->
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="emi_amount">EMI Amount</label>
                            <input type="number" step="0.01" name="emi_amount" id="emi_amount" class="form-control" value="0.00">
                        </div>
                    </div>

                    <!-- EMI Duration -->
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="emi_duration">EMI Duration (Months)</label>
                            <input type="number" id="emi_duration" name="emi_duration" class="form-control" min="1" max="12">
                        </div>
                    </div>

                    <!-- Calculated EMI -->
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="calculated_emi">Monthly Installment</label>
                            <input type="number" step="0.01" name="calculated_emi" id="calculated_emi" class="form-control" value="0.00" readonly>
                        </div>
                    </div>

                </div>
            </div>
            <br>


            <div class="row justify-content-end">
                <div class="col-lg-6">
                    <!-- @php
                    $subtotal = $sales->order_items->sum('total_amount');
                    $discountPercent = $sales->discount ?? 0;
                    $discountAmount = ($subtotal * $discountPercent) / 100;

                    $taxRates = $TaxRate;

                    // Calculate total tax on full subtotal (before discount)
                    $totalTaxAmount = 0;
                    $taxDetails = [];

                    foreach ($taxRates as $tax) {
                    $taxAmount = ($subtotal * $tax->tax_rate) / 100;
                    $taxDetails[] = [
                    'name' => $tax->tax_name,
                    'rate' => $tax->tax_rate,
                    'amount' => $taxAmount,
                    ];
                    $totalTaxAmount += $taxAmount;
                    }

                    // Grand total = subtotal + tax - discount
                    $grandTotal = $subtotal + $totalTaxAmount - $discountAmount;
                    @endphp

                    <div class="total-order w-100 max-widthauto m-auto mb-4">
                        <ul>
                            <li class="subtotal">
                                <h4>Subtotal</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="subtotal-display">{{ number_format($sales->order_items->sum('total_amount'), 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="subtotal-display">{{ number_format($sales->order_items->sum('total_amount'), 2) }}</span>
                                    @endif
                                </h5>
                            </li>

                            <li class="discount">
                                <h4>Discount</h4>
                                <h5>
                                    <span id="discount-percent">{{ number_format($sales->discount ?? 0, 2) }}</span>% (
                                    @if($setting->currency_position === 'right')
                                    <span id="discount-amount">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="discount-amount">0.00</span>
                                    @endif
                                    )
                                </h5>
                            </li>

                            <li class="after-discount">
                                <h4>After Discount</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="after-discount-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="after-discount-display">0.00</span>
                                    @endif
                                </h5>
                            </li>

                            @foreach($TaxRate as $tax)
                            <li>
                                <h4>{{ $tax->tax_name }}</h4>
                                <h5>{{ number_format($tax->tax_rate, 2) }}% (
                                    @if($setting->currency_position === 'right')
                                    <span class="tax-amount" data-rate="{{ $tax->tax_rate }}">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span class="tax-amount" data-rate="{{ $tax->tax_rate }}">0.00</span>
                                    @endif
                                    )
                                </h5>
                            </li>
                            @endforeach

                            <li class="total">
                                <h4>Grand Total</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="grand-total">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="grand-total">0.00</span>
                                    @endif
                                </h5>
                            </li>
                        </ul>
                    </div> -->

                    @php
                    $subtotal = $sales->order_items->sum('total_amount');

                    // Determine GST option (default to 'without' if not set)
                    $gstOption = $sales->gst_option ?? 'without';

                    $totalTaxAmount = 0;
                    $taxDetails = [];

                    if ($gstOption === 'with') {
                    foreach ($TaxRate as $tax) {
                    $taxAmount = ($subtotal * $tax->tax_rate) / 100;
                    $taxDetails[] = [
                    'id' => $tax->id ?? null,
                    'name' => $tax->tax_name,
                    'rate' => $tax->tax_rate,
                    'amount' => $taxAmount,
                    ];
                    $totalTaxAmount += $taxAmount;
                    }
                    }

                    // Grand total = subtotal + taxes (discount ignored)
                    $grandTotal = $subtotal + $totalTaxAmount;

                    // Pass tax rates to JS
                    $taxRatesJson = $TaxRate->map(function($tax){
                    return [
                    'id' => $tax->id ?? null,
                    'name' => $tax->tax_name,
                    'rate' => $tax->tax_rate,
                    ];
                    });
                    @endphp


                    <div class="total-order w-100 max-widthauto m-auto mb-4">
                        <ul>
                            <li class="subtotal">
                                <h4>Subtotal</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="subtotal-display">{{ number_format($subtotal, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="subtotal-display">{{ number_format($subtotal, 2) }}</span>
                                    @endif
                                </h5>
                            </li>
                            <li class="discount">
                                <h4>Discount</h4>
                                <h5>
                                    
                                    @if($setting->currency_position === 'right')
                                    <span id="discount-amount">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="discount-amount">0.00</span>
                                    @endif
                                    
                                </h5>
                            </li>
                            

                            <li class="after-discount">
                                <h4>After Discount</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="after-discount-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="after-discount-display">0.00</span>
                                    @endif
                                </h5>
                            </li>

                            @if($gstOption === 'with')
                            @foreach($taxDetails as $tax)
                            <li>
                                <h4>{{ $tax['name'] ?? '' }} Tax</h4>
                                <h5>
                                    {{ number_format($tax['rate'] ?? 0, 2) }}% (
                                    @if($setting->currency_position === 'right')
                                    <span class="tax-amount"
                                        data-id="{{ $tax['id'] ?? $loop->index }}"
                                        data-rate="{{ $tax['rate'] ?? 0 }}">
                                        {{ number_format($tax['amount'] ?? 0, 2) }}
                                    </span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}
                                    <span class="tax-amount"
                                        data-id="{{ $tax['id'] ?? $loop->index }}"
                                        data-rate="{{ $tax['rate'] ?? 0 }}">
                                        {{ number_format($tax['amount'] ?? 0, 2) }}
                                    </span>
                                    @endif
                                    )
                                </h5>
                            </li>
                            @endforeach
                            @endif

                            <li class="total">
                                <h4>Grand Total</h4>
                                <h5>
                                    @if($setting->currency_position === 'right')
                                    <span id="grand-total">{{ number_format($grandTotal, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                    @else
                                    {{ $setting->currency_symbol ?? '₹' }}<span id="grand-total">{{ number_format($grandTotal, 2) }}</span>
                                    @endif
                                </h5>
                            </li>
                        </ul>

                        <input type="hidden" name="total" value="{{ $grandTotal }}">
                    </div>


                </div>
            </div>
            <div class="col-lg-12">
                <button type="submit" class="btn btn-submit me-2">Update Order</button>
                <a href="{{ route('sales.list') }}" class="btn btn-cancel">Cancel</a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('js')
<script>
    $(".select2").select2({
        tags: true,
    });
    $('#customer_select').on('change', function() {
        var phone = $(this).find(':selected').data('phone');
        $('#customer_phone').val(phone || '');
    });
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                const quantity = parseFloat(this.value) || 0;
                const price = parseFloat(this.dataset.price) || 0;
                const total = quantity * price;

                // Find the closest row
                const row = this.closest('tr');
                if (row) {
                    const totalSpan = row.querySelector('.total-amount');
                    if (totalSpan) {
                        totalSpan.textContent = total.toFixed(2);
                    }
                }
            });
        });
    });

    function updateSubtotal() {
        let subtotal = 0;
        document.querySelectorAll('.quantity-input').forEach(input => {
            const quantity = parseFloat(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            subtotal += quantity * price;
            

            const row = input.closest('tr');
            const totalSpan = row.querySelector('.total-amount');
            if (totalSpan) {
                totalSpan.textContent = (quantity * price).toFixed(2);
            }
        });
        
        var discountrate = parseFloat($('#discount-amount').text()) || 0;
        $('#grand_total_form').val(subtotal.toFixed(2) - discountrate);

        // Update the order subtotal
        const subtotalSpan = document.getElementById('order-subtotal');
        if (subtotalSpan) {
            subtotalSpan.textContent = subtotal.toFixed(2);
        }
    }

    // Add event listener to all quantity inputs
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', updateSubtotal);
        });
    });
    $(document).ready(function() {
        // Initialize Select2 with images
        function formatProduct(product) {
            if (!product.id) return product.text;
            var $product = $(
                '<span><img src="' + $(product.element).data('image') + '" class="img-flag" style="width: 20px; margin-right: 10px;" />' + product.text + '</span>'
            );
            return $product;
        }

        $('.product-select').select2({
            templateResult: formatProduct,
            templateSelection: formatProduct,
            closeOnSelect: false
        });

        // Handle customer selection change
        $('#customer_name').on('change', function() {
            var phone = $(this).find(':selected').data('phone');
            $('#customer_phone').val(phone || '');
        });

        // Add product to table
        function addProductToTable(productId, productName, productImage, productPrice) {
            const rowCount = $('tbody tr[data-product-id]').length + 1;
            const newRow = `
            <tr data-product-id="${productId}">
                <td>${rowCount}</td>
                <td class="productimgname">
                    <a class="product-img">
                        <img src="${productImage}" alt="product" width="40">
                    </a>
                    <a href="javascript:void(0);">${productName}</a>
                </td>
                <td>
                    <input type="number" 
                           name="quantities[${productId}]" 
                           class="form-control quantity-input" 
                           value="1"
                           step="1"
                           min="0"
                           data-price="${productPrice}"
                           style="width: 80px;">
                </td>
                <td>${parseFloat(productPrice).toFixed(2)}</td>
                <td>
                    <span class="total-amount">${parseFloat(productPrice).toFixed(2)}</span>
                </td>
                <td>
                    <a href="javascript:void(0);" class="delete-set">
                        <img src="{{ asset('/admin/assets/img/icons/delete.svg') }}" alt="svg">
                    </a>
                </td>
            </tr>
        `;

            $('#no-products-row').remove();
            $('tbody').append(newRow);

            // Add event listeners to the new row
            $('tbody tr[data-product-id="' + productId + '"] .quantity-input').on('input', calculateAllTotals);
            $('tbody tr[data-product-id="' + productId + '"] .delete-set').on('click', function() {
                $(this).closest('tr').remove();
                updateProductSelection();
                toggleNoProductsMessage();
                calculateAllTotals();
            });
        }

        // Update product selection in dropdown
        function updateProductSelection() {
            const selectedProducts = $('tbody tr[data-product-id]').map(function() {
                return $(this).data('product-id').toString();
            }).get();
            $('.product-select').val(selectedProducts).trigger('change');
        }

        // Toggle "no products" message
        function toggleNoProductsMessage() {
            if ($('tbody tr[data-product-id]').length === 0) {
                $('tbody').append('<tr id="no-products-row"><td colspan="6" class="text-center">No products selected</td></tr>');
            }
        }

        // Calculate all totals including discount and taxes
        function calculateAllTotals() {
    let subtotal = 0;

    // Calculate subtotal from all products
    $('tbody tr[data-product-id]').each(function() {
        const $row = $(this);
        const quantity = parseFloat($row.find('.quantity-input').val()) || 0;
        const price = parseFloat($row.find('.quantity-input').data('price')) || 0;
        const rowTotal = quantity * price;

        $row.find('.total-amount').text(rowTotal.toFixed(2));
        subtotal += rowTotal;
    });

    // Update subtotal display
    $('#subtotal-display').text(subtotal.toFixed(2));

    // Discount (fixed amount in your case)
    const discountValue = parseFloat($('#discount-input').val()) || 0;
    const discountAmount = discountValue;

    // Update discount display
    $('#discount-percent').text(discountValue.toFixed(2));
    $('#discount-amount').text(discountAmount.toFixed(2));

    // After discount
    let afterDiscount = subtotal - discountAmount;
    if (afterDiscount < 0) afterDiscount = 0;
    $('#after-discount-display').text(afterDiscount.toFixed(2));

    // ✅ Apply tax on AFTER DISCOUNT amount
    let totalTax = 0;
    $('.tax-amount').each(function() {
        const taxRate = parseFloat($(this).data('rate')) || 0;
        const taxAmount = afterDiscount * (taxRate / 100);

        $(this).text(taxAmount.toFixed(2));
        totalTax += taxAmount;
    });

    // Grand total = after discount + tax
    const grandTotal = afterDiscount + totalTax;
    $('#grand-total').text(grandTotal.toFixed(2));
}

// Bind events
$(document).on('input', '.quantity-input, #discount-input', function() {
    calculateAllTotals();
});

// Init
calculateAllTotals();

        // Handle product selection changes
        $('.product-select').on('change', function() {
            const selectedProducts = $(this).val() || [];
            const existingProducts = $('tbody tr[data-product-id]').map(function() {
                return $(this).data('product-id').toString();
            }).get();

            // Add new products
            selectedProducts.forEach(productId => {
                if (!existingProducts.includes(productId)) {
                    const option = $(this).find('option[value="' + productId + '"]');
                    addProductToTable(
                        productId,
                        option.data('name'),
                        option.data('image'),
                        option.data('price')
                    );
                }
            });

            // Remove unselected products
            $('tbody tr[data-product-id]').each(function() {
                const rowProductId = $(this).data('product-id').toString();
                if (!selectedProducts.includes(rowProductId)) {
                    $(this).remove();
                }
            });

            toggleNoProductsMessage();
            calculateAllTotals();
        });

        // Initialize existing quantity input events
        $('.quantity-input').on('input', calculateAllTotals);

        // Initialize existing delete buttons
        $('.delete-set').on('click', function() {
            $(this).closest('tr').remove();
            updateProductSelection();
            toggleNoProductsMessage();
            calculateAllTotals();
        });

        // Handle discount input change
        $('#discount-input').on('input', calculateAllTotals);

        $(document).on("click", '.btn-submit', function(e) {
            e.preventDefault();

            var authToken = localStorage.getItem("authToken");
            let grandTotal = parseFloat($("#grand-total").text()) || 0;
            let paidBy = $("#paid_by").val() || "";
            let cashType = $("#cash_type").val() || "";
            let onlineType = $("#online_type").val() || "";
            let cashPartial = parseFloat($("#cash_partial_amount").val()) || 0;
            let partialAmount = parseFloat($("#partial_amount").val()) || 0;
            let cashOnlineType = $("#cash_online_type").val() || "";
            let cashOnlineCash = parseFloat($("#cash_online_cash_amount").val()) || 0;
            let cashOnlinePartialCash = parseFloat($("#cash_online_partial_cash").val()) || 0;
            let cashOnlinePartialUpi = parseFloat($("#cash_online_partial_upi").val()) || 0;
            let emiDuration = parseInt($("#emi_duration").val()) || 0;
            let discount = parseInt($("#discount-input").val()) || 0;
            let gstOption = $('input[name="gst_option"]:checked').val() || "without";




            let isValid = true;

            console.log(grandTotal);
            console.log(cashPartial);




            // Reset previous errors
            $("select, input").removeClass("is-invalid");
            $(".text-danger").text("");

            // ✅ Paid By validation
            if (!paidBy) {
                isValid = false;
                $("#paid_by").addClass("is-invalid").after('<span class="text-danger paid_by_error">Please select payment method</span>');
            }



            // ✅ Cash validation
            if (paidBy === "Cash") {
                if ($("#cash_type_container").is(":visible") && !cashType) {
                    isValid = false;
                    $("#cash_type").addClass("is-invalid").after('<span class="text-danger cash_type_error">Select cash type</span>');
                }

                if (cashType === "Fully") { // Match actual value of your select
                    cashPartial = grandTotal; // Auto-fill partial amount for fully
                }

                if (cashType === "Fully" && cashPartial !== grandTotal) {
                    isValid = false;
                    $("#cash_partial_amount").addClass("is-invalid").closest(".form-group").append('<span class="text-danger cash_partial_error">Amount must equal total</span>');
                }

                if (cashType === "Partially" && (cashPartial <= 0 || cashPartial > grandTotal)) {
                    isValid = false;
                    $("#cash_partial_amount").addClass("is-invalid").closest(".form-group").append('<span class="text-danger cash_partial_error">Enter valid partial amount</span>');
                }
            }




            // ✅ Online validation
            if (paidBy === "Online") {
                // Validate online type selection
                if ($("#online_type_container").is(":visible") && !onlineType) {
                    isValid = false;
                    $("#online_type").addClass("is-invalid").closest(".form-group").append('<span class="text-danger online_type_error">Select online type</span>');
                }

                // Auto-fill partialAmount if fully paid
                if (onlineType === "Fully") {
                    partialAmount = grandTotal; // Auto-fill
                }

                // Validate fully paid amount
                if (onlineType === "Fully" && partialAmount !== grandTotal) {
                    isValid = false;
                    $("#partial_amount").addClass("is-invalid").closest(".form-group").append('<span class="text-danger partial_amount_error">Amount must equal total</span>');
                }

                // Validate partial payment
                if (onlineType === "Partially" && (partialAmount <= 0 || partialAmount > grandTotal)) {
                    isValid = false;
                    $("#partial_amount").addClass("is-invalid").closest(".form-group").append('<span class="text-danger partial_amount_error">Enter valid partial amount</span>');
                }
            }




            // ✅ Cash + Online validation
            if ($("#cash_online_type_container").is(":visible")) {
                if (!cashOnlineType) {
                    isValid = false;
                    $("#cash_online_type").addClass("is-invalid").closest(".form-group").append('<span class="text-danger cash_online_type_error">Select type</span>');
                } else if (cashOnlineType === "cash_online_Fully" && cashOnlineCash !== grandTotal) {
                    isValid = false;
                    $("#cash_online_cash_amount").addClass("is-invalid").closest(".form-group").append('<span class="text-danger cash_online_cash_error">Amount must equal total</span>');
                } else if (cashOnlineType === "cash_online_Partially") {
                    let totalSplit = cashOnlinePartialCash + cashOnlinePartialUpi;
                    if (totalSplit <= 0 || totalSplit > grandTotal) {
                        isValid = false;
                        $("#cash_online_partial_cash, #cash_online_partial_upi").addClass("is-invalid");
                        $("#cash_online_partial_upi").closest(".form-group").append('<span class="text-danger cash_online_partial_upi_error">Split must be > 0 and ≤ total</span>');
                    }
                }
            }



            // ✅ EMI validation
            if (paidBy === "EMI") {
                if (!emiDuration || emiDuration < 1 || emiDuration > 12) {
                    isValid = false;
                    $("#emi_duration").addClass("is-invalid").closest(".form-group").append('<span class="text-danger emi_duration_error">1-12 months only</span>');
                }
            }



            // ✅ Stop if validation fails
            if (!isValid) return false;

            const remaining_amount = parseFloat($("#cash_online_remaining").val()) ||
                parseFloat($("#cash_remaining_amount").val()) ||
                parseFloat($("#remaining_amount").val()) || 0;

            if (cashType === "cash_partially" || onlineType === "Partially" || cashOnlineType === "cash_online_partially") {
                type = "partially";
            } else if (cashType === "cash_Fully" || onlineType === "Fully" || cashOnlineType === "cash_online_Fully") {
                type = "fully";
            } else {
                type = "emi";
            }

            let selectedTaxIds = [];
            $(".tax-amount").each(function() {
                selectedTaxIds.push(parseInt($(this).data("id")));
            });

            let taxIdsJson = JSON.stringify(selectedTaxIds);
            console.log(taxIdsJson);




            // ✅ Prepare form data for AJAX
            const formData = {
                customer_id: $('#customer_name').val(),
                update_id: $('#update_selse_id').val(),
                customer_phone: $('#customer_phone').val(),
                product_ids: $('.product-select').val(),
                quantities: {},
                discount: discount,
                payment_method: paidBy,
                status: $('select[name="status"]').val(),
                grand_total: grandTotal,
                type: type,
                cash_type: $("#cash_type").val() || "",
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
                car_model: $("#car_model").val() || "",
                vehicle_no: $("#vehicle_no").val() || "",
                remaining_amount: remaining_amount,
                tax_ids: selectedTaxIds,
                gst_option: gstOption,
                // remark: remark,
            };

            $('.quantity-input').each(function() {
                const productId = $(this).closest('tr').data('product-id');
                formData.quantities[productId] = $(this).val();
            });

            // ✅ AJAX call
            $.ajax({
                url: "/api/update_sale",
                type: "POST",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: "Product updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43"
                        }).then((result) => {
                            if (result.isConfirmed) window.location.href = "{{ route('sales.list') }}";
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43"
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred while updating the order';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.message) message = res.message;
                    } catch (e) {
                        console.error(e);
                    }
                    Swal.fire({
                        title: "Error",
                        text: message,
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#ff9f43"
                    });
                }
            });
        });

        // Initial calculation
        calculateAllTotals();
    });
    // Initialize other select2 elements
    $(".select2").select2({
        tags: true,
    });

    // Handle customer name change
    $('#customer_name').on('change', function() {
        var phone = $(this).find(':selected').data('phone');
        $('#customer_phone').val(phone || '');
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

            let duration = parseInt($("#emi_duration").val(), 10);

            // Prevent invalid or empty duration
            if (isNaN(duration) || duration < 1) duration = 1;
            if (duration > 12) duration = 12;

            // Update input field to reflect allowed range
            $("#emi_duration").val(duration);

            // Calculate monthly EMI
            let monthly = amount > 0 ? amount / duration : 0;

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
    $(document).ready(function () {
    const subtotal = parseFloat($('#subtotal-display').text()) || 0;

    function recalcTotals(discountPercent) {
        // const discountAmount = (subtotal * discountPercent) / 100;
        const discountAmount = discountPercent;
        const afterDiscount = subtotal - discountAmount;

        // Update discount & after discount displays
        $('#discount-amount').text(discountAmount.toFixed(2));
        $('#after-discount-display').text(afterDiscount.toFixed(2));

        // Update taxes
        let totalTax = 0;
        $('.tax-amount').each(function () {
            const rate = parseFloat($(this).data('rate')) || 0;
            const taxAmount = (afterDiscount * rate) / 100;
            $(this).text(taxAmount.toFixed(2));
            totalTax += taxAmount;
        });

        // Recalculate grand total
        const grandTotal = afterDiscount + totalTax;
        $('#grand-total').text(grandTotal.toFixed(2));
        $('#grand_total_form').val(grandTotal.toFixed(2));
        $('input[name="total"]').val(grandTotal.toFixed(2));
    }

    // When discount group changes
    $('#discount_group').on('change', function () {
        const selectedValue = parseFloat($(this).find(':selected').data('value')) || 0;
        $('#discount-input').val(selectedValue);
        recalcTotals(selectedValue);
    });

    // When discount input changes manually
    $('#discount-input').on('input', function () {
        const discountPercent = parseFloat($(this).val()) || 0;
        recalcTotals(discountPercent);
    });

    // Run once on page load (with 2s delay)
    setTimeout(function () {
        const initialDiscount = parseFloat($('#discount-input').val()) || 0;
        recalcTotals(initialDiscount);
    }, 2000);
});

</script>

<script>
    var originalSubtotal = {{ $subtotal }}; // keep original subtotal always same
    var taxRates = @json($taxRatesJson);
    var currencySymbol = "{{ $setting->currency_symbol ?? '₹' }}";
    var currencyPosition = "{{ $setting->currency_position ?? 'left' }}";

    function formatMoney(amount) {
        return amount.toFixed(2);
    }

    function updateTotals(gstOption) {
        var discountAmount = parseFloat($('#discount-amount').text()) || 0;

        // Apply discount → After Discount
        var afterDiscount = originalSubtotal - discountAmount;
        if (afterDiscount < 0) afterDiscount = 0;

        var totalTaxAmount = 0;
        var $totalOrderUl = $('.total-order ul');
        
        // Remove old tax rows
        $totalOrderUl.find('li').not('.subtotal, .total, .discount, .after-discount').remove();

        if (gstOption === 'with') {
            $.each(taxRates, function(index, tax) {
                var taxAmount = (afterDiscount * tax.rate) / 100;
                totalTaxAmount += taxAmount;

                var taxHtml = '<li>' +
                    '<h4>' + tax.name + ' Tax</h4>' +
                    '<h5>' +
                    (currencyPosition === 'right'
                        ? '<span class="tax-amount" data-id="' + tax.id + '" data-rate="' + tax.rate + '">' + formatMoney(taxAmount) + '</span>' + currencySymbol
                        : currencySymbol + '<span class="tax-amount" data-id="' + tax.id + '" data-rate="' + tax.rate + '">' + formatMoney(taxAmount) + '</span>'
                    ) +
                    '</h5></li>';

                $totalOrderUl.find('li.total').before(taxHtml);
            });
        }

        // Grand Total = After Discount + Tax
        var grandTotal = afterDiscount + totalTaxAmount;

        // Update DOM
        $('#after-discount-display').text(formatMoney(afterDiscount));
        $('#grand-total').text(formatMoney(grandTotal));

        // Update hidden input for form
        $('input[name="total"]').val(grandTotal);
    }

    // Event listener for GST option change
    $('input[name="gst_option"]').on('change', function() {
        updateTotals($(this).val());
    });

    // Event listener when discount changes dynamically
    $(document).on('input', '#discount-amount', function() {
        updateTotals($('input[name="gst_option"]:checked').val());
    });
   

    // Initialize on page load
    $(document).ready(function() {
        // Keep subtotal fixed
        $('#subtotal-display').text(formatMoney(originalSubtotal));
        updateTotals($('input[name="gst_option"]:checked').val());
    });
</script>



@endpush