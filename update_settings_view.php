<?php

$file = 'd:/xampp/htdocs/garage-erp/resources/views/settings/generalsettings.blade.php';
$content = file_get_contents($file);

$companyRulesHTML = <<<HTML
                    <!-- ================= COMPANY RULES TAB ================= -->
                    <div class="tab-pane fade" id="company-rules" role="tabpanel" aria-labelledby="rules-tab">
                        
                        <!-- Working Rules Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-briefcase me-2"></i> Working Rules</h5>
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Working Hours (per day) <span class="text-danger">*</span></label>
                                        <input type="time" id="working_hours" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label d-block">Sunday Off?</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="sunday_off" id="sunday_yes" value="yes">
                                            <label class="btn btn-outline-primary" for="sunday_yes">Yes</label>
                                            
                                            <input type="radio" class="btn-check" name="sunday_off" id="sunday_no" value="no" checked>
                                            <label class="btn btn-outline-primary" for="sunday_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Grace Period <span class="text-danger">*</span></label>
                                        <input type="time" id="grace_period" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Lunch Break</label>
                                        <div class="input-group">
                                            <input type="number" id="lunch_break" class="form-control" placeholder="e.g. 45" min="0">
                                            <span class="input-group-text">mins</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Company Open Time <span class="text-danger">*</span></label>
                                        <input type="time" id="open_time" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Company Close Time <span class="text-danger">*</span></label>
                                        <input type="time" id="close_time" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Overtime After</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" id="overtime_after_hours" class="form-control" placeholder="e.g. 9" min="0">
                                            <span class="input-group-text">hours</span>
                                        </div>
                                        <small class="text-muted d-block mt-1">Hours after which overtime starts.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Security Settings Section -->
                        <div class="mb-4">
                            <h5 class="text-warning mb-3"><i class="fas fa-shield-alt me-2"></i> Security Settings</h5>
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card bg-light border-0 shadow-sm rounded-3">
                                        <div class="card-body p-3">
                                            <label class="form-label fw-bold">Location Check on Login</label>
                                            <div class="d-flex align-items-center gap-3 mt-2">
                                                <div class="form-check form-switch fs-5">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" id="location_check_enabled_switch">
                                                    <label class="form-check-label ms-2 fs-6" for="location_check_enabled_switch" id="location_check_label">OFF (Allow anywhere)</label>
                                                </div>
                                                <input type="hidden" name="location_check_enabled" id="location_check_enabled" value="0">
                                            </div>
                                            <small class="text-muted d-block mt-2">If enabled, staff must be within the allowed office radius to log in and mark attendance.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Additional Company Policies Section -->
                        <div class="mb-4">
                            <h5 class="text-info mb-3"><i class="fas fa-list-alt me-2"></i> Additional Company Policies</h5>
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Late Mark After</label>
                                        <div class="input-group">
                                            <input type="number" id="late_mark_after_minutes" class="form-control" placeholder="e.g. 15" min="0">
                                            <span class="input-group-text">mins</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Half Day After</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" id="half_day_after_hours" class="form-control" placeholder="e.g. 4" min="0">
                                            <span class="input-group-text">hours</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Auto Absent After</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" id="auto_absent_after_hours" class="form-control" placeholder="e.g. 2" min="0">
                                            <span class="input-group-text">hours</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label d-block">Allow Early Checkout</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="allow_early_checkout" id="early_yes" value="yes">
                                            <label class="btn btn-outline-info" for="early_yes">Yes</label>
                                            
                                            <input type="radio" class="btn-check" name="allow_early_checkout" id="early_no" value="no" checked>
                                            <label class="btn btn-outline-info" for="early_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label d-block">Require Attendance Photo</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="require_attendance_photo" id="photo_yes" value="yes">
                                            <label class="btn btn-outline-info" for="photo_yes">Yes</label>
                                            
                                            <input type="radio" class="btn-check" name="require_attendance_photo" id="photo_no" value="no" checked>
                                            <label class="btn btn-outline-info" for="photo_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label d-block">Require GPS for Attendance</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="require_gps_location_for_attendance" id="gps_yes" value="yes">
                                            <label class="btn btn-outline-info" for="gps_yes">Yes</label>
                                            
                                            <input type="radio" class="btn-check" name="require_gps_location_for_attendance" id="gps_no" value="no" checked>
                                            <label class="btn btn-outline-info" for="gps_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label d-block">Allow Overtime Approval</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="allow_overtime_approval" id="ot_yes" value="yes">
                                            <label class="btn btn-outline-info" for="ot_yes">Yes</label>
                                            
                                            <input type="radio" class="btn-check" name="allow_overtime_approval" id="ot_no" value="no" checked>
                                            <label class="btn btn-outline-info" for="ot_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Max Overtime (Per Day)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" id="max_overtime_hours_per_day" class="form-control" placeholder="e.g. 4" min="0">
                                            <span class="input-group-text">hours</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Weekly Off Day</label>
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

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Monthly Leave Limit</label>
                                        <input type="number" id="monthly_leave_limit" class="form-control" placeholder="e.g. 2" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top sticky-bottom bg-white pb-3">
                            <button type="button" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" id="saveCompanyRules">
                                <i class="fas fa-save me-2"></i> Save Rules
                            </button>
                            <button type="button" class="btn btn-light px-4 py-2 border fw-bold shadow-sm" id="resetCompanyRules">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                        </div>
                    </div>
HTML;

// Find the end of #shop-settings
$searchString = '                    <div class="col-lg-12">
                        <a href="javascript:void(0);" class="btn btn-submit me-2">Submit</a>
                    </div>
                </div>
            </div>';

if (strpos($content, $searchString) !== false) {
    // If company-rules already exists, remove it first
    $existingStart = strpos($content, '<!-- ================= COMPANY RULES TAB ================= -->');
    if ($existingStart !== false) {
        $existingEnd = strpos($content, '<!-- ================= DASHBOARD SETTINGS TAB ================= -->', $existingStart);
        if ($existingEnd !== false) {
             // remove the existing chunk
             $content = substr($content, 0, $existingStart) . substr($content, $existingEnd);
        } else {
             // fallback: just remove until the end of the div containing it
             // we'll just not do this to be safe, since it doesn't exist
        }
    }
    
    // Insert after shop-settings ends
    $content = str_replace($searchString, $searchString . "\n" . $companyRulesHTML, $content);
    file_put_contents($file, $content);
    echo "Successfully updated HTML structure.\n";
} else {
    echo "Could not find insertion point.\n";
}

?>
