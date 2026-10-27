@extends('layout.app')
@section('title', 'Model Add')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Add Model</h4>
        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="modelForm">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="model_name">Model Name</label>
                            <input type="text" name="model_name" id="model_name" class="form-control" required>
                            <span class="error_model_name text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="model_brand">Model Brand</label>
                            <input type="text" name="model_brand" id="model_brand" class="form-control" required>
                            <span class="error_model_brand text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit submit me-2">Submit</button>
                        <a href="{{ route('autocar.model.list') }}" class="btn btn-cancel">Cancel</a>
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
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            let formData = new FormData($('#modelForm')[0]);
            if (selectedSubAdminId) {
                formData.append("sub_admin_id", selectedSubAdminId);
            }
            $('.error_model_name, .error_model_brand').text('');
            $.ajax({
                url: "/api/addmodel",
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
                            text: "Model added successfully!",
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('autocar.model.list') }}";
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