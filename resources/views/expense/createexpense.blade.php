@extends('layout.app')

@section('title', 'Expense Add')

@section('content')
<style>
    @media screen and (max-width: 768px) {
        .form-group {
            margin-bottom: 10px !important
        }
        #description {
            height: 70px !important;
            resize: none;
        }
    }
    @media screen and (max-width:768px){
    #payment_method,
    #payment_method + .select2-container {
        width: 100% !important;
    }
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Expense Add</h4>
            <!-- <h6>Add/Update Expenses</h6> -->
        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>
    <form id="expenseForm">
        <div class="card">
            <div class="card-body">
                <input type="hidden" name="selectedSubAdminId" id="selectedSubAdminId">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Expense Name</label>
                            <div class="input-groupicon">
                                <input type="text" name="expense_name" placeholder="Expense Name">
                                <span class="text-danger error" id="expense_name_error"></span>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Expense Date </label>
                            <div class="input-groupicon">
                                <input type="text" name="expense_date" placeholder="Choose Date" class="datetimepicker" value="{{ \Carbon\Carbon::yesterday()->format('d-m-Y') }}">
                                <span class="text-danger error" id="expense_date_error"></span>
                                <div class="addonset">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/calendars.svg' }}" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Amount</label>
                            <div class="input-groupicon">
                                <input type="number" class="form-control" name="amount" placeholder="Enter Amount">
                                <span class="text-danger error" id="amount_error"></span>
                                <!-- <div class="addonset">
                                    <img src="admin/assets/img/icons/dollar.svg" alt="img">
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Expense Type</label>
                            <select class="select" name="expense_type_id" id="expense_type_id">
                                <option value="">Select Expense Type</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="error text-danger" id="expense_type_id_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select class="select" name="payment_method" id="payment_method">
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                            <span class="error text-danger" id="payment_method_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Expense for</label>
                            <textarea name="description" id="description" class="form-control" placeholder="Purpose of Expense" rows="3"></textarea>
                            <span class="text-danger error" id="description_error"></span>
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2">Submit</button>
                        <a href="{{ route('expense.list') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')

<script>
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        var authToken = localStorage.getItem("authToken");
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        if (selectedSubAdminId) {
            document.getElementById('selectedSubAdminId').value = selectedSubAdminId;
        }
        // Cache submit button
        var $btn = $('#expenseForm button[type="submit"]');
        var originalText = $btn.html();

        // Show loader and disable button
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...')
            .prop('disabled', true);

        // Clear previous errors
        $('.error').text('');

        $.ajax({
            url: '{{ route("expenses.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                "Authorization": "Bearer " + authToken,
            },
            success: function(response) {
                // Restore button
                $btn.html(originalText).prop('disabled', false);
                // Swal.fire("Expense Added!", response.message, "success");
                // $('#expenseForm')[0].reset();
                Swal.fire({
                    title: "Success",
                    text: "Expense Added successfully",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#ff9f43" // Set custom button color
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('expense.list') }}";

                    }
                });
            },
            error: function(xhr) {
                // Restore button
                $btn.html(originalText).prop('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    // Loop and show errors
                    for (let field in errors) {
                        $('#' + field + '_error').text(errors[field][0]);
                    }
                }
            }
        });
    });

    $(document).ready(function() {
        let authToken = localStorage.getItem("authToken");
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        let url = "/api/expense-types";
        if (selectedSubAdminId) {
            url += `?selectedSubAdminId=${selectedSubAdminId}`;
        }

        function loadExpenseTypes() {
            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    // Clear existing options
                    $('#expense_type_id').empty().append('<option value="">Select Expense Type</option>');

                    // Append new options from API
                    response.data.forEach(function(type) {
                        $('#expense_type_id').append(`<option value="${type.id}">${type.type}</option>`);
                    });
                },
                error: function() {
                    console.error('Failed to load expense types.');
                }
            });
        }

        // Call the function on page load
        loadExpenseTypes();
    });
</script>


@endpush
