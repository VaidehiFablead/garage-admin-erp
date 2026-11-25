@extends('layout.app')

@section('title', 'Shop Settings')

@section('content')
@php
    $currentSubAdminId = session('selectedSubAdminId');
    $currentUser = Auth::user();
    $currentRole = $currentUser ? strtolower($currentUser->role) : '';
    $isSubAdminBranch = (!empty($currentSubAdminId) && $currentSubAdminId != 1) || ($currentRole === 'sub-admin');
@endphp
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 15px !important;
            }

            .image-upload input[type=file] {
                height: 115px !important;
            }
        }

        .settings-tabs {
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e6eaf0 !important;
            border-radius: 8px;
            display: inline-flex;
            gap: 4px;
            margin-bottom: 18px;
            padding: 4px;
        }

        .settings-tabs .nav-item {
            margin: 0;
        }

        .settings-tabs .nav-link {
            align-items: center;
            background: transparent;
            border: 0 !important;
            border-radius: 6px;
            color: #667085;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 7px;
            line-height: 1.2;
            min-height: 36px;
            padding: 9px 14px;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .settings-tabs .nav-link {
            font-size: 0;
        }

        .settings-tabs .nav-link::before,
        .settings-tabs .nav-link::after {
            font-size: 13px;
        }

        .settings-tabs .nav-link::before {
            color: #98a2b3;
            font-family: "Font Awesome 6 Free", "Font Awesome 5 Free";
            font-weight: 900;
            transition: color 0.2s ease;
        }

        #shop-tab::before {
            content: "\f54e";
        }

        #shop-tab::after {
            content: "Shop Settings";
        }

        #rules-tab::before {
            content: "\f1ad";
        }

        #rules-tab::after {
            content: "Company Rules";
        }

        #dashboard-tab::before {
            content: "\f201";
        }

        #dashboard-tab::after {
            content: "Dashboard";
        }

        #barcode-tab::before {
            content: "\f02a";
        }

        #barcode-tab::after {
            content: "Barcode Setting";
        }

        #order-format-tab::before {
            content: "\f0cb";
        }

        #order-format-tab::after {
            content: "Order No Format";
        }

        .settings-tabs .nav-link i {
            color: #98a2b3;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .settings-tabs .nav-link:hover {
            background: #fff;
            color: #1f2937;
        }

        .settings-tabs .nav-link.active {
            background: #fff;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1);
            color: #ff9f43;
        }

        .settings-tabs .nav-link.active i {
            color: #ff9f43;
        }

        .settings-tabs .nav-link.active::before {
            color: #ff9f43;
        }

        #settingsTabsContent {
            margin-top: 0 !important;
        }

        @media screen and (max-width: 575px) {
            .settings-tabs {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .settings-tabs .nav-link {
                justify-content: flex-start;
                width: 100%;
            }
        }

        .dashboard-mobile-subsection-tabs {
            display: none;
        }

        @media screen and (max-width: 767px) {
            .dashboard-mobile-subsection-tabs {
                background: #f8fafc;
                border: 1px solid #e6eaf0;
                border-radius: 8px;
                display: grid;
                gap: 6px;
                grid-template-columns: repeat(3, 1fr);
                padding: 4px;
            }

            .dashboard-mobile-subsection-tab {
                background: transparent;
                border: 0;
                border-radius: 6px;
                color: #667085;
                font-size: 12px;
                font-weight: 600;
                min-height: 34px;
                padding: 7px 6px;
            }

            .dashboard-mobile-subsection-tab.active {
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1);
                color: #ff9f43;
            }

            #dashboard-settings .dashboard-mobile-panel:not(.active) {
                display: none !important;
            }
        }

        /* Modern Multi-step Form Styles */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: flex;
            /* Ensure it's flex as it's a row */
        }

        .mobile-steps {
            margin-bottom: 25px;
        }

        .step-btns-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            background: #f8f9fa;
            padding: 5px;
            border-radius: 50px;
            border: 1px solid #e8ebed;
            overflow-x: auto;
            white-space: nowrap;
        }

        .step-btns-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .step-btns-wrapper::-webkit-scrollbar-thumb {
            background-color: #ddd;
            border-radius: 4px;
        }

        .step-btn {
            background: transparent;
            color: #637381;
            border-radius: 50px;
            flex: 1;
            font-size: 13px;
            padding: 10px 15px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
            text-align: center;
        }

        .step-btn.active {
            background: #ff9f43;
            color: #fff;
            box-shadow: 0 4px 10px rgba(255, 159, 67, 0.2);
        }

        #shop-settings:not(.active) {
            display: none !important;
        }

        #shop-settings.active {
            display: block;
        }

        /* Dark Mode Overrides for Tabs and Steps */
        :root[data-theme="dark"] .step-btns-wrapper {
            background: #0f172a;
            border-color: #334155;
        }

        :root[data-theme="dark"] .step-btn {
            color: #d1d5db;
        }

        :root[data-theme="dark"] .step-btn.active {
            background: #ff9f43;
            color: #fff;
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Shop Setting</h4>

            </div>
            <div class="page-btn">
                <a href="{{ route('auth.profile') }}" class="btn btn-added">View Profile</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
<ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="shop-tab" data-toggle="tab" href="#shop-settings"
                            role="tab" aria-controls="shop-settings" aria-selected="true">
                            🏪 Shop Settings
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="rules-tab" data-toggle="tab" href="#company-rules"
                            role="tab" aria-controls="company-rules" aria-selected="false">
                            🏢 Company Rules
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="dashboard-tab" data-toggle="tab" href="#dashboard-settings"
                            role="tab" aria-controls="dashboard-settings" aria-selected="false">
                            📊 Dashboard
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="barcode-tab" data-toggle="tab" href="#barcode-settings"
                            role="tab" aria-controls="barcode-settings" aria-selected="false">
                            Barcode Setting
                        </a>
                    </li>
                    <li class="nav-item sub-admin-only-tab" role="presentation" style="{{ $isSubAdminBranch ? '' : 'display: none !important;' }}">
                        <a class="nav-link" id="order-format-tab" data-toggle="tab" data-bs-toggle="tab" href="#order-format-settings"
                            role="tab" aria-controls="order-format-settings" aria-selected="false">
                            Order No Format
                        </a>
                    </li>
                    <!-- <li class="nav-item" role="presentation">
                        <a class="nav-link" id="dashboard-tab" data-toggle="tab" href="#dashboard-settings"
                            role="tab" aria-controls="dashboard-settings" aria-selected="false">
                            Dashboard
                        </a>
                    </li> -->
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-4" id="settingsTabsContent">
                    <!-- ================= SHOP SETTINGS TAB ================= -->
                    <div class="tab-pane fade show active" id="shop-settings" role="tabpanel" aria-labelledby="shop-tab">
                        <div class="mobile-steps">
                            <div class="step-btns-wrapper">
                                <button type="button" class="step-btn active" data-step="1">Basic Information</button>
                                <button type="button" class="step-btn" data-step="2">System Preferences</button>
                                <button type="button" class="step-btn" data-step="3">Media & Branding</button>
                            </div>
                        </div>

                        <!-- Step 1: Basic Information -->
                        <div class="step-content step-1 active row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Name<span class="manitory">*</span></label>
                                    <input type="text" id="shop_name" placeholder="Enter Title">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Email<span class="manitory">*</span></label>
                                    <input type="text" id="email" placeholder="Enter email">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Phone<span class="manitory">*</span></label>
                                    <input type="text" id="phone" placeholder="Enter Phone">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Alternate Phone</label>
                                    <input type="text" id="alt_phone" name="alt_phone" placeholder="Enter Phone">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>GST Number</label>
                                    <input type="text" id="gst_num" placeholder="Enter GST Number" maxlength="15">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>State Code</label>
                                    <input type="text" id="state_code" placeholder="Enter State Code">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Bank Name<span class="manitory">*</span></label>
                                    <input type="text" id="bank_name" placeholder="Enter Bank Name">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Branch<span class="manitory">*</span></label>
                                    <input type="text" id="branch" placeholder="Enter Branch">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>A/C No.<span class="manitory">*</span></label>
                                    <input type="number" id="ac_no" class="form-control" placeholder="Enter A/C No.">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>IFSC Code<span class="manitory">*</span></label>
                                    <input type="text" id="ifsc_code" placeholder="Enter IFSC Code">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Shop Address<span class="manitory">*</span></label>
                                    <textarea id="address" placeholder="Enter Address" rows="3" class="form-control"></textarea>
                                    <div class="d-flex justify-content-between align-items-start mt-1">
                                        <small id="coordinateLookupStatus" class="text-muted" style="line-height:1.2;"></small>
                                        <button type="button" class="btn btn-sm btn-primary ms-2 flex-shrink-0" id="btnFetchCoordinates" style="padding: 2px 8px; font-size: 11px;">Get GPS from Address</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: System Preferences -->
                        <div class="step-content step-2 row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Currency Symbol <span class="manitory">*</span></label>
                                    <select id="currency_symbol" class="form-select">
                                        <option value="₹" selected>₹ (Indian Rupee)</option>
                                        <option value="$">$ (US Dollar)</option>
                                        <option value="€">€ (Euro)</option>
                                        <option value="£">£ (British Pound)</option>
                                        <option value="¥">¥ (Japanese Yen)</option>
                                        <option value="₩">₩ (South Korean Won)</option>
                                        <option value="₽">₽ (Russian Ruble)</option>
                                        <option value="₺">₺ (Turkish Lira)</option>
                                        <option value="₫">₫ (Vietnamese Dong)</option>
                                        <option value="฿">฿ (Thai Baht)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label for="currency_position" class="form-label">
                                        Currency Symbol Position
                                    </label>
                                    <select class="form-select" id="currency_position" name="currency_position" required>
                                        <option value="left">Left (e.g. ₹100 or $100)</option>
                                        <option value="right">Right (e.g. 100₹ or 100$)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Low Stock Warning Quantity</label>
                                    <input type="number" id="low_stock" class="form-control"
                                        placeholder="Enter Low Stock Warning Quantity">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Latitude</label>
                                    <input type="text" id="office_latitude" class="form-control" placeholder="e.g. 21.1268432">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Longitude</label>
                                    <input type="text" id="office_longitude" class="form-control" placeholder="e.g. 73.1051204">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Radius (m)</label>
                                    <input type="number" id="office_radius" class="form-control" placeholder="e.g. 200" min="0" value="200">
                                    <small class="text-muted">Staff must be within this distance from office GPS to clock in.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Media & Branding -->
                        <div class="step-content step-3 row">
                            <div class="col-lg-3 col-6">
                                <div class="form-group">
                                    <label>Shop Logo</label>
                                    <div class="image-upload">
                                        <input type="file" id="logo" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ image_path('admin/assets/img/icons/upload.svg') }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="logo_preview" src="" alt="Logo Preview"
                                        style="display:none; max-width: 100px; margin-top: 10px;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-6">
                                <div class="form-group">
                                    <label>Shop Favicon</label>
                                    <div class="image-upload">
                                        <input type="file" id="favicon" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ image_path('admin/assets/img/icons/upload.svg') }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="favicon_preview" src="" alt="Favicon Preview"
                                        style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-6">
                                <div class="form-group">
                                    <label>Shop QR Code Image</label>
                                    <div class="image-upload">
                                        <input type="file" id="qr_code" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ image_path('admin/assets/img/icons/upload.svg') }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="qr_preview" src="" alt="QR Preview"
                                        style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 align-items-center">
                            <div class="col-6">
                                <a href="javascript:void(0);" class="btn btn-submit btn-prev d-none"><i
                                        class="fas fa-arrow-left me-1"></i> Previous</a>
                            </div>
                            <div class="col-6 d-flex justify-content-end gap-2">
                                <a href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
                                <a href="javascript:void(0);" class="btn btn-submit btn-next">Next <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                                <a href="javascript:void(0);" class="btn btn-submit btn-submit-mobile d-none">Submit</a>
                                <button type="button" id="btn-setting-submit" class="d-none">Submit</button>
                            </div>
                        </div>
                    </div>
                                                            <!-- ================= COMPANY RULES TAB ================= -->
                    <div class="tab-pane fade" id="company-rules" role="tabpanel" aria-labelledby="rules-tab">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Working Hours (per day)<span class="manitory">*</span></label>
                                    <input type="number" step="0.5" min="0" max="24" id="working_hours" class="form-control" placeholder="e.g. 8">
                                    <small class="text-muted">Enter hours (e.g. 8 or 8.5)</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Sunday Off?</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sunday_off"
                                                id="sunday_yes" value="yes">
                                            <label class="form-check-label" for="sunday_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sunday_off"
                                                id="sunday_no" value="no" checked>
                                            <label class="form-check-label" for="sunday_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Saturday Off?</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="saturday_off"
                                                id="saturday_yes" value="yes">
                                            <label class="form-check-label" for="saturday_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="saturday_off"
                                                id="saturday_no" value="no" checked>
                                            <label class="form-check-label" for="saturday_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Grace Period(minutes)<span class="manitory">*</span></label>
                                    <input type="number" min="0" max="120" id="grace_period" class="form-control" placeholder="e.g. 15">
                                    <small class="text-muted">Enter minutes (e.g. 15)</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Lunch Break</label>
                                    <input type="number" id="lunch_break" class="form-control" placeholder="Enter minutes">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Company Open Time<span class="manitory">*</span></label>
                                    <input type="time" id="open_time" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Company Close Time<span class="manitory">*</span></label>
                                    <input type="time" id="close_time" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Overtime After (hours)</label>
                                    <input type="number" step="0.5" id="overtime_after_hours" class="form-control" placeholder="e.g. 9">
                                    <small class="text-muted">Hours after which overtime starts (e.g. 9 for 8h shift + 1h grace)</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <h5>Security Settings</h5>
                                <hr>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Location Check on Login</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="location_check_enabled"
                                                id="location_check_on" value="1">
                                            <label class="form-check-label" for="location_check_on">ON (Check GPS location)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="location_check_enabled"
                                                id="location_check_off" value="0" checked>
                                            <label class="form-check-label" for="location_check_off">OFF (Allow anywhere)</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">If ON, staff must be within the radius of their assigned office location to log in and clock in.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <h5>Additional Company Policies</h5>
                                <hr>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Late Mark After (Minutes)</label>
                                    <input type="number" id="late_mark_after_minutes" class="form-control" placeholder="e.g. 15">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Half Day After (Hours)</label>
                                    <input type="number" step="0.5" id="half_day_after_hours" class="form-control" placeholder="e.g. 4">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Auto Absent After (Hours)</label>
                                    <input type="number" step="0.5" id="auto_absent_after_hours" class="form-control" placeholder="e.g. 2">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Allow Early Checkout</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="allow_early_checkout"
                                                id="early_yes" value="yes">
                                            <label class="form-check-label" for="early_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="allow_early_checkout"
                                                id="early_no" value="no" checked>
                                            <label class="form-check-label" for="early_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Require Attendance Photo</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="require_attendance_photo"
                                                id="photo_yes" value="yes">
                                            <label class="form-check-label" for="photo_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="require_attendance_photo"
                                                id="photo_no" value="no" checked>
                                            <label class="form-check-label" for="photo_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Require GPS for Attendance</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="require_gps_location_for_attendance"
                                                id="gps_yes" value="yes">
                                            <label class="form-check-label" for="gps_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="require_gps_location_for_attendance"
                                                id="gps_no" value="no" checked>
                                            <label class="form-check-label" for="gps_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Allow Overtime Approval</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="allow_overtime_approval"
                                                id="ot_yes" value="yes">
                                            <label class="form-check-label" for="ot_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="allow_overtime_approval"
                                                id="ot_no" value="no" checked>
                                            <label class="form-check-label" for="ot_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Max Overtime (Per Day)</label>
                                    <input type="number" step="0.5" id="max_overtime_hours_per_day" class="form-control" placeholder="e.g. 4">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Weekly Off Day</label>
                                    <select id="weekly_off_day" class="form-select">
                                        <option value="">None</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Monthly Leave Limit</label>
                                    <input type="number" id="monthly_leave_limit" class="form-control" placeholder="e.g. 2">
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-1">Tax Deduction Settings</h5>
                                <hr>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Tax Deduction Amount</label>
                                    <input type="number" step="0.01" min="0" id="tax_deduction_amount" class="form-control" placeholder="e.g. 200" value="200">
                                    <small class="text-muted">Tax deduction will be applied only when the salary amount exceeds this value.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Salary Amount Exceeds</label>
                                    <input type="number" step="0.01" min="0" id="salary_exceeds" class="form-control" placeholder="e.g. 14000">
                                    <small class="text-muted">Tax deduction will be applied only when the salary amount exceeds this value.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveCompanyRules">Save Rules</a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= DASHBOARD SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="dashboard-settings" role="tabpanel" aria-labelledby="dashboard-tab">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <h5 class="mb-1">ERP Module Section On Dashboard</h5>
                                <p class="text-muted small mb-0">Control whether ERP sections are visible on the dashboard.</p>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>ERP Module Section On Dashboard</label>
                                    <select id="show_erp_dashboard" name="show_erp_dashboard" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                    <small class="text-muted">When disabled, the entire ERP section is hidden from the dashboard.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-1">ERP Dashboard Subsections</h5>
                                <p class="text-muted small mb-0">Control visibility of individual ERP dashboard components:</p>
                            </div>

                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Total Collections Box</label>
                                    <select id="show_erp_total_collections" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Total Expense Box</label>
                                    <select id="show_erp_total_expense" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Pending Payment Box</label>
                                    <select id="show_erp_pending_payment" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Job Card Bills Box</label>
                                    <select id="show_erp_job_card_bills" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Daily Register Box</label>
                                    <select id="show_erp_daily_register" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Appointments Box</label>
                                    <select id="show_erp_appointments" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Attendance Box</label>
                                    <select id="show_erp_attendance" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Job Card Chart</label>
                                    <select id="show_erp_job_card_chart" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Daily Data Chart</label>
                                    <select id="show_erp_daily_data_chart" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Latest Job Card Table</label>
                                    <select id="show_erp_latest_job_card" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Latest Daily Data Table</label>
                                    <select id="show_erp_latest_daily_data" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-1">HR Portal Section On Dashboard</h5>
                                <p class="text-muted small mb-0">Control what HR sections are visible on the dashboard.</p>
                            </div>

                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>HR Portal Section On Dashboard</label>
                                    <select id="show_hr_dashboard" name="show_hr_dashboard" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                    <small class="text-muted">When disabled, the entire HR section is hidden from the dashboard.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-1">HR Dashboard Subsections</h5>
                                <p class="text-muted small mb-0">Control visibility of individual HR dashboard components:</p>
                            </div>

                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Staff Strength Box</label>
                                    <select id="show_hr_staff_strength" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Active Staff Box</label>
                                    <select id="show_hr_active_staff" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Monthly Attendance Box</label>
                                    <select id="show_hr_monthly_attendance" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Personal Progress Box</label>
                                    <select id="show_hr_personal_progress" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>7 Day Attendance Pattern Chart</label>
                                    <select id="show_hr_attendance_pattern" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Salary Payroll Trend Chart</label>
                                    <select id="show_hr_salary_payroll_trend" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Payroll Snapshot Table</label>
                                    <select id="show_hr_payroll_snapshot" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Attendance Watch Table</label>
                                    <select id="show_hr_attendance_watch" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Payroll Status Table</label>
                                    <select id="show_hr_payroll_status" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveDashboardSettings">
                                    Save Dashboard Settings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= BARCODE SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="barcode-settings" role="tabpanel" aria-labelledby="barcode-tab">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Barcode Type</label>
                                    <select id="barcode_type" name="barcode_type" class="form-select">
                                        <option value="barcode">Barcode</option>
                                        <option value="qrcode">QR Code</option>
                                    </select>
                                    <small class="text-muted">Choose between barcode or QR code format.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Barcode Setting Type</label>
                                    <select id="barcode_setting_type" name="barcode_setting_type" class="form-select">
                                        <option value="simple">Simple</option>
                                        <option value="detailed">Detailed</option>
                                    </select>
                                    <small class="text-muted">Choose simple barcode or detailed barcode output.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12 d-none" id="barcodeDetailWrapper">
                                <div class="form-group">
                                    <label>Detailed Barcode Format</label>
                                    <select id="barcode_detail_option" name="barcode_detail_option" class="form-select">
                                        <option value="long">Long</option>
                                        <option value="short">Short</option>
                                    </select>
                                    <small class="text-muted">Shown only when Barcode Setting Type is Detailed.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveBarcodeSettings">
                                    Save Barcode Settings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= ORDER NO FORMAT TAB ================= -->
                    <div class="tab-pane fade" id="order-format-settings" role="tabpanel" aria-labelledby="order-format-tab">
                        <div class="order-format-container mt-2">
                            <!-- 1. With GST Bill No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">With GST Bill No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_with_gst" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">INV-2026-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="with_gst" data-format-title="With GST Bill No" data-default-format="INV-{YYYY}-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Without GST Bill No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Without GST Bill No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_without_gst" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">W-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="without_gst" data-format-title="Without GST Bill No" data-default-format="W-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Rental Bill No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-truck-loading"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Rental Bill No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_rental" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">R-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="rental" data-format-title="Rental Bill No" data-default-format="R-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>

                            <!-- 4. Advance Receipt No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Advance Receipt No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_advance_payment" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">ADV-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="advance_payment" data-format-title="Advance Receipt No" data-default-format="ADV-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>

                            <!-- 5. Quotation No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Quotation No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_quotation" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">Q-2026-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="quotation" data-format-title="Quotation No" data-default-format="Q-{YYYY}-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>

                            <!-- 6. Quotation with GST No Preview -->
                            <div class="order-format-card d-flex align-items-center justify-content-between p-3 mb-3" style="border: 1px solid #ffd8af; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                                <div class="d-flex align-items-center">
                                    <div class="order-format-icon me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px; border-radius: 8px; background: #fff3e6; color: #ff9f43; font-size: 20px;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Quotation with GST No Preview:</span>
                                            <span class="badge-order-preview" id="preview_badge_quotation_with_gst" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: #fffaf5; font-weight: 700; padding: 3px 12px; border-radius: 6px; font-size: 13px;">QGST-2026-0001</span>
                                        </div>
                                        <div style="font-size: 12px; color: #888;">
                                            Supported tags: <code style="color: #666;">{NUMBER}</code>, <code style="color: #666;">{0000}</code>, <code style="color: #666;">{YYYY}</code>, <code style="color: #666;">{YY}</code>, <code style="color: #666;">{MM}</code>, <code style="color: #666;">{DD}</code>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-edit-format" data-bs-toggle="modal" data-bs-target="#editOrderFormatModal" data-toggle="modal" data-target="#editOrderFormatModal" data-format-key="quotation_with_gst" data-format-title="Quotation with GST No" data-default-format="QGST-{YYYY}-{0000}" style="border: 1.5px solid #ff9f43; color: #ff9f43; background: transparent; font-weight: 600; border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                                        + Edit Format
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

        </div>

    </div>

    <!-- ================= EDIT ORDER NO FORMAT PATTERN MODAL ================= -->
    <div class="modal fade" id="editOrderFormatModal" tabindex="-1" aria-labelledby="editOrderFormatModalLabel" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
                <div class="modal-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                    <div class="d-flex align-items-center">
                        <div style="background: #fff8eb; width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #ff9f43; font-size: 18px; margin-right: 12px;">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <h5 class="modal-title fw-bold" id="modal_format_title_text" style="color: #1e293b; font-size: 18px; margin-bottom: 0;">
                            Add With GST Bill No Format
                        </h5>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 22px; line-height: 1; color: #ef4444; font-weight: 700; cursor: pointer; padding: 0 4px;">
                        &times;
                    </button>
                </div>
                <div class="modal-body" style="padding: 18px 24px 24px 24px;">
                    <input type="hidden" id="modal_format_key">
                    
                    <p id="modal_format_description" class="text-muted mb-3" style="font-size: 13px; color: #64748b; line-height: 1.5;">
                        Enter a pattern with custom prefix, symbols, and tags to format sale bill order numbers.
                    </p>

                    <!-- Order No Format Pattern Outline Input Box -->
                    <div class="position-relative mt-3 mb-4" style="border: 1.5px solid #64748b; border-radius: 8px; padding: 10px 16px;">
                        <span style="position: absolute; top: -10px; left: 14px; background: #fff; padding: 0 6px; font-size: 12px; font-weight: 600; color: #64748b;">Order No Format Pattern</span>
                        <input type="text" id="modal_format_input" class="w-100" style="border: none; outline: none; font-size: 15px; font-weight: 600; color: #334155; background: transparent;" value="INV-{YYYY}-{0000}" placeholder="INV-{YYYY}-{0000}">
                    </div>

                    <!-- Quick Insert Tags -->
                    <div class="mb-2" style="font-size: 13px; font-weight: 600; color: #334155;">Quick Insert Tags:</div>
                    <div class="d-flex flex-wrap gap-2 mb-4" id="format_tag_buttons">
                        <button type="button" class="btn btn-tag-insert" data-tag="{NUMBER}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {NUMBER} (1)</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{0000}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {0000} (0001)</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{00000}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {00000} (00001)</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{YYYY}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {YYYY} ({{ date('Y') }})</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{YY}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {YY} ({{ date('y') }})</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{MM}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {MM} ({{ date('m') }})</button>
                        <button type="button" class="btn btn-tag-insert" data-tag="{DD}" style="background: #f0f7ff; border: 1px solid #d0e3ff; color: #0284c7; border-radius: 6px; font-weight: 600; font-size: 12.5px; padding: 6px 12px; cursor: pointer;">+ {DD} ({{ date('d') }})</button>
                    </div>

                    <!-- Live Preview Box -->
                    <div class="d-flex align-items-center p-3 mb-4 rounded" style="background: #fffcf6; border: 1.5px solid #fed7aa; border-radius: 8px;">
                        <i class="fas fa-eye me-2" style="color: #f59e0b; font-size: 15px;"></i>
                        <span style="font-weight: 600; color: #475569; font-size: 13.5px; margin-right: 6px;">Live Preview:</span>
                        <span id="modal_live_preview_text" style="color: #f59e0b; font-weight: 700; font-size: 14.5px;">INV-{{ date('Y') }}-0001</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <button type="button" class="btn" data-bs-dismiss="modal" data-dismiss="modal" style="background: #fff; border: 1.5px solid #e2e8f0; color: #475569; font-weight: 600; font-size: 13.5px; border-radius: 6px; padding: 9px 34px;">Cancel</button>
                        <button type="button" class="btn text-white fw-bold" id="btn_save_order_format" style="background: #ff9f43; border: none; font-size: 13.5px; border-radius: 6px; padding: 9px 24px; box-shadow: 0 2px 5px rgba(255,159,67,0.3);">Add & Select Format</button>
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
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            const ImagePath = ("{{ image_path() }}").replace(/\/+$/, '');
            const getStorageUrl = function(path) {
                if (!path) return '';
                if (path.startsWith('http://') || path.startsWith('https://')) return path;
                var cleanPath = String(path).replace(/^\/+/, '').replace(/^storage\/+/, '');
                return ImagePath + '/storage/' + cleanPath;
            };
            // Load General Settings Data
            function loadGeneralSettings() {
                $.ajax({
                    url: "{{ route('general-settings.show') }}",
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: {
                        selectedSubAdminId: selectedSubAdminId
                    }, // ✅ Pass branch
                    success: function(response) {
                        console.log("Branch ID:", response.branch_id);

                        if (response.settings) {
                            $("#low_stock").val(response.settings.low_stock ?? "");
                            $("#shop_name").val(response.settings.name ?? "");
                            $("#gst_num").val(response.settings.gst_num ?? "");
                            $("#state_code").val(response.settings.state_code ?? "");
                            $("#email").val(response.settings.email ?? "");
                            $("#phone").val(response.settings.phone ?? "");
                            $("#alt_phone").val(response.settings.alt_phone ?? "");
                            $("#address").val(response.settings.address ?? "");
                            $("#bank_name").val(response.settings.bank_name ?? "");
                            $("#branch").val(response.settings.branch ?? "");
                            $("#ac_no").val(response.settings.ac_no ?? "");
                            $("#ifsc_code").val(response.settings.ifsc_code ?? "");

                            if (response.settings.currency_position) {
                                $("#currency_position")
                                    .val(response.settings.currency_position)
                                    .trigger("change");
                            }
                            $("#currency_symbol").val(response.settings.currency_symbol ?? "");

                            if (response.settings.logo) {
                                $("#logo_preview").attr("src", getStorageUrl(response.settings.logo)).show();
                            }
                            if (response.settings.favicon) {
                                $("#favicon_preview").attr("src", getStorageUrl(response.settings.favicon)).show();
                            }
                            if (response.settings.qr_code) {
                                $("#qr_preview").attr("src", getStorageUrl(response.settings.qr_code)).show();
                            }
                            
                            $("#office_latitude").val(response.settings.office_latitude ?? "");
                            $("#office_longitude").val(response.settings.office_longitude ?? "");
                            $("#office_radius").val(response.settings.radius ?? "200");
                            // Company Rules Fields
                            $("#working_hours").val(response.settings.working_hours ?? "");
                            if (response.settings.sunday_off === 'yes') {
                                $("#sunday_yes").prop("checked", true);
                            } else {
                                $("#sunday_no").prop("checked", true);
                            }
                            if (response.settings.saturday_off === 'yes') {
                                $("#saturday_yes").prop("checked", true);
                            } else {
                                $("#saturday_no").prop("checked", true);
                            }
                            $("#grace_period").val(response.settings.grace_period ?? "");
                            $("#lunch_break").val(response.settings.lunch_break ?? "");
                            $("#open_time").val(response.settings.open_time ?? "");
                            $("#close_time").val(response.settings.close_time ?? "");
                            $("#overtime_after_hours").val(response.settings.overtime_after_hours ?? "");

                            if (response.settings.location_check_enabled == 1) {
                                $("#location_check_on").prop("checked", true);
                            } else {
                                $("#location_check_off").prop("checked", true);
                            }

                            $("#late_mark_after_minutes").val(response.settings.late_mark_after_minutes ?? "");
                            $("#half_day_after_hours").val(response.settings.half_day_after_hours ?? "");
                            $("#auto_absent_after_hours").val(response.settings.auto_absent_after_hours ?? "");
                            
                            if (response.settings.allow_early_checkout === 'yes') {
                                $("#early_yes").prop("checked", true);
                            } else {
                                $("#early_no").prop("checked", true);
                            }

                            if (response.settings.require_attendance_photo === 'yes') {
                                $("#photo_yes").prop("checked", true);
                            } else {
                                $("#photo_no").prop("checked", true);
                            }

                            if (response.settings.require_gps_location_for_attendance === 'yes') {
                                $("#gps_yes").prop("checked", true);
                            } else {
                                $("#gps_no").prop("checked", true);
                            }

                            if (response.settings.allow_overtime_approval === 'yes') {
                                $("#ot_yes").prop("checked", true);
                            } else {
                                $("#ot_no").prop("checked", true);
                            }

                            $("#max_overtime_hours_per_day").val(response.settings.max_overtime_hours_per_day ?? "");
                            $("#weekly_off_day").val(response.settings.weekly_off_day ?? "");
                            $("#monthly_leave_limit").val(response.settings.monthly_leave_limit ?? "");
                            $("#tax_deduction_amount").val(response.settings.tax_deduction_amount ?? "200");
                            $("#salary_exceeds").val(response.settings.salary_exceeds ?? "");

                            // Dashboard settings
                            $("#show_erp_dashboard").val(response.settings.show_erp_dashboard ?? 1);
                            $("#show_erp_total_collections").val(response.settings.show_erp_total_collections ?? 1);
                            $("#show_erp_total_expense").val(response.settings.show_erp_total_expense ?? 1);
                            $("#show_erp_pending_payment").val(response.settings.show_erp_pending_payment ?? 1);
                            $("#show_erp_job_card_bills").val(response.settings.show_erp_job_card_bills ?? 1);
                            $("#show_erp_daily_register").val(response.settings.show_erp_daily_register ?? 1);
                            $("#show_erp_appointments").val(response.settings.show_erp_appointments ?? 1);
                            $("#show_erp_attendance").val(response.settings.show_erp_attendance ?? 1);
                            $("#show_erp_job_card_chart").val(response.settings.show_erp_job_card_chart ?? 1);
                            $("#show_erp_daily_data_chart").val(response.settings.show_erp_daily_data_chart ?? 1);
                            $("#show_erp_latest_job_card").val(response.settings.show_erp_latest_job_card ?? 1);
                            $("#show_erp_latest_daily_data").val(response.settings.show_erp_latest_daily_data ?? 1);
                            $("#show_hr_dashboard").val(response.settings.show_hr_dashboard ?? 1);
                            $("#show_hr_staff_strength").val(response.settings.show_hr_staff_strength ?? 1);
                            $("#show_hr_active_staff").val(response.settings.show_hr_active_staff ?? 1);
                            $("#show_hr_monthly_attendance").val(response.settings.show_hr_monthly_attendance ?? 1);
                            $("#show_hr_personal_progress").val(response.settings.show_hr_personal_progress ?? 1);
                            $("#show_hr_attendance_pattern").val(response.settings.show_hr_attendance_pattern ?? 1);
                            $("#show_hr_salary_payroll_trend").val(response.settings.show_hr_salary_payroll_trend ?? 1);
                            $("#show_hr_payroll_snapshot").val(response.settings.show_hr_payroll_snapshot ?? 1);
                            $("#show_hr_attendance_watch").val(response.settings.show_hr_attendance_watch ?? 1);
                            $("#show_hr_payroll_status").val(response.settings.show_hr_payroll_status ?? 1);

                            // Barcode settings
                            $("#barcode_type").val(
                                response.settings.barcode_type === null || response.settings.barcode_type === undefined || response.settings.barcode_type === '' ?
                                'barcode' : response.settings.barcode_type
                            );
                            $("#barcode_setting_type").val(
                                response.settings.barcode_setting_type === null || response.settings.barcode_setting_type === undefined || response.settings.barcode_setting_type === '' ?
                                'simple' : response.settings.barcode_setting_type
                            );
                            $("#barcode_detail_option").val(
                                response.settings.barcode_detail_option === null || response.settings.barcode_detail_option === undefined || response.settings.barcode_detail_option === '' ?
                                'long' : response.settings.barcode_detail_option
                            );
                            toggleBarcodeDetailOption();

                            // Order Number Format Settings
                            window.orderFormats = window.orderFormats || {};
                            window.orderFormats.with_gst = response.settings.with_gst_format || 'INV-{YYYY}-{0000}';
                            window.orderFormats.without_gst = response.settings.without_gst_format || 'W-{0000}';
                            window.orderFormats.rental = response.settings.rental_format || 'R-{0000}';
                            window.orderFormats.advance_payment = response.settings.advance_payment_format || 'ADV-{0000}';
                            window.orderFormats.quotation = response.settings.quotation_format || 'Q-{YYYY}-{0000}';
                            window.orderFormats.quotation_with_gst = response.settings.quotation_with_gst_format || 'QGST-{YYYY}-{0000}';
                            if (typeof renderAllOrderFormatPreviews === 'function') {
                                renderAllOrderFormatPreviews();
                            }
                            if (typeof checkSubAdminBranch === 'function') {
                                checkSubAdminBranch();
                            }
                        } else {
                            console.warn("⚠️ No settings returned from backend:", response);
                        }
                    }

                });
            }
            // ERP Main Section Toggle
