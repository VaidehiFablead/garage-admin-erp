<?php

$file = 'd:/xampp/htdocs/garage-erp/resources/views/settings/generalsettings.blade.php';
$content = file_get_contents($file);

// Remove the HTML block for Location Check on Login in Shop Settings
$blockStart = '                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Location Check on Login</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="location_check_enabled"
                                        id="location_check_on" value="1">
                                    <label class="form-check-label" for="location_check_on">ON</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="location_check_enabled"
                                        id="location_check_off" value="0" checked>
                                    <label class="form-check-label" for="location_check_off">OFF</label>
                                </div>
                            </div>
                        </div>
                    </div>';

$content = str_replace($blockStart, '', $content);

// Remove JS formData append for Shop Settings
$jsAppend = 'formData.append("location_check_enabled", $("input[name=\'location_check_enabled\']:checked").val());';
$content = str_replace($jsAppend, '', $content);

file_put_contents($file, $content);
echo "Successfully removed Location Check on Login from Shop Settings.";
