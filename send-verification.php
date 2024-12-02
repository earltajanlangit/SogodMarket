<?php
require 'vendor/autoload.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure that the keys exist in $_POST
    if (isset($_POST['contact']) && isset($_POST['generated_code'])) {
        $contact = $_POST['contact'];
        $generated_code = $_POST['generated_code'];

        try {
            // Send the SMS via Semaphore API
            $api_key = "c07761afafbbeb8051c2b6fbb1e329af"; // Your Semaphore API Key
            $sender_name = "SogodMarket"; // Registered Sender Name in Semaphore
            $message = "Your otp is  $generated_code";       

            // Semaphore API Endpoint
            $url = "https://api.semaphore.co/api/v4/messages";

            // Data to be sent
            $data = [
                'apikey' => $api_key,
                'number' => $contact, // Recipient's number from the form
                'message' => $message,
                'sendername' => $sender_name,
            ];

            // Initialize cURL session
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Adjust response time
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout to 30 seconds

            // Execute the cURL request
            $response = curl_exec($ch);

            // Handle errors
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                echo "Response: " . $response;
            }

            // Close the cURL session
            curl_close($ch);

            // Store the verification code in the session
            $_SESSION['verification_code'][$contact] = $generated_code;

            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            error_log("Twilio error: " . $e->getMessage()); // Log the error
            echo json_encode(['status' => 'failed', 'msg' => 'Failed to send verification code.']);
        }
    }
}
?>
