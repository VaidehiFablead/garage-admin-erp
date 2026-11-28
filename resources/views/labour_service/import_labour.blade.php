@extends('layout.app')

@section('title', 'Import Labour Services')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Import Labour Services</h4>
            <h6>Upload CSV or Excel files with labour service details.</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('labour.list') }}" class="btn btn-cancel">
                <i class="fa fa-arrow-left me-1"></i> Back to Labour Services
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Left Column: Upload -->
                <div class="col-lg-7 col-sm-12">
                    <form id="importLabourPageForm" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Upload CSV / Excel File <span class="text-danger">*</span></label>
                            <div class="image-upload" style="border: 2px dashed #d7dde8; border-radius: 8px; padding: 40px 20px; text-align: center; cursor: pointer; background: #fafbfc;">
                                <input type="file" name="file" id="page_csv_file" accept=".csv, .xlsx, .xls, .txt" style="display: none;" required>
                                <div class="image-uploads" onclick="$('#page_csv_file').click();">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="upload icon" style="width: 56px; height: 56px; margin-bottom: 12px;">
                                    <h4 class="upload-message fw-bold mb-1" id="page_upload_message">Drag and drop a file to upload</h4>
                                    <p class="text-muted small mb-0">Supports .csv, .xlsx, or .xls (up to 10MB)</p>
                                </div>
                            </div>
                            <span class="text-danger small mt-1 d-block" id="page_file_error"></span>
                        </div>

                        <div class="form-group text-end mt-4">
                            <button type="submit" class="btn btn-submit submit me-2" id="btnPageImportSubmit" style="background-color: #ff9f43; border-color: #ff9f43; color: #fff;">
                                <i class="fas fa-upload me-1"></i> Import Now
                            </button>
                            <a href="{{ route('labour.list') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Instructions & Sample Files -->
                <div class="col-lg-5 col-sm-12">
                    <div class="card border p-3 bg-light">
                        <h5 class="fw-bold mb-3"><i class="fas fa-file-download text-primary me-2"></i>Download Sample Files</h5>
                        <p class="text-muted small">You can download and use our pre-formatted sample templates with standard columns:</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a href="{{ route('labour.sample.csv') }}" class="btn btn-outline-secondary btn-sm" download>
                                <i class="fas fa-file-csv text-danger me-1"></i> Download Sample CSV
                            </a>
                            <a href="{{ route('labour.sample.excel') }}" class="btn btn-outline-secondary btn-sm" download>
                                <i class="fas fa-file-excel text-success me-1"></i> Download Sample Excel (.xlsx)
                            </a>
                        </div>

                        <h6 class="fw-bold mb-2">Column Instructions:</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <strong>Service Name:</strong>
                                <span class="badge bg-success ms-1">Required</span>
                                <div class="text-muted">Unique name of the labour service (e.g. <em>Full Engine Service</em>, <em>Car Wash</em>).</div>
                            </li>
                            <li class="mb-2">
                                <strong>Price:</strong>
                                <span class="badge bg-success ms-1">Required</span>
                                <div class="text-muted">Service charge / rate (numeric, e.g. <em>800.00</em>). Currency symbols will be cleaned automatically.</div>
                            </li>
                        </ul>
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
    $('#page_csv_file').on('change', function() {
        const file = this.files && this.files[0];
        if (file) {
            const sizeKb = (file.size / 1024).toFixed(1);
            $('#page_upload_message').html(`<i class="fas fa-file-excel text-success me-1"></i> ${file.name} <span class="badge bg-secondary ms-1">(${sizeKb} KB)</span>`);
            $('#page_file_error').text('');
        } else {
            $('#page_upload_message').text('Drag and drop a file to upload');
        }
    });

    $('#importLabourPageForm').on('submit', function(e) {
        e.preventDefault();

        const fileInput = $('#page_csv_file')[0];
        if (!fileInput.files || !fileInput.files.length) {
            $('#page_file_error').text('Please select a CSV or Excel file.');
            return;
        }

        const $btn = $('#btnPageImportSubmit');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');
        $('#page_file_error').text('');

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
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
                "Authorization": "Bearer " + localStorage.getItem("authToken"),
            },
            success: function(response) {
                $btn.prop('disabled', false).html(originalHtml);
                if (response.status) {
                    Swal.fire({
                        title: "Import Successful!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#ff9f43",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "{{ route('labour.list') }}";
                    });
                } else {
                    Swal.fire({
                        title: "Import Failed",
                        text: response.message || "Failed to import file.",
                        icon: "error",
                        confirmButtonColor: "#ff9f43",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                let message = "Something went wrong during import.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $('#page_file_error').text(message);
                Swal.fire({
                    title: "Error!",
                    text: message,
                    icon: "error",
                    confirmButtonColor: "#ff9f43",
                    confirmButtonText: "OK"
                });
            }
        });
    });
});
</script>
@endpush