function toggleERPSubSections() {
    let status = $("#show_erp_dashboard").val();

    let erpFields = [
        "#show_erp_total_collections",
        "#show_erp_total_expense",
        "#show_erp_pending_payment",
        "#show_erp_job_card_bills",
        "#show_erp_daily_register",
        "#show_erp_appointments",
        "#show_erp_attendance",
        "#show_erp_job_card_chart",
        "#show_erp_daily_data_chart",
        "#show_erp_latest_job_card",
        "#show_erp_latest_daily_data"
    ];

    if (status == "0") {
        erpFields.forEach(function(field) {
            $(field).val("0").prop("disabled", true);
        });
    } else {
        erpFields.forEach(function(field) {
            $(field).prop("disabled", false);
        });
    }
}

// HR Main Section Toggle
function toggleHRSubSections() {
    let status = $("#show_hr_dashboard").val();

    let hrFields = [
        "#show_hr_staff_strength",
        "#show_hr_active_staff",
        "#show_hr_monthly_attendance",
        "#show_hr_personal_progress",
        "#show_hr_attendance_pattern",
        "#show_hr_salary_payroll_trend",
        "#show_hr_payroll_snapshot",
        "#show_hr_attendance_watch",
        "#show_hr_payroll_status"
    ];

    if (status == "0") {
        hrFields.forEach(function(field) {
            $(field).val("0").prop("disabled", true);
        });
    } else {
        hrFields.forEach(function(field) {
            $(field).prop("disabled", false);
        });
    }
}

