<?php
include ('../conn/conn.php');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the QR code from the POST data
    $qrCode = $_POST['qr-code'];

    // Create a PDO connection
    try {
        $conn = new PDO("mysql:host=localhost;dbname=sogod_market_db", "root", "");
        // Set PDO to throw exceptions on error
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("SELECT * FROM `clients` WHERE `generated_code` = :generated_code");
        $stmt->bindParam(':generated_code', $qrCode);
        $stmt->execute();

        // Fetch the result as an associative array
        $accountExist =  $stmt->fetch(PDO::FETCH_ASSOC);

        // If the account exists
       // If the account exists
if ($accountExist) {
    session_start();
    $_SESSION['id'] = $accountExist['id'];

    // Set session data directly
    foreach ($accountExist as $k => $v) {
        $_SESSION[$k] = $v;
    }
    $_SESSION['login_type'] = 1;

    // Return success response
    $resp['status'] = 'success';
    echo json_encode($resp);
    echo "
        <script>
            alert('Login Successfully!');
            window.location.href = 'http://localhost/sogodmarket';
        </script>
        "; 
} else {
    // Return incorrect response
    $resp['status'] = 'incorrect';
    echo json_encode($resp);
}

    } catch (PDOException $e) {
        // Return error response
        $resp['status'] = 'failed';
        $resp['_error'] = $e->getMessage();
        echo json_encode($resp);
    }
} else {
    // Return method not allowed response
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
