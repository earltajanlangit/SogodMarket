<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data sent from the client
    $data = json_decode(file_get_contents('php://input'), true);

    // Retrieve the verification code and contact number from the request
    $receivedCode = isset($data['verification_code']) ? trim($data['verification_code']) : '';
    $receivedContactNumber = isset($data['contact_number']) ? trim($data['contact_number']) : '';

    // Retrieve stored session values
    $storedCode = isset($_SESSION['verification_code']) ? $_SESSION['verification_code'] : '';
    $storedContactNumber = isset($_SESSION['contact_number']) ? $_SESSION['contact_number'] : '';

    // Validate the received code and contact number
    if ($receivedCode === $storedCode && $receivedContactNumber === $storedContactNumber) {
        // Verification successful
        echo json_encode(['success' => true]);
    } else {
        // Verification failed
        echo json_encode(['success' => false, 'message' => 'Invalid verification code or contact number']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