// Change Event
$("#show_erp_dashboard").on("change", function () {
    toggleERPSubSections();
});

$("#show_hr_dashboard").on("change", function () {
    toggleHRSubSections();
});

// Initial Load
toggleERPSubSections();
toggleHRSubSections();

            function toggleBarcodeDetailOption() {
                const isDetailed = $("#barcode_setting_type").val() === "detailed";
                $("#barcodeDetailWrapper").toggleClass("d-none", !isDetailed);
                $("#barcode_detail_option").prop("disabled", !isDetailed);
            }

            $("#barcode_setting_type").on("change", function() {
                toggleBarcodeDetailOption();
            });

            loadGeneralSettings(); // Load on page load

            // Explicitly handle tab switching to bypass Bootstrap conflicts
            $('.settings-tabs .nav-link').on('click', function(e) {
                e.preventDefault();
                $('.settings-tabs .nav-link').removeClass('active');
                $(this).addClass('active');
                var target = $(this).attr('href') || $(this).data('target') || $(this).data('bs-target');
                $('.tab-content .tab-pane').removeClass('show active');
                $(target).addClass('show active');
            });

            // Show Image Preview
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr("src", e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#logo").change(function() {
                previewImage(this, "#logo_preview");
            });

            $("#favicon").change(function() {
                previewImage(this, "#favicon_preview");
            });

            $("#qr_code").change(function() {
                previewImage(this, "#qr_preview");
            });

            // Restrict State Code to letters only
            $("#state_code").on("input", function() {
                this.value = this.value.replace(/[^a-zA-Z\s]/g, "");
            });

            // Multi-step Form Logic for Shop Settings
            let currentStep = 1;
            const totalSteps = 3;

            function updateSteps() {
                // Update Step Buttons
                $('.step-btn').removeClass('active');
                $(`.step-btn[data-step="${currentStep}"]`).addClass('active');

                // Update Step Content
                $('.step-content').removeClass('active');
                $(`.step-${currentStep}`).addClass('active');

                // Update Navigation Buttons
                if (currentStep === 1) {
                    $('.btn-prev').addClass('d-none');
                    $('.btn-next').removeClass('d-none');
                    $('.btn-submit-mobile').addClass('d-none');
                } else if (currentStep === totalSteps) {
                    $('.btn-prev').removeClass('d-none');
                    $('.btn-next').addClass('d-none');
                    $('.btn-submit-mobile').removeClass('d-none');
                } else {
                    $('.btn-prev').removeClass('d-none');
                    $('.btn-next').removeClass('d-none');
                    $('.btn-submit-mobile').addClass('d-none');
                }

                // Scroll to top of the tab content on step change for better UX
                if ($("#settingsTabsContent").length) {
                    $('html, body').animate({
                        scrollTop: $("#settingsTabsContent").offset().top - 100
                    }, 300);
                }
            }

            // Validation rules for each step
            const stepValidations = {
                1: [
                    { id: 'shop_name', name: 'Shop Name' },
                    { id: 'email', name: 'Shop Email' },
                    { id: 'phone', name: 'Shop Phone' },
                    { id: 'bank_name', name: 'Bank Name' },
                    { id: 'branch', name: 'Branch' },
                    { id: 'ac_no', name: 'A/C No.' },
                    { id: 'ifsc_code', name: 'IFSC Code' },
                    { id: 'address', name: 'Shop Address' }
                ],
                2: [
                    { id: 'currency_symbol', name: 'Currency Symbol' },
                    { id: 'currency_position', name: 'Currency Position' }
                ],
                3: []
            };

            function validateCurrentStep() {
                $('.step-content.active .text-danger').remove();

                const rules = stepValidations[currentStep] || [];
                let hasError = false;

                rules.forEach(field => {
                    const el = $("#" + field.id);
                    const value = el.val();
                    if (!value || String(value).trim() === '') {
                        el.closest('.form-group').find('.text-danger').remove();
                        el.after(
                            `<div class="text-danger mt-1"><i class="fas fa-exclamation-circle"></i> ${field.name} is required</div>`
                        );
                        hasError = true;
                    }
                });

                return !hasError;
            }

            $('.btn-next').on('click', function() {
                if (currentStep < totalSteps) {
                    if (validateCurrentStep()) {
                        currentStep++;
                        updateSteps();
                    }
                }
            });

            $('.btn-prev').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateSteps();
                }
            });

            $('.step-btn').on('click', function() {
                const targetStep = parseInt($(this).data('step'));
                if (targetStep < currentStep || targetStep === currentStep) {
                    currentStep = targetStep;
                    updateSteps();
                } else if (targetStep > currentStep) {
                    if (validateCurrentStep()) {
                        currentStep = targetStep;
                        updateSteps();
                    }
                }
            });

            // Handle submit button click (trigger hidden submit)
            $('.btn-submit-mobile').on('click', function() {
                $("#btn-setting-submit").click();
            });

            $('#shop-settings .btn-cancel').on('click', function(e) {
                e.preventDefault();
                loadGeneralSettings();
                currentStep = 1;
                updateSteps();
            });

            // Fetch GST Details
            $("#gst_num").on("blur", function() {
                const gst = $(this).val();
                if (gst.length === 15) {
                    $.ajax({
                        url: "{{ route('gst.fetch') }}",
                        type: "POST",
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        data: {
                            gst_number: gst
                        },
                        success: function(response) {
                            if (response.state_code) {
                                $("#state_code").val(response.state_code);
                            }
                            if (response.company_name && !$("#shop_name").val()) {
                                $("#shop_name").val(response.company_name);
                            }
                            if (response.primary_address && !$("#address").val()) {
                                $("#address").val(response.primary_address);
                            }
                        },
                        error: function(xhr) {
                            console.error("GST fetch failed:", xhr);
                        }
                    });
                }
            });

            // Update General Settings
            $("#btn-setting-submit").on("click", function(e) {
                e.preventDefault(); // prevent form submission if there are errors

                $(".text-danger").remove(); // clear previous errors

                let hasError = false;

                const fields = [{
                        id: "low_stock",
                        name: "Low Stock Warning Quantity"
                    },
                    {
                        id: "shop_name",
                        name: "Shop Name"
                    },
                    {
                        id: "gst_num",
                        name: "GST Number"
                    },
                    {
                        id: "state_code",
                        name: "State Code"
                    },
                    {
                        id: "email",
                        name: "Email"
                    },
                    {
                        id: "phone",
                        name: "Phone"
                    },
                    {
                        id: "address",
                        name: "Address"
                    },
                    {
                        id: "bank_name",
                        name: "Bank Name"
                    },
                    {
                        id: "branch",
                        name: "Branch"
                    },
                    {
                        id: "ac_no",
                        name: "A/C No."
                    },
                    {
                        id: "ifsc_code",
                        name: "IFSC Code"
                    },
                    {
                        id: "currency_position",
                        name: "Currency Position"
                    },
                    {
                        id: "currency_symbol",
                        name: "Currency Symbol"
                    },
                ];

                // Validate each required field
                fields.forEach(field => {
                    const value = $("#" + field.id).val();
                    if (!value) {
                        $("#" + field.id)
                            .after(`<div class="text-danger mt-1">${field.name} is required</div>`);
                        hasError = true;
                    }
                });

                if (hasError) {
                    if ($('.step-1 .text-danger').length > 0) {
                        currentStep = 1;
                        updateSteps();
                    } else if ($('.step-2 .text-danger').length > 0) {
                        currentStep = 2;
                        updateSteps();
                    }
                    return;
                }

                // Prepare FormData
                let formData = new FormData();
                formData.append("low_stock", $("#low_stock").val());
                formData.append("shop_name", $("#shop_name").val());
                formData.append("gst_num", $("#gst_num").val());
                formData.append("state_code", $("#state_code").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("alt_phone", $("#alt_phone").val());
                formData.append("address", $("#address").val());
                formData.append("bank_name", $("#bank_name").val());
                formData.append("branch", $("#branch").val());
                formData.append("ac_no", $("#ac_no").val());
                formData.append("ifsc_code", $("#ifsc_code").val());
                formData.append("currency_position", $("#currency_position").val());
                formData.append("currency_symbol", $("#currency_symbol").val());
                formData.append("office_latitude", $("#office_latitude").val());
                formData.append("office_longitude", $("#office_longitude").val());
                formData.append("radius", $("#office_radius").val());
                
                if (logo) formData.append("logo", logo);
                if (favicon) formData.append("favicon", favicon);
                if (qr_code) formData.append("qr_code", qr_code);
                // In update
                if (selectedSubAdminId) {
                    formData.append("selectedSubAdminId", selectedSubAdminId);
                }
                formData.append("_token", "{{ csrf_token() }}");

                // Send AJAX
                $.ajax({
                    url: "{{ route('general-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", xhr.responseJSON.message, "error");
                    }
                });
            });


            $('#btnFetchCoordinates').click(function() {
                var fullAddr = $('#address').val().trim();
                var $status = $('#coordinateLookupStatus');
                if (!fullAddr) {
                    $status.text('Please enter Shop Address first.').removeClass('text-success').addClass('text-danger');
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true).text('Fetching...');
                $status.text('Looking up coordinates...').removeClass('text-danger text-success').addClass('text-muted');

                function fetchGeocode(query, isFallback) {
                    $.ajax({
                        url: "https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(query),
                        method: 'GET',
                        success: function(data) {
                            if (data && data.length > 0) {
                                $btn.prop('disabled', false).text('Get GPS from Address');
                                $('#office_latitude').val(data[0].lat);
                                $('#office_longitude').val(data[0].lon);
                                if (isFallback) {
                                    $status.text('Exact address not found. Coordinates fetched for city/area instead.').removeClass('text-danger text-muted').addClass('text-success');
                                } else {
                                    $status.text('Coordinates found & updated!').removeClass('text-danger text-muted').addClass('text-success');
                                }
                            } else {
                                if (!isFallback && fullAddr.includes(',')) {
                                    // Try fallback with the last 2 parts (usually City, State Pincode)
                                    var parts = fullAddr.split(',');
                                    if (parts.length > 1) {
                                        var fallbackQuery = parts.slice(-2).join(',').trim();
                                        $status.text('Exact address not found. Trying simpler search...').removeClass('text-success text-danger').addClass('text-muted');
                                        fetchGeocode(fallbackQuery, true);
                                    } else {
                                        $btn.prop('disabled', false).text('Get GPS from Address');
                                        $status.text('Could not find coordinates. Please try a simpler address.').removeClass('text-success text-muted').addClass('text-danger');
                                    }
                                } else {
                                    $btn.prop('disabled', false).text('Get GPS from Address');
                                    $status.text('Could not find coordinates. Please try City and Pincode manually.').removeClass('text-success text-muted').addClass('text-danger');
                                }
                            }
                        },
                        error: function() {
                            $btn.prop('disabled', false).text('Get GPS from Address');
                            $status.text('Error connecting to lookup service.').removeClass('text-success text-muted').addClass('text-danger');
                        }
                    });
                }

                fetchGeocode(fullAddr, false);
            });

            // Handle Reset button
            $("#resetCompanyRules").on("click", function(e) {
                e.preventDefault();
                loadGeneralSettings();
                Swal.fire({
                    title: "Reset",
                    text: "Rules have been reloaded from saved settings.",
                    icon: "info",
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // ================== COMPANY RULES SAVE ==================
            $("#saveCompanyRules").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");

                // Clear previous validation errors
                $(".text-danger").remove();

                let formData = new FormData();
                formData.append("working_hours", $("#working_hours").val());
                formData.append("sunday_off", $("input[name='sunday_off']:checked").val() || 'no');
                formData.append("saturday_off", $("input[name='saturday_off']:checked").val() || 'no');
                formData.append("grace_period", $("#grace_period").val());
                formData.append("lunch_break", $("#lunch_break").val());
                formData.append("open_time", $("#open_time").val());
                formData.append("close_time", $("#close_time").val());
                formData.append("overtime_after_hours", $("#overtime_after_hours").val());
                formData.append("location_check_enabled", $("input[name='location_check_enabled']:checked").val() || 0);
                
                // New Fields
                formData.append("late_mark_after_minutes", $("#late_mark_after_minutes").val());
                formData.append("half_day_after_hours", $("#half_day_after_hours").val());
                formData.append("auto_absent_after_hours", $("#auto_absent_after_hours").val());
                formData.append("allow_early_checkout", $("input[name='allow_early_checkout']:checked").val() || 'no');
                formData.append("require_attendance_photo", $("input[name='require_attendance_photo']:checked").val() || 'no');
                formData.append("require_gps_location_for_attendance", $("input[name='require_gps_location_for_attendance']:checked").val() || 'no');
                formData.append("allow_overtime_approval", $("input[name='allow_overtime_approval']:checked").val() || 'no');
                formData.append("max_overtime_hours_per_day", $("#max_overtime_hours_per_day").val());
                formData.append("weekly_off_day", $("#weekly_off_day").val());
                formData.append("monthly_leave_limit", $("#monthly_leave_limit").val());
                formData.append("tax_deduction_amount", $("#tax_deduction_amount").val());
                formData.append("salary_exceeds", $("#salary_exceeds").val());

                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('company-rules.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Display validation messages below each input
                            $.each(errors, function(key, value) {
                                const field = $("#" + key);
                                if (field.length) {
                                    field
                                        .closest(".form-group")
                                        .append('<div class="text-danger mt-1">' +
                                            value[0] + "</div>");
                                }
                            });
                        } else {
                            Swal.fire("Error!", xhr.responseJSON.message ||
                                "Something went wrong!", "error");
                        }
                    },
                });
            });

            // ================== DASHBOARD SETTINGS SAVE ==================
            $("#saveDashboardSettings").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();

                let formData = new FormData();
                formData.append("show_erp_dashboard",           $("#show_erp_dashboard").val());
                formData.append("show_erp_total_collections",   $("#show_erp_total_collections").val());
                formData.append("show_erp_total_expense",       $("#show_erp_total_expense").val());
                formData.append("show_erp_pending_payment",     $("#show_erp_pending_payment").val());
                formData.append("show_erp_job_card_bills",      $("#show_erp_job_card_bills").val());
                formData.append("show_erp_daily_register",      $("#show_erp_daily_register").val());
                formData.append("show_erp_appointments",        $("#show_erp_appointments").val());
                formData.append("show_erp_attendance",          $("#show_erp_attendance").val());
                formData.append("show_erp_job_card_chart",      $("#show_erp_job_card_chart").val());
                formData.append("show_erp_daily_data_chart",    $("#show_erp_daily_data_chart").val());
                formData.append("show_erp_latest_job_card",     $("#show_erp_latest_job_card").val());
                formData.append("show_erp_latest_daily_data",   $("#show_erp_latest_daily_data").val());
                formData.append("show_hr_dashboard",            $("#show_hr_dashboard").val());
                formData.append("show_hr_staff_strength",       $("#show_hr_staff_strength").val());
                formData.append("show_hr_active_staff",         $("#show_hr_active_staff").val());
                formData.append("show_hr_monthly_attendance",   $("#show_hr_monthly_attendance").val());
                formData.append("show_hr_personal_progress",    $("#show_hr_personal_progress").val());
                formData.append("show_hr_attendance_pattern",   $("#show_hr_attendance_pattern").val());
                formData.append("show_hr_salary_payroll_trend", $("#show_hr_salary_payroll_trend").val());
                formData.append("show_hr_payroll_snapshot",     $("#show_hr_payroll_snapshot").val());
                formData.append("show_hr_attendance_watch",     $("#show_hr_attendance_watch").val());
                formData.append("show_hr_payroll_status",       $("#show_hr_payroll_status").val());
                formData.append("selectedSubAdminId", selectedSubAdminId ?? "");
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('general-dashboard-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + authToken },
                    beforeSend: function() {
                        $button.addClass("disabled").html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...');
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Saved!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => { window.location.reload(); });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) || "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").html(defaultHtml);
                    }
                });
            });

            // ================== BARCODE SETTINGS SAVE ==================
            $("#saveBarcodeSettings").on("click", function(e) {
                e.preventDefault();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();
                $button.addClass("disabled").attr("aria-disabled", "true").html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                );

                const data = {
                    barcode_type: $("#barcode_type").val(),
                    barcode_setting_type: $("#barcode_setting_type").val(),
                    _token: "{{ csrf_token() }}"
                };

                if (data.barcode_setting_type === "detailed") {
                    data.barcode_detail_option = $("#barcode_detail_option").val();
                }

                if (selectedSubAdminId) {
                    data.selectedSubAdminId = selectedSubAdminId;
                }

                $.ajax({
                    url: "{{ route('barcode-settings.update') }}",
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message || "Barcode settings updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(defaultHtml);
                    }
                });
            });

            // ================= SUB-ADMIN BRANCH TAB VISIBILITY =================
            function checkSubAdminBranch() {
                const subId = localStorage.getItem("selectedSubAdminId");
                const uRole = (localStorage.getItem("userRole") || "{{ Auth::user()->role ?? '' }}").toLowerCase();
                const isSub = (subId && subId !== "1" && subId !== "null" && subId !== "") || (uRole === 'sub-admin');

                if (isSub) {
                    $('.sub-admin-only-tab').show();
                } else {
                    $('.sub-admin-only-tab').hide();
                    if ($('#order-format-tab').hasClass('active')) {
                        $('#shop-tab').tab('show');
                    }
                }
            }
            checkSubAdminBranch();

            // ================= ORDER NO FORMAT LOGIC =================
            const defaultOrderFormats = {
                with_gst: 'INV-{YYYY}-{0000}',
                without_gst: 'W-{0000}',
                rental: 'R-{0000}',
                advance_payment: 'ADV-{0000}',
                quotation: 'Q-{YYYY}-{0000}',
                quotation_with_gst: 'QGST-{YYYY}-{0000}'
            };

            window.orderFormats = window.orderFormats || Object.assign({}, defaultOrderFormats);

            const formatDescriptions = {
                with_gst: 'Enter a pattern with custom prefix, symbols, and tags to format sale bill order numbers.',
                without_gst: 'Enter a pattern with custom prefix, symbols, and tags to format without GST bill numbers.',
                rental: 'Enter a pattern with custom prefix, symbols, and tags to format rental order numbers.',
                advance_payment: 'Enter a pattern with custom prefix, symbols, and tags to format advance receipt numbers.',
                quotation: 'Enter a pattern with custom prefix, symbols, and tags to format quotation numbers.',
                quotation_with_gst: 'Enter a pattern with custom prefix, symbols, and tags to format quotation with GST numbers.'
            };

            function evaluateOrderFormatPreview(formatStr, sampleNum = 1) {
                if (!formatStr || String(formatStr).trim() === '') return '';
                const now = new Date();
                const yyyy = String(now.getFullYear());
                const yy = yyyy.slice(-2);
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                
                let result = String(formatStr);
                result = result.replace(/\{YYYY\}/g, yyyy);
                result = result.replace(/\{YY\}/g, yy);
                result = result.replace(/\{MM\}/g, mm);
                result = result.replace(/\{DD\}/g, dd);
                
                result = result.replace(/\{00000\}/g, String(sampleNum).padStart(5, '0'));
                result = result.replace(/\{0000\}/g, String(sampleNum).padStart(4, '0'));
                result = result.replace(/\{000\}/g, String(sampleNum).padStart(3, '0'));
                result = result.replace(/\{00\}/g, String(sampleNum).padStart(2, '0'));
                result = result.replace(/\{0\}/g, String(sampleNum));
                result = result.replace(/\{1\}/g, String(sampleNum));
                result = result.replace(/\{NUMBER\}/g, String(sampleNum));
                
                return result;
            }

            function renderAllOrderFormatPreviews() {
                $("#preview_badge_with_gst").text(evaluateOrderFormatPreview(window.orderFormats.with_gst || defaultOrderFormats.with_gst));
                $("#preview_badge_without_gst").text(evaluateOrderFormatPreview(window.orderFormats.without_gst || defaultOrderFormats.without_gst));
                $("#preview_badge_rental").text(evaluateOrderFormatPreview(window.orderFormats.rental || defaultOrderFormats.rental));
                $("#preview_badge_advance_payment").text(evaluateOrderFormatPreview(window.orderFormats.advance_payment || defaultOrderFormats.advance_payment));
                $("#preview_badge_quotation").text(evaluateOrderFormatPreview(window.orderFormats.quotation || defaultOrderFormats.quotation));
                $("#preview_badge_quotation_with_gst").text(evaluateOrderFormatPreview(window.orderFormats.quotation_with_gst || defaultOrderFormats.quotation_with_gst));
            }

            // Initial render of badges
            renderAllOrderFormatPreviews();

            let modalFormatCursorPos = null;

            function populateOrderFormatModal(key, title, defaultFormat) {
                const fallbackDefault = defaultFormat || defaultOrderFormats[key] || 'INV-{YYYY}-{0000}';
                const currentFormat = (window.orderFormats && window.orderFormats[key] !== undefined && window.orderFormats[key] !== null && window.orderFormats[key] !== '') 
                    ? window.orderFormats[key] 
                    : fallbackDefault;

                const now = new Date();
                const currentYYYY = now.getFullYear();
                const currentYY = String(currentYYYY).slice(-2);
                const currentMM = String(now.getMonth() + 1).padStart(2, '0');
                const currentDD = String(now.getDate()).padStart(2, '0');

                // Dynamically update quick tag button text
                $(`.btn-tag-insert[data-tag="{YYYY}"]`).text(`+ {YYYY} (${currentYYYY})`);
                $(`.btn-tag-insert[data-tag="{YY}"]`).text(`+ {YY} (${currentYY})`);
                $(`.btn-tag-insert[data-tag="{MM}"]`).text(`+ {MM} (${currentMM})`);
                $(`.btn-tag-insert[data-tag="{DD}"]`).text(`+ {DD} (${currentDD})`);

                $("#modal_format_key").val(key);
                $("#modal_format_title_text").text("Add " + (title || "Order No") + " Format");
                $("#modal_format_description").text(formatDescriptions[key] || 'Enter a pattern with custom prefix, symbols, and tags to format order numbers.');
                $("#modal_format_input").val(currentFormat).attr('placeholder', fallbackDefault);
                
                modalFormatCursorPos = currentFormat.length;
                
                const previewResult = evaluateOrderFormatPreview(currentFormat);
                const previewEl = document.getElementById("modal_live_preview_text");
                if (previewEl) {
                    previewEl.innerText = previewResult;
                }
                $("#modal_live_preview_text").text(previewResult);
            }

            function openOrderFormatModal() {
                const modalEl = document.getElementById('editOrderFormatModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.show();
                } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('show');
                }
            }

            function closeOrderFormatModal() {
                const modalEl = document.getElementById('editOrderFormatModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.hide();
                } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('hide');
                }
            }

            // Click on + Edit Format button
            $(document).on("click", ".btn-edit-format", function(e) {
                const key = $(this).data("format-key") || $(this).attr("data-format-key");
                const title = $(this).data("format-title") || $(this).attr("data-format-title");
                const defaultFormat = $(this).data("default-format") || $(this).attr("data-default-format");

                populateOrderFormatModal(key, title, defaultFormat);
                openOrderFormatModal();
            });

            // Handle bootstrap modal show event
            $('#editOrderFormatModal').on('show.bs.modal', function(e) {
                const triggerBtn = $(e.relatedTarget);
                if (triggerBtn && triggerBtn.length && triggerBtn.hasClass('btn-edit-format')) {
                    const key = triggerBtn.data("format-key") || triggerBtn.attr("data-format-key");
                    const title = triggerBtn.data("format-title") || triggerBtn.attr("data-format-title");
                    const defaultFormat = triggerBtn.data("default-format") || triggerBtn.attr("data-default-format");
                    populateOrderFormatModal(key, title, defaultFormat);
                }
            });

            // Focus and set cursor to end when modal is fully displayed
            $('#editOrderFormatModal').on('shown.bs.modal', function() {
                const input = document.getElementById('modal_format_input');
                if (input) {
                    const len = input.value.length;
                    input.focus();
                    try {
                        input.setSelectionRange(len, len);
                    } catch (e) {}
                    modalFormatCursorPos = len;
                }
            });

            // Close modal triggers
            $(document).on("click", "#editOrderFormatModal [data-bs-dismiss='modal'], #editOrderFormatModal [data-dismiss='modal'], #editOrderFormatModal .btn-close-custom", function(e) {
                e.preventDefault();
                closeOrderFormatModal();
            });

            // Track cursor position and update live preview in real-time as user types, pastes, or moves cursor
            $(document).on("input keyup mouseup select", "#modal_format_input", function() {
                if (this.selectionStart !== null && this.selectionStart !== undefined) {
                    modalFormatCursorPos = this.selectionStart;
                }
                const previewResult = evaluateOrderFormatPreview(this.value);
                const previewEl = document.getElementById("modal_live_preview_text");
                if (previewEl) {
                    previewEl.innerText = previewResult;
                }
                $("#modal_live_preview_text").text(previewResult);
            });

            // Function to insert tag into input pattern
            function insertOrderFormatTag(tag) {
                if (!tag) return;
                const input = document.getElementById("modal_format_input");
                if (!input) return;

                const currentVal = input.value || '';
                let startPos = currentVal.length;
                let endPos = currentVal.length;

                if (document.activeElement === input && input.selectionStart !== null && input.selectionStart !== undefined) {
                    startPos = input.selectionStart;
                    endPos = input.selectionEnd !== null ? input.selectionEnd : startPos;
                } else if (modalFormatCursorPos !== null && modalFormatCursorPos !== undefined && !isNaN(modalFormatCursorPos)) {
                    startPos = modalFormatCursorPos;
                    endPos = modalFormatCursorPos;
                }

                if (startPos < 0 || startPos > currentVal.length || isNaN(startPos)) {
                    startPos = currentVal.length;
                    endPos = currentVal.length;
                }
                if (endPos < startPos || endPos > currentVal.length || isNaN(endPos)) {
                    endPos = startPos;
                }

                const newVal = currentVal.substring(0, startPos) + tag + currentVal.substring(endPos);
                input.value = newVal;

                const newCursorPos = startPos + tag.length;
                modalFormatCursorPos = newCursorPos;

                input.focus();
                try {
                    input.setSelectionRange(newCursorPos, newCursorPos);
                } catch (err) {}

                // Update Live Preview instantly
                const previewResult = evaluateOrderFormatPreview(newVal);
                const previewEl = document.getElementById("modal_live_preview_text");
                if (previewEl) {
                    previewEl.innerText = previewResult;
                }
                $("#modal_live_preview_text").text(previewResult);
            }

            // Expose globally
            window.insertOrderFormatTag = insertOrderFormatTag;

            // Insert tag at cursor position when quick insert tag button is clicked
            $(document).on("click", ".btn-tag-insert", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const tag = $(this).data("tag") || $(this).attr("data-tag");
                if (tag) {
                    insertOrderFormatTag(tag);
                }
            });

            // Save Format via AJAX
            $("#btn_save_order_format").on("click", function(e) {
                e.preventDefault();
                const $button = $(this);
                const key = $("#modal_format_key").val();
                const formatValue = $("#modal_format_input").val().trim();

                if (!key) return;

                const defaultHtml = $button.html();
                $button.addClass("disabled").attr("aria-disabled", "true").html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                );

                const data = {};
                data[key + "_format"] = formatValue;

                const currentSubAdminId = localStorage.getItem("selectedSubAdminId");
                if (currentSubAdminId) {
                    data.selectedSubAdminId = currentSubAdminId;
                }

                $.ajax({
                    url: "{{ route('order-format-settings.update') }}",
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + (typeof authToken !== 'undefined' ? authToken : ''),
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: data,
                    success: function(response) {
                        window.orderFormats[key] = formatValue;
                        renderAllOrderFormatPreviews();
                        closeOrderFormatModal();
                        Swal.fire({
                            title: "Success!",
                            text: response.message || "Order number format updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) || "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(defaultHtml);
                    }
                });
            });
        });
    </script>

@endpush
