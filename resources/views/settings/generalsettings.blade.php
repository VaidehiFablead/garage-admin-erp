@extends('layout.app')

@section('title', 'Shop Settings')

@section('content')
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
        @media screen and (max-width: 991px) {
            .step-content {
                display: none;
            }

            .step-content.active {
                display: flex; /* Ensure it's flex as it's a row */
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
            }

            .step-btn {
                background: transparent;
                color: #637381;
                border-radius: 50px;
                flex: 1;
                font-size: 11px;
                padding: 10px 5px;
                border: none;
                font-weight: 600;
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .step-btn.active {
                background: #ff9f43;
                color: #fff;
                box-shadow: 0 4px 10px rgba(255, 159, 67, 0.2);
            }

            .mobile-nav-btns {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #f1f1f1;
            }

            .btn-prev {
                background: #1B2850;
                color: #fff;
                border: none;
                padding: 10px 25px;
                border-radius: 8px;
                font-weight: 600;
                flex: 1;
            }

            .btn-next {
                background: #ff9f43;
                color: #fff;
                border: none;
                padding: 10px 25px;
                border-radius: 8px;
                font-weight: 600;
                flex: 1;
            }

            .btn-submit-mobile {
                background: #ff9f43;
                color: #fff;
                border: none;
                padding: 10px 25px;
                border-radius: 8px;
                font-weight: 600;
                flex: 1;
            }

            /* Hide desktop submit button in shop settings on mobile steps 1 & 2 */
            #shop-settings .btn-submit:not(.btn-submit-mobile) {
                display: none !important;
            }
        }

        @media screen and (min-width: 992px) {
            .mobile-steps,
            .mobile-nav-btns {
                display: none !important;
            }

            .step-content {
                display: flex !important;
            }

            #shop-settings:not(.active) {
                display: none !important;
            }

            #shop-settings.active {
                display: flex;
                flex-wrap: wrap;
            }

            #shop-settings .step-content {
                display: contents !important;
            }

            #shop-settings .desktop-submit {
                width: 100%;
                margin-top: 8px;
            }
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
                <div class="row">
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
                            <input type="text" id="gst_num" placeholder="Enter GST Number">
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
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Shop Address<span class="manitory">*</span></label>
                            <textarea id="address" placeholder="Enter Address" rows="3" class="form-control"></textarea>
                            <div class="d-flex justify-content-between align-items-start mt-1">
                                <small id="coordinateLookupStatus" class="text-muted" style="line-height:1.2;"></small>
                                <button type="button" class="btn btn-sm btn-primary ms-2 flex-shrink-0" id="btnFetchCoordinates" style="padding: 2px 8px; font-size: 11px;">Get GPS from Address</button>
                            </div>
                        </div>
                    </div>

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

                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label>Shop Logo</label>
                            <div class="image-upload">
                                <input type="file" id="logo" accept="image/*">
                                <div class="image-uploads">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
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
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
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
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
                                        alt="img">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                            <img id="qr_preview" src="" alt="QR Preview"
                                style="display:none; max-width: 50px; margin-top: 10px;">
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



                    <div class="col-lg-12">
                        <a href="javascript:void(0);" class="btn btn-submit me-2">Submit</a>
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

        </div>

    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            const ImagePath = "{{ env('ImagePath') }}";
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
                                $("#logo_preview").attr("src", ImagePath + '/storage/' + response
                                    .settings.logo).show();
                            }
                            if (response.settings.favicon) {
                                $("#favicon_preview").attr("src", ImagePath + '/storage/' + response
                                    .settings.favicon).show();
                            }
                            if (response.settings.qr_code) {
                                $("#qr_preview").attr("src", ImagePath + '/storage/' + response.settings
                                    .qr_code).show();
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
            $("#shop-settings .btn-submit").on("click", function(e) {
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

                // Check if logo and favicon are selected (optional: remove this if not mandatory)
                let logo = $("#logo")[0].files[0];
                let favicon = $("#favicon")[0].files[0];
                let qr_code = $("#qr_code")[0].files[0];

                if (hasError) return; // stop submission if errors found

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
        });
    </script>

@endpush
