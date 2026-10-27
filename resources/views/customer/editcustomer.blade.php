@extends('layout.app')

@section('title', 'Edit Customer')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 10px !important
            }
            .image-upload .image-uploads h4 {
            font-size: 11px !important;
        }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Customer</h4>

            </div>
            <a href="{{ route('customer.list') }}" class="btn btn-added">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" id="customer_name" class="form-control">
                            <div class="text-danger error-customer_name"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control">
                            <div class="text-danger error-email"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="phone" class="form-control">
                            <div class="text-danger error-phone"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" id="country" name="country" class="form-control">
                            <div class="text-danger error-country"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="city" name="city" class="form-control">
                            <div class="text-danger error-city"></div>

                        </div>
                    </div>
                    <!-- State Code -->
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>State Code</label>
                            <input type="text" id="state_code" name="state_code" class="form-control" placeholder="e.g. 24">
                            <div class="text-danger error-state_code"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>State Name</label>
                            <input type="text" id="state_name" name="state_name" class="form-control" readonly placeholder="Auto-filled">
                            <div class="text-danger error-state_name"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="form-group">
                            <label>GST Number</label>
                            <input type="text" id="gst_num" name="gst_num" class="form-control">
                            <div class="text-danger error-gst_num"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Address</label>
                            <textarea id="address" name="address" class="form-control"></textarea>
                            <div class="text-danger error-address"></div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Photo</label>
                            <div class="image-upload">
                                <input type="file" id="avatar-input" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}"
                                        alt="Upload Icon">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <div class="text-danger error-avatar"></div>

                            <!-- Profile Image Preview -->
                            <div id="avatar-preview-container" style="display: none; margin-top: 10px;">
                                <img id="avatar-preview" src="" alt="Profile Image"
                                    style="max-width: 150px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2" id="updateCustomer">Submit</button>
                        <a class="btn btn-cancel" href="{{ route('customer.list') }}">Cancel</a>
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
            let customerId = url.split("/").pop();

            const stateNames = {
                '01': 'Jammu & Kashmir',
                '02': 'Himachal Pradesh',
                '03': 'Punjab',
                '04': 'Chandigarh',
                '05': 'Uttarakhand',
                '06': 'Haryana',
                '07': 'Delhi',
                '08': 'Rajasthan',
                '09': 'Uttar Pradesh',
                '10': 'Bihar',
                '11': 'Sikkim',
                '12': 'Arunachal Pradesh',
                '13': 'Nagaland',
                '14': 'Manipur',
                '15': 'Mizoram',
                '16': 'Tripura',
                '17': 'Meghalaya',
                '18': 'Assam',
                '19': 'West Bengal',
                '20': 'Jharkhand',
                '21': 'Odisha',
                '22': 'Chhattisgarh',
                '23': 'Madhya Pradesh',
                '24': 'Gujarat',
                '25': 'Daman and Diu',
                '26': 'Dadra and Nagar Haveli',
                '27': 'Maharashtra',
                '28': 'Andhra Pradesh',
                '29': 'Karnataka',
                '30': 'Goa',
                '31': 'Lakshadweep',
                '32': 'Kerala',
                '33': 'Tamil Nadu',
                '34': 'Pondicherry',
                '35': 'Andaman and Nicobar Islands',
                '36': 'Telangana',
                '37': 'Andhra Pradesh',
                '38': 'Ladakh'
            };

            function getStateNameFromCode(code) {
                if (!code) return '';
                code = code.toString().padStart(2, '0');
                return stateNames[code] || '';
            }

            $('#gst_num').on('input', function() {
                var gst = $(this).val().trim();
                if (gst.length !== 15) return;

                let $gstInput = $(this);
                let $errorDiv = $('.error-gst_num');

                $.ajax({
                    url: '/api/fetch-gst-details',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        gst_number: gst
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Authorization": "Bearer " + authToken,
                    },
                    beforeSend: function() {
                        $gstInput.prop('readonly', true);
                        $errorDiv.html(
                            '<span style="color: #1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching details...</span>'
                        );
                    },
                    success: function(res) {
                        $errorDiv.html('');
                        if (res.error) {
                            $errorDiv.html(
                                '<span class="text-danger">GST details not found.</span>');
                            return;
                        }
                        // Populate fields based on API response
                        const fetchedStateCode = res.state_code || res.state || '';
                        $('#customer_name').val(res.legal_name || '');
                        $('#address').val(res.primary_address || '');
                        $('#city').val(res.city || '');
                        $('#state_code').val(fetchedStateCode);
                        $('#state_name').val(getStateNameFromCode(fetchedStateCode));
                        $('#country').val(res.country || '');
                    },
                    error: function(xhr, status, error) {
                        $errorDiv.html(
                            '<span class="text-danger">Failed to fetch GST details.</span>');
                        console.error('GST fetch failed', xhr, status, error);
                    },
                    complete: function() {
                        $gstInput.prop('readonly', false);
                    }
                });
            });

            // AJAX call to get customer data
            $.ajax({
                url: `/api/getCustomer/${customerId}`,
                type: "GET",
                headers: {
                    Authorization: `Bearer ${authToken}`,
                },
                success: function(response) {
                    if (response.status) {
                        let customer = response.customer;

                        // Populate form fields
                        $("#customer_name").val(customer.name);
                        $("#email").val(customer.email || ""); // Use empty string if email is null
                        $("#phone").val(customer.phone || "");
                        $("#gst_num").val(customer.gst_num || "");
                        $("#state_code").val(customer.state_code || "");
                        $("#state_name").val(customer.state_name || getStateNameFromCode(customer.state_code || ""));
                        $("#country").val(customer.details.country ||""); // Use empty string if country is null
                        $("#city").val(customer.details.city || ""); // Use empty string if city is null
                        $("#address").val(customer.details.address ||""); // Use empty string if address is null

                        // Show profile image if exists
                        if (customer.profile_image) {
                            $("#avatar-preview").attr("src",
                                `{{ env('ImagePath') . '/storage/' }}${customer.profile_image}`);
                            $("#avatar-preview-container").show(); // Make sure preview is visible
                        }
                    } else {
                        Swal.fire("Error!", "Customer not found!", "error");
                        window.location.href = "{{ route('customer.list') }}";
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Customer not found!", "error");
                    window.location.href = "{{ route('customer.list') }}";
                },
            });

            let avatarUploadBox = document.querySelector('.image-upload');

            avatarUploadBox.addEventListener('dragover', function(e) {
                e.preventDefault();
                avatarUploadBox.classList.add('drag-over');
            });

            avatarUploadBox.addEventListener('dragleave', function() {
                avatarUploadBox.classList.remove('drag-over');
            });

            avatarUploadBox.addEventListener('drop', function(e) {
                e.preventDefault();
                avatarUploadBox.classList.remove('drag-over');

                let file = e.dataTransfer.files[0];
                let errorDiv = document.querySelector('.error-avatar');
                let previewContainer = document.getElementById('avatar-preview-container');

                errorDiv.textContent = '';
                $("#avatar-preview").attr("src", "");
                previewContainer.style.display = 'none';

                if (file) {
                    if (!file.type.startsWith("image/")) {
                        errorDiv.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        errorDiv.textContent = "Image must be less than 2MB.";
                        return;
                    }

                    let input = document.getElementById('avatar-input');
                    let dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    input.dispatchEvent(new Event('change'));
                }
            });

            $("#avatar-input").change(function(event) {
                let file = event.target.files[0];
                let errorDiv = document.querySelector('.error-avatar');
                let previewContainer = document.getElementById('avatar-preview-container');

                errorDiv.textContent = '';

                if (file) {
                    if (!file.type.startsWith("image/")) {
                        errorDiv.textContent = "Only JPG, JPEG, PNG, WEBP, GIF files are allowed.";
                        event.target.value = "";
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        errorDiv.textContent = "Image must be less than 2MB.";
                        event.target.value = "";
                        return;
                    }

                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $("#avatar-preview").attr("src", e.target.result);
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Restrict state code to digits only and auto-fill state name
            $('#state_code').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 2) {
                    this.value = this.value.slice(0, 2);
                }
                $('#state_name').val(getStateNameFromCode(this.value));
            });

            // Restrict phone field to 10 digits and numbers only
            $('#phone').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });

            $("#updateCustomer").on("click", function(e) {
                e.preventDefault();
                const $btn = $(this);
                const originalText = $btn.html();

                // Disable button and show loader
                $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                        )
                    .prop("disabled", true);
                console.log($("#country").val());

                let formData = new FormData();
                formData.append("customer_name", $("#customer_name").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("gst_num", $("#gst_num").val());
                formData.append("state_code", $("#state_code").val());
                formData.append("state_name", $("#state_name").val());
                formData.append("country", $("#country").val());
                formData.append("city", $("#city").val());
                formData.append("address", $("#address").val());

                let avatar = $("#avatar-input")[0].files[0];
                console.log(avatar);
                if (avatar) {
                    formData.append("avatar", avatar);
                }

                $.ajax({
                    url: `/api/updateCustomer/${customerId}`,
                    type: "POST", // Laravel doesn't support `PUT` directly with FormData, so use POST with `_method` as PUT
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: `Bearer ${authToken}`,
                    },
                    success: function(response) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (response.status) {
                            Swal.fire({
                                title: "Success!",
                                text: "Customer update successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                            }).then(() => {
                                window.location.href = "/customer";
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.html(originalText).prop("disabled", false); // Restore button
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(".error-" + key).html(value[
                                0]); // Show error below each field
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                });
            });


        });
    </script>
@endpush
