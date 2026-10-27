@extends('layout.app')

@section('title', 'Edit Brand')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Brand</h4>
            <!-- <h6>Update your Brand</h6> -->
        </div>
        <a href="{{route('brand.list')}}" class="btn btn-added">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="editbrand">
                <input type="hidden" name="brand_id" id="brand_id" value="{{ $brand->id ?? '' }}">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" name="name" id="name" value="{{ $brand->name ?? '' }}">
                            <span class="error_name text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label> Brand Image</label>
                            <div class="image-upload">
                                <input type="file" name="image" id="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg'}}" alt="img">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                             <!-- ✅ ERROR MESSAGE -->
                            <span class="error_image text-danger"></span>

                            <!-- ✅ NEW IMAGE PREVIEW -->
                            <div class="image-preview" style="margin-top:10px;"></div>
                        </div>
                    </div>
                    
                    @if($brand->logo)
                    <div class="col-12">
                        <div class="product-list">
                            <ul class="row">
                                <li>
                                    <div class="productviews">
                                        <div class="productviewsimg">
                                            <img src="{{ env('ImagePath').'storage/' . $brand->logo }}" alt="Brand Image" width="100">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif
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
            let errorSpan = document.querySelector('.error_image');
            let previewDiv = document.querySelector('.image-preview');

            // ✅ clear error
            errorSpan.textContent = '';
            previewDiv.innerHTML = '';

            if (file) {

                // ❌ invalid type
                if (!file.type.startsWith("image/")) {
                    errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                    return;
                }

                // ❌ size
                if (file.size > 2 * 1024 * 1024) {
                    errorSpan.textContent = "Image must be less than 2MB.";
                    return;
                }

                // ✅ assign file to input
                let input = document.getElementById('image');
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;

                // trigger change
                input.dispatchEvent(new Event('change'));
            }
        });
        document.getElementById("image").addEventListener("change", function(event) {

            var file = event.target.files[0];
            var previewDiv = document.querySelector(".image-preview");
            var errorSpan = document.querySelector(".error_image");

            // ✅ clear error
            errorSpan.textContent = "";
            previewDiv.innerHTML = "";

            if (file) {

                // ❌ invalid type
                if (!file.type.startsWith("image/")) {
                    errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                    event.target.value = "";
                    return;
                }

                // ❌ size
                if (file.size > 2 * 1024 * 1024) {
                    errorSpan.textContent = "Image must be less than 2MB.";
                    event.target.value = "";
                    return;
                }

                // ✅ preview
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<img src="${e.target.result}" style="max-width:100px;">`;
                };
                reader.readAsDataURL(file);

                // ✅ hide old image
                document.querySelector('.product-list')?.remove();
            }
        });
    $(document).ready(function() {
        $(document).on('click', '.submit', function(e) {
            e.preventDefault();
            var authToken = localStorage.getItem("authToken");
            let formData = new FormData($('#editbrand')[0]);

            $('.error_name').text('');

            $.ajax({
                url: "/api/updateBrand", // Update this with your actual route
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
                            text: "Brand update successfully!",
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
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('.error_name, .error_image').text('');

                        $.each(errors, function(key, value) {

                            let errorKey = key.split('.')[0];

                            if (errorKey === 'logo') {
                                errorKey = 'image';
                            }

                            $('.error_' + errorKey).text(value.join(' '));
                        });
                    }
                }
            });
        });
    });
</script>
@endpush