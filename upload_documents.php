<?php
session_start();
include('db_connection.php'); // Assuming db_connection.php contains your DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if Cedule and Photo Copy Valid ID files are uploaded
    if (isset($_FILES['document']) && isset($_FILES['document_photocopy'])) {
        // Extract file details
        $ceduleFile = $_FILES['document'];
        $photocopyFile = $_FILES['document_photocopy'];
        
        // Define upload directories
        $uploadDir = 'uploads/documents/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file names and paths
        $ceduleFilePath = $uploadDir . basename($ceduleFile['name']);
        $photocopyFilePath = $uploadDir . basename($photocopyFile['name']);

        // File upload validation (check for errors)
        $errors = [];
        
        // Validate Cedule file
        if ($ceduleFile['error'] != 0) {
            $errors[] = "Error uploading Cedule file.";
        }
        
        // Validate Photocopy file
        if ($photocopyFile['error'] != 0) {
            $errors[] = "Error uploading Photo Copy Valid ID.";
        }

        if (empty($errors)) {
            // Move files to the upload directory
            if (move_uploaded_file($ceduleFile['tmp_name'], $ceduleFilePath) && move_uploaded_file($photocopyFile['tmp_name'], $photocopyFilePath)) {
                // Insert file details into the database
                $description = isset($_POST['document_description']) ? $_POST['document_description'] : '';
                $clientId = $_SESSION['id']; // Assuming client ID is stored in the session
                
                // Prepare SQL to insert file data
                $sql = "INSERT INTO documents (client_id, cedule, photocopy_valid_id, description) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                // Bind parameters and execute
                $stmt->bind_param("isss", $clientId, $ceduleFilePath, $photocopyFilePath, $description);
                $stmt->execute();

                // Check if insertion was successful
                if ($stmt->affected_rows > 0) {
                    // Redirect back with success message
                    header("Location: ./?p=my_account&msg=Document uploaded successfully.");
                    exit();
                } else {
                    $errors[] = "Failed to save document information to database.";
                }
            } else {
                $errors[] = "Failed to upload Cedule or Photo Copy Valid ID.";
            }
        }
        
        // Handle errors
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header("Location: ./?p=my_account");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Cedule and Photo Copy Valid ID are required.";
        header("Location: ./?p=my_account");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: ./?p=my_account");
    exit();
}
