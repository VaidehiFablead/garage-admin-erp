@extends('layout.app')

@section('title', 'Add Job Card')

@section('content')
    <style>
        .is-invalid {
            border-color: #dc3545;
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #555;
            background-color: #f9f9f9;
            transition: 0.3s; 
            font-weight: 500;
            border-radius: 8px 8px 0 0; 
            margin: 0 5px;
        }

        .nav-tabs .nav-link:hover {
            background-color: #e9ecef;
            color: #ff9f43;
        }

        .nav-tabs .nav-link.active {
            background-color: #fff;
            color: #333;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        }

        .nav-tabs {
            border-bottom: none;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 8px 8px 0 0;
        }

    </style>

    <style>
        .add-row-btn {
            margin-top: 2rem;
        }

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
            .parts-price-input, .rate-input {
                width: 95px;
            }
            .qty{width: 100px;} 
        }
        @media (max-width:1024px){

            .form-group{
                display:block !important;
            }

            .form-group label{
                display:block;
                min-width:100% !important;
                margin-bottom:4px;
            }

            .form-group .flex-grow-1{
                width:100%;
            }

        }
        input[type="date"]{
            width:100%;
        }
        .select2-container{
            width:100% !important;
        }

        @media (max-width:1024px){

            .form-group{
                display:block !important;
            }

            .form-group label{
                display:block;
                min-width:100% !important;
                margin-bottom:4px;
            }

            .form-group .flex-grow-1{
                width:100%;
            }

        }
        .card{
            overflow:hidden;
        }
        /* @media screen and (max-width:768px){
            #vehicle_number,
            #vehicle_number + .select2-container {
                width: 184px !important;
            }
            #customer,
            #customer + .select2-container {
                width: 184px !important;
            }
        } */

        /* .select2-container {
            min-width: 66% !important
        } */
    </style>
    <style>
        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            user-select: none;
            position: relative;
            padding-left: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            transition: border-color 0.2s ease;
        }

        .custom-radio-label input[type="radio"]:checked {
            border-color: #007bff;
            background-color: #007bff;
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
            border-color: #0056b3;
        }

        .parts-price-input {
            max-width: 80px;
            /* adjust width as needed */
            padding: 2px 5px;
            /* optional: smaller padding */
            font-size: 0.9rem;
            /* optional: smaller font */
            text-align: center;
        }

        .rate-input {
            max-width: 80px;
            /* adjust width as needed */
            padding: 2px 5px;
            /* optional: smaller padding */
            font-size: 0.9rem;
            /* optional: smaller font */
            text-align: center;
        }
            #generateBillBtn {
                position: relative;
                min-width: 120px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            #generateBillBtn .btn-text {
                display: inline-block;
            }
            
            #generateBillBtn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }
            
            #generateBillSpinner {
                margin-left: 5px;
            }

        /* .form-control {
                                                font-size: 0.9rem !important;
                                            } */
        .job-card-header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        @media screen and (max-width: 768px) {
            .job-card-header-actions {
                flex-wrap: wrap;
                gap: 15px;
                padding-bottom: 10px;
                justify-content: flex-start !important;
                width: 100%;
            }

            .page-header, .page-title {
                text-align: left !important;
                justify-content: flex-start !important;
            }

            .job-card-header-actions h5 {
                width: 100%;
                margin-bottom: 8px !important;
                text-align: left !important;
            }

            .custom-radio-label {
                font-size: 14px;
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
            h5 {
                font-size: 15px;
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

        /* Tablet-specific styles for iPad Mini, iPad Air, Surface Pro 7, Asus Zenbook */
        @media only screen and (min-width: 769px) and (max-width: 834px) {
            /* iPad Mini (768x1024), iPad Air (820x1180) */
            .col-lg-4.col-md-6.col-12 .row.g-2 > [class*="col-6"] {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media only screen and (min-width: 835px) and (max-width: 912px) {
            /* Surface Pro 7 (912x1368) */
            .col-lg-4.col-md-6.col-12 .row.g-2 > [class*="col-6"] {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media only screen and (min-width: 913px) and (max-width: 1024px) {
            /* Asus Zenbook and similar tablets */
            .col-lg-4.col-md-6.col-12 .row.g-2 > [class*="col-6"] {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <div class="job-card-header-actions">

                    <h5 style="font-weight: bold; margin: 0;">
                        Add Job Card
                    </h5>

                    <label class="custom-radio-label">
                        <input type="radio" name="gst" value="yes" />
                        With GST
                    </label>

                    <label class="custom-radio-label">
                        <input type="radio" name="gst" value="no" checked />
                        Without GST
                    </label>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="quotation" id="quotation" value="quotation"
                            style="width: 18px; height: 18px; border: 2.5px solid #888; border-radius: 4px; cursor: pointer;">
                        <label class="form-check-label" for="quotation"
                            style="font-weight: 600; color: #333; cursor: pointer; user-select: none;  margin-top: 3px;">
                            Quotation
                        </label>
                    </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>


                </div>

            </div>
        </div>
 

        <div class="card">
            <div class="card-body">
                <form id="jobCardForm" method="POST">
                    @csrf
                    <div class="mobile-stepper" aria-label="Form steps">
                        <div class="mobile-stepper-track">
                            <div class="mobile-stepper-step" data-step="1">
                                <span class="mobile-stepper-circle">1</span>
                                <span class="mobile-stepper-label">Basic Info</span>
                            </div>
                            <div class="mobile-stepper-step" data-step="2">
                                <span class="mobile-stepper-circle">2</span>
                                <span class="mobile-stepper-label">Details</span>
                            </div>
                            <div class="mobile-stepper-step" data-step="3">
                                <span class="mobile-stepper-circle">3</span>
                                <span class="mobile-stepper-label">Summary</span>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-step" data-step="1">
                        <div class="row">
                        {{-- Vehicle Details --}}
                        <div class="col-lg-4 col-md-6 col-6">
                            <h5 class="mb-3 text-center"><strong>Vehicle Details</strong></h5>

                            <!-- Vehicle No -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="vehicle_number" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Vehicle No</label>
                                <div class="flex-grow-1">
                                    <select name="vehicle_number" id="vehicle_number"
                                        class="form-control select2-vehicle @error('vehicle_number') is-invalid @enderror">
                                    </select>
                                    @error('vehicle_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>



                            <!-- Model/Make -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="model_make" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Model/Make</label>
                                <div class="flex-grow-1">
                                    <select name="model_make" id="model_make"
                                        class="form-control @error('model_make') is-invalid @enderror">
                                        @if ($carModel->count() > 0)
                                            <option value="">Select Model/Make</option>
                                            @foreach ($carModel as $model)
                                                <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Model/Make Available</option>
                                        @endif
                                    </select>
                                    @error('model_make')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Chassis No -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="chassis_no" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Chassis No</label>
                                <div class="flex-grow-1">
                                    <input type="text" name="chassis_no" id="chassis_no"
                                        class="form-control @error('chassis_no') is-invalid @enderror"
                                        value="{{ old('chassis_no') }}">
                                    @error('chassis_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Engine No -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="engine_no" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Engine No</label>
                                <div class="flex-grow-1">
                                    <input type="text" name="engine_no" id="engine_no"
                                        class="form-control @error('engine_no') is-invalid @enderror"
                                        value="{{ old('engine_no') }}">
                                    @error('engine_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- KM/Hours -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="due_km_hours" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">KM/Hours</label>
                                <div class="flex-grow-1">
                                    <div class="d-flex gap-2">
                                        <input type="number" id="due_km_hours" name="km_hours"
                                            class="form-control me-2 @error('km_hours') is-invalid @enderror"
                                            placeholder="0" value="{{ old('km_hours') }}" min="0">
                                        <input type="text" id="due_km" name="due_km" class="form-control" placeholder="2500"
                                            readonly>
                                    </div>
                                    <span id="due_km_hours_error" class="text-danger"></span>
                                    @error('km_hours')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                        </div>


                        {{-- Customer Details --}}
                        <div class="col-lg-4 col-md-6 col-6">
                            <h5 class="mb-3 text-center"><strong>Customer Details</strong></h5>

                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="customer" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Customer Name</label>
                                <div class="flex-grow-1">
                                    <select name="customer" id="customer"
                                        class="form-control select2-customer @error('customer') is-invalid @enderror">
                                        <option value="">Select or Add Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-gst="{{ $customer->gst_num ?? '' }}"
                                                data-phone="{{ $customer->phone ?? '' }}"
                                                data-address="{{ $customer->details->address ?? '' }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('customer')
                                        <small class="text-danger d-block mt-1 customer-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>



                            <!-- GST No -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="gst_no" class="mb-0 me-2" style="white-space: nowrap; min-width: 120px;">GST
                                    No</label>
                                <div class="flex-grow-1">
                                    <input type="text" name="gst_no" id="gst_no"
                                        class="form-control @error('gst_no') is-invalid @enderror"
                                        value="{{ old('gst_no') }}">
                                    @error('gst_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div class="form-group d-flex align-items-center mb-2">
                                <label for="mobile" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Mobile</label>
                                <div class="flex-grow-1">
                                    <input type="text" name="mobile" id="mobile"
                                        class="form-control @error('mobile') is-invalid @enderror"
                                        value="{{ old('mobile') }}">
                                    @error('mobile')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <!-- Address -->
                            <div class="form-group d-flex align-items-start mb-2">
                                <label for="address" class="mb-0 me-2"
                                    style="white-space: nowrap; min-width: 120px;">Address</label>
                                <textarea name="address" class="form-control" rows="2"
                                    placeholder="Customer address"></textarea>
                            </div>
                        </div>


                        {{-- Job Card Details --}}
                        <div class="col-lg-4 col-md-12 col-12">
                            <h5 class="mb-3 text-center"><strong>Job Card Details</strong></h5>

                            {{-- Two-column grid on mobile, single column on md+ --}}
                            <div class="row g-2">

                                <!-- Job Card No -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="job_card_number" class="form-label mb-1" style="font-size:13px;">Job Card No</label>
                                        <input type="text" name="job_card_number" class="form-control form-control-sm"
                                            placeholder="Enter Job Card No." readonly>
                                        @error('job_card_number')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Job Category -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="category" class="form-label mb-1" style="font-size:13px;">Job Category</label>
                                        <select name="category" id="category"
                                            class="form-control form-control-sm @error('category') is-invalid @enderror">
                                            @if ($carCategory->count() > 0)
                                                <option value="">Select Category</option>
                                                @foreach ($carCategory as $category)
                                                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">No Categories Available</option>
                                            @endif
                                        </select>
                                        @error('category')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Technician -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="technician" class="form-label mb-1" style="font-size:13px;">Technician</label>
                                        <select name="technician" id="technician"
                                            class="form-control form-control-sm @error('technician') is-invalid @enderror">
                                            <option value="">Select Technician</option>
                                            @if ($technicians->isNotEmpty())
                                                @foreach ($technicians as $tech)
                                                    <option value="{{ $tech->id }}" {{ old('technician') == $tech->id ? 'selected' : '' }}>
                                                        {{ $tech->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">No technicians found</option>
                                            @endif
                                        </select>
                                        @error('technician')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="date" class="form-label mb-1" style="font-size:13px;">Date</label>
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="due_date" class="form-label mb-1" style="font-size:13px;">Due Date</label>
                                        <input type="text" id="due_date" name="due_date" class="form-control form-control-sm" readonly
                                            placeholder="Auto-calculated">
                                    </div>
                                </div>

                                <!-- Next Due -->
                                <div class="col-6 col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="next_due_date" class="form-label mb-1" style="font-size:13px;">Next Due</label>
                                        <div class="d-flex gap-1">
                                            <input type="number" id="next_due_date" name="next_due_date" class="form-control form-control-sm"
                                                value="90">
                                            <select id="next_due_type" name="next_due_type" class="form-control form-control-sm">
                                                <option value="Days">Days</option>
                                                <option value="KM">KM</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- end .row --}}
                        </div>

                    </div>

                    <hr>
                        <div class="mobile-step-actions">
                            <button type="button" class="btn btn-submit js-step-next">Next</button>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <div class="mobile-step" data-step="2">
                        <ul class="nav nav-tabs w-100 d-flex justify-content-between" id="jobTabs" role="tablist">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active" data-toggle="tab" href="#labour">Labour Details</a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" data-toggle="tab" href="#parts">Items</a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link" data-toggle="tab" href="#history">History</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            {{-- LABOUR TAB --}}
                            <div class="tab-pane fade show active" id="labour">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select id="labourSelect" class="form-control mb-3 select2" multiple {{ empty($labourService) ? 'disabled' : '' }}>
                                            @if (empty($labourService))
                                                <option value="">No labour services found</option>
                                            @else
                                                <option value="">Select Labour Service</option>
                                                @foreach ($labourService ?? [] as $labour)
                                                    <option value="{{ $labour->id }}" data-name="{{ $labour->name }}"
                                                        data-sac="{{ $labour->sac_code }}" data-price="{{ $labour->price }}">
                                                        {{ $labour->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>


                                <br>

                                <!-- <div class="row mb-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label d-block"><strong>With GST</strong></label>
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input gst-option" type="checkbox" id="with_gst">
                                                                                <label class="form-check-label" for="with_gst"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->


                                <div class="table-responsive">
                                    <table class="table table-bordered" id="labourTable">
                                        <thead>
                                            <tr></tr> <!-- Leave empty, JS will fill -->
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PARTS TAB --}}
                            <div class="tab-pane fade" id="parts">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Select Items</label>
                                        <select id="partsSelect" name="parts_details[]" class="form-control mb-3 select2"
                                            multiple></select>
                                    </div>
                                </div>


                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="partsTable">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Part Name</th>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>Total Amount</th> <!-- Total with GST included if applicable -->
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="history">
                                <div id="history_content" class="text-center">
                                    <p>No history found.</p>
                                </div>
                            </div>


                        </div>


                        <hr>
                        <div class="mobile-step-actions">
                            <button type="button" class="btn btn-cancel js-step-prev">Back</button>
                            <button type="button" class="btn btn-submit js-step-next">Next</button>
                        </div>
                    </div>

                    <div class="mobile-step" data-step="3">
                        {{-- Remarks and Totals --}}
                        <div class="row">
                        <!-- Tab Navigation -->
                        <div class="col-md-8">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item flex-fill text-center">
                                    <a class="nav-link active" data-toggle="tab" href="#remark_printable" role="tab">Remarks
                                        (Printable)</a>
                                </li>
                                <li class="nav-item flex-fill text-center">
                                    <a class="nav-link" data-toggle="tab" href="#remark_non_printable" role="tab">Remarks
                                        (Non-Printable)</a>
                                </li>
                                {{--
                                <li class="nav-item flex-fill text-center">
                                    <a class="nav-link" data-toggle="tab" href="#payment_note" role="tab">Payment</a>
                                    <div name="payment"></div>
                                </li>
                                --}}
                            </ul>

                            <!-- Single Tab Content Container -->
                            <div class="tab-content p-2 border border-top-0">
                                <div id="remark_printable" class="tab-pane fade show active" role="tabpanel">
                                    <textarea name="remark_printable" rows="7" class="form-control"
                                        placeholder="Remark to show on print..."></textarea>
                                </div>
                                <div id="remark_non_printable" class="tab-pane fade" role="tabpanel">
                                    <textarea name="remark_non_printable" rows="7" class="form-control"
                                        placeholder="Internal remarks..."></textarea>
                                </div>
                                <div id="payment_note" class="tab-pane fade" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label for="payment_date">Payment Date</label>
                                                <input type="date" name="payment_date" id="payment_date"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-4 order-2">

                                        </div>

                                        <!-- Previous Payments Field at the end -->
                                        <div class="col-md-4 order-3">
                                            <div class="form-group mb-2">
                                                <label for="previous_payments">Previous Payments</label>
                                                <input type="number" step="0.01" name="previous_payments"
                                                    id="previous_payments" class="form-control" readonly value="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Paid By -->
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label for="paid_by">Paid By</label>
                                                <select name="paid_by" id="paid_by" class="form-control">

                                                    <option value="Cash">Cash</option>
                                                    <option value="Online">Online</option>
                                                    <option value="Cash_online">Cash + online</option>
                                                    <option value="EMI">EMI</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Cash Payment Type -->
                                        <div class="col-md-4" id="cash_type_container" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="cash_type">Cash Payment Type</label>
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
                                                <label for="cash_online_type">Cash + Online Payment Type</label>
                                                <select name="cash_online_type" id="cash_online_type" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="cash_online_Fully">Fully</option>
                                                    <option value="cash_online_Partially">Partially</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Fully -->
                                        <div class="col-md-4" id="cash_online_fully_fields" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="cash_online_cash_amount">Cash Amount</label>
                                                <input type="number" name="cash_online_cash_amount"
                                                    id="cash_online_cash_amount" class="form-control"
                                                    placeholder="Enter cash amount">
                                                <span
                                                    class="text-danger">{{ $errors->first('cash_online_cash_amount') }}</span>
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="cash_online_upi_amount">Online Amount</label>
                                                <input type="number" name="cash_online_upi_amount"
                                                    id="cash_online_upi_amount" class="form-control"
                                                    placeholder="Enter online amount" readonly>
                                                <span
                                                    class="text-danger">{{ $errors->first('cash_online_upi_amount') }}</span>
                                            </div>
                                        </div>

                                        <!-- Partially -->
                                        <div class="col-md-4" id="cash_online_partial_fields" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="cash_online_partial_cash">Cash Amount</label>
                                                <input type="number" name="cash_online_partial_cash"
                                                    id="cash_online_partial_cash" class="form-control"
                                                    placeholder="Enter cash amount">
                                                <span
                                                    class="text-danger">{{ $errors->first('cash_online_partial_cash') }}</span>
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="cash_online_partial_upi">Online Amount</label>
                                                <input type="number" name="cash_online_partial_upi"
                                                    id="cash_online_partial_upi" class="form-control"
                                                    placeholder="Enter online amount">
                                                <span
                                                    class="text-danger">{{ $errors->first('cash_online_partial_upi') }}</span>
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="cash_online_remaining">Remaining Amount</label>
                                                <input type="number" name="cash_online_remaining" id="cash_online_remaining"
                                                    class="form-control" placeholder="Remaining amount" readonly>
                                                <span
                                                    class="text-danger">{{ $errors->first('cash_online_remaining') }}</span>
                                            </div>
                                        </div>




                                        <!-- Cash Fully Payment Fields -->
                                        <div class="col-md-4" id="cash_fully_fields" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="cash_amount">Cash Amount</label>
                                                <input type="number" name="cash_amount" id="cash_amount"
                                                    class="form-control" placeholder="Enter cash amount">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="upi_amount">Online Amount</label>
                                                <input type="float" name="upi_amount" id="upi_amount" class="form-control"
                                                    placeholder="Enter online amount" readonly>
                                            </div>
                                        </div>

                                        <!-- Cash Partially Payment Fields -->
                                        <div class="col-md-4" id="cash_partially_fields" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="cash_partial_amount">Cash Amount</label>
                                                <input type="number" name="cash_partial_amount" id="cash_partial_amount"
                                                    class="form-control" placeholder="Enter cash amount">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="cash_remaining_amount">Remaining Amount</label>
                                                <input type="number" name="cash_remaining_amount" id="cash_remaining_amount"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>




                                        <!-- Online Payment Type -->
                                        <div class="col-md-4" id="online_type_container" style="display: none;">
                                            <div class="form-group mb-2">
                                                <label for="online_type">Online Payment Type</label>
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
                                                <label for="grand_total_form" class="font-weight-bold text-primary">This
                                                    Payment</label>
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
                                                    class="form-control" value="0.00">
                                            </div>
                                        </div>

                                        <!-- Remaining Amount -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label for="remaining_amount">Remaining Amount</label>
                                                <input type="number" step="0.01" name="remaining_amount"
                                                    id="remaining_amount" class="form-control" value="0.00" readonly>
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
                                                    class="form-control" value="0.00" readonly>
                                            </div>
                                        </div>

                                        <!-- EMI Duration -->
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label for="emi_duration">EMI Duration (Months)</label>
                                                <input type="number" id="emi_duration" name="emi_duration"
                                                    class="form-control" min="1" max="12">
                                            </div>
                                        </div>

                                        <!-- EMI Interest Rate -->
                                        <!-- <div class="col-md-3">
                                                                                <div class="form-group mb-2">
                                                                                    <label for="emi_interest">Interest Rate (%)</label>
                                                                                    <input type="number" step="0.01" name="emi_interest" id="emi_interest" class="form-control" value="0.00">
                                                                                </div>
                                                                            </div> -->

                                        <!-- Calculated EMI -->
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label for="calculated_emi">Monthly Installment</label>
                                                <input type="number" step="0.01" name="calculated_emi" id="calculated_emi"
                                                    class="form-control" value="0.00" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-4">

                            <!-- Labour -->
                            <label>Total Labour</label>
                            <input type="text" id="total_labour" name="total_labour" class="form-control mb-2" readonly>

                            <div class="d-flex mb-2">
                                <select id="labour_discount_type" class="form-control me-2">
                                    <option value="%">%</option>
                                    <option value="Rs">Rs</option>
                                </select>
                                <input type="number" id="labour_discount_amount" class="form-control" value="0" min="0">
                            </div>
                            <span id="labour_discount_amount_error" class="text-danger"></span><br>

                            <!-- Parts -->
                            <label>Total Items</label>
                            <input type="text" id="total_parts" name="total_parts" class="form-control mb-2" readonly>

                            <div class="d-flex mb-2 labour_details_container">
                                <select id="parts_discount_type" class="form-control me-2">
                                    <option value="%">%</option>
                                    <option value="Rs">Rs</option>
                                </select>
                                <input type="number" id="parts_discount_amount" class="form-control" value="0" min="0">
                            </div>
                            <span id="parts_discount_amount_error" class="text-danger"></span><br>

                            <!-- Grand Total -->
                            <label><strong>Grand Total</strong></label>
                            <input type="hidden" id="grand_total_form" name="grand_total_form" value="0.00">
                            <input type="text" id="grand_total_summary" class="form-control" readonly value="0.00">


                        </div>
                    </div>

                    @if (!empty($gst) && $gst->count() > 0)
                        @foreach ($gst as $tax)
                            @if ($tax->tax_name === 'CGST' && !empty($tax->tax_rate))
                                <input type="hidden" id="cgst_rate" value="{{ $tax->tax_rate }}">
                            @elseif($tax->tax_name === 'SGST' && !empty($tax->tax_rate))
                                <input type="hidden" id="sgst_rate" value="{{ $tax->tax_rate }}">
                            @elseif($tax->tax_name === 'IGST' && !empty($tax->tax_rate))
                                <input type="hidden" id="igst_rate" value="{{ $tax->tax_rate }}">
                            @endif
                        @endforeach
                    @endif

                    <hr>
                    {{-- Submit --}}
                    <div class="text-end">
                        {{-- <button type="submit" name="action" value="generate_bill" class="btn btn-submit action-btn"> --}}
                        <button type="submit" name="action" value="generate_bill" class="btn btn-submit action-btn" id="generateBillBtn">
                            <span class="btn-text">Generate Bill</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" id="generateBillSpinner"></span>
                        </button>
                        {{--
                        <button type="submit" name="action" value="pay_now" class="btn btn-submit action-btn">
                            Pay Now
                        </button>
                        --}}
                        <a href="{{ route('job_card.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                        <div class="mobile-step-actions">
                            <button type="button" class="btn btn-cancel js-step-prev">Back</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- Labour Details Modal -->
    <div class="modal fade" id="labourModal" tabindex="-1" aria-labelledby="labourModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="labourForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="labourModalLabel">Add Labour Details</h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        --}}
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="labourId" name="labour_id" value="">
                        <div class="mb-3">
                            <label for="labourName" class="form-label">Labour Name</label>
                            <input type="text" class="form-control" id="labourName" name="labour_name" required>
                        </div>
                        <!-- <div class="mb-3">
                                                                <label for="labourMobile" class="form-label">Mobile Number</label>
                                                                <input type="tel" class="form-control" id="labourMobile" name="labour_mobile" placeholder="Optional">
                                                            </div> -->
                        <div class="mb-3">
                            <label for="labourPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="labourPrice" name="labour_price" min="0"
                                step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="labourSubmitBtn" type="submit" class="btn btn-submit">Save Labour</button>
                        <button type="button" class="btn btn-cancel" id="closed_labour"
                            data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Parts Details Modal -->
    <div class="modal fade" id="partsModal" tabindex="-1" aria-labelledby="partsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="partsForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="partsModalLabel">Add Item Details</h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        --}}
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="partId" name="part_id" value="">
                        <div class="mb-3">
                            <label for="partsName" class="form-label">item Name</label>
                            <input type="text" class="form-control" id="partsName" name="parts_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="partsCategory" class="form-label">Category</label>
                            <select id="partsCategory" class="form-select" required>
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="partsQty" class="form-label">Quantity</label>
                            <div class="input-group" style="max-width: 220px;">
                                <button type="button" class="btn btn-outline-secondary" id="partsQtyMinus">-</button>
                                <input type="number" class="form-control text-center" id="partsQty" min="1" step="1"
                                    value="1">
                                <button type="button" class="btn btn-outline-secondary" id="partsQtyPlus">+</button>
                            </div>
                        </div>
                        <!-- <div class="mb-3">
                                                                <label for="partsHSN" class="form-label">HSN Code</label>
                                                                <input type="text" class="form-control" id="partsHSN" name="parts_hsn" placeholder="Optional">
                                                            </div> -->
                        <div class="mb-3">
                            <label for="partsPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="partsPrice" name="parts_price" min="0" step="0.01"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="partsSubmitBtn" type="submit" class="btn btn-submit">Save Part</button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('js')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

    <script>
        $(document).ready(function () {
            // Target all number fields
            const fields = ['#due_km_hours', '#labour_discount_amount', '#parts_discount_amount'];

            fields.forEach(function (selector) {
                $(selector).on('input', function () {
                    let value = parseFloat($(this).val());
                    let $errorSpan = $('#' + this.id +
                        '_error'); // assume your span ID is like due_km_hours_error

                    if (!isNaN(value) && value < 0) {
                        $(this).val(0); // reset to 0
                        $errorSpan.text('Negative values are not allowed');
                    } else {
                        $errorSpan.text(''); // clear error
                    }
                });
            });
        });
        $(document).ready(function () {
            // General Select2 (for other fields)
            $('.select2').select2({
                placeholder: "Select Service",
                allowClear: true,
                width: '100%'
            });

            // Only for Customer field - allow custom names
            $('.select2-customer').select2({
                placeholder: "Select or Add Customer",
                allowClear: true,
                width: '100%',
                tags: true,
                tokenSeparators: [',']
            });
        });
    </script>



    <script>
        function calculateDueDate() {
            const baseDate = $('#date').val();
            const days = parseInt($('#next_due_date').val(), 10);
            const type = $('#next_due_type').val();

            if (baseDate && !isNaN(days) && type === 'Days') {
                const dateObj = new Date(baseDate);
                dateObj.setDate(dateObj.getDate() + days);

                const day = String(dateObj.getDate()).padStart(2, '0');
                const month = dateObj.toLocaleString('default', {
                    month: 'short'
                });
                const year = dateObj.getFullYear();

                const formattedDate = `${day}-${month}-${year}`;
                $('#due_date').val(formattedDate);
            } else {
                $('#due_date').val('');
            }
        }

        $(document).ready(function () {
            $('#date, #next_due_date, #next_due_type').on('input change', calculateDueDate);
            calculateDueDate(); // Run on page load
        });



        // --------------------------------- Handle form submission ---------------------------------
        let clickedAction = null;
        let isSubmitting = false;
        // Track which button was clicked
        $(document).on('click', '.action-btn', function () {
            clickedAction = $(this).val(); // "generate_bill" or "pay_now"
        });

        $('#jobCardForm').on('submit', function (e) {
            e.preventDefault();

            // Prevent double submission
            if (isSubmitting) return false;
            isSubmitting = true;
            
            // Get button elements
            const generateBillBtn = $('#generateBillBtn');
            const btnText = generateBillBtn.find('.btn-text');
            const spinner = $('#generateBillSpinner');
            
            // Show loader and disable button
            generateBillBtn.prop('disabled', true);
            btnText.text('Generating...');
            spinner.removeClass('d-none');

            $('.text-danger').remove();

            // Get values
            let totalLabour = parseFloat($('#total_labour').val()) || 0;
            let totalParts = parseFloat($('#total_parts').val()) || 0;

            let isValid = true;

            // Check total labour
            // if (totalLabour <= 0 && totalParts <= 0) {
            //     isValid = false;
            //     $('#total_parts').after('<div class="text-danger" style="color:red;">Either Total labour or Total parts must be greater than 0.</div>');
            //     return false;
            // }
            if (totalLabour <= 0 && totalParts <= 0) {
                isValid = false;
                $('#total_parts').after('<div class="text-danger" style="color:red;">Either Total labour or Total parts must be greater than 0.</div>');
                
                // FIX: Re-enable button and hide spinner when validation fails
                generateBillBtn.prop('disabled', false);
                btnText.text('Generate Bill');
                spinner.addClass('d-none');
                isSubmitting = false;
                
                return false;
            }

            let withservices = $("input[name='gst']:checked").val() === "yes";
            let formData = new FormData(this);

            // Ensure the hidden 'type' value is synced before sending
            const hiddenType = $('#payment_type_hidden').val();
            if (hiddenType) {
                formData.set('type', hiddenType);
            }

            // Ensure grand_total uses the "This Payment" field value
            const thisPayment = $('#grand_total_form').val();
            if (thisPayment) {
                formData.set('grand_total', thisPayment);
            }
            formData.set('withservices', withservices ? 1 : 0);
            formData.set('gst', $("input[name='gst']:checked").val());
            formData.set('quotation', $('#quotation').is(':checked') ? 'quotation' : '');

            if (clickedAction) {
                formData.set('action', clickedAction);
            }

            formData.set('total_labour', $('#total_labour').val());
            formData.set('labour_discount_type', $('#labour_discount_type').val());
            formData.set('labour_discount_amount', $('#labour_discount_amount').val());

            // Add total parts & discount values
            formData.set('total_parts', $('#total_parts').val());
            formData.set('parts_discount_type', $('#parts_discount_type').val());
            formData.set('parts_discount_amount', $('#parts_discount_amount').val());


            $.ajax({
                url: "{{ route('job_card.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (response) {
                    if (response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Job card saved successfully!',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open PDF in new tab
                                window.open("/job-card/" + response.job_card_id + "/download-pdf-view", "_blank");
                                // Redirect to job cards list
                                window.location.href = '/job-cards';
                            }
                        });

                        $('#jobCardForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Something went wrong!',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function (xhr) {
                        // Always re-enable button on error
                        generateBillBtn.prop('disabled', false);
                        btnText.text('Generate Bill');
                        spinner.addClass('d-none');
                        isSubmitting = false;
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $('.text-danger').remove();
                        $('.is-invalid').removeClass('is-invalid');

                        $.each(errors, function (fieldName, errorMessages) {    

                            let input = $('[name="' + fieldName + '"]');

                            input.addClass('is-invalid');

                            let errorText = '<div class="text-danger mt-1">' + errorMessages
                                .join('<br>') + '</div>';

                            // Check if Select2 is applied
                            if (input.hasClass('select2-customer') || input.hasClass(
                                'select2')) {
                                // Insert error after Select2 container
                                input.next('.select2').after(errorText);
                            } else {
                                // Normal fields
                                input.after(errorText);
                            }


                            if (!input.length) {
                                // If not found, maybe show it in a group container
                                $('.labour_details_container').after(errorText);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                 complete: function() {
            // Always re-enable button when request completes (success or error)
            generateBillBtn.prop('disabled', false);
            btnText.text('Generate Bill');
            spinner.addClass('d-none');
            isSubmitting = false;
        }

            });
        });

        // Reset button if form is manually reset
$('#jobCardForm').on('reset', function() {
    $('#generateBillBtn').prop('disabled', false)
        .find('.btn-text').text('Generate Bill');
    $('#generateBillSpinner').addClass('d-none');
    isSubmitting = false;
});

// Reset button when user clicks cancel
$('.btn-cancel').on('click', function() {
    $('#generateBillBtn').prop('disabled', false)
        .find('.btn-text').text('Generate Bill');
    $('#generateBillSpinner').addClass('d-none');
    isSubmitting = false;
});
    </script>    
    <script>
        document.getElementById('due_km_hours').addEventListener('input', function () {
            const kmHours = parseFloat(this.value) || 0;
            const calculatedDueKm = kmHours + 2500; // aapka multiplication value yahan set karein
            document.getElementById('due_km').value = calculatedDueKm;
        });
       
    </script>
    <script>
        $(document).ready(function () {
            $('#labourSelect').select2({
            placeholder: "Select Labour Service",
            }).on('select2:select', function (e) {
                let element = e.params.data.element;
                let $element = $(element);

                $element.detach();  // remove from list
                $(this).append($element); // add back at the end (preserves order)
                $(this).trigger('change'); // refresh select2
            });





            let selectedLabours = new Set();
            let labourModal = new bootstrap.Modal(document.getElementById('labourModal'));
            let modalOpen = false;
            let currentCustomLabour = null;

            rebuildLabourTable(false); // default without GST
            $('#closed_labour').on('click', function () {
                // Close modal properly using Bootstrap
                labourModal.hide();

                // Remove the custom labour option if Cancelled
                if (currentCustomLabour) {
                    $('#labourSelect').find('option[value="' + currentCustomLabour + '"]').remove();
                    $('#labourSelect').trigger('change');
                    currentCustomLabour = null;
                }

                modalOpen = false;
            });

            // GST toggle change
            // $("#with_gst").on("change", function() {
            //     rebuildLabourTable($(this).is(":checked"));
            // });

            $("input[name='gst']").on("change", function () {
                const withGST = $(this).val() === "yes";
                rebuildLabourTable(withGST);
                updateJobCardNumber();
            });

            $('#quotation').on('change', function () {
                updateJobCardNumber();
            });

            function updateJobCardNumber() {
                const isQuotation = $('#quotation').is(':checked');
                const withGST = $("input[name='gst']:checked").val() === "yes";
                let prefix = 'J';
                if (isQuotation) {
                    prefix = 'Q';
                } else if (withGST) {
                    prefix = 'G';
                }

                $.ajax({
                    url: "{{ route('api.job_card.get_next_number') }}",
                    method: 'GET',
                    data: {
                        prefix: prefix
                    },
                    success: function (response) {
                        $('input[name="job_card_number"]').val(response.next_number);
                    }
                });
            }

            // Initial call to set the job card number
            updateJobCardNumber();

            $('#labourSelect').on('select2:select', function (e) {
                if (modalOpen) return;
                const data = e.params.data;
                const value = data.id;
                const name = data.text;
                const isCustom = isNaN(parseInt(value));
                if (isCustom) {
                    modalOpen = true;
                    currentCustomLabour = value;
                    openLabourModal(name);
                }
            });

            $('#labourSelect').on('change', function () {
                if (modalOpen) return;
                const withGST = $("input[name='gst']:checked").val() === "yes";
                rebuildLabourTable(withGST);
            });

            function rebuildLabourTable(withGST) {
                const selectedOptions = $('#labourSelect').find(':selected');
                let thead = $("#labourTable thead");
                let tbody = $('#labourTable tbody');

                // 🔹 Save existing user-entered values
                let existingData = {};
                tbody.find("tr").each(function () {
                    const labourId = $(this).find("input[name*='[id]']").val();
                    const rate = parseFloat($(this).find(".rate-input").val()) || 0;
                    existingData[labourId] = rate;
                });

                // Update header
                thead.html(`
                <tr>
                    <th class="text-center">Sr. No.</th>
                    <th class="text-center">Particular</th>
                    <th class="text-center">Rate</th>
                    <th class="text-center">Total Amount</th>
                </tr>
            `);

                // Update body
                tbody.empty();
                selectedLabours.clear();
                let rowCount = 0;

                selectedOptions.each(function () {
                    const labourId = $(this).val();
                    const hasMeta = $(this).data('name') !== undefined;
                    const isCustomWithoutMeta = isNaN(parseInt(labourId)) && !hasMeta;
                    if (isCustomWithoutMeta) return;

                    const name = $(this).data('name') || labourId;

                    // 🔹 If user edited, keep that value, else fallback to default
                    let price = existingData[labourId] !== undefined
                        ? existingData[labourId]
                        : (parseFloat($(this).data('price')) || 0);

                    selectedLabours.add(labourId);
                    rowCount += 1;

                    let total = price;

                    let row = `
                    <tr>
                        <td class="text-center">${rowCount}</td>
                        <td class="text-center">${name}</td>
                        <td class="text-center">
                            <input type="number" step="0.01" class="form-control rate-input" 
                                   value="${price.toFixed(2)}" 
                                   data-row="${rowCount}">
                        </td>
                        <td class="text-center total-amt">${price.toFixed(2)}</td>

                        <!-- Hidden inputs -->
                        <input type="hidden" name="labour_details[${rowCount}][id]" value="${labourId}">
                        <input type="hidden" class="price-hidden" name="labour_details[${rowCount}][price]" value="${price.toFixed(2)}">
                        <input type="hidden" class="total-hidden" name="labour_details[${rowCount}][total]" value="${price.toFixed(2)}">
                    </tr>
                `;

                    tbody.prepend(row);
                });

                tbody.find("tr").each(function(index) {
                    $(this).find("td:first").text(index + 1); // update Sr. No.
                });


                // 🔹 Rate change event
                $(document).off("input", ".rate-input").on("input", ".rate-input", function () {
                    const newRate = parseFloat($(this).val()) || 0;
                    const row = $(this).closest("tr");

                    let total = newRate;

                    row.find(".total-amt").text(total.toFixed(2));
                    row.find(".price-hidden").val(newRate.toFixed(2));
                    row.find(".total-hidden").val(total.toFixed(2));

                    calculateTotalLabour();
                    if (typeof calculateGrandTotal === "function") {
                        calculateGrandTotal();
                    }
                });

                calculateTotalLabour();
                if (typeof calculateGrandTotal === 'function') {
                    calculateGrandTotal();
                }
            }
            $(document).on("keydown", ".rate-input", function (e) {
                if (e.key === "Tab" && !e.shiftKey) {
                    e.preventDefault(); // stop normal tabbing
                    $("#labourSelect").select2("open"); // jump back to dropdown
                }
            });


            function openLabourModal(name) {
                $('#labourName').val(name);
                $('#labourMobile').val('');
                $('#labourPrice').val('');
                $('#labourId').val('');
                labourModal.show();
            }

            $('#labourForm').on('submit', function (e) {
                e.preventDefault();

                const name = $('#labourName').val().trim();
                const price = parseFloat($('#labourPrice').val()) || 0;

                if (!name || price < 0) {
                    alert('Please enter valid labour name and price.');
                    return;
                }

                $('#labourSubmitBtn').prop('disabled', true);

                // Use FormData only once
                let formData = new FormData($('#labourForm')[0]);
                formData.append('name', name);
                formData.append('price', price);

                const authToken = localStorage.getItem('authToken') || '';
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }

                $.ajax({
                    url: '/api/addservice',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                    },
                    complete: function () {
                        $('#labourSubmitBtn').prop('disabled', false);
                    }
                });

                const customValue = currentCustomLabour;
                modalOpen = false;
                currentCustomLabour = null;
                labourModal.hide();

                let option = $('#labourSelect option[value="' + customValue + '"]');
                if (option.length === 0) {
                    option = $(new Option(name, customValue, true, true));
                    $('#labourSelect').append(option);
                }

                option.text(name);
                option.prop('selected', true);
                option.attr('data-name', name);
                option.attr('data-price', price);
                option.data('name', name);
                option.data('price', price);

                $('#labourSelect').trigger('change');
            });

            $('#labourModal').on('hidden.bs.modal', function () {
                if (modalOpen && currentCustomLabour) {
                    var option = $('#labourSelect option[value="' + currentCustomLabour + '"]');
                    if (option.length) {
                        option.remove();
                        $('#labourSelect').trigger('change');
                    }
                    modalOpen = false;
                    currentCustomLabour = null;
                }
            });

            function calculateTotalLabour() {
                let total = 0;
                $('#labourTable tbody tr').each(function () {
                    const rowTotal = parseFloat($(this).find('td').last().text()) || 0;
                    total += rowTotal;
                });
                $('#total_labour').val(total.toFixed(2)).trigger('input').trigger('change');
            }
        });
    </script>
    <script>
        let selectedParts = new Set();

        $(document).ready(function () {
            $('#partsSelect').select2({
                placeholder: "Search Parts",
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('get.parts') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    return data.text + ' (₹' + data.price + ')';
                },
                templateSelection: function (data) {
                    // 🔑 Inject extra attributes into the <option>
                    let option = $('#partsSelect').find('option[value="' + data.id + '"]');
                    if (option.length) {
                        option.data('name', data.name);
                        option.data('price', data.price);
                        option.data('product-gst', data.product_gst);
                    }
                    return data.text || data.id;
                }
            });

            // Bootstrap modal instance
            let partsModal = new bootstrap.Modal(document.getElementById('partsModal'));
            let partsModalOpen = false;
            let currentCustomPart = null;

            // Open modal on new custom tag select
            $('#partsSelect').on('select2:select', function (e) {
                const data = e.params.data;
                const value = data.id;
                const text = data.text;
                const isCustom = isNaN(parseInt(value));
                if (isCustom && !partsModalOpen) {
                    partsModalOpen = true;
                    currentCustomPart = value;
                    $('#partsName').val(text);
                    $('#partsHSN').val('');
                    $('#partsPrice').val('');

                    // Load categories for modal select (your existing ajax)
                    const token = localStorage.getItem('authToken') || '';
                    const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';
                    $('#partsCategory').html('<option value="">Loading...</option>');
                    $.ajax({
                        url: '/api/getAllCategory',
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        data: selectedSubAdminId ? {
                            selectedSubAdminId: selectedSubAdminId
                        } : {},
                        success: function (res) {
                            if (res && res.status && Array.isArray(res.data)) {
                                const options = ['<option value="">Select Category</option>']
                                    .concat(res.data.map(c =>
                                        `<option value="${c.id}">${c.name}</option>`));
                                $('#partsCategory').html(options.join(''));
                            } else {
                                $('#partsCategory').html(
                                    '<option value="">No categories</option>');
                            }
                        },
                        error: function () {
                            $('#partsCategory').html(
                                '<option value="">Failed to load</option>');
                        }
                    });
                    partsModal.show();
                }
            });

            // Rebuild parts table depending on GST toggle
            function rebuildPartsTable(withGST) {
                const selectedOptions = $('#partsSelect').find(':selected');
                let tbody = $('#partsTable tbody');

                // 🔹 Store current values before clearing
                let currentValues = {};
                tbody.find('tr').each(function () {
                    let partId = $(this).data('part-id');
                    if (partId) {
                        currentValues[partId] = {
                            qty: parseInt($(this).find('.parts-qty').val()) || 1,
                            price: parseFloat($(this).find('.parts-price-input').val()) || 0
                        };
                    }
                });

                tbody.empty();
                selectedParts.clear();

                // Adjust table header
                let thead = $('#partsTable thead tr');
                if (!withGST) {
                    thead.html(`
                <th class="text-center">Sr. No.</th>
                <th class="text-center">Part Name</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Total Amount</th>
            `);
                } else {
                    thead.html(`
                <th>Sr. No.</th>
                <th>Part Name</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>SGST (%)</th>
                <th>SGST (Rs.)</th>
                <th>CGST (%)</th>
                <th>CGST (Rs.)</th>
                <th>IGST (%)</th>
                <th>IGST (Rs.)</th>
                <th>Total Amount</th>
            `);
                }

                selectedOptions.each(function () {
                    const id = $(this).val();
                    const hasMeta = $(this).data('name') !== undefined;
                    const isCustomWithoutMeta = isNaN(parseInt(id)) && !hasMeta;

                    if (isCustomWithoutMeta) return;

                    const name = $(this).data('name') || id;
                    let price = parseFloat($(this).data('price')) || 0;
                    let qty = parseInt($(this).data('qty')) || 1;
                    const productGstRaw = $(this).data('product-gst');

                    // 🔹 Use preserved values if available
                    if (currentValues[id]) {
                        price = currentValues[id].price;
                        qty = currentValues[id].qty;
                    }

                    if (id) {
                        selectedParts.add(id);

                        const rowCount = $('#partsTable tbody tr').length + 1;

                        let cgstPercent = 0;
                        let sgstPercent = 0;
                        let igstPercent = 0;

                        if (withGST && productGstRaw) {
                            try {
                                const taxes = typeof productGstRaw === 'string' ? JSON.parse(productGstRaw) : productGstRaw;
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        if (tax.tax_name === 'CGST') cgstPercent = parseFloat(tax.tax_rate) || 0;
                                        if (tax.tax_name === 'SGST') sgstPercent = parseFloat(tax.tax_rate) || 0;
                                        if (tax.tax_name === 'IGST') igstPercent = parseFloat(tax.tax_rate) || 0;
                                    });
                                }
                            } catch (e) {
                                console.error("Error parsing product_gst", e);
                            }
                        }

                        // Fallback to global if still 0 and withGST is on
                        if (withGST && cgstPercent === 0 && sgstPercent === 0 && igstPercent === 0) {
                            cgstPercent = parseFloat($('#cgst_rate').val()) || 0;
                            sgstPercent = parseFloat($('#sgst_rate').val()) || 0;
                            igstPercent = parseFloat($('#igst_rate').val()) || 0;
                        }

                        const lineAmount = price * qty;
                        const sgstAmt = withGST ? (lineAmount * sgstPercent) / 100 : 0;
                        const cgstAmt = withGST ? (lineAmount * cgstPercent) / 100 : 0;
                        const igstAmt = withGST ? (lineAmount * igstPercent) / 100 : 0;
                        const total = lineAmount + sgstAmt + cgstAmt + igstAmt;

                        let row = '';

                        if (!withGST) {
                            row = `
                        <tr data-part-id="${id}" data-product-gst='${typeof productGstRaw === "string" ? productGstRaw : JSON.stringify(productGstRaw || [])}'>
                            <td class="text-center">${rowCount}</td>
                            <td class="text-center">${name}</td>
                            <td class="text-center">
                                <div class="input-group input-group-sm qty" style="max-width: 130px;">
                                    <button type="button" class="btn btn-outline-secondary btn-parts-qty-minus">-</button>
                                    <input type="number" class="form-control text-center parts-qty" min="1" step="1" value="${qty}">
                                    <button type="button" class="btn btn-outline-secondary btn-parts-qty-plus">+</button>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control text-center parts-price-input" min="0" step="0.01" value="${price.toFixed(2)}">
                            </td>
                            <td class="text-center part-total">${total.toFixed(2)}</td>
                            <td style="display:none;">
                                <input type="hidden" name="parts[${rowCount}][id]" value="${id}">
                                <input type="hidden" name="parts[${rowCount}][name]" value="${name}">
                                <input type="hidden" name="parts[${rowCount}][price]" value="${price}" class="hidden-price">
                                <input type="hidden" name="parts[${rowCount}][qty]" value="${qty}" class="hidden-qty">
                                <input type="hidden" name="parts[${rowCount}][total]" value="${total.toFixed(2)}" class="hidden-total">
                            </td>
                        </tr>
                    `;
                        } else {
                            row = `
                        <tr data-part-id="${id}" data-product-gst='${typeof productGstRaw === "string" ? productGstRaw : JSON.stringify(productGstRaw || [])}'>
                            <td>${rowCount}</td>
                            <td>${name}</td>
                            <td>
                                <div class="input-group input-group-sm" style="max-width: 130px;">
                                    <button type="button" class="btn btn-outline-secondary btn-parts-qty-minus">-</button>
                                    <input type="number" class="form-control text-center parts-qty" min="1" step="1" value="${qty}">
                                    <button type="button" class="btn btn-outline-secondary btn-parts-qty-plus">+</button>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control text-center parts-price-input" min="0" step="0.01" value="${price.toFixed(2)}">
                            </td>
                            <td class="sgst-percent">${sgstPercent}</td>
                            <td class="sgst-amt">${sgstAmt.toFixed(2)}</td>
                            <td class="cgst-percent">${cgstPercent}</td>
                            <td class="cgst-amt">${cgstAmt.toFixed(2)}</td>
                            <td class="igst-percent">${igstPercent}</td>
                            <td class="igst-amt">${igstAmt.toFixed(2)}</td>
                            <td class="part-total">${total.toFixed(2)}</td>
                            <input type="hidden" name="parts[${rowCount}][id]" value="${id}">
                            <input type="hidden" name="parts[${rowCount}][name]" value="${name}">
                            <input type="hidden" name="parts[${rowCount}][price]" value="${price}" class="hidden-price">
                            <input type="hidden" name="parts[${rowCount}][qty]" value="${qty}" class="hidden-qty">
                            <input type="hidden" name="parts[${rowCount}][sgst_percent]" value="${sgstPercent}" class="hidden-sgst-percent">
                            <input type="hidden" name="parts[${rowCount}][cgst_percent]" value="${cgstPercent}" class="hidden-cgst-percent">
                            <input type="hidden" name="parts[${rowCount}][igst_percent]" value="${igstPercent}" class="hidden-igst-percent">
                            <input type="hidden" name="parts[${rowCount}][sgst]" value="${sgstAmt.toFixed(2)}" class="hidden-sgst">
                            <input type="hidden" name="parts[${rowCount}][cgst]" value="${cgstAmt.toFixed(2)}" class="hidden-cgst">
                            <input type="hidden" name="parts[${rowCount}][igst]" value="${igstAmt.toFixed(2)}" class="hidden-igst">
                            <input type="hidden" name="parts[${rowCount}][total]" value="${total.toFixed(2)}" class="hidden-total">
                        </tr>
                    `;
                        }

                        tbody.prepend(row);
                    }
                });
                tbody.find("tr").each(function(index) {
                    $(this).find("td:first").text(index + 1); // update Sr. No.
                });

                calculateTotalParts();
                if (typeof calculateGrandTotal === 'function') calculateGrandTotal();
            }


            $(document).on("keydown", ".parts-price-input", function (e) {
                // if (e.key === "Tab" && !e.shiftKey) {
                //     const $inputs = $(".parts-price-input"); // all dynamic inputs
                //     const currentIndex = $inputs.index(this);

                //     // Only if it's the last input, go back to dropdown
                //     if (currentIndex === $inputs.length - 1) {
                //         e.preventDefault();
                //         $("#partsSelect").select2("open");
                //     }
                // }
                if (e.key === "Tab" && !e.shiftKey) {
                    e.preventDefault(); // stop normal tabbing
                    $("#partsSelect").select2("open"); // jump back to dropdown
                }
            });


            // Handle GST radio toggle change
            $("input[name='gst']").on("change", function () {
                const withGST = $(this).val() === "yes";
                rebuildPartsTable(withGST);
            });

            // On partsSelect change, rebuild table with current GST setting
            $('#partsSelect').on('change', function () {
                if (partsModalOpen) return;
                const withGST = $("input[name='gst']:checked").val() === "yes";
                rebuildPartsTable(withGST);
            });


            // Modal submit for creating new part via API
            $('#partsForm').on('submit', function (e) {
                e.preventDefault();
                const name = $('#partsName').val().trim();
                const categoryId = $('#partsCategory').val();
                const qty = Math.max(1, parseInt($('#partsQty').val()) || 1);
                // const hsn = $('#partsHSN').val().trim();
                // 🔹 FIX: always define hsn (safe fallback to empty string)
                const hsn = $('#partsHSN').length ? $('#partsHSN').val().trim() : '';
                const price = parseFloat($('#partsPrice').val()) || 0;

                if (!name || !categoryId || price < 0) {
                    alert('Please enter valid part name and price.');
                    return;
                }

                $('#partsSubmitBtn').prop('disabled', true);

                const formData = new FormData();
                formData.append('name', name);
                formData.append('category_id', categoryId);
                formData.append('price', price);
                formData.append('quantity', qty);
                formData.append('status', 'active');
                formData.append('SKU', Math.floor(Math.random() * 90000000) + 10000000); // temp SKU
                // if (hsn) formData.append('barcode', hsn);
                if (hsn) {
                    formData.append('barcode', hsn);
                }
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';
                if (selectedSubAdminId) formData.append('sub_admin_id', selectedSubAdminId);

                const authToken = localStorage.getItem('authToken') || '';

                $.ajax({
                    url: '/api/createProduct',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function (res) {
                        partsModal.hide();
                        const customValue = currentCustomPart;
                        partsModalOpen = false;
                        currentCustomPart = null;

                        if (res && res.status && res.product) {
                            const p = res.product;
                            $('#partsSelect option[value="' + customValue + '"]').remove();
                            let opt = $('#partsSelect option[value="' + p.id + '"]');
                            if (opt.length === 0) {
                                opt = $(new Option(p.name, p.id, true, true));
                                $('#partsSelect').append(opt);
                            }
                            opt.text(p.name);
                            opt.prop('selected', true)
                                .attr('data-name', p.name)
                                .attr('data-hsn', hsn || '-')
                                .attr('data-price', p.price)
                                .attr('data-product-gst', p.product_gst)
                                .data('name', p.name)
                                .data('hsn', hsn || '-')
                                .data('price', parseFloat(p.price) || 0)
                                .data('product-gst', p.product_gst);
                            $('#partsSelect').trigger('change');
                        } else {
                            // fallback local option
                            let opt = $('#partsSelect option[value="' + customValue + '"]');
                            if (opt.length === 0) {
                                opt = $(new Option(name, customValue, true, true));
                                $('#partsSelect').append(opt);
                            }
                            opt.text(name)
                                .prop('selected', true)
                                .attr('data-name', name)
                                .attr('data-hsn', hsn || '-')
                                .attr('data-price', price)
                                .data('name', name)
                                .data('hsn', hsn || '-')
                                .data('price', price);
                            $('#partsSelect').trigger('change');
                        }
                    },
                    error: function () {
                        // keep local option if ajax fails
                        const customValue = currentCustomPart;
                        partsModal.hide();
                        partsModalOpen = false;
                        currentCustomPart = null;
                        let opt = $('#partsSelect option[value="' + customValue + '"]');
                        if (opt.length === 0) {
                            opt = $(new Option(name, customValue, true, true));
                            $('#partsSelect').append(opt);
                        }
                        opt.text(name)
                            .prop('selected', true)
                            .attr('data-name', name)
                            .attr('data-hsn', hsn || '-')
                            .attr('data-price', price)
                            .data('name', name)
                            .data('hsn', hsn || '-')
                            .data('price', price);
                        $('#partsSelect').trigger('change');
                    },
                    complete: function () {
                        $('#partsSubmitBtn').prop('disabled', false);
                    }
                });
            });

            // Quantity +/- buttons for modal qty input (if exists)
            $('#partsQtyMinus').on('click', function () {
                const current = Math.max(1, parseInt($('#partsQty').val()) || 1);
                $('#partsQty').val(Math.max(1, current - 1));
            });
            $('#partsQtyPlus').on('click', function () {
                const current = Math.max(1, parseInt($('#partsQty').val()) || 1);
                $('#partsQty').val(current + 1);
            });
            $('#partsQty').on('input', function () {
                const v = parseInt($(this).val());
                if (isNaN(v) || v < 1) $(this).val(1);
            });

            // If modal closed without submit, remove temp option
            $('#partsModal').on('hidden.bs.modal', function () {
                if (partsModalOpen && currentCustomPart) {
                    const opt = $('#partsSelect option[value="' + currentCustomPart + '"]');
                    if (opt.length) {
                        opt.remove();
                        $('#partsSelect').trigger('change');
                    }
                    partsModalOpen = false;
                    currentCustomPart = null;
                }
            });


            // Delegate qty controls events for dynamic table rows
            // Function to recalc total
            function recalcRow($row) {
                const qty = parseFloat($row.find('.parts-qty').val()) || 0;
                const price = parseFloat($row.find('.parts-price-input').val()) || 0;
                let total = qty * price;

                $row.find('.hidden-qty').val(qty);
                $row.find('.part-total').text(total.toFixed(2));
                $row.find('.hidden-total').val(total.toFixed(2));

                // GST calculations
                if ($row.find('.hidden-sgst').length && $row.find('.hidden-cgst').length) {
                    const sgstPercent = parseFloat($row.find('.sgst-percent').text()) || 0;
                    const cgstPercent = parseFloat($row.find('.cgst-percent').text()) || 0;
                    const igstPercent = parseFloat($row.find('.igst-percent').text()) || 0;
                    
                    const sgstAmt = (total * sgstPercent) / 100;
                    const cgstAmt = (total * cgstPercent) / 100;
                    const igstAmt = (total * igstPercent) / 100;

                    $row.find('.sgst-amt').text(sgstAmt.toFixed(2));
                    $row.find('.cgst-amt').text(cgstAmt.toFixed(2));
                    $row.find('.igst-amt').text(igstAmt.toFixed(2));

                    $row.find('.hidden-sgst').val(sgstAmt.toFixed(2));
                    $row.find('.hidden-cgst').val(cgstAmt.toFixed(2));
                    $row.find('.hidden-igst').val(igstAmt.toFixed(2));

                    total += sgstAmt + cgstAmt + igstAmt;
                    $row.find('.hidden-total').val(total.toFixed(2));
                    $row.find('.part-total').text(total.toFixed(2));
                }

                // Recalculate parts total and grand total
                calculateTotalParts();
            }

            // Bind to qty +/- buttons
            $('#partsTable').on('click', '.btn-parts-qty-minus, .btn-parts-qty-plus', function () {
                const $row = $(this).closest('tr');
                const $qty = $row.find('.parts-qty');
                const current = parseInt($qty.val()) || 1;
                const newQty = $(this).hasClass('btn-parts-qty-plus') ? current + 1 : Math.max(1, current - 1);
                $qty.val(newQty);
                recalcRow($row);
            });

            // Bind to manual input changes
            $('#partsTable').on('input change', '.parts-qty, .parts-price-input', function () {
                const $row = $(this).closest('tr');
                recalcRow($row);
            });





            // Initial build without GST on page load
            rebuildPartsTable(false);

            // Function to calculate total parts amount and update hidden input or display
            function calculateTotalParts() {
                let total = 0;
                $('#partsTable tbody tr').each(function () {
                    const rowTotal = parseFloat($(this).find('.hidden-total').val()) || 0;
                    total += rowTotal;
                });
                $('#total_parts').val(total.toFixed(2));
                calculateGrandTotal();
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            let lastVehicleNumber = null;

            // Initialize Select2
            $('#vehicle_number').select2({
                placeholder: "Search or Add Vehicle",
                tags: true,
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('job_card.search_vehicle') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        if (data.exact) {
                            lastVehicleNumber = data.vehicle.vehicle_no;

                            // Prefill vehicle details
                            updateVehicleDetails(data.vehicle);

                            // ✅ Select customer in dropdown and trigger select2:select
                            if (data.vehicle.customer_id) {
                                $('#customer')
                                    .val(data.vehicle.customer_id) // set selected
                                    .trigger('change')             // update select2 UI
                                    .trigger({                     // manually trigger select2:select event
                                        type: 'select2:select',
                                        params: { data: { text: $("#customer option:selected").text() } }
                                    });
                            }

                            return {
                                results: [{
                                    id: data.vehicle.vehicle_no,
                                    text: data.vehicle.vehicle_no,
                                    selected: true
                                }]
                            };
                        } else {
                            return { results: data.vehicles };
                        }
                    }
                }
            });


            // Helper function to update vehicle details
            function updateVehicleDetails(vehicle) {
                $('#vehicleDetails').html(`
                <strong>Chassis No:</strong> ${vehicle.chassis_no || '-'}<br>
                <strong>Engine No:</strong> ${vehicle.engine_no || '-'}<br>
                <strong>KM/Hours:</strong> ${vehicle.km_hours || '-'}
            `);

                $('#chassis_no').val(vehicle.chassis_no || '');
                $('#engine_no').val(vehicle.engine_no || '');
                $('#due_km').val('');

                if (vehicle.model) {
                    $('#model_make').val(vehicle.model).trigger('change.select2');
                } else {
                    $('#model_make').val(null).trigger('change.select2');
                }
            }

            // On vehicle select
            // $('#vehicle_number').on('select2:select', function (e) {
            //     lastVehicleNumber = e.params.data.id;
            //     const vehicleNumber = lastVehicleNumber;

            //     // Fetch vehicle details (AJAX)
            //     $.ajax({
            //         url: "{{ route('job_card.search_vehicle') }}",
            //         dataType: 'json',
            //         type: 'GET',
            //         data: { q: vehicleNumber },
            //         success: function (data) {
            //             console.log(data);
            //             if (data.exact) {
            //                 updateVehicleDetails(data.vehicle);
            //             } else {
            //                 $('#vehicleDetails').html('<span class="text-muted">New vehicle — please fill details manually.</span>');
            //                 $('#chassis_no, #engine_no, #due_km_hours, #due_km').val('');
            //                 $('#model_make').val(null).trigger('change.select2');
            //             }
            //         },
            //         error: function () {
            //             $('#vehicleDetails').html('<span class="text-danger">Error fetching vehicle details.</span>');
            //         }
            //     });

            //     // Fetch vehicle history
            //     $.ajax({
            //         url: "{{ route('job_card.get-history') }}",
            //         method: 'GET',
            //         data: { vehicle_number: vehicleNumber },
            //         beforeSend: function () {
            //             $('#history_content').html('<p>Loading history...</p>');
            //         },
            //         success: function (response) {
            //             if (!response.status || response.data.length === 0) {
            //                 $('#history_content').html('<p>No history found.</p>');
            //                 return;
            //             }

            //             let historyHtml = `<div style="max-height:300px; overflow-y:auto;">
            //             <table class="table table-bordered">
            //                 <thead>
            //                     <tr>
            //                         <th>Job Card No</th>
            //                         <th>Date</th>
            //                         <th>Amount</th>
            //                         <th>View</th>
                                    
            //                     </tr>
            //                 </thead>
            //                 <tbody>`;

            //             response.data.forEach(item => {
            //                 historyHtml += `<tr>
            //                 <td>${item.job_card_number}</td>
            //                 <td>${item.job_date}</td>
            //                 <td>${item.total_amount}</td>
            //                 <td>
            //                     <a href="/job-card/${item.job_card_id}/view-pdf" target="_blank">
            //                         <i class="fas fa-eye" style="font-size: 20px;"></i>
            //                     </a>
            //                 </td>
            //             </tr>`;
            //             });

            //             historyHtml += `</tbody></table></div>`;
            //             $('#history_content').html(historyHtml);
            //         },
            //         error: function () {
            //             $('#history_content').html('<p>Error fetching service history.</p>');
            //         }
            //     });
            // });
            $('#vehicle_number').on('select2:select', function (e) {
    lastVehicleNumber = e.params.data.id;
    const vehicleNumber = lastVehicleNumber;

    // Fetch vehicle details (AJAX)
    $.ajax({
        url: "{{ route('job_card.search_vehicle') }}",
        dataType: 'json',
        type: 'GET',
        data: { q: vehicleNumber },
        success: function (data) {
            console.log(data);
            if (data.exact) {
                updateVehicleDetails(data.vehicle);

                // ✅ Auto-select customer also
                if (data.vehicle.customer_id) {
                    $('#customer')
                        .val(data.vehicle.customer_id) // set selected
                        .trigger('change')             // update select2 UI
                        .trigger({                     // manually trigger select2:select
                            type: 'select2:select',
                            params: { data: { text: $("#customer option:selected").text() } }
                        });
                }

            } else {
                $('#vehicleDetails').html('<span class="text-muted">New vehicle — please fill details manually.</span>');
                $('#chassis_no, #engine_no, #due_km_hours, #due_km').val('');
                $('#model_make').val(null).trigger('change.select2');
            }
        },
        error: function () {
            $('#vehicleDetails').html('<span class="text-danger">Error fetching vehicle details.</span>');
        }
    });

    // Fetch vehicle history
    $.ajax({
        url: "{{ route('job_card.get-history') }}",
        method: 'GET',
        data: { vehicle_number: vehicleNumber },
        beforeSend: function () {
            $('#history_content').html('<p>Loading history...</p>');
        },
        success: function (response) {
            if (!response.status || response.data.length === 0) {
                $('#history_content').html('<p>No history found.</p>');
                return;
            }

            let historyHtml = `<div style="max-height:300px; overflow-y:auto;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Job Card No</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>`;

            response.data.forEach(item => {
                historyHtml += `<tr>
                    <td>${item.job_card_number}</td>
                    <td>${item.job_date}</td>
                    <td>${item.total_amount}</td>
                    <td>
                        <a href="/job-card/${item.job_card_id}/view-pdf" target="_blank">
                            <i class="fas fa-eye" style="font-size: 20px;"></i>
                        </a>
                    </td>
                </tr>`;
            });

            historyHtml += `</tbody></table></div>`;
            $('#history_content').html(historyHtml);
        },
        error: function () {
            $('#history_content').html('<p>Error fetching service history.</p>');
        }
    });
});

            $('#vehicle_number').on('select2:open', function () {
                // Delay a little more to let Select2 finish rendering
                setTimeout(function () {
                    let searchBox = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchBox) {
                        searchBox.focus();
                        // Also move cursor to end just in case
                        searchBox.setSelectionRange(searchBox.value.length, searchBox.value.length);
                    }
                }, 100); // give 100ms to let select2 finish creating input
            });
        });
    </script>
    <script>
        function calculateGrandTotal() {
            const labour = parseFloat($('#total_labour').val()) || 0;
            const parts = parseFloat($('#total_parts').val()) || 0;

            // Labour discount
            const labourDiscountType = $('#labour_discount_type').val();
            const labourDiscountValue = parseFloat($('#labour_discount_amount').val()) || 0;
            let labourDiscount = (labourDiscountType === '%') ?
                (labour * labourDiscountValue / 100) :
                labourDiscountValue;

            // Parts discount
            const partsDiscountType = $('#parts_discount_type').val();
            const partsDiscountValue = parseFloat($('#parts_discount_amount').val()) || 0;
            let partsDiscount = (partsDiscountType === '%') ?
                (parts * partsDiscountValue / 100) :
                partsDiscountValue;

            // Totals after discount
            const finalLabour = labour - labourDiscount;
            const finalParts = parts - partsDiscount;

            const grandTotal = finalLabour + finalParts;
            const grandTotalFormatted = grandTotal.toFixed(2);

            // Update both summary and hidden total
            $('#grand_total_summary').val(grandTotalFormatted);
            $('#grand_total_form').val(grandTotalFormatted);

            // EMI auto-fill
            if ($('#paid_by').val() === 'EMI') {
                $('#emi_amount').val(grandTotalFormatted);
                calculateMonthlyEMI();
            }
        }




        function calculateMonthlyEMI() {
            let amount = parseFloat($('#emi_amount').val()) || 0;
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

            if (monthlyRate > 0) {
                emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, duration)) /
                    (Math.pow(1 + monthlyRate, duration) - 1);
            } else {
                emi = amount / duration;
            }

            $('#calculated_emi').val(emi.toFixed(2));
        }


        $(document).ready(function () {

            // Master controller: Paid By selection
            $('#paid_by').on('change', function () {
                const paidBy = $(this).val();

                // Hide all sections initially
                $('#online_type_container').hide();
                $('#cash_type_container').hide();
                $('#cash_fully_fields').hide();
                $('#cash_partially_fields').hide();
                $('#emi_container').hide();
                $('#partial_remaining_container').hide();
                $('#full_payment_block').hide();
                $('#cash_online_type_container').hide(); // ✅ hide Cash + Online container
                $('#cash_online_fully_fields').hide();
                $('#cash_online_partial_fields').hide();

                if (paidBy === 'Cash') {
                    $('#cash_type_container').show();
                    $('#cash_type').trigger('change');
                    $('input[name="type"]').remove();
                    $('<input>', {
                        type: 'hidden',
                        name: 'type',
                        id: 'payment_type_hidden'
                    }).appendTo('#jobCardForm');
                } else if (paidBy === 'Online') {
                    $('#online_type_container').show();
                    $('#online_type').trigger('change');
                    $('input[name="type"]').remove();
                    $('<input>', {
                        type: 'hidden',
                        name: 'type',
                        id: 'payment_type_hidden'
                    }).appendTo('#jobCardForm');
                } else if (paidBy === 'Cash_online') {
                    $('#cash_online_type_container').show();
                    $('#cash_online_type').trigger('change');
                    $('input[name="type"]').remove();
                    $('<input>', {
                        type: 'hidden',
                        name: 'type',
                        id: 'payment_type_hidden'
                    }).appendTo('#jobCardForm');
                } else if (paidBy === 'EMI') {
                    $('#emi_container').show();
                    $('#full_payment_block').hide(); // optional: hide if EMI replaces it
                    const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                    $('#emi_amount').val(grandTotal.toFixed(2));
                    calculateMonthlyEMI();
                    $('input[name="type"]').remove();
                }
            });


            // Cash type change logic
            $('#cash_type').on('change', function () {
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
                    $('#cash_partially_fields').show();
                    $('#payment_type_hidden').val('cash_partially');
                }
            });


            // Online type logic
            $('#online_type').on('change', function () {
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



            $('#cash_online_type').on('change', function () {
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

            $('#cash_online_cash_amount').on('input', function () {
                const cash = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                const upi = grandTotal - cash;
                $('#cash_online_upi_amount').val(upi.toFixed(2));
            });

            // Partially: Auto-calculate UPI & Remaining
            $('#cash_online_partial_cash').on('input', function () {
                const cash = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;

                // Auto-calculate UPI when cash entered
                const upi = grandTotal - cash;
                // $('#cash_online_partial_upi').val(upi.toFixed(2));

                // Remaining after both payments
                const remaining = grandTotal - (cash + upi);
                $('#cash_online_remaining').val(remaining.toFixed(2));
            });

            // Also update remaining if UPI manually changed
            $('#cash_online_partial_upi').on('input', function () {
                const cash = parseFloat($('#cash_online_partial_cash').val()) || 0;
                const upi = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;

                const remaining = grandTotal - (cash + upi);
                $('#cash_online_remaining').val(remaining.toFixed(2));
            });

            // Cash Fully: Auto-calculate UPI
            $('#cash_amount').on('input', function () {
                const cash = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                const upiAmount = grandTotal - cash;
                $('#upi_amount').val(upiAmount.toFixed(2));
            });

            // Cash Partially: Calculate remaining
            $('#cash_partial_amount').on('input', function () {
                const partial = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                const remaining = grandTotal - partial;
                $('#cash_remaining_amount').val(remaining.toFixed(2));
            });

            // Online Partially: Calculate remaining
            $('#partial_amount').on('input', function () {
                const partial = parseFloat($(this).val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                const remaining = grandTotal - partial;
                $('#remaining_amount').val(remaining.toFixed(2));
            });

            // EMI Calculation
            $('#emi_amount, #emi_duration, #emi_interest').on('input', calculateMonthlyEMI);

            $('#emi_duration').on('input', function () {
                const emiAmount = parseFloat($('#emi_amount').val()) || 0;
                const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                if (emiAmount <= 0 && $('#paid_by').val() === 'EMI') {
                    $('#emi_amount').val(grandTotal.toFixed(2));
                }
                calculateMonthlyEMI();
            });

            // Grand total calculation
            $('#labour_discount_type, #labour_discount_amount, #parts_discount_type, #parts_discount_amount, #total_labour, #total_parts')
                .on('input change', calculateGrandTotal);

            calculateGrandTotal();

            // Initial trigger
            $('#paid_by').trigger('change');
        });
    </script>
    <script>
        $(document).ready(function () {
            let lastCustomerName = null;

            // Enable select2 with tagging
            $('#customer').select2({
                tags: true,
                placeholder: "Select or Add Customer",
                allowClear: true
            });

            // Whenever a customer is selected
            $('#customer').on('select2:select', function (e) {
                let selectedOption = $(this).find(':selected');
                lastCustomerName = e.params.data.text; // store last selected or typed name

                // Extract data from option
                let gstNo = selectedOption.data('gst') || '';
                let phone = selectedOption.data('phone') || '';
                let address = selectedOption.data('address') || '';

                if (selectedOption.length && selectedOption.data('gst') !== undefined) {
                    // Existing customer from DB → populate fields
                    $('#gst_no').val(gstNo).prop('readonly', gstNo !== '');
                    $('#mobile').val(phone).prop('readonly', phone !== '');
                    $('textarea[name="address"]').val(address).prop('readonly', address !== '');
                } else {
                    // New customer → clear and allow typing
                    $('#gst_no').val('').prop('readonly', false);
                    $('#mobile').val('').prop('readonly', false);
                    $('textarea[name="address"]').val('').prop('readonly', false);
                }
            });
            $('#customer').on('select2:open', function () {
                // Delay to let Select2 fully render and grab focus
                setTimeout(() => {
                    const searchBox = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchBox) {
                        searchBox.focus({ preventScroll: true });
                    }
                }, 200); // 200ms is usually enough
            });

            // Trigger on page load if there's an old value
            $('#customer').trigger('change');
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
