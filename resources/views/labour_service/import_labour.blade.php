@extends('layout.app')

@section('title', 'Import Labour Services')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Labour Services</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="requiredfield">
                    <h4>Upload CSV or Excel File (.csv / .xlsx)</h4>
                </div>
                <div class="page-btn">
                    <a href="{{ route('labour.import.sample') }}" class="btn btn-submit w-100">Download Sample File</a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Upload & Download -->
                <div class="col-lg-6 col-sm-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="image-upload">
                                    <input type="file" name="csv_file" id="csv_file" accept=".csv,.xlsx,.xls">
                                    <div class="image-uploads">
                                        <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg'}}" alt="upload icon">
                                        <h4 class="upload-message">Drag and drop a CSV or Excel (.xlsx) file to upload</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit & Cancel -->
                        <div class="col-lg-12 text-end">
                            <div class="form-group mb-3">
                                <a href="javascript:void(0);" class="btn btn-submit submit me-2">Submit</a>
                                <a href="{{ route('labour.list') }}" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Labour Service Field Details -->
                <div class="col-lg-6 col-sm-12">
                    <div class="row">
                        <!-- Required Fields -->
                        <div class="col-lg-12">
                            <div class="productdetails productdetailnew mb-3">
                                <ul class="product-bar">
                                    <li>
                                        <h4>Service Name</h4>
                                        <h6 class="manitorygreen">Excel: "Service Name" / "Name" &nbsp;|&nbsp; CSV: "name" / "service"</h6>
                                    </li>
                                    <li>
                                        <h4>Price</h4>
                                        <h6 class="manitorygreen">Excel: "Price" / "Amount" / "Rate" &nbsp;|&nbsp; CSV: "price" / "rate"</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            var csv_file = $('#csv_file')[0].files[0];

            if (!csv_file) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a CSV or Excel (.xlsx) file.',
                    confirmButtonColor: '#ff9f43',
                    confirmButtonText: 'OK'
                });
                return;
            }

            var $btn = $(this);
            var originalText = $btn.html();
            $btn.css('pointer-events', 'none').html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            var formData = new FormData();
            formData.append("file", csv_file);
            formData.append("csv_file", csv_file);
            if (selectedSubAdminId) {
                formData.append("sub_admin_id", selectedSubAdminId);
                formData.append("selectedSubAdminId", selectedSubAdminId);
            }

            $.ajax({
                url: "/api/importLabourServices",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    $btn.css('pointer-events', 'auto').html(originalText);
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: response.message,
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('labour.list') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed',
                            text: response.message || 'Failed to import file.',
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $btn.css('pointer-events', 'auto').html(originalText);
                    let message = 'An unexpected error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        document.getElementById('csv_file').addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            const messageDiv = document.querySelector('.upload-message');
            if (fileName) {
                messageDiv.textContent = fileName;
            } else {
                messageDiv.textContent = 'Drag and drop a CSV or Excel (.xlsx) file to upload';
            }
        });
    });
</script>
@endpush
