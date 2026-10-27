@extends('layout.app')
@section('title', ' Service Add')
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add Labour Service</h4>
            </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="serviceForm">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name">Labour Service Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control" required>
                                <span class="error_price text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-submit submit me-2">Submit</button>
                            <a href="{{ route('labour.list') }}" class="btn btn-cancel">Cancel</a>
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
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                var authToken = localStorage.getItem("authToken");
                let formData = new FormData($('#serviceForm')[0]);
                if (selectedSubAdminId) {
                    formData.append("sub_admin_id", selectedSubAdminId);
                }
                $('.error_name, .error_price').text('');

                $.ajax({
                    url: "/api/addservice",
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
                                text: "Labour Service added successfully!",
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('labour.list') }}";
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
