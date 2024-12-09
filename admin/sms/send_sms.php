<?php
header('Content-Type: application/json');

// Database configuration
$db_host = "localhost";      // Replace with your database host
$db_user = "root";           // Replace with your database username
$db_pass = "";               // Replace with your database password
$db_name = "your_database";  // Replace with your database name

// Semaphore API configuration
$api_key = "c07761afafbbeb8051c2b6fbb1e329af"; // Replace with your Semaphore API key
$sender_name = "SogodMarket";                  // Replace with your registered sender name
$sms_url = "https://api.semaphore.co/api/v4/messages";

// Function to send SMS via Semaphore API
function send_sms($contact_number, $message, $api_key, $sender_name, $sms_url)
{
    $sms_data = [
        'apikey' => $api_key,
        'number' => $contact_number,
        'message' => $message,
        'sendername' => $sender_name,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sms_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => 'failed', 'error' => $error];
    }
    return ['status' => 'success', 'response' => $response];
}

// Handle API requests
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'POST') {
    // Database connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $input = $_POST;
    $contact = isset($input['contact']) ? trim($input['contact']) : null;
    $message = isset($input['message']) ? trim($input['message']) : null;

    if (!$message) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
        exit;
    }

    if ($contact) {
        // Send to a single contact
        $result = send_sms($contact, $message, $api_key, $sender_name, $sms_url);
        echo json_encode($result);
    } else {
        // Send to all contacts
        $results = [];
        $query = "SELECT contact FROM clients WHERE contact IS NOT NULL AND contact != ''";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $contact_number = $row['contact'];
                $sms_result = send_sms($contact_number, $message, $api_key, $sender_name, $sms_url);
                $results[] = ['contact' => $contact_number, 'result' => $sms_result];
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No contacts found in the database.']);
            exit;
        }

        echo json_encode(['status' => 'completed', 'results' => $results]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
