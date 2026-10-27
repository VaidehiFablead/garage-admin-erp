@extends('layout.app')

@section('title', 'Add Daily Data')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add Daily Data</h4>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="customerForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Customer Details -->
                        <div class="row">
                            <!-- Left Column (5 fields) -->

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Record Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="expense_date" placeholder="Choose Date"
                                        class="datetimepicker form-control"
                                        value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
                                    <span class="text-danger error-expense_date" id="expense_date_error"></span>
                                    <div class="addonset">
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/calendars.svg' }}"
                                            alt="calendar">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control">
                                <div class="text-danger error-customer_name"></div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Phone</label>
                                <input type="number" name="phone" id="phone" class="form-control">
                                <div class="text-danger error-phone"></div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Vehicle Model</label>
                                <select name="model" id="model" class="form-control">
                                    <option value="">Select Model</option>
                                    @foreach($auto_car_model as $model)
                                        <option value="{{ $model->id }}">{{ ucwords($model->model_name) }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger error-model"></div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Vehicle No.</label>
                                <input type="text" name="vehicle_no" id="vehicle_no" class="form-control">
                                <div class="text-danger error-vehicle_no"></div>
                            </div>


                            <!-- Right Column (5 fields) -->

                            <div class="col-md-2 col-sm-6 col-6 form-group mb-3">
                                <label>Technician Name</label>
                                <select name="technician_name" id="technician_name" class="form-control">
                                    <option value="">Select Technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ ucwords($technician->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger error-technician_name"></div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group">
                                <label>Start Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="start_date" placeholder="Choose Date"
                                        class="datetimepicker form-control">
                                    <span class="text-danger error-start_date" id="start_date_error"></span>
                                    <div class="addonset">
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/calendars.svg' }}"
                                            alt="calendar">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group">
                                <label>End Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="end_date" placeholder="Choose Date"
                                        class="datetimepicker form-control">
                                    <span class="text-danger error-end_date" id="end_date_error"></span>
                                    <div class="addonset">
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/calendars.svg' }}"
                                            alt="calendar">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6 col-6 form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                                <div class="text-danger error-status"></div>
                            </div>

                            <div class="col-6 col-md-6 col-sm-6">
                                <div class="form-group align-self-end">
                                    <label>Comment</label>
                                    <textarea id="comment" name="comment" class="form-control"
                                        placeholder="Enter comment here"></textarea>

                                </div>
                            </div>
                        </div>

                    </div>
                    <hr>
                    <h5 class="mb-3"><strong>Expense Details</strong></h5>
                    <div class="row form-row mb-1">
                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group align-self-end">
                                <label>Vendor Name</label>
                                <input type="text" name="vendor_name[]" class="form-control vendor-name-input"
                                    placeholder="Enter Vendor Name">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group align-self-end">
                                <label>Item Name</label>
                                <input type="text" name="name[]" class="form-control name-input" placeholder="Enter Name">
                                <span class="error text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group align-self-end">
                                <label>Item Price</label>
                                <input type="number" name="price[]" class="form-control price-input"
                                    placeholder="Enter Price" min="0">
                                <span class="error text-danger"></span>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group align-self-end">
                                <label>Total Amount</label>
                                <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select name="payment_mode_expense[]" id="payment_mode_expense" class="form-control">
                                    <option value="">Select Payment Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-sm-12 d-flex justify-content-center align-self-center">
                            <button type="button" class="btn btn-success add-row" title="Add Item">+</button>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="form-group align-self-end">
                            <label>Total Expense Amount</label>
                            <!-- Add name attribute so it submits -->
                            <input type="text" id="total_expense_amount" name="total_expense_amount" class="form-control"
                                placeholder="0" readonly>
                        </div>
                    </div>
                    <hr>



                    <div class="row mt-4">
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group align-self-end">
                                <label>Payable Amount</label>
                                <!-- Add name attribute so it submits -->
                                <input type="number" id="payable_amount" name="payable_amount" class="form-control"
                                    placeholder="0">
                                <span class="text-danger error-payable_amount" id="payable_amount_error"></span>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-lg-3">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control">
                                    <option value="">Select Payment Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                                <div class="text-danger error-payment_mode"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="paid_type_container">
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

                        <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="cash_amount_input_container">
                            <div class="form-group">
                                <label>Cash Amount</label>
                                <input type="number" id="cash_amount_input" name="cash_amount" class="form-control"
                                    placeholder="Enter Cash Amount">
                                <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="amount_input_container">
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" id="amount_input" name="amount" class="form-control"
                                    placeholder="Enter Amount">
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="upi_amount_input_container">
                            <div class="form-group">
                                <label>UPI Amount</label>
                                <input type="number" id="upi_amount_input" name="upi_amount" class="form-control"
                                    placeholder="Enter UPI Amount">
                                <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-lg-2 d-none" id="pending_amount_container">
                            <div class="form-group">
                                <label>Pending Amount</label>
                                <input type="text" id="pending_amount" name="pending_amount" class="form-control" readonly>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-submit me-2">Submit</button>
                            <a href="{{ route('daily_data.index') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>
                </form>



            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");

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

                if (selectedMode === "cash" || selectedMode === "online") {
                    $("#paid_type_container").removeClass("d-none");
                } else {
                    $("#paid_type_container, #method_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container").addClass("d-none");
                }

                // Hide all specific inputs initially
                $("#cash_amount_input_container").addClass("d-none");
                $("#upi_amount_input_container").addClass("d-none");
                $("#amount_input_container").addClass("d-none");
                $("#pending_amount_container").addClass("d-none");
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
                $("#amount_input, #cash_amount_input, #upi_amount_input").prop("required", false);

                if (type === "full") {
                    if (selectedMode === "cash") {
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");

                        // Add required
                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                    } else if (selectedMode === "online") {
                        $("#upi_amount_input_container").removeClass("d-none");

                        // Add required
                        $("#upi_amount_input").prop("required", true);
                    }
                } else if (type === "partial") {
                    $("#amount_input_container").removeClass("d-none");
                    $("#pending_amount_container").removeClass("d-none");

                    // Add required
                    $("#amount_input").prop("required", true);
                }
            });

            $("#amount_input").on("input", function () {
                const partialPaid = parseFloat($(this).val()) || 0;
                const payable = parseFloat($("#payable_amount").val()) || 0;
                const pending = payable - partialPaid;

                $("#pending_amount").val(pending > 0 ? pending.toFixed(2) : "0.00");
            });

            function validateFullPayment() {
                const payable = parseFloat($("#payable_amount").val()) || 0;
                const cash = parseFloat($("#cash_amount_input").val()) || 0;
                const upi = parseFloat($("#upi_amount_input").val()) || 0;
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
            $("#cash_amount_input, #upi_amount_input").on("input", function () {
                validateFullPayment();
            });

            // Remove row functionality
            $(document).on('click', '.remove-row', function () {
                $(this).closest('.form-row').remove();
            });

            

            // Handle form submission
            $("#customerForm").submit(function (e) {
                e.preventDefault();
                
                if (!validateFullPayment()) {
                    e.preventDefault(); // stop form if validation fails
                    return false;
                }


                let formData = new FormData(this);
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
@endpush