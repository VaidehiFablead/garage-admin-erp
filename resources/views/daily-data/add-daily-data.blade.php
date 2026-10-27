@extends('layout.app')

@section('title', 'Add Daily Data')

@section('content')
    <style>
        #customerForm {
            font-size: 13px;
            /* or whatever size you want */
        }

        #customerForm label,
        #customerForm input,
        #customerForm select,
        #customerForm textarea,
        #customerForm button {
            font-size: inherit;
        }

        .form-control {
            height: 38px;
            /* Adjust as needed */
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
        }

        select.form-control {
            padding-right: 30px;
            /* For arrow space */
            background-position: right 10px center;
        }

        /* Global Select2 improvements */
        .select2-dropdown {
            z-index: 9999 !important;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #customerForm .col-6 {
            padding-left: 10px;
            padding-right: 10px;
        }

        @media screen and (max-width: 991.98px) {
            #customerForm .select2-container {
                width: 100% !important;
            }

            #customerForm .mobile-step > .row > [class*="col-"] {
                flex: 0 0 50%;
                max-width: 50%;
                overflow: visible !important;
            }

            .select2-dropdown {
                max-width: calc(100vw - 24px);
            }

            .select2-results__option {
                white-space: normal;
                word-break: break-word;
            }
        }

        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
