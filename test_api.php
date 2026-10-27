<?php
$url = 'https://fabproducts.fableadtech.com/license-erp/api/verify-license';
$data = ['username' => 'fablead_admin', 'purchase_code' => '12345678-ABCD-1234-EFGH-123456789XYZ', 'domain' => 'localhost', 'product_code' => 'FABLEAD_ERP'];
$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);
if ($result === false) {
    echo "Request failed. Error: " . error_get_last()['message'] . "\n";
} else {
    echo "Response: " . $result . "\n";
}
