@extends('layout.app')

@section('title', 'Debit Notes Add')

@push('css')
<style>
@media (max-width: 768px) {
    .select2-container {
        width: 100% !important;
    }
    #reason {
        height: 70px !important;
        resize: none;
    }
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Create Debit Note</h4>
        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="debit-note-item-form">
                @csrf
                <input type="hidden" name="order_id" id="order_id">
                <input type="hidden" name="purchase_id" id="purchase_id">
                <input type="hidden" name="user_id" id="user_id">
                
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Transaction Type</label>
                            <select name="transaction_type" id="transaction_type" class="form-control transaction_type-select2">
                                <option value="payment">Payment</option>
                                <option value="receipt">Job Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6" id="invoice_number_div">
                        <div class="form-group">
                            <label>Invoice Number</label>
                            <select name="invoice_number" id="invoice_number" class="form-control invoice_number-select2">
                                <option value="">Select Invoice Number</option>
                                {{-- Data will be loaded via AJAX --}}
                            </select>
                            <div class="text-danger error-purchase_id"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6" id="order_number_div" style="display: none;">
                        <div class="form-group">
                            <label>Job Card Number</label>
                            <select name="order_number" id="order_number" class="form-control order_number-select2">
                                <option value="">Select Job Card Number</option>
                                {{-- Data will be loaded via AJAX --}}
                            </select>
                            <div class="text-danger error-order_id"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">   
                        <div class="form-group">
                            <label id="user_label">Vendor Name</label>
                            <input type="text" id="user_name" class="form-control" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Debit Note Type</label>
                            <select name="credit_note_type" id="credit_note_type" class="form-control credit_note_type-select2">
                                <option value="">Select Type</option>
                                {{-- Data will be loaded via AJAX --}}
                            </select>
                            <div class="text-danger error-credit_note_type"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" name="total_amount" id="total_amount" class="form-control" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Paid Amount</label>
                            <input type="text" name="paid_amount" id="paid_amount" class="form-control" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Remaining Amount</label>
                            <input type="text" name="remaining_amount" id="remaining_amount" class="form-control" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Settlement Amount</label>
                            <input type="number" step="0.01" name="settlement_amount" id="settlement_amount" class="form-control" value="0">
                            <div class="text-danger error-settlement_amount"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Final Total</label>
                            <input type="text" name="final_total" id="final_total" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" id="reason" class="form-control"></textarea>
                            <div class="text-danger error-reason"></div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2" id="submit-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Submit
                        </button>
                        <a href="{{ route('debit-notes-items.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

        $('.transaction_type-select2, .invoice_number-select2, .order_number-select2, .credit_note_type-select2').select2({
            placeholder: "Select option",
            allowClear: true,
            width: '100%'
        });

        function toggleLoading(isLoading) {
            const btn = $('#submit-btn');
            const spinner = btn.find('.spinner-border');
            if (isLoading) {
                btn.attr('disabled', true);
                spinner.removeClass('d-none');
            } else {
                btn.attr('disabled', false);
                spinner.addClass('d-none');
            }
        }

        $('#transaction_type').on('change', function() {
            let type = $(this).val();
            if (type === 'receipt') {
                $('#order_number_div').show();
                $('#invoice_number_div').hide();
                $('#user_label').text(' Customer Name');
            } else {
                $('#order_number_div').hide();
                $('#invoice_number_div').show();
                $('#user_label').text('Vendor Name');
            }
            // Clear fields
            $('#order_id, #purchase_id, #user_id, #user_name, #total_amount, #paid_amount, #remaining_amount, #final_total').val('');
            $('#order_number, #invoice_number').val('').trigger('change');
        });

        function loadCreateData() {
            let url = "{{ route('debit-note-items.create-data') }}";
            if (selectedSubAdminId && selectedSubAdminId !== "null") {
                url += `?selectedSubAdminId=${selectedSubAdminId}`;
            }

            $.ajax({
                url: url,
                type: "GET",
                headers: { "Authorization": "Bearer " + authToken },
                success: function(response) {
                    if (response.status === 'success') {
                        let invoiceOptions = '<option value="">Select Invoice Number</option>';
                        response.invoices.forEach(function(invoice) {
                            invoiceOptions += `<option value="${invoice.invoice_number}">${invoice.invoice_number}</option>`;
                        });
                        $('#invoice_number').html(invoiceOptions).trigger('change');

                        let orderOptions = '<option value="">Select Job Card Number</option>';
                        if (Array.isArray(response.jobCards) && response.jobCards.length) {
                            response.jobCards.forEach(function(card) {
                                if (card.job_card_number) {
                                    orderOptions += `<option value="${card.job_card_number}">${card.job_card_number}</option>`;
                                }
                            });
                        } else if (Array.isArray(response.orders)) {
                            response.orders.forEach(function(order) {
                                orderOptions += `<option value="${order.order_number}">${order.order_number}</option>`;
                            });
                        }
                        $('#order_number').html(orderOptions).trigger('change');

                        let typeOptions = '<option value="">Select Type</option>';
                        response.creditNoteTypes.forEach(function(type) {
                            typeOptions += `<option value="${type.id}">${type.type_name}</option>`;
                        });
                        $('#credit_note_type').html(typeOptions).trigger('change');
                    }
                }
            });
        }

        loadCreateData();

        $('#invoice_number').on('change', function() {
            var invoiceNumber = $(this).val();
            if (invoiceNumber) {
                let url = `/api/getPurchaseDetails/${invoiceNumber}`;
                if (selectedSubAdminId && selectedSubAdminId !== "null") {
                    url += `?selectedSubAdminId=${selectedSubAdminId}`;
                }
                $.ajax({
                    url: url,
                    type: "GET",
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(response) {
                        if (response.purchase) {
                            var data = response.purchase;
                            $('#purchase_id').val(data.id);
                            $('#user_id').val(data.vendor_id);
                            $('#user_name').val(data.vendor_name);
                            $('#total_amount').val(data.total_amount);
                            $('#paid_amount').val(data.paid_amount);
                            $('#remaining_amount').val(data.remaining_amount);
                            $('#settlement_amount').val(0);
                            calculateFinalTotal();
                        }
                    }
                });
            }
        });

        $('#order_number').on('change', function() {
            var orderNumber = $(this).val();
            if (orderNumber && $('#transaction_type').val() === 'receipt') {
                let url = `/api/jobcards/details/${orderNumber}`;
                if (selectedSubAdminId && selectedSubAdminId !== "null") {
                    url += `?selectedSubAdminId=${selectedSubAdminId}`;
                }
                $.ajax({
                    url: url,
                    type: "GET",
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(response) {
                        if (response.jobcard) {
                            var data = response.jobcard;
                            $('#order_id').val(data.id);
                            $('#user_id').val(data.customer_id);
                            $('#user_name').val(data.customer_name);
                            $('#total_amount').val(data.total_amount);
                            $('#paid_amount').val(data.paid_amount);
                            $('#remaining_amount').val(data.remaining_amount);
                            $('#settlement_amount').val(0);
                            calculateFinalTotal();
                        }
                    }
                });
            }
        });

        $('#settlement_amount').on('input', function() {
            calculateFinalTotal();
        });

        function calculateFinalTotal() {
            var totalAmt = parseFloat($('#total_amount').val()) || 0;
            var settlement = parseFloat($('#settlement_amount').val()) || 0;
            $('#final_total').val((totalAmt - settlement).toFixed(2));
        }

        $('#debit-note-item-form').on('submit', function(e) {
            e.preventDefault();
            $('.text-danger').text('');
            toggleLoading(true);
            
            let formData = $(this).serialize();
            if (selectedSubAdminId) {
                formData += `&selectedSubAdminId=${selectedSubAdminId}`;
            }

            $.ajax({
                url: "{{ url('api/debit-note-items/store') }}",
                type: 'POST',
                headers: { "Authorization": "Bearer " + authToken },
                data: formData,
                success: function(response) {
                    toggleLoading(false);
                    if (response.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.href = "{{ route('debit-notes-items.index') }}";
                        });
                    }
                },
                error: function(xhr) {
                    toggleLoading(false);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            $(`.error-${key}`).text(errors[key][0]);
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonColor: "#ff9f43",
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
