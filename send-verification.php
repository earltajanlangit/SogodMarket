<?php
require 'vendor/autoload.php';

use Twilio\Rest\Client;

// Twilio credentials
$sid = 'ACf135ab5e39c48fcdbb605db4696c768c'; 
$token = '9dcad237fbc5afa4dc00e3e2011f8ec7'; 
$twilio_number = '+12242315707'; 

$client = new Client($sid, $token);
session_start(); 
if (isset($_POST['contact'])) {
    $contact = $_POST['contact'];
    $contact1 = "+63 991 960 9412";
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
