@extends('layout.app')

@section('title', 'Category Edit')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Product Category</h4>
            <!-- <h6>Edit a product Category</h6> -->
        </div>
        <a href="{{route('category.list')}}" class="btn btn-added">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="categoryForm">
                <input type="hidden" name="category_id" id="category_id" value="{{ $category->id ?? '' }}">
                <div class="row">
                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="category_name">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $category->name }}" class="form-control" required>
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="category_image">Category Image</label>
                                <div class="image-upload">
                                    <input type="file" name="image" id="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif">
                                    <div class="image-uploads">
                                        <img src="{{ env('ImagePath').'/admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                        <h4>Drag and drop a file to upload</h4>
                                    </div>
                                </div>
                                <span class="error_image text-danger"></span>
                                <div id="oldImageContainer" class="category-image-preview-wrapper mt-3" @if(!$category->image) style="display:none;" @endif>
                                    <img id="oldImage" src="{{ $category->image ? env('ImagePath').'/storage/' . $category->image : '' }}" alt="Category Image">
                                </div>
                                <div id="newImageContainer" class="category-image-preview-wrapper mt-3" style="display:none;">
                                    <img id="imagePreview" alt="Category Image Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit submit me-2">Submit</button>
                        <a href="{{ route('category.list') }}" class="btn btn-cancel">Cancel</a><br>
                        <span class="success_submit text-danger"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    .category-image-preview-wrapper {
        width: 180px;
        height: 180px;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .category-image-preview-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border: none;
    }
</style>
@endpush

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

        // clear error
        errorSpan.textContent = '';

        if (file) {
            // ❌ INVALID FILE
            if (!file.type.startsWith("image/")) {
                errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                return;
            }

            // ❌ SIZE VALIDATION
            if (file.size > 2 * 1024 * 1024) {
                errorSpan.textContent = "Image must be less than 2MB.";
                return;
            }

            // ✅ VALID → assign to input
            let input = document.getElementById('image');
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            // trigger preview manually
            input.dispatchEvent(new Event('change'));
        }
    });
    $(document).ready(function() {
        $('#image').on('change', function(e) {
            let file = e.target.files[0];
            let errorSpan = document.querySelector('.error_image');

            // ✅ CLEAR ERROR when user selects file
            errorSpan.textContent = '';

            if (file) {

                // ✅ VALIDATE AGAIN (important)
                if (!file.type.startsWith("image/")) {
                    errorSpan.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                    this.value = "";
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    errorSpan.textContent = "Image must be less than 2MB.";
                    this.value = "";
                    return;
                }

                let reader = new FileReader();

                reader.onload = function(event) {
                    $('#oldImageContainer').hide();
                    $('#newImageContainer').show();
                    $('#imagePreview').attr('src', event.target.result);
                };

                reader.readAsDataURL(file);

            } else {
                $('#newImageContainer').hide();
                @if($category->image)
                    $('#oldImageContainer').show();
                @endif
            }
        });
        $(document).on('click', '.submit', function(e) {
            e.preventDefault();
            var authToken = localStorage.getItem("authToken");
            let formData = new FormData($('#categoryForm')[0]);

            $('.error_name').text('');

            $.ajax({
                url: "/api/updatecategory", // Update this with your actual route
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
                            text: "Category update successfully!",
                            icon: "success",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('category.list') }}";
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