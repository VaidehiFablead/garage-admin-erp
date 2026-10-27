@extends('layout.app')

@section('title', 'Item Edit')

@section('content')
<style>
    @media screen and (max-width: 768px) {
        .select2-container {
            width: 0 !important;
        }
        .form-group {
            margin-bottom: 10px !important
        }
        .image-upload .image-uploads h4 {
            font-size: 11px;
            font-weight: 500;
            color: #212b36;
        }
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Items</h4>
        </div>

                        <div class="page-btn">
                <a href="javascript:history.back()" class="btn btn-added">Back</a>
            </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="productForm">
                <input type="hidden" name="product_id" id="product_id" value="{{ $product->id ?? '' }}">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" name="name" id="name" value="{{ $product->name ?? '' }}">
                            <span class="error_name text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="select" name="category_id" id="category_id">
                                <option value="">Choose Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            <span class="error_category_id text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Brand</label>
                            <select class="select" name="brand_id" id="brand_id">
                                <option value="">Choose Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                            <span class="error_brand_id text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="number" name="SKU" id="SKU" class="form-control" value="{{ $product->SKU ?? '' }}" step="1" min="0">
                            <span class="error_SKU text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $product->quantity ?? '' }}" step="1" min="0">
                            <span class="error_quantity text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" id="price" class="form-control" value="{{ $product->price ?? '' }}" step="0.01" min="0">
                            <span class="error_price text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="select" name="status" id="status">
                                <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>DeActive</option>
                            </select>
                            <span class="error_status text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Stock</label>
                            <select class="select" name="availablility" id="availablility" disabled>
                                <option value="in_stock" {{ $product->availablility == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="out_stock" {{ $product->availablility == 'out_stock' ? 'selected' : '' }}>Out Of Stock</option>
                            </select>
                            <span class="error_availablility text-danger"></span>
                        </div>
                    </div>
                     <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const qtyInput = document.getElementById('quantity');

                            if (qtyInput) {
                                qtyInput.addEventListener('input', function() {
                                    const qty = parseInt(this.value);

                                    // Update using jQuery + Select2
                                    if (!isNaN(qty) && qty === 0) {
                                        $('#availablility').val('out_stock').trigger('change');
                                    } else {
                                        $('#availablility').val('in_stock').trigger('change');
                                    }

                                    console.log("Quantity changed to", qty);
                                });
                            }
                        });
                    </script>

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Barcode</label>
                            <input type="text" name="barcode" id="barcode" class="form-control" value="{{ $product->barcode ?? '' }}">
                            <span class="error_barcode text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description">{{ $product->description ?? '' }}</textarea>
                            <span class="error_description text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Item Image</label>
                            <div class="image-upload">
                                <input type="file" name="images[]" id="images" multiple>
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath').'admin/assets/img/icons/upload.svg' }}" alt="img">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            @if(!empty($product->images))
                            <div class="existing-images" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @foreach(json_decode($product->images, true) as $image)
                                <div class="image-container" data-image="{{ $image }}" style="position: relative;">
                                    <img src="{{ env('ImagePath').'storage/' . $image }}" width="100" height="100" style="border-radius: 5px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1);">
                                    <button class="remove-image" data-id="{{ $product->id }}" data-image="{{ $image }}" style="
                                        position: absolute;
                                        top: -5px;
                                        right: -5px;
                                        background: red;
                                        color: white;
                                        border: none;
                                        border-radius: 50%;
                                        width: 20px;
                                        height: 20px;
                                        cursor: pointer;
                                        font-size: 14px;
                                    ">&times;</button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="image-preview" style="margin-top: 10px;">

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <a href="javascript:void(0);" class="btn btn-submit me-2 submit">Submit</a>
                        <a href="{{ route('product.list') }}" class="btn btn-cancel">Cancel</a><br>
                        <span class="success_submit text-danger"></span>
                    </div>
                </div>
            </form>

        </div>

    </div>
    @endsection
    @push('js')
    <script>
        document.getElementById("images").addEventListener("change", function(event) {
            var files = event.target.files; // Get selected files
            var previewDiv = document.querySelector(".image-preview"); // Select preview div
            previewDiv.innerHTML = ""; // Clear existing previews

            Array.from(files).forEach((file, index) => {
                var reader = new FileReader(); // Create FileReader instance

                reader.onload = function(e) {
                    // Create image container
                    var imageContainer = document.createElement("div");
                    imageContainer.style.position = "relative";
                    imageContainer.style.display = "inline-block";

                    // Create image element
                    var img = document.createElement("img");
                    img.src = e.target.result;
                    img.alt = "Preview";
                    img.style.maxWidth = "100px";
                    img.style.maxHeight = "100px";
                    img.style.borderRadius = "5px";
                    img.style.boxShadow = "2px 2px 10px rgba(0,0,0,0.1)";
                    img.style.marginRight = "5px";

                    // Create remove button
                    var removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.style.position = "absolute";
                    removeBtn.style.top = "0";
                    removeBtn.style.right = "0";
                    removeBtn.style.background = "red";
                    removeBtn.style.color = "white";
                    removeBtn.style.border = "none";
                    removeBtn.style.borderRadius = "50%";
                    removeBtn.style.width = "20px";
                    removeBtn.style.height = "20px";
                    removeBtn.style.cursor = "pointer";
                    removeBtn.style.display = "flex";
                    removeBtn.style.alignItems = "center";
                    removeBtn.style.justifyContent = "center";
                    removeBtn.style.fontSize = "14px";

                    // Remove image on click
                    removeBtn.addEventListener("click", function() {
                        imageContainer.remove();
                    });

                    // Append elements
                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeBtn);
                    previewDiv.appendChild(imageContainer);
                };

                reader.readAsDataURL(file); // Read file as Data URL
            });
        });
        $(document).on("click", ".remove-image", function(e) {
            e.preventDefault(); // ❗ prevent page reload and form submission

            var authToken = localStorage.getItem("authToken");
            var productId = $(this).data("id");
            var imageName = $(this).data("image");
            var imageContainer = $(this).parent();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to delete this image?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#ff9f43",
                cancelButtonColor: "#6c757d"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/api/remove-product-image",
                        type: "POST",
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        data: {
                            product_id: productId,
                            image: imageName,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response) {
                            if (response.success) {
                                imageContainer.remove();
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Item image removed successfully!",
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43"
                                }).then(() => {

                                });
                            } else {
                                Swal.fire("Error", "Error removing image.", "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });


        $(document).ready(function() {
            $(document).on('click', '.submit', function(e) {
                e.preventDefault();

                let qty = parseInt($("#quantity").val(), 10);

                if (isNaN(qty) || qty < 0) {
                    $(".error_quantity").text("Quantity must be 0 or greater.");
                    return; // stop submit
                } else {
                    $(".error_quantity").text(""); // clear old error
                }

                 var $btn = $(this); // cache the button
                var originalText = $btn.html();

            // Show loading text and disable the button
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);


                var authToken = localStorage.getItem("authToken");
                let formData = new FormData($('#productForm')[0]);

                $('.error_name, .error_category_id, .error_brand_id, .error_SKU, .error_quantity, .error_price, .error_status, .error_barcode, .error_description, .error_availablility').text('');

                $.ajax({
                    url: "/api/updateProduct",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop('disabled', false);

                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Item updated successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // your custom color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('product.list') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.error_' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire("Error", "Something went wrong.", "error");
                        }
                    }
                });
            });
        });
    </script>
    @endpush
