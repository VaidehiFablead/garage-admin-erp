@extends('layout.app')

@section('title', 'Edit Staff')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group { margin-bottom: 10px !important }
        }
        .step-tab {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: #f0f0f0;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .step-tab.active {
            background: #ff9f43;
            color: #fff;
        }
        .step-tab:first-child { border-radius: 6px 0 0 6px; }
        .step-tab:last-child  { border-radius: 0 6px 6px 0; }
        .step-section { display: none; }
        .step-section.active { display: block; }
        .image-upload-box {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 30px 15px;
            text-align: center;
            cursor: pointer;
            position: relative;
            background: #fafafa;
        }
        .image-upload-box input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .image-upload-box .upload-icon { font-size: 32px; color: #ff9f43; }
        .image-upload-box p { margin: 8px 0 0; font-size: 13px; color: #888; }
        #face-preview-container img,
        #avatar-preview { max-width: 120px; border-radius: 8px; margin-top: 8px; }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title"><h4>Edit Staff</h4></div>
            <a href="{{ route('staff.list') }}" class="btn btn-added">Back</a>
        </div>
        

        <div class="card">
            <div class="card-body">

                {{-- Step Tabs --}}
                <div class="d-flex mb-4">
                    <button type="button" class="step-tab active" id="tab-step1" onclick="goToStep(1)">
                        Step 1 - Basic Info
                    </button>
                    <button type="button" class="step-tab" id="tab-step2" onclick="goToStep(2)">
                        Step 2 - Job &amp; Documents
                    </button>
                </div>

                <form id="customerForm" enctype="multipart/form-data" onsubmit="return false;">
                    <input type="hidden" name="face_descriptor" id="face_descriptor">
                    <input type="hidden" name="captured_photo" id="captured_photo">

                    {{-- ===================== STEP 1 ===================== --}}
                    <div class="step-section active" id="step1">
                        <h5 class="fw-bold mb-3">Personal Information</h5>
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Staff Name <span class="text-danger">*</span></label>
                                    <input type="text" name="staff_name" id="customer_name" class="form-control" placeholder="Staff Name">
                                    <div class="text-danger error-staff_name"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@gmail.com" autocomplete="off">
                                    <div class="text-danger error-email"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="Leave blank to keep current">
                                        <span class="position-absolute top-50 end-0 translate-middle-y me-2" style="cursor:pointer;" onclick="togglePasswordVisibility()">
                                            <i id="togglePasswordIcon" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="text-danger error-password"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                    <div class="text-danger error-phone"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" name="country" id="country" class="form-control" placeholder="India">
                                    <div class="text-danger error-country"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" id="city" class="form-control" placeholder="Surat">
                                    <div class="text-danger error-city"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-control form-select">
                                        <option value="staff">Staff</option>
                                        <option value="hr">HR</option>
                                    </select>
                                    <div class="text-danger error-role"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>GST Number</label>
                                    <input type="text" name="gst_num" id="gst_num" class="form-control" placeholder="e.g. 22AAAAA0000A1Z5">
                                    <div class="text-danger error-gst_num"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>PAN Number</label>
                                    <input type="text" name="pan_number" id="pan_number" class="form-control" placeholder="e.g. ABCDE1234F">
                                    <div class="text-danger error-pan_number"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                    <div class="text-danger error-address"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-submit me-2" onclick="validateStep1()">Next</button>
                            <a href="{{ route('staff.list') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>

                    {{-- ===================== STEP 2 ===================== --}}
                    <div class="step-section" id="step2">
                        <h5 class="fw-bold mb-3">Job &amp; Documents</h5>
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department_id" id="department_id" class="form-control form-select">
                                        <option value="">Select Department</option>
                                    </select>
                                    <div class="text-danger error-department_id"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <select name="designation_id" id="designation_id" class="form-control form-select">
                                        <option value="">Select Designation</option>
                                    </select>
                                    <div class="text-danger error-designation_id"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Shift Time</label>
                                    <input type="text" name="shift_time" id="shift_time" class="form-control" placeholder="Select Shift Time">
                                    <div class="text-danger error-shift_time"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Joining Date</label>
                                    <input type="date" name="joining_date" id="joining_date" class="form-control">
                                    <div class="text-danger error-joining_date"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Working Location</label>
                                    <select name="working_location" id="working_location" class="form-control form-select">
                                        <option value="">Select Working Location</option>
                                        <option value="office">Office</option>
                                        <option value="remote">Remote</option>
                                        <option value="hybrid">Hybrid</option>
                                    </select>
                                    <div class="text-danger error-working_location"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Salary</label>
                                    <input type="number" name="salary" id="salary" class="form-control" min="0" step="0.01">
                                    <div class="text-danger error-salary"></div>
                                </div>
                            </div>
                            

                            {{-- Photo --}}
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Photo</label>
                                    <div class="image-upload-box" id="avatar-upload-box">
                                        <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.webp,.gif">
                                        <div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                                        <p>Drag and drop a file to upload</p>
                                    </div>
                                    <div class="text-danger error-avatar"></div>
                                    <div id="avatar-preview-container" style="display:none;">
                                        <img id="avatar-preview" src="" alt="Preview">
                                    </div>
                                </div>
                            </div>

                            {{-- Face Photo --}}
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>Face Photo <small class="text-muted">(upload or use camera)</small></label>
                                    <div class="image-upload" id="face-photo-upload-box" style="margin: 0; position: relative; overflow: hidden; display: flex; justify-content: center; align-items: center; min-height: 120px;">
                                        <input type="file" name="face_photo" id="face_photo_input" class="form-control" accept="image/*">
                                        <div class="image-uploads" id="face-upload-content">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}" alt="Upload Icon">
                                            <h4 id="face-photo-text">Drag and drop a file to upload</h4>
                                        </div>
                                        <img id="face-preview" src="" alt="Face Preview" style="display: none; max-width: 100%; max-height: 120px; border-radius: 8px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                                    </div>
                                    <div class="text-danger error-face_photo"></div>
                                    <button type="button" class="btn btn-capture-face mt-2" id="captureFaceBtn" style="background:#1b2850;color:#fff;width:100%;font-weight:600;padding:8px;border-radius:5px;">
                                        <i class="fa-solid fa-camera me-1"></i>Capture Face via Camera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-danger error-permissions mt-2"></div>

                        <div class="d-flex align-items-center mt-4">
                            <h5 class="fw-bold mb-0 me-3">PERMISSION :</h5>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="permission_type" id="withPermission" value="1">
                                <label class="form-check-label" for="withPermission">With Permission</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="permission_type" id="withoutPermission" value="0" checked>
                                <label class="form-check-label" for="withoutPermission">Without Permission</label>
                            </div>
                        </div>

                        <div id="permissionsSection" style="display:none; margin-top:15px;">
                            <div class="form-group mb-3">
                                <label class="fw-normal">
                                    <input type="checkbox" id="select_all_module"> Select All Module
                                </label>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th class="text-start">Module Name</th>
                                            <th>View</th>
                                            <th>Insert</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>All</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($modules as $module)
                                            <tr>
                                                <td class="align-middle">
                                                    <i class="fa fa-folder-open text-primary me-2"></i> {{ $module->module }}
                                                    <input type="hidden" name="modules[{{ $module->id }}][module_id]" value="{{ $module->id }}">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="modules[{{ $module->id }}][view]" class="permission-checkbox form-check-input">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="modules[{{ $module->id }}][add]" class="permission-checkbox form-check-input">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="modules[{{ $module->id }}][edit]" class="permission-checkbox form-check-input">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="modules[{{ $module->id }}][delete]" class="permission-checkbox form-check-input">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" class="check-all-row form-check-input">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary me-2" onclick="goToStep(1)">Previous</button>
                            <button type="submit" class="btn btn-submit me-2" id="submitCustomerBtn">Submit</button>
                            <a href="{{ route('staff.list') }}" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @include('partials.face-recognition-modal')
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
@include('partials.face-recognition-script')
<script>
    // â”€â”€ Step navigation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function goToStep(step) {
        document.querySelectorAll('.step-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.step-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('step' + step).classList.add('active');
        document.getElementById('tab-step' + step).classList.add('active');
    }

    function validateStep1() {
        let valid = true;
        const name  = $('#customer_name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();

        $('.error-staff_name, .error-email, .error-phone').html('');

        if (!name)  { $('.error-staff_name').html('Staff name is required.'); valid = false; }
        if (!email) { $('.error-email').html('Email is required.'); valid = false; }
        if (!phone) { $('.error-phone').html('Phone is required.'); valid = false; }

        if (valid) goToStep(2);
    }

    // â”€â”€ Password toggle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // â”€â”€ Image preview helper â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function bindImagePreview(inputId, previewId, containerId, errorClass) {
        $('#' + inputId).on('change', function (e) {
            const file = e.target.files[0];
            const err  = $('.' + errorClass);
            err.html('');
            $('#' + previewId).attr('src', '');
            $('#' + containerId).hide();
            if (!file) return;
            if (!file.type.startsWith('image/')) { err.html('Only image files are allowed.'); this.value = ''; return; }
            if (file.size > 2 * 1024 * 1024)    { err.html('Image must be less than 2MB.');  this.value = ''; return; }
            const reader = new FileReader();
            reader.onload = ev => { $('#' + previewId).attr('src', ev.target.result); $('#' + containerId).show(); };
            reader.readAsDataURL(file);
        });
    }

    $(document).ready(function () {

        let url = window.location.pathname;
        let customerId = url.split("/").pop();
        const authToken = localStorage.getItem('authToken');

        // ── Load departments ─────────────────────────────────────────
        $.ajax({
            url: '/api/departments',
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function (res) {
                const list = res.data || res;
                $.each(list, function (i, dept) {
                    $('#department_id').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                });
            }
        });

        // ── Load designations ────────────────────────────────────────
        $.ajax({
            url: '/api/designations',
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function (res) {
                const list = res.data || res;
                $.each(list, function (i, d) {
                    $('#designation_id').append('<option value="' + d.id + '">' + d.designation_name + '</option>');
                });
            }
        });

        // Fetch staff data to pre-fill
        $.ajax({
            url: `/api/getStaff/${customerId}`,
            type: "GET",
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function(response) {
                if (response.status) {
                    let customer = response.customer;
                    $('#customer_name').val(customer.name);
                    $('#email').val(customer.email || '');
                    $('#phone').val(customer.phone);
                    if (customer.details) {
                        $('#country').val(customer.details.country || '');
                        $('#city').val(customer.details.city || '');
                        $('#address').val(customer.details.address || '');
                        $('#department_id').val(customer.details.department_id || '');
                        $('#designation_id').val(customer.details.designation_id || '');
                        $('#shift_time').val(customer.details.shift_time || '');
                        $('#joining_date').val(customer.details.joining_date || '');
                        $('#working_location').val(customer.details.working_location || '');
                        $('#salary').val(customer.details.salary || '');
                    }
                    var roleValue = (customer.role || 'staff').toString().trim().toLowerCase();
                    $('#role').val(roleValue);

                    // Populate GST and PAN
                    $('#gst_num').val(customer.gst_num || '');
                    if (customer.details) {
                        $('#pan_number').val(customer.details.pan_number || '');
                    }

                    // Show profile image
                    if (customer.profile_image) {
                        $("#avatar-preview").attr("src", '{{ env('ImagePath') . '/storage/' }}' + customer.profile_image);
                        $("#avatar-preview-container").show();
                    }

                    // Show face photo
                    if (customer.details && customer.details.face_photo) {
                        $('#face-preview').attr('src', '{{ env('ImagePath') . '/storage/' }}' + customer.details.face_photo).show();
                        $('#face-upload-content').hide();
                    }
                    if (customer.face_descriptor) {
                        $('#face_descriptor').val(JSON.stringify(customer.face_descriptor));
                    }

                    // Permissions
                    if (customer.haspermission == 1) {
                        $('#withPermission').prop('checked', true).trigger('change');
                        let modules = response.modules;
                        let permissions = response.permissions;

                        // Wait a short moment to ensure checkboxes exist, then check them
                        setTimeout(() => {
                            modules.forEach(module => {
                                let perm = permissions.find(p => p.module_id === module.id) || {};
                                if (perm.view) $(`input[name="modules[${module.id}][view]"]`).prop('checked', true);
                                if (perm.insert || perm.add) $(`input[name="modules[${module.id}][add]"]`).prop('checked', true);
                                if (perm.edit) $(`input[name="modules[${module.id}][edit]"]`).prop('checked', true);
                                if (perm.delete) $(`input[name="modules[${module.id}][delete]"]`).prop('checked', true);
                                
                                // trigger change to update select-all
                                $(`input[name="modules[${module.id}][view]"]`).trigger('change');
                            });
                        }, 500);
                    } else {
                        $('#withoutPermission').prop('checked', true).trigger('change');
                    }
                }
            }
        });

        bindImagePreview('avatar', 'avatar-preview', 'avatar-preview-container', 'error-avatar');

        // Face photo file input change (use face-api modal system for preview)
        $('#face_photo_input').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#face-preview').attr('src', event.target.result).show();
                    $('#face-upload-content').hide();
                    $('#captured_photo').val('');
                };
                reader.readAsDataURL(file);
            }
        });

        // Face capture via face-api.js modal
        if (typeof window.OmsaiFaceRecognition !== 'undefined') {
            const faceRecognition = window.OmsaiFaceRecognition.init();
            $('#captureFaceBtn').on('click', function() {
                const staffName = $('#customer_name').val().trim() || 'New Staff';
                faceRecognition.open({
                    title: `Register Face for ${staffName}`,
                    subtitle: 'Ask the staff member to face the camera and capture one clear frame.',
                    captureLabel: 'Capture & Set',
                    onCapture: async function(descriptor, modal, imageDataUrl) {
                        $('#face_descriptor').val(JSON.stringify(descriptor));
                        if (imageDataUrl) {
                            $('#face-preview').attr('src', imageDataUrl).show();
                            $('#face-upload-content').hide();
                            $('#captured_photo').val(imageDataUrl);
                        }
                        modal.showMatchInfo('Face captured successfully.');
                        setTimeout(function() { modal.close(); }, 600);
                    }
                });
            });
        }



        // â”€â”€ Permission toggle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $('input[name="permission_type"]').on('change', function () {
            if ($('#withPermission').is(':checked')) {
                $('#permissionsSection').slideDown();
            } else {
                $('#permissionsSection').slideUp();
                $('#permissionsSection input[type="checkbox"]').prop('checked', false);
            }
        });

        // â”€â”€ Permission checkboxes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $('#select_all_module').on('change', function () {
            $('.permission-checkbox, .check-all-row').prop('checked', $(this).is(':checked'));
        });
        $(document).on('change', '.check-all-row', function () {
            $(this).closest('tr').find('.permission-checkbox').prop('checked', $(this).is(':checked'));
        });
        $(document).on('change', '.permission-checkbox', function () {
            const row = $(this).closest('tr');
            row.find('.check-all-row').prop('checked',
                row.find('.permission-checkbox').length === row.find('.permission-checkbox:checked').length
            );
            $('#select_all_module').prop('checked',
                $('.permission-checkbox').length === $('.permission-checkbox:checked').length
            );
        });

        // â”€â”€ Form submit â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // Triggered by the Submit button click (not native form submit)
        $('#submitCustomerBtn').on('click', function (e) {
            e.preventDefault();
            $('.text-danger').html('');

            // Default permission_type to 0 (without) if nothing selected
            if (!$('input[name="permission_type"]:checked').length) {
                $('#withoutPermission').prop('checked', true);
            }

            if ($('#withPermission').is(':checked') && !$('.permission-checkbox:checked').length) {
                goToStep(2); // make sure step 2 is visible
                $('.error-permissions').html('** Please select at least one permission. **');
                return;
            }

            const $btn = $('#submitCustomerBtn');
            const orig = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...').prop('disabled', true);

            const formData = new FormData(document.getElementById('customerForm'));
            const subAdminId = localStorage.getItem('selectedSubAdminId');
            if (subAdminId) formData.append('sub_admin_id', subAdminId);

            $.ajax({
                url: `/api/updateStaff/${customerId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization': 'Bearer ' + authToken,
                },
                success: function (res) {
                    $btn.html(orig).prop('disabled', false);
                    if (res.status) {
                        Swal.fire({ title: 'Success!', text: 'Staff updated successfully!', icon: 'success',
                            confirmButtonText: 'OK', confirmButtonColor: '#ff9f43' })
                            .then(() => window.location.href = '/staff');
                    }
                },
                error: function (xhr) {
                    $btn.html(orig).prop('disabled', false);
                    if (xhr.status === 401) {
                        Swal.fire({ title: 'Session Expired', text: 'Your API token is invalid or expired. Please log out and log back in.', icon: 'error' });
                    } else if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function (key, val) {
                            $('.error-' + key).html(val[0]);
                        });
                        // If error is on step-1 field, go back
                        const step1Fields = ['staff_name','email','password','phone','country','city','address','role'];
                        const errKeys = Object.keys(xhr.responseJSON.errors);
                        if (errKeys.some(k => step1Fields.includes(k))) goToStep(1);
                    } else {
                        Swal.fire({ title: 'Error!', text: 'Something went wrong.', icon: 'error', confirmButtonText: 'OK' });
                    }
                }
            });
        });
    });
</script>

@endpush
