<?php

$file = 'd:/xampp/htdocs/garage-erp/resources/views/settings/generalsettings.blade.php';
$content = file_get_contents($file);

$startMarker = '                            <div class="col-lg-12 mt-4">
                                <h5>Additional Company Policies</h5>
                                <hr>
                            </div>';
$endMarker = '                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveCompanyRules">Save Rules</a>
                            </div>';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $contentBefore = substr($content, 0, $startPos);
    $contentAfter = substr($content, $endPos);
    $newContent = $contentBefore . $contentAfter;
    file_put_contents($file, $newContent);
    echo "Successfully removed Additional Company Policies block.";
} else {
    echo "Could not find markers to remove block.";
}
?>
