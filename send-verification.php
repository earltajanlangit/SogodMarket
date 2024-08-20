<?php
require 'vendor/autoload.php';

use Twilio\Rest\Client;

// Twilio credentials
$sid = 'AC4fafee2b5eecc224a18fe740a9123df2'; 
$token = '73d5227bdef1c0eaead01bcf66f9c7f5'; 
$twilio_number = '+12093754713'; 

$client = new Client($sid, $token);
session_start(); 
if (isset($_POST['contact'])) {
    $contact = $_POST['contact'];
    $contact1 = "+63 936 064 8398";
    $generated_code = $_POST['generated_code'];
    
    try {
        // Ensure the contact number is in E.164 format
        $message = $client->messages->create(
            $contact1, 
            [
                'from' => $twilio_number, 
                'body' => "Your verification code is: $generated_code"
            ]
        );
        $_SESSION['verification_code'][$contact] = $generated_code;
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        error_log("Twilio error: " . $e->getMessage()); // Log the error
        echo json_encode(['status' => 'failed', 'msg' => 'Failed to send verification code.']);
    }
} else {
    echo json_encode(['status' => 'failed', 'msg' => 'No contact number provided']);
}
?>
