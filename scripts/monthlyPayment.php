<?php
function sendSMS($message, $recipient) {
    $apiKey = "c07761afafbbeb8051c2b6fbb1e329af"; // Replace with your Semaphore API key
    $url = "https://api.semaphore.co/api/v4/messages";

    $data = array(
        "apikey" => $apiKey,
        "number" => $recipient,
        "message" => $message,
        "sendername" => "SogodMarket" // Optional: Customize sender ID
    );

    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        die("Error sending SMS.");
    }

    return json_decode($result, true);
}

// Use the specific number and message
$message = "Hello";
$recipient = "09667713831"; // The specified phone number

$response = sendSMS($message, $recipient);

if (isset($response['status']) && $response['status'] === 'Success') {
    echo "SMS sent successfully!";
} else {
    echo "Failed to send SMS. Response: " . print_r($response, true);
}
?>
