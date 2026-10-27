@extends('layout.app')

@section('title', 'Expense Edit')

@section('content')
<style>
    @media screen and (max-width: 768px) {
        .form-group {
            margin-bottom: 10px !important
        }
        #payment_method,
        #payment_method + .select2-container {
            width: 100% !important;
        }
        #description {
            height: 70px !important;
            resize: none;
        }
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Expense</h4>

        </div>
        <a href="{{ route('expense.list') }}" class="btn btn-added">Back</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                {{-- Expense Name --}}
                <div class="col-lg-4 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Expense Name</label>
                        <div class="input-groupicon">
                            <input type="text" id="expense_name" placeholder="Choose Expense Name">
                        </div>
                    </div>
                </div>

                {{-- Expense Date --}}
                <div class="col-lg-4 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Expense Date</label>
                        <div class="input-groupicon">
                            <input type="text" id="expense_date" placeholder="Choose Date" class="datetimepicker">
                            <div class="addonset">
                                <img src="{{ env('ImagePath').'/admin/assets/img/icons/calendars.svg' }}" alt="img">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Amount --}}
                <div class="col-lg-4 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Amount</label>
                        <div class="input-groupicon">
                            <input type="text" id="amount">

                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Expense Type</label>
                        <select class="select" name="expense_type_id" id="expense_type_id">
                            <option value="">Select Expense Type</option>

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
                        <textarea id="description" name="description" class="form-control" placeholder="Purpose of Expense" rows="3"></textarea>
                        <span class="text-danger error" id="description_error"></span>
                    </div>
                </div>


                {{-- Hidden ID --}}
                <input type="hidden" id="expense_id">

                {{-- Buttons --}}
                <div class="col-lg-12">
                    <a class="btn btn-submit me-2" id="updateExpense">Update</a>
                    <a href="{{ route('expense.list') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        let url = window.location.pathname;
        let expenseId = url.split("/").pop();
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        let urledit = `/api/expenses/${expenseId}/edit`;
        if (selectedSubAdminId) {
            urledit += `?selectedSubAdminId=${selectedSubAdminId}`;
        }
        // Fetch and populate data
        $.ajax({
            url: urledit,
            method: 'GET',
            headers: {
                Authorization: `Bearer ${authToken}`,
            },
            success: function(response) {
                const expense = response.data;
                const expenseTypes = response.expenseTypes;
                
                $('#payment_method').val(expense.payment_method).trigger('change');

                // Build the options list with Expense Type names
                let options = '<option value="">Select Expense Type</option>';
                expenseTypes.forEach(function(type) {
                    // Convert both to numbers before comparing
                    const selected = (Number(type.id) === Number(expense.expense_type_id)) ? 'selected' : '';
                    options += `<option value="${type.id}" ${selected}>${type.type}</option>`;
                });
                $('#expense_type_id').html(options);
                $('#expense_name').val(expense.expense_name);
                $('#expense_date').val(formatDate(expense.expense_date));
                $('#amount').val(expense.amount);
                $('#description').val(expense.description);
                $('#expense_id').val(expense.id);
            },
            error: function() {
                window.location.href = "{{ route('expense.list') }}";
            }
        });

        // Format date from yyyy-mm-dd to dd-mm-yyyy
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        // Update logic
        $('#updateExpense').click(function() {
            const id = $('#expense_id').val();

            const $btn = $(this);
            const originalText = $btn.html();

            // Show loader and disable button
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...')
                .prop('disabled', true);

            $.ajax({
                url: `/api/expenses/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    expense_name: $('#expense_name').val(),
                    expense_date: $('#expense_date').val(),
                    amount: $('#amount').val(),
                    payment_method: $('#payment_method').val(),
                    description: $('#description').val(),
                    expense_type_id: $('#expense_type_id').val(), // <-- this was missing!
                },
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
                success: function(res) {
                    // Restore original button
                    $btn.html(originalText).prop('disabled', false);

                    Swal.fire({
                        title: "Success!",
                        text: "Expense updated successfully!",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#ff9f43" // your custom color
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('expense.list') }}";
                        }
                    });
                },
                error: function(xhr) {
                    $btn.html(originalText).prop('disabled', false);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let messages = Object.values(errors).map(msg => msg[0]).join("\n");
                        Swal.fire("Validation Error", messages, "error"); // show proper error instead of redirect
                    } else {
                        Swal.fire("Error", "Something went wrong. Try again.", "error");
                    }
                }
            });
        });
    });
</script>

@endpush