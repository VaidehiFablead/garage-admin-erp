@extends('layout.app')

@section('title', 'Edit Advance Payment Details')

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
            <h4>Edit Advance Payment Details</h4>

        </div>
        <a href="{{ route('advance_pay.index') }}" class="btn btn-added">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="advancePayForm" method="POST" enctype="multipart/form-data"
                action="{{ route('advance_pay.update', $payment->id) }}">
                @csrf
                @method('PUT')


                <div class="row">
                    <!-- Staff -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Staff Name</label>
                            <select name="staff_id" class="form-control">
                                <option value="">Select Staff</option>
                                @foreach($staff as $member)
                                <option value="{{ $member->id }}"
                                    {{ $payment->staff_id == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="text-danger error-staff_id"></div>
                        </div>
                    </div>


                    <!-- Amount -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01"
                                value="{{ $payment->amount }}" placeholder="Enter amount">
                            <div class="text-danger error-amount"></div>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $payment->date }}">
                            <div class="text-danger error-date"></div>
                        </div>
                    </div>

                    <!-- Method -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="Cash" {{ $payment->method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online"
                                    {{ $payment->method == 'Online' ? 'selected' : '' }}>Online</option>

                            </select>
                            <div class="text-danger error-method"></div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="Enter reason">{{ $payment->reason }}</textarea>
                            <div class="text-danger error-reason"></div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2">Submit</button>
                        <a href="{{ route('advance_pay.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>




</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@push('js')
<script>
$(document).ready(function() {
    var authToken = localStorage.getItem("authToken");

    const paymentId = '{{ $payment->id }}';

    $("#advancePayForm").submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        $(".text-danger").html("");

        const $btn = $(".btn-submit");
        const originalText = $btn.html();

        $btn.html(
            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating...'
        ).prop("disabled", true);

        $.ajax({
            url: `/api/advance-payments/${paymentId}`,
            type: "POST", // Laravel expects POST + _method: PUT
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Authorization": "Bearer " + authToken,
            },
            success: function(response) {
                $btn.html(originalText).prop("disabled", false);
                Swal.fire({
                    title: "Success!",
                    text: "Advance payment updated successfully!",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#ff9f43",
                }).then(() => {
                    window.location.href = "/advance_pay";
                });
            },
            error: function(xhr) {
                $btn.html(originalText).prop("disabled", false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(".error-" + key).html(value[0]);
                    });
                } else {
                    Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                }
            },
        });
    });



});
</script>



@endpush
