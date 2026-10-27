@extends('layout.app')
@section('title', 'Service Category Edit')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Service Category</h4>
        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="categoryForm">
                <input type="hidden" name="category_id" id="category_id" value="{{ $autoCarCategory->id ?? '' }}">
                <div class="row">
                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" name="name" id="name" value="{{ $autoCarCategory->name ?? '' }}" class="form-control" required>
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit submit me-2">Submit</button>
                        <a href="{{ route('autocar.category.list') }}" class="btn btn-cancel">Cancel</a><br>
                        <span class="success_submit text-danger"></span>
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
        $(document).on('click', '.submit', function(e) {
            e.preventDefault();
            var authToken = localStorage.getItem("authToken");
            let formData = new FormData($('#categoryForm')[0]);
            $('.error_name').text('');
            $.ajax({
                url: "/api/autocar/updatecategory",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: "Success!",
                            text: "Service Category updated successfully!",
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('autocar.category.list') }}";
                            }
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('.error_' + key).text(value[0]);
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
