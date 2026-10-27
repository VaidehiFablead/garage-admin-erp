@extends('layout.app')

@section('title', 'Add Advance Payment Details')

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
            <h4>Add Advance Payment Details</h4>

        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="advancePayForm" method="POST" enctype="multipart/form-data"
                action="{{ route('advance_pay.store') }}">
                @csrf
                <div class="row">
                    <!-- Staff -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Staff Name</label>
                            <select name="staff_id" class="form-control">
                                <option value="">Select Staff</option>
                                @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
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
                                placeholder="Enter amount">
                            <div class="text-danger error-amount"></div>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control">
                            <div class="text-danger error-date"></div>
                        </div>
                    </div>

                    <!-- Method -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Online">Online</option>

                            </select>
                            <div class="text-danger error-method"></div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason"></textarea>
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
    // const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
    // Handle form submission
    $("#advancePayForm").submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
        let formData = new FormData(this);

        $(".text-danger").html(""); // Clear previous error messages

        const $btn = $("#submitCustomerBtn"); // Replace with your actual button ID
        const originalText = $btn.html();
        formData.append("selectedSubAdminId", selectedSubAdminId) ?? null;
        // Show spinner and disable button
        $btn.html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
            )
            .prop("disabled", true);

        $.ajax({
            url: "/api/advance-payments", // Ensure API route is correct
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Authorization": "Bearer " + authToken,
            },
            success: function(response) {
                $btn.html(originalText).prop("disabled", false); // Restore button
                if (response.status) {
                    Swal.fire({
                        title: "Success!",
                        text: "Advance payment details added successfully!",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#ff9f43",
                    }).then(() => {
                        window.location.href = "/advance_pay";
                    });
                }
            },
            error: function(xhr) {
                $btn.html(originalText).prop("disabled", false); // Restore button
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(".error-" + key).html(value[
                            0]); // Show error below each field
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
        });
    });


});
</script>



@endpush
