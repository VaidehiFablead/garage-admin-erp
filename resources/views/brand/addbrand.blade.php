@extends('layout.app')

@section('title', 'Add Brand')

@section('content')

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Add Brand</h4>
            <!-- <h6>Create new Brand</h6> -->
        </div>
        <a href="{{route('brand.list')}}" class="btn btn-added">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="addbrand">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" name="name" id="name">
                            <span class="error_name text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label> Product Image</label>
                            <div class="image-upload">
                                <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg'}}" alt="img">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="image-preview" style="margin-top: 10px;"></div>
                            <span class="error_logo text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a href="javascript:void(0);" class="btn btn-submit submit me-2">Submit</a>
                        <a href="{{route('brand.list')}}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
@push('js')
<script>
    document.getElementById("logo").addEventListener("change", function(event) {

        var file = event.target.files[0];
        var previewDiv = document.querySelector(".image-preview");
        var errorSpan = document.querySelector(".error_logo");

        // ✅ CLEAR ERROR
        errorSpan.textContent = "";
        previewDiv.innerHTML = "";

        if (file) {

            // ❌ INVALID TYPE
            if (!file.type.startsWith("image/")) {
                errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                event.target.value = "";
                return;
            }

            // ❌ SIZE VALIDATION
            if (file.size > 2 * 1024 * 1024) {
                errorSpan.textContent = "Image must be less than 2MB.";
                event.target.value = "";
                return;
            }

            // ✅ PREVIEW
            var reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.innerHTML = `<img src="${e.target.result}" style="max-width:100px;">`;
            };
            reader.readAsDataURL(file);
        }
    });
    let uploadBox = document.querySelector('.image-upload');

    uploadBox.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadBox.classList.add('drag-over');
    });

    uploadBox.addEventListener('dragleave', function(e) {
        uploadBox.classList.remove('drag-over');
    });

    uploadBox.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadBox.classList.remove('drag-over');

        let file = e.dataTransfer.files[0];
        let errorSpan = document.querySelector('.error_logo');
        let previewDiv = document.querySelector('.image-preview');

        // ✅ CLEAR ERROR
        errorSpan.textContent = '';
        previewDiv.innerHTML = '';

        if (file) {

            // ❌ INVALID TYPE
            if (!file.type.startsWith("image/")) {
                errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                return;
            }

            // ❌ SIZE
            if (file.size > 2 * 1024 * 1024) {
                errorSpan.textContent = "Image must be less than 2MB.";
                return;
            }

            // ✅ SET FILE TO INPUT
            let input = document.getElementById('logo');
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            // trigger preview
            input.dispatchEvent(new Event('change'));
        }
    });
    $(document).ready(function() {
        $(document).on('click', '.submit', function(e) {
            e.preventDefault();
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            var authToken = localStorage.getItem("authToken");
            let formData = new FormData($('#addbrand')[0]);
            if (selectedSubAdminId) {
                formData.append("sub_admin_id", selectedSubAdminId);
            }
            $('.error_name').text('');

            $.ajax({
                url: "/api/addBrand", // Update this with your actual route
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    if (response.status) { // Check 'status' instead of 'success'
                        Swal.fire({
                            title: "Success!",
                            text: "Brand added successfully!",
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('brand.list') }}";
                            }
                        });
                    }
                },
                // error: function(xhr) {
                //     if (xhr.status === 422) {
                //         let errors = xhr.responseJSON.errors;
                //         $.each(errors, function(key, value) {
                //             $('.error_' + key).text(value[0]);
                //         });
                //     }
                // }
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // Clear all error texts first
                        $('.error_name, .error_logo').text('');

                        $.each(errors, function(key, value) {
                            // For image array errors like images.0, images.1 etc, map them to 'images'
                            let errorKey = key.split('.')[0]; // Take 'images' from 'images.0'

                            // Append errors if multiple messages for same field
                            let errorMsg = value.join(' ');

                            // Display error message with custom text for duplicate brand
                            if (errorKey === 'name' && errorMsg.includes('unique')) {
                                $('.error_' + errorKey).text('This brand already exists. Please use a different name.');
                            } else {
                                $('.error_' + errorKey).text(errorMsg);
                            }
                        });
                    }
                }
            });
        });
    });
</script>
@endpush