/* 
            #customerForm .col-6,
            #customerForm .col-12 {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            } */

            #customerForm .col-6[style*="border-left"] {
                border-left: none !important;
                margin-top: 16px;
            }
            /* #type_select,
            #type_select + .select2-container {
                width: 100% !important;
            }
            #customer_name,
            #customer_name + .select2-container {
                width: 100% !important;
            }
            #model,
            #model + .select2-container {
                width: 100% !important;
            }
            #technician_name,
            #technician_name + .select2-container {
                width: 100% !important;
            }
            #status,
            #status + .select2-container {
                width: 100% !important;
            } */
            
            
        }

        /* @media screen and (max-width: 575.98px) {
            #customerForm .mobile-step > .row > [class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }
        } */

        /* iPad Pro (1024x1366) - Portrait */
        @media only screen and (min-width: 1000px) and (max-width: 1050px) and (min-height: 1300px) {
            #customerForm .col-md-4,
            #customerForm .col-lg-4,
            #customerForm .col-sm-6 {
                overflow: visible !important;
            }

            #customerForm .select2-container {
                width: 100% !important;
            }

            .select2-dropdown {
                max-width: calc(100vw - 32px) !important;
                width: auto !important;
            }

            #customerForm .form-group {
                position: relative;
                z-index: 1;
            }

            #customerForm .select2-container--open {
                z-index: 1050 !important;
            }

            #customerForm .select2-container--open + .select2-dropdown {
                position: fixed !important;
                left: auto !important;
            }
        }

        /* Nest Hub (1024x600) - Landscape */
        @media only screen and (min-width: 1000px) and (max-width: 1050px) and (max-height: 650px) {
            #customerForm .col-md-4,
            #customerForm .col-lg-4,
            #customerForm .col-sm-6 {
                overflow: visible !important;
            }

            #customerForm .select2-container {
                width: 100% !important;
            }

            .select2-dropdown {
                max-width: calc(100vw - 32px) !important;
                width: auto !important;
                max-height: 400px !important;
                overflow-y: auto !important;
            }

            #customerForm .form-group {
                position: relative;
                z-index: 1;
            }

            #customerForm .select2-container--open {
                z-index: 1050 !important;
            }
        }

        /* iPad Pro (1024x1366) and similar - General fix for 1024px width */
        @media only screen and (width: 1024px) {
            /* Remove overflow hidden that clips Select2 */
            #customerForm [style*="overflow: hidden"] {
                overflow: visible !important;
            }

            /* Ensure all select2 containers take full width of parent */
            #customerForm .select2-container {
                width: 100% !important;
                z-index: 100;
            }

            /* Make Select2 dropdown properly sized and positioned */
            .select2-dropdown {
                max-width: calc(100vw - 32px) !important;
                min-width: 200px !important;
                position: fixed !important;
                z-index: 9999 !important;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            }

            /* Ensure form groups don't clip content */
            #customerForm .form-group {
                position: relative;
                z-index: auto;
            }

            /* Boost z-index for open containers */
            #customerForm .select2-container--open {
                z-index: 9998 !important;
            }

            /* Ensure select results are visible */
            .select2-results__option {
                white-space: normal;
                word-break: break-word;
            }

            /* Fix column padding for better spacing */
            #customerForm .col-6,
            #customerForm .col-md-4 {
                position: relative;
                z-index: 1;
            }

            /* Prevent Select2 search field from clipping */
            .select2-search__field {
                width: 100% !important;
            }

            /* Ensure Select2 results container is visible */
            .select2-results {
                max-height: 300px;
                overflow-y: auto;
            }
        }
    </style>
    <style>
        .mobile-stepper {
            display: none;
            margin-bottom: 12px;
        }

        .mobile-stepper-track {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .mobile-stepper-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        .mobile-stepper-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid #cfcfcf;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: #fff;
            margin-bottom: 4px;
        }

        .mobile-stepper-step.active .mobile-stepper-circle {
            border-color: #ff9f43;
            background: #ff9f43;
            color: #fff;
        }

        .mobile-stepper-step.active {
            color: #333;
            font-weight: 600;
        }

        .mobile-step-actions {
            display: none;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
        }

        @media screen and (max-width: 768px) {
            .mobile-stepper {
                display: block;
            }

            .mobile-step {
                display: none;
            }

            .mobile-step.active {
                display: block;
            }

            .mobile-step-actions {
                display: flex;
            }
        }
        /* @media screen and (width: 768px) and (height: 1024px) {
            #type_select,
            #type_select + .select2-container {
                width: 230px !important;
            }
            #customer_name,
            #customer_name + .select2-container {
                width: 230px !important;
            }
            #model,
            #model + .select2-container {
                width: 230px !important;
            }
            #technician_name,
            #technician_name + .select2-container {
                width: 230px !important;
            }
            #status,
            #status + .select2-container {
                width: 230px !important;
            } 
        }
         @media screen and (width: 1024px) and (height: 1366px) {
            #type_select,
            #type_select + .select2-container {
                width: 100% !important;
            }
            #customer_name,
            #customer_name + .select2-container {
                width: 100% !important;
            }
            #model,
            #model + .select2-container {
                width: 100% !important;
            }
            #technician_name,
            #technician_name + .select2-container {
                width: 100% !important;
            }
            #status,
            #status + .select2-container {
                width: 100% !important;
            } 
        } */
      
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add Daily Data</h4>

            </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="customerForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mobile-stepper" aria-label="Form steps">
                        <div class="mobile-stepper-track">
                            <div class="mobile-stepper-step" data-step="1">
                                <span class="mobile-stepper-circle">1</span>
                                <span class="mobile-stepper-label">Customer</span>
                            </div>
                            <div class="mobile-stepper-step" data-step="2">
                                <span class="mobile-stepper-circle">2</span>
                                <span class="mobile-stepper-label">Payment</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Customer Details -->
                        <div class="col-12 col-lg-6 mobile-step" data-step="1">
                            <h6 class="mb-3">Customer Details</h6>
                            <div class="row">
                                <!-- Left Column (5 fields) -->

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4">
                                    <label>Record Date</label>
                                    <div class="input-groupicon">
                                        <input type="text" name="expense_date" placeholder="Choose Date"
                                            class="datetimepicker form-control no-asterisk"
                                            value="{{ \Carbon\Carbon::yesterday()->format('d-m-Y') }}">
                                        <span class="text-danger error-expense_date" id="expense_date_error"></span>
                                        <div class="addonset">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/calendars.svg' }}"
                                                alt="calendar">
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $subAdminId = session('selectedSubAdminId');
                                @endphp
                               {{-- @if(empty($subAdminId)) --}}
                                    <div class="col-md-4 col-sm-6 col-6 form-group mb-4" style="overflow: hidden;">
                                        <label>Select Type</label>
                                        <select id="type_select" name="type_select" id="type_select" class="form-control select2 no-asterisk">
                                            <option value="">Select Type</option>
                                            <option value="jobcard">Job Card</option>
                                            <option value="dailydata">Daily Data</option>
                                        </select>
                                        <div class="text-danger error-type_select"></div>
                                    </div>
                                {{-- @endif --}}

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4" id="customer_dropdown_container"
                                    style="overflow: hidden; display:none;">
                                    <label>Select Customer</label>
                                    <select id="customer_select" name="customer_select" class="form-control select2">
                                        <option value="">Select Customer</option>
                                    </select>
                                    <div class="text-danger error-customer_select"></div>
                                </div>

                                <!-- Jobcard Dropdown -->
                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4" id="jobcard_dropdown_container"
                                    style="overflow: hidden; display:none;">
                                    <label>Customer Job Card</label>
                                    <select id="jobcard_select" name="jobcard_select" class="form-control select2">
                                        <option value="">Select Job Card</option>
                                    </select>
                                    <div class="text-danger error-jobcard_select"></div>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4" style="overflow: hidden;">
                                    <label>Customer Name</label>
                                    <select id="customer_name" name="customer_name" id="customer_name" class="form-control select2">
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-phone="{{ $customer->phone }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-customer_name"></div>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4">
                                    <label>Phone</label>
                                    <input type="number" name="phone" id="phone" class="form-control no-asterisk">
                                    <div class="text-danger error-phone"></div>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4" style="overflow: hidden;">
                                    <label>Vehicle Model</label>
                                    <select name="model" id="model" class="form-control work_status no-asterisk">
                                        <option value="">Select Model</option>
                                        @foreach($auto_car_model as $model)
                                            <option value="{{ $model->id }}">{{ ucwords($model->model_name) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-model"></div>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4">
                                    <label>Vehicle No.</label>
                                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control no-asterisk">
                                    <div class="text-danger error-vehicle_no"></div>
                                </div>


                                <!-- Right Column (5 fields) -->

                                <div class="col-md-4 col-sm-6 col-6 form-group mb-3" style="overflow: hidden;">
                                    <label>Technician Name</label>
                                    <select name="technician_name" id="technician_name" class="form-control work_status">
                                        <option value="">Select Technician</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}">{{ ucwords($technician->name) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-technician_name"></div>
                                </div>

                                @php
                                    $today = \Carbon\Carbon::now()->format('Y-m-d'); // Correct format for date input
                                @endphp

                                <div class="col-md-4 col-sm-6 col-6 form-group">
                                    <label>Start Date</label>
                                    <div class="input-groupicon">
                                        <input type="date" name="start_date" id="start_date" class="form-control no-asterisk"
                                            value="{{ $today }}" min="{{ $today }}">
                                        <span class="text-danger error-start_date" id="start_date_error"></span>

                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group">
                                    <label>End Date</label>
                                    <div class="input-groupicon">
                                        <input type="date" name="end_date" id="end_date" class="form-control no-asterisk"
                                            value="{{ $today }}" min="{{ $today }}">

                                    </div>
                                    <span class="text-danger error-end_date" id="end_date_error"></span>
                                </div>

                                <div class="col-md-4 col-sm-6 col-6 form-group" style="overflow: hidden;">
                                    <label>Work Status</label>
                                    <select name="status" id="status" class="form-control work_status no-asterisk">
                                        <option value="">Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    <div class="text-danger error-status"></div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-6 form-group mb-4">
                                    <label>Advance / Extra Amount</label>
                                    <input type="number" name="extra_pay" id="extra_pay" class="form-control">
                                    <div class="text-danger error-extra_pay"></div>
                                </div>


                            </div>
                            <div class="mobile-step-actions">
                                <button type="button" class="btn btn-submit js-step-next">Next</button>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mobile-step" data-step="2" style="border-left: 1px solid #dfdfdf;">
                            <h6 class="mb-3">Payment Details</h6>
                            <div class="row">
                                <div class="col-md-4 col-lg-4 col-sm-6">
                                    <div class="form-group align-self-end">
                                        <label>Payable Amount</label>
                                        <!-- Add name attribute so it submits -->
                                        <input type="number" id="payable_amount" name="payable_amount" class="form-control"
                                            placeholder="0" min="0" step="0.01">
                                        <span class="text-danger error-payable_amount" id="payable_amount_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-6">
                                    <div class="form-group align-self-end">
                                        <label>Discount(₹)</label>
                                        <input type="number" id="discount_amount" name="discount_amount"
                                            class="form-control no-asterisk" placeholder="0" min="0" step="0.01">
                                        <span class="text-danger error-discount" id="discount_error"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4">
                                    <div class="form-group">
                                        <label>Payment Mode</label>
                                        <select name="payment_mode" id="payment_mode" class="form-control">
                                            <option value="">Select Payment Mode</option>
                                            <option value="pending">Pending</option>
                                            <option value="cash">Cash</option>
                                            <option value="online">Online</option>
                                            <option value="cashonline">Cash + Online</option>
                                            <option value="emi">EMI</option>
                                        </select>
                                        <div class="text-danger error-payment_mode"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4 d-none" id="paid_type_container">
                                    <div class="form-group">
                                        <label>Paid Type</label>
                                        <select id="paid_type" name="paid_type" class="form-control">
                                            <option value="">Select Paid Type</option>
                                            <option value="full">Fully Paid</option>
                                            <option value="partial">Partially Paid</option>
                                        </select>
                                        <div class="text-danger error-paid_type"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4 d-none" id="cash_amount_input_container">
                                    <div class="form-group">
                                        <label>Cash Amount</label>
                                        <input type="number" id="cash_amount_input" name="cash_amount" class="form-control"
                                            placeholder="Enter Cash Amount">
                                        <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4 d-none" id="amount_input_container">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="number" id="amount_input" name="amount" class="form-control"
                                            placeholder="Enter Amount">
                                        <span class="text-danger error-cash-amount" id="amount_error"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4 d-none" id="upi_amount_input_container">
                                    <div class="form-group">
                                        <label>Online Amount</label>
                                        <input type="number" id="upi_amount_input" name="upi_amount" class="form-control"
                                            placeholder="Enter Online Amount">
                                        <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 col-lg-4 d-none" id="pending_amount_container">
                                    <div class="form-group">
                                        <label>Pending Amount</label>
                                        <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                                            readonly>
                                        <span id="pending_error" style="color:red; font-size:12px;"></span>
                                    </div>
                                </div>
                                <div class="row" id="emi_container" style="display: none;">

                                    <!-- EMI Duration -->
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label for="emi_duration">EMI Duration (Months)</label>
                                            <input type="number" id="emi_duration" name="emi_duration" class="form-control"
                                                min="1" max="12" value="1">
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
                                <div class="col-12 col-md-12 col-sm-6">
                                    <div class="form-group align-self-end">
                                        <label>Comment</label>
                                        <textarea id="comment" name="comment" class="form-control"
                                            placeholder="Enter comment here"></textarea>

                                    </div>
                                </div>
                            </div>
                            <div class="mobile-step-actions">
                                <button type="button" class="btn btn-cancel js-step-prev">Back</button>
                            </div>
                        </div>
                    </div>




                    <div class="mobile-step" data-step="2">
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-submit me-2">Submit</button>
                                <a href="{{ route('daily_data.index') }}" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>



            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let todayStr = `${yyyy}-${mm}-${dd}`;

            document.getElementById("start_date").value = todayStr;
            document.getElementById("start_date").setAttribute("min", todayStr);

            document.getElementById("end_date").value = todayStr;
            document.getElementById("end_date").setAttribute("min", todayStr);
        });
        $('#customer_name').on('change', function () {
            let phone = $(this).find(':selected').data('phone') || '';
            $('#phone').val(phone);
        });
        let invalid = false;
        let invalid1 = false;
        $('#payable_amount').on('keyup input', function () {
            let value = parseFloat($(this).val());
            let $errorSpan = $('#payable_amount_error');

            if (!isNaN(value) && value < 0) {
                invalid = true;
                $errorSpan.text('Negative amount is not valid');
            } else {
                invalid = false;
                $errorSpan.text('');
            }
        });
        $('#upi_amount_input').on('keyup input', function () {
            let value = parseFloat($(this).val());
            let $errorSpan = $('#upi_amount_error');

            if (!isNaN(value) && value < 0) {
                invalid = true;
                $errorSpan.text('Negative amount is not valid');
            } else {
                invalid = false;
                $errorSpan.text('');
            }
        });
        $('#amount_input').on('keyup input', function () {
            let value = parseFloat($(this).val());
            let $errorSpan = $('#amount_error');

            if (!isNaN(value) && value < 0) {
                invalid = true;
                $errorSpan.text('Negative amount is not valid');
            } else {
                invalid = false;
                $errorSpan.text('');
            }
        });
        $('#cash_amount_input').on('keyup input', function () {
            let value = parseFloat($(this).val());
            let $errorSpan = $('#cash_amount_error');

            if (!isNaN(value) && value < 0) {
                invalid = true;
                $errorSpan.text('Negative amount is not valid');
            } else {
                invalid = false;
                $errorSpan.text('');
            }
        });
        $(document).ready(function () {
            // Detect if device is iPad Pro or Nest Hub
            const isTabletDevice = () => {
                const width = window.innerWidth;
                const height = window.innerHeight;
                // iPad Pro: 1024x1366, Nest Hub: 1024x600
                return (width === 1024 && (height === 1366 || height === 600)) || 
                       (width === 1024 && height === 1024);
            };

            const isTablet = isTabletDevice();

            // Function to position Select2 dropdown properly
            const positionSelect2Dropdown = (selectElement) => {
                if (!isTablet) return;

                selectElement.on('select2:open', function() {
                    setTimeout(() => {
                        const dropdown = $('.select2-dropdown--below');
                        if (dropdown.length) {
                            const select2Container = selectElement.next('.select2-container');
                            const offset = select2Container.offset();
                            const height = select2Container.height();

                            dropdown.css({
                                'position': 'fixed',
                                'top': (offset.top + height + 8) + 'px',
                                'left': offset.left + 'px',
                                'width': select2Container.width() + 'px',
                                'z-index': 9999
                            });
                        }
                    }, 50);
                });
            };

            const select2Config = {
                tags: true,
                dropdownParent: isTablet ? $(document.body) : undefined,
                width: '100%'
            };

            const workStatusConfig = {
                dropdownParent: isTablet ? $(document.body) : undefined,
                width: '100%'
            };

            const select2All = $(".select2");
            select2All.select2(select2Config);
            select2All.each(function() {
                positionSelect2Dropdown($(this));
            });

            const workStatusAll = $(".work_status");
            workStatusAll.select2(workStatusConfig);
            workStatusAll.each(function() {
                positionSelect2Dropdown($(this));
            });

            function parseDate(dateStr) {
                // Expected format: dd-mm-yyyy
                let parts = dateStr.split('-');
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }

            let lastCustomerName = null; // store last selected/typed name

            $('#customer_name').select2({
                tags: true, // allow new customer names
                allowClear: true,
                placeholder: "Select or Add Customer",
                dropdownParent: isTablet ? $(document.body) : undefined,
                width: '100%'
            });

            positionSelect2Dropdown($('#customer_name'));

            // Update lastCustomerName when a customer is selected
            $('#customer_name').on('select2:select', function (e) {
                lastCustomerName = e.params.data.text.trim(); // trim extra spaces
            });

            // Prefill search box when dropdown opens
            $('#customer_name').on('select2:open', function () {
                setTimeout(() => {
                    const searchBox = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchBox) {
                        searchBox.value = ''; // clear any text
                        searchBox.focus({ preventScroll: true }); // focus cursor
                    }
                }, 150); // small delay to ensure Select2 has rendered
            });

            // Optional: trigger change on page load
            $('#customer_name').trigger('change');

            let lastTechnician = null;
            let lastModel = null;

            // Initialize Select2
            $('#technician_name').select2({
                placeholder: "Select Technician",
                allowClear: true,
                dropdownParent: isTablet ? $(document.body) : undefined,
                width: '100%'
            });

            positionSelect2Dropdown($('#technician_name'));

            $('#model').select2({
                placeholder: "Select Model",
                allowClear: true,
                dropdownParent: isTablet ? $(document.body) : undefined,
                width: '100%'
            });

            positionSelect2Dropdown($('#model'));

            // Update last selected values
            $('#technician_name').on('select2:select', function (e) {
                lastTechnician = e.params.data.text.trim();
            });

            $('#model').on('select2:select', function (e) {
                lastModel = e.params.data.text.trim();
            });

            // Prefill search box on dropdown open
            $('#technician_name').on('select2:open', function () {
                setTimeout(() => {
                    const searchBox = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchBox) {
                        searchBox.value = ''; // clear any text
                        searchBox.focus({ preventScroll: true }); // focus cursor
                    }
                }, 150); // small delay to ensure Select2 has rendered
            });

            $('#model').on('select2:open', function () {
                if (lastModel) {
                    let $searchbox = $(this).data('select2').$dropdown.find('.select2-search__field');
                    $searchbox.val(lastModel);
                    $searchbox[0].setSelectionRange(0, $searchbox.val().length);
                }
            });

            function validateDates() {
                let startDateVal = $('input[name="start_date"]').val();
                let endDateVal = $('input[name="end_date"]').val();

                // Clear error messages
                $('#start_date_error').text('');
                $('#end_date_error').text('');

                if (startDateVal && endDateVal) {
                    let startDate = parseDate(startDateVal);
                    let endDate = parseDate(endDateVal);

                    if (startDate > endDate) {
                        $('#end_date_error').text('End date cannot be earlier than start date.');
                        $('input[name="end_date"]').val('');
                    }
                }
            }

            $('input[name="start_date"], input[name="end_date"]').on('change', validateDates);
            var authToken = localStorage.getItem("authToken");

            // $.ajax({
            //     url: '/api/jobcards-get', // Laravel route
            //     type: 'GET',
            //     headers: {
            //         "Authorization": "Bearer " + authToken,
            //     },
            //     success: function(response) {
            //         if (response.success) {
            //             const dropdown = $('#jobcard_select');
            //             dropdown.empty();
            //             dropdown.append('<option value="">Select Job Card</option>');

            //             response.data.forEach(function(jobcard) {
            //                 dropdown.append(`<option value="${jobcard.id}">${jobcard.job_card_number}</option>`);
            //             });

            //             // Initialize select2
            //             dropdown.select2();
            //         }
            //     },
            //     error: function(err) {
            //         console.error('Error fetching jobcards:', err);
            //     }
            // });

            // Show/hide dropdown based on type selection
            $('#type_select').on('change', function () {
                const selectedSubAdminId = localStorage.setItem('selectedSubAdminId', 'null');
                const selectedType = $(this).val();

                if (selectedType === 'jobcard') {
                    $('#customer_dropdown_container').show();

                    // Fetch customers for this branch (main dropdown)
                    $.ajax({
                        url: '/api/getAllCustomer',
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            "X-Selected-SubAdmin-Id": selectedSubAdminId,
                        },
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        success: function (response) {
                            if (response.status) {
                                const customerNameDropdown = $('#customer_name');
                                customerNameDropdown.empty().append('<option value="">Select Customer</option>');
                                response.data.forEach(customer => {
                                    customerNameDropdown.append(`<option value="${customer.id}" data-phone="${customer.phone}">${customer.name}</option>`);
                                });
                                customerNameDropdown.select2();
                            }
                        }
                    });

                    // Fetch customers + jobcards (specifically for jobcard linking)
                    $.ajax({
                        url: '/api/jobcards-get',
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            ...(selectedSubAdminId && selectedSubAdminId !== "null" 
                                ? { "X-Selected-SubAdmin-Id": selectedSubAdminId } 
                                : {})
                        },
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        success: function (response) {
                            if (response.success) {
                                const customers = {};
                                response.data.forEach(jobcard => {
                                    if (!customers[jobcard.customer_id]) {
                                        customers[jobcard.customer_id] = jobcard.customer_name;
                                    }
                                });

                                const customerDropdown = $('#customer_select');
                                customerDropdown.empty().append('<option value="">Select Customer</option>');

                                $.each(customers, function (id, name) {
                                    customerDropdown.append(`<option value="${id}">${name}</option>`);
                                });

                                customerDropdown.select2();
                            }
                        },
                        error: function (err) {
                            console.error('Error fetching jobcards:', err);
                        }
                    });

                } else {
                    $('#customer_dropdown_container, #jobcard_dropdown_container').hide();
                    $('#customer_select, #jobcard_select').val('').trigger('change');
                }
            });

            // When customer is selected → filter jobcards
            $('#customer_select').on('change', function () {
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

                const customerId = $(this).val();
                const jobcardDropdown = $('#jobcard_select');
                jobcardDropdown.empty().append('<option value="">Select Job Card</option>');

                if (customerId) {
                    $('#jobcard_dropdown_container').show();

                    $.ajax({
                        url: '/api/jobcards-get',
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            "X-Selected-SubAdmin-Id": selectedSubAdminId,
                        },
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        success: function (response) {
                            if (response.success) {
                                response.data
                                    .filter(jobcard => jobcard.customer_id == customerId)
                                    .forEach(jobcard => {
                                        jobcardDropdown.append(
                                            `<option value="${jobcard.id}">${jobcard.job_card_number}</option>`
                                        );
                                    });

                                jobcardDropdown.select2();
                            }
                        }
                    });
                } else {
                    $('#jobcard_dropdown_container').hide();
                    jobcardDropdown.val('').trigger('change');
                }
            });

            $('#jobcard_select').on('change', function () {
                const jobcardId = $(this).val();

                if (jobcardId) {
                    $.ajax({
                        url: '/api/jobcard-details/' + jobcardId, // Laravel route
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function (response) {
                            if (response.success) {
                                const data = response.data;

                                // Example: populate form fields
                                $('#customer_name').val(data.customer_name).trigger('change');
                                console.log(data.customer_name);
                                
                                console.log($('#customer_name option').map(function () {
                                    return $(this).val();
                                }).get());
                                $('#model').val(data.model).trigger('change'); // If using select2
                                $('#vehicle_no').val(data.vehicle_no);
                                // Use remaining amount if present (handles partial payments)
                                let payableVal = data.remaining_amount !== undefined && data.remaining_amount !== null && parseFloat(data.remaining_amount) > 0
                                    ? data.remaining_amount
                                    : data.total_amount;
                                // Normalize to two decimals for display
                                $('#payable_amount').val(parseFloat(payableVal).toFixed(2));
                                $('#comment').val(data.comment || '');
                                $('#technician_name').val(data.technician).trigger('change');
                                $('#payment_mode').prop('disabled', false);

                                // Add more fields as needed
                            }
                        },
                        error: function (err) {
                            console.error('Error fetching jobcard data:', err);
                        }
                    });
                } else {
                    // Clear fields if no jobcard selected

                    $('#customer_name').val('').trigger('change');
                    $('#model').val('').trigger('change');
                    $('#technician_name').val('').trigger('change');
                    $('#vehicle_no').val('');
                    $('#payable_amount').val('');
                    $('#comment').val('');
                    $('#payment_mode').prop('disabled', true);
                }
            });

            $(document).on('click', '.add-row', function () {
                var $currentRow = $(this).closest('.form-row');
                var $newRow = $currentRow.clone();

                // Clear inputs in the cloned row
                $newRow.find('input').val('');

                // Replace the add (+) button with a remove (–) button in the new row
                $newRow.find('.add-row')
                    .removeClass('add-row btn-success')
                    .addClass('remove-row btn-danger')
                    .text('-');

                // Append the new row after the current one
                $newRow.insertAfter($currentRow);
            });

            $(document).on('input', '.price-input', function () {
                let total = 0;

                $(".price-input").each(function () {
                    let val = parseFloat($(this).val());
                    if (!isNaN(val)) {
                        total += val;
                    }

                    // Update only the corresponding .total-input in this row
                    $(this)
                        .closest('.row.form-row') // Go up to the row
                        .find('.total-input')
                        .val(isNaN(val) ? '' : val.toFixed(2));
                });

                // Update the grand total
                $("#total_expense_amount").val(total.toFixed(2));
            });

            $('#payment_mode').prop('disabled', true);

            // Watch for changes in the payable amount field
            $('#payable_amount').on('input', function () {
                let amount = parseFloat($(this).val());

                if (!isNaN(amount) && amount > 0) {
                    $('#payment_mode').prop('disabled', false);
                } else {
                    $('#payment_mode').val('').prop('disabled', true);
                }
            });

            $("#payment_mode").change(function () {
                const selectedMode = $(this).val();

                // Reset all
                $("#paid_type").val("");
                $("#amount_input").val("");
                $("#cash_amount_input").val("");
                $("#upi_amount_input").val("");
                $("#pending_amount").val("");

                if (selectedMode === "cash" || selectedMode === "online" || selectedMode === "cashonline") {
                    $("#paid_type_container").removeClass("d-none");
                    $("#emi_container").addClass("d-none");
                } else {
                    $("#paid_type_container, #method_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #emi_container").addClass("d-none");
                }

                if (selectedMode === "emi") {
                    $("#emi_container").removeClass("d-none").show();
                }

                // Hide all specific inputs initially
                $("#cash_amount_input_container").addClass("d-none");
                $("#upi_amount_input_container").addClass("d-none");
                $("#amount_input_container").addClass("d-none");
                $("#pending_amount_container").addClass("d-none");
            });

            function getEffectivePayable1() {
                const payable = parseFloat($('#payable_amount').val()) || 0;
                const discount = parseFloat($('#discount_amount').val()) || 0;
                return payable - discount;
            }

            function calculateMonthlyEMI() {
                let amount = getEffectivePayable1();  // ✅ use payable - discount
                let duration = parseInt($('#emi_duration').val()) || 0;
                const annualRate = parseFloat($('#emi_interest').val()) || 0;

                // 🔐 Enforce 1 to 12 months only
                if (duration > 12) {
                    duration = 12;
                    $('#emi_duration').val(12); // reset in input field
                }

                if (amount <= 0 || duration <= 0) {
                    $('#calculated_emi').val('0.00');
                    return;
                }

                const monthlyRate = (annualRate / 12) / 100;
                let emi;

                if (duration === 1) {
                    // ✅ If duration = 1 → just show total amount
                    emi = amount;
                } else if (monthlyRate > 0) {
                    emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, duration)) /
                        (Math.pow(1 + monthlyRate, duration) - 1);
                } else {
                    emi = amount / duration;
                }

                $('#calculated_emi').val(emi.toFixed(2));
            }

            // 🔄 Recalculate EMI when inputs change
            $('#emi_duration, #payable_amount, #emi_interest, #discount_amount').on('input', function () {
                calculateMonthlyEMI();
            });

            // ✅ Run once on page load
            $(document).ready(function () {
                calculateMonthlyEMI();
            });

            $("#paid_type").change(function () {
                const type = $(this).val();
                const selectedMode = $("#payment_mode").val();

                // Clear all fields and errors
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#cash_amount_error, #upi_amount_error").text("");

                // Hide all input containers first
                $("#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container").addClass("d-none");

                // Remove required attributes initially
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").prop("required", false);

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

            function getEffectivePayable() {
                const payable = parseFloat($("#payable_amount").val()) || 0;
                const discount = parseFloat($("#discount_amount").val()) || 0;
                return payable - discount;
            }

            // For single partial payment (amount_input)
            $("#amount_input").on("input", function () {
                const cashPaid = parseFloat($(this).val()) || 0;
                const effectivePayable = getEffectivePayable();

                const pending = effectivePayable - cashPaid;

                // Update pending field
                $("#pending_amount").val(pending > 0 ? pending.toFixed(2) : "0.00");

                // Validation
                if (pending < 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text("Cannot enter more than payable amount (after discount)");
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            // For split payments (cash + UPI)
            $("#cash_amount_input, #upi_amount_input").on("input", function () {
                const cashPaid = parseFloat($("#cash_amount_input").val()) || 0;
                const onlinePaid = parseFloat($("#upi_amount_input").val()) || 0;
                const effectivePayable = getEffectivePayable();

                const totalPaid = cashPaid + onlinePaid;
                const pending = effectivePayable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending >= 0 ? pending.toFixed(2) : "0.00");

                // Validation
                if (pending < 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text("Enter correct amount cash + online (after discount)");
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            // Recalculate if discount changes
            $("#discount_amount").on("input", function () {
                $("#amount_input, #cash_amount_input, #upi_amount_input").trigger("input");
            });


            function validateFullPayment() {
                const effectivePayable = getEffectivePayable();
                const cash = parseFloat($("#cash_amount_input").val()) || 0;
                const upi = parseFloat($("#upi_amount_input").val()) || 0;
                const total = cash + upi;

                // Clear previous messages
                $("#cash_amount_error").text("");
                $("#upi_amount_error").text("");

                if ($("#paid_type").val() === "full") {
                    if (total !== effectivePayable) {
                        const msg = `Cash + UPI must equal payable amount after discount (₹${effectivePayable})`;
                        $("#cash_amount_error").text(msg);
                        $("#upi_amount_error").text(msg);
                        return false;
                    }
                }

                return true;
            }

            // Bind validation on input
            $("#cash_amount_input, #upi_amount_input, #discount_amount").on("input", function () {
                validateFullPayment();
            });

            // Remove row functionality
            $(document).on('click', '.remove-row', function () {
                $(this).closest('.form-row').remove();
            });


            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            // console.log(selectedSubAdminId);
            // Handle form submission
            $("#customerForm").submit(function (e) {
                e.preventDefault();

                // if (!validateFullPayment()) {
                //     e.preventDefault(); // stop form if validation fails
                //     return false;
                // }
                // console.log(invalid);
                // console.log(invalid1);

                let isValid = true;

                if ($("#emi_container").is(":visible")) {
                    let duration = parseInt($("#emi_duration").val()) || 0;

                    if (duration <= 0) {
                        isValid = false;
                        $("#emi_duration").addClass("is-invalid"); // Bootstrap style (optional)
                        $("#emi_duration_error").remove(); // avoid duplicates
                        $("#emi_duration").after('<span id="emi_duration_error" class="text-danger">Duration must be greater than 0</span>');
                    } else {
                        $("#emi_duration").removeClass("is-invalid");
                        $("#emi_duration_error").remove();
                    }
                }

                if (!isValid) {
                    return false; // ❌ stop form submit
                }


                if (invalid) {
                    return false; // stop submit if any field is invalid
                }
                if (invalid1) {
                    return false; // stop submit if any field is invalid
                }


                let formData = new FormData(this);
                formData.append('selectedSubAdminId', selectedSubAdminId);
                $(".text-danger").html(""); // Clear previous error messages

                const $btn = $("#submitCustomerBtn"); // Replace with your actual button ID
                const originalText = $btn.html();

                // Show spinner and disable button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                )
                    .prop("disabled", true);

                $.ajax({
                    url: "/api/createdailydata", // Ensure API route is correct
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (response) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Daily Data added successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                            }).then(() => {
                                window.location.href = "{{ route('daily_data.index') }}";
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.html(originalText).prop("disabled", false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Clear all previous errors
                            $('.error').text('');
                            $('[class^="error-"]').text('');

                            $.each(errors, function (key, value) {
                                if (key.includes('.')) {
                                    let [field, index] = key.split('.');
                                    let $input = $(`input[name="${field}[]"]`).eq(index);
                                    $input.closest('.form-group').find('.error').text(value[0]);
                                } else {
                                    // Use class selector like .error-customer_name
                                    $(`.error-${key}`).text(value[0]);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            });

            // Remove error message when user starts typing
            $("input, select").on("input", function () {
                let fieldName = $(this).attr("name");
                $(".error-" + fieldName).html("");
            });

            // Handle File Input Change (Live Preview)
            $("#avatar").change(function (event) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $("#avatar-preview").attr("src", e.target.result);
                    $("#avatar-preview-container").show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    </script>
    <script>
        (function () {
            const steps = Array.from(document.querySelectorAll('.mobile-step'));
            const stepIndicators = Array.from(document.querySelectorAll('.mobile-stepper-step'));
            const mq = window.matchMedia('(max-width: 768px)');
            let currentStep = 1;

            function setActiveStep(step) {
                currentStep = step;
                steps.forEach((el) => {
                    const stepNum = parseInt(el.getAttribute('data-step'), 10);
                    el.classList.toggle('active', stepNum === currentStep);
                });
                stepIndicators.forEach((el) => {
                    const stepNum = parseInt(el.getAttribute('data-step'), 10);
                    el.classList.toggle('active', stepNum === currentStep);
                });
            }

            function applyLayout() {
                if (mq.matches) {
                    const activeSteps = steps.filter((el) => el.classList.contains('active'));
                    if (activeSteps.length !== 1) {
                        setActiveStep(1);
                    } else {
                        const stepNum = parseInt(activeSteps[0].getAttribute('data-step'), 10);
                        setActiveStep(stepNum);
                    }
                } else {
                    steps.forEach((el) => el.classList.add('active'));
                    stepIndicators.forEach((el) => el.classList.remove('active'));
                }
            }

            document.addEventListener('click', function (e) {
                if (!mq.matches) {
                    return;
                }
                if (e.target.closest('.js-step-next')) {
                    e.preventDefault();
                    setActiveStep(Math.min(steps.length, currentStep + 1));
                }
                if (e.target.closest('.js-step-prev')) {
                    e.preventDefault();
                    setActiveStep(Math.max(1, currentStep - 1));
                }
            });

            applyLayout();
            if (mq.addEventListener) {
                mq.addEventListener('change', applyLayout);
            } else {
                mq.addListener(applyLayout);
            }
        })();
    </script>
@endpush
