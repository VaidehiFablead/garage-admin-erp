{{-- Payment Details Section (synced with daily-data/create billing logic) --}}
@php
    $existingJobCard = $jobCard ?? null;
    $existingPaymentMode = $existingJobCard->payment_method ?? '';
    $existingPaidType = $existingJobCard->payment_type ?? '';
    $existingDiscount = (float) ($existingJobCard->discount ?? 0);
    $existingCashAmount = $existingJobCard->cash_amount ?? '';
    $existingOnlineAmount = $existingJobCard->online_amount ?? '';
    $existingRemaining = $existingJobCard->remaining_amount ?? '';
    $existingEmiDuration = $existingJobCard->emi_duration ?? 1;
    $existingEmiMonthly = $existingJobCard->emi_months ?? '0.00';
    $existingPayable = $existingJobCard->total_subtotal_amount ?? '0.00';
    $isPaymentCompleted = $existingJobCard && (float) ($existingJobCard->remaining_amount ?? 0) <= 0;
@endphp

<div class="card mt-3 mb-3" style="border: 1px solid #e2e8f0; border-radius: 8px;">
    <div class="card-body p-3">
        <h6 class="mb-3 fw-bold">Payment Details</h6>
        @if ($isPaymentCompleted)
            <div class="alert alert-success d-flex align-items-center justify-content-between mb-0" role="alert"
                style="border-radius: 8px; background-color: #ecfdf5; border-color: #a7f3d0; color: #065f46;">
                <div>
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Payment Already Completed</strong>
                    @if (!empty($existingPaymentMode))
                        <span class="ms-2">(Mode: {{ strtoupper($existingPaymentMode) }})</span>
                    @endif
                </div>
                <span class="badge" style="background-color: #22c55e; color: #fff; padding: 6px 12px; border-radius: 6px;">
                    Paid: ₹{{ number_format(max((float) $existingPayable - $existingDiscount, 0), 2) }}
                </span>
            </div>
        @else
        <div class="row">
            <div class="col-md-4 col-lg-3 col-sm-6">
                <div class="form-group mb-2">
                    <label for="payable_amount">Payable Amount</label>
                    <input type="number" id="payable_amount" name="payable_amount" class="form-control"
                        placeholder="0" min="0" step="0.01" value="{{ $existingPayable }}" readonly>
                    <span class="text-danger error-payable_amount" id="payable_amount_error"></span>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-6">
                <div class="form-group mb-2">
                    <label for="discount_amount">Discount (₹)</label>
                    <input type="number" id="discount_amount" name="discount_amount"
                        class="form-control no-asterisk" placeholder="0" min="0" step="0.01"
                        value="{{ $existingDiscount > 0 ? $existingDiscount : '' }}">
                    <span class="text-danger error-discount" id="discount_error"></span>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12">
                <div class="form-group mb-2">
                    <label for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control">
                        <option value="">Select Payment Mode</option>
                        <option value="pending" {{ $existingPaymentMode === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cash" {{ $existingPaymentMode === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="online" {{ $existingPaymentMode === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="cashonline" {{ $existingPaymentMode === 'cashonline' ? 'selected' : '' }}>Cash + Online</option>
                        <option value="emi" {{ $existingPaymentMode === 'emi' ? 'selected' : '' }}>EMI</option>
                    </select>
                    <div class="text-danger error-payment_mode"></div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12 d-none" id="paid_type_container">
                <div class="form-group mb-2">
                    <label for="paid_type">Paid Type</label>
                    <select id="paid_type" name="paid_type" class="form-control">
                        <option value="">Select Paid Type</option>
                        <option value="full" {{ $existingPaidType === 'full' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ $existingPaidType === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                    </select>
                    <div class="text-danger error-paid_type"></div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12 d-none" id="cash_amount_input_container">
                <div class="form-group mb-2">
                    <label for="cash_amount_input">Cash Amount</label>
                    <input type="number" id="cash_amount_input" name="cash_amount" class="form-control"
                        placeholder="Enter Cash Amount" min="0" step="0.01" value="{{ $existingCashAmount }}">
                    <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12 d-none" id="amount_input_container">
                <div class="form-group mb-2">
                    <label for="amount_input">Amount</label>
                    <input type="number" id="amount_input" name="amount" class="form-control"
                        placeholder="Enter Amount" min="0" step="0.01"
                        value="{{ $existingPaymentMode === 'online' ? $existingOnlineAmount : $existingCashAmount }}">
                    <span class="text-danger error-cash-amount" id="amount_error"></span>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12 d-none" id="upi_amount_input_container">
                <div class="form-group mb-2">
                    <label for="upi_amount_input">Online Amount</label>
                    <input type="number" id="upi_amount_input" name="upi_amount" class="form-control"
                        placeholder="Enter Online Amount" min="0" step="0.01" value="{{ $existingOnlineAmount }}">
                    <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                </div>
            </div>

            <div class="col-md-4 col-lg-3 col-sm-12 d-none" id="pending_amount_container">
                <div class="form-group mb-2">
                    <label for="pending_amount">Pending Amount</label>
                    <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                        value="{{ $existingRemaining }}" readonly>
                    <span id="pending_error" style="color:red; font-size:12px;"></span>
                </div>
            </div>
        </div>

        <div class="row d-none" id="jc_emi_container">
            <div class="col-md-4 col-lg-3">
                <div class="form-group mb-2">
                    <label for="jc_emi_duration">EMI Duration (Months)</label>
                    <input type="number" id="jc_emi_duration" name="emi_duration" class="form-control"
                        min="1" max="12" value="{{ $existingEmiDuration ?: 1 }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="form-group mb-2">
                    <label for="jc_calculated_emi">Monthly Installment</label>
                    <input type="number" step="0.01" name="calculated_emi" id="jc_calculated_emi"
                        class="form-control" value="{{ $existingEmiMonthly ?: '0.00' }}" readonly>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jQuery === 'undefined') return;
        const $ = jQuery;

        window.jcPaymentInvalid = false;

        function getEffectivePayable() {
            const payable = parseFloat($('#payable_amount').val()) || 0;
            const discount = parseFloat($('#discount_amount').val()) || 0;
            return Math.max(payable - discount, 0);
        }

        function calculateJcMonthlyEMI() {
            const amount = getEffectivePayable();
            let duration = parseInt($('#jc_emi_duration').val(), 10) || 0;

            if (duration > 12) {
                duration = 12;
                $('#jc_emi_duration').val(12);
            }

            if (amount <= 0 || duration <= 0) {
                $('#jc_calculated_emi').val('0.00');
                return;
            }

            const emi = duration === 1 ? amount : (amount / duration);
            $('#jc_calculated_emi').val(emi.toFixed(2));
        }

        function togglePaymentModeDisabled() {
            const amount = parseFloat($('#payable_amount').val());
            if (!isNaN(amount) && amount > 0) {
                $('#payment_mode').prop('disabled', false);
            } else {
                $('#payment_mode').val('').prop('disabled', true).trigger('change');
            }
        }

        window.validateJcPaymentBeforeSubmit = function () {
            const mode = $('#payment_mode').val();
            const paidType = $('#paid_type').val();

            if (!mode || mode === 'pending' || mode === 'emi') {
                return true;
            }

            if (!paidType) {
                $('.error-paid_type').text('Please select Paid Type.');
                return false;
            }

            const effectivePayable = getEffectivePayable();

            if (paidType === 'full' && mode === 'cashonline') {
                const cash = parseFloat($('#cash_amount_input').val()) || 0;
                const upi = parseFloat($('#upi_amount_input').val()) || 0;
                const total = cash + upi;
                if (Math.abs(total - effectivePayable) > 0.01) {
                    const msg = `Cash + Online must equal payable amount after discount (₹${effectivePayable.toFixed(2)})`;
                    $('#cash_amount_error').text(msg);
                    $('#upi_amount_error').text(msg);
                    return false;
                }
            }

            if (window.jcPaymentInvalid) {
                return false;
            }

            return true;
        };

        $('#payable_amount').on('input change', function () {
            togglePaymentModeDisabled();
            calculateJcMonthlyEMI();
            $('#amount_input, #cash_amount_input, #upi_amount_input').trigger('input');
        });

        $('#jc_emi_duration, #discount_amount').on('input', function () {
            calculateJcMonthlyEMI();
        });

        $('#payment_mode').on('change', function () {
            const selectedMode = $(this).val();
            $('.error-paid_type, #cash_amount_error, #upi_amount_error, #pending_error').text('');

            if (selectedMode === 'cash' || selectedMode === 'online' || selectedMode === 'cashonline') {
                $('#paid_type_container').removeClass('d-none');
                $('#jc_emi_container').addClass('d-none');
            } else {
                $('#paid_type_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #jc_emi_container')
                    .addClass('d-none');
            }

            if (selectedMode === 'emi') {
                $('#jc_emi_container').removeClass('d-none');
                calculateJcMonthlyEMI();
            }

            $('#paid_type').trigger('change');
        });

        $('#paid_type').on('change', function () {
            const type = $(this).val();
            const selectedMode = $('#payment_mode').val();

            $('.error-paid_type, #cash_amount_error, #upi_amount_error, #pending_error').text('');
            $('#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container')
                .addClass('d-none');
            $('#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount')
                .prop('required', false);

            if (type === 'full') {
                window.jcPaymentInvalid = false;
                if (selectedMode === 'cashonline') {
                    $('#cash_amount_input_container, #upi_amount_input_container').removeClass('d-none');
                    $('#cash_amount_input, #upi_amount_input').prop('required', true);
                }
            } else if (type === 'partial') {
                if (selectedMode === 'cash' || selectedMode === 'online') {
                    $('#amount_input_container, #pending_amount_container').removeClass('d-none');
                    $('#amount_input, #pending_amount').prop('required', true);
                } else if (selectedMode === 'cashonline') {
                    $('#cash_amount_input_container, #upi_amount_input_container, #pending_amount_container')
                        .removeClass('d-none');
                    $('#cash_amount_input, #upi_amount_input, #pending_amount').prop('required', true);
                }
            }
        });

        $('#amount_input').on('input', function () {
            const paid = parseFloat($(this).val()) || 0;
            const effectivePayable = getEffectivePayable();
            const pending = effectivePayable - paid;

            $('#pending_amount').val(pending > 0 ? pending.toFixed(2) : '0.00');

            if (pending < -0.01 || isNaN(pending)) {
                window.jcPaymentInvalid = true;
                $('#pending_error').text('Cannot enter more than payable amount (after discount)');
            } else {
                window.jcPaymentInvalid = false;
                $('#pending_error').text('');
            }
        });

        $('#cash_amount_input, #upi_amount_input').on('input', function () {
            const cashPaid = parseFloat($('#cash_amount_input').val()) || 0;
            const onlinePaid = parseFloat($('#upi_amount_input').val()) || 0;
            const effectivePayable = getEffectivePayable();
            const totalPaid = cashPaid + onlinePaid;
            const pending = effectivePayable - totalPaid;

            $('#pending_amount').val(pending >= 0 ? pending.toFixed(2) : '0.00');
            $('#cash_amount_error, #upi_amount_error').text('');

            if (pending < -0.01 || isNaN(pending)) {
                window.jcPaymentInvalid = true;
                $('#pending_error').text('Enter correct amount cash + online (after discount)');
            } else {
                window.jcPaymentInvalid = false;
                $('#pending_error').text('');
            }
        });

        $('#discount_amount').on('input', function () {
            $('#amount_input, #cash_amount_input, #upi_amount_input').trigger('input');
        });

        // Initial state on page load (handles edit page pre-populated values)
        togglePaymentModeDisabled();
        if ($('#payment_mode').val()) {
            $('#payment_mode').trigger('change');
        }
    });
</script>
