<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.semaphore.co/api/v4/messages");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'apikey' => '65ca60177cd2cc0e2f5184e3fa2d6d81',   // replace with your real Semaphore API key
    'number' => '+639619754591',  // replace with your test mobile number
    'message' => 'Hello from Semaphore!'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    echo "Response: " . $response;
}

curl_close($ch);
?>
