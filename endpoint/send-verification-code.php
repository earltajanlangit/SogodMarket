<?php
require '../admin/vendor/autoload.php';
use Twilio\Rest\Client;

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
        $contactNumber = "+63 936 064 8398"; // Extract contact number from JSON data

        // Validate contact number format if needed
        // Example: $contactNumber = validateAndFormatPhoneNumber($contactNumber);

        // Twilio credentials (replace with your own credentials)
       // $sid = 'AC4fafee2b5eecc224a18fe740a9123df2';
       // $token = '7917196bf13b7080e2c821b513817c67';
        $twilio = new Client($sid, $token);

        // Generate a random 6-digit verification code
        $verificationCode = rand(100000, 999999);

        try {
            // Send the verification code via SMS
            $message = $twilio->messages->create(
                $contactNumber,
                array(
                    'from' => '+12093754713', // Replace with your Twilio number
                    'body' => "Your verification code is: $verificationCode"
                )
            );

            // Save the verification code and phone number to session
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['contact_number'] = $contactNumber;

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // Add more debugging information
            error_log("Twilio Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to send verification code.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid content type']);
    }
}
?>
