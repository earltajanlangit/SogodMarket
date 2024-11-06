<?php
require_once('../config.php');

class Clients extends DBConnection {
    private $settings;

    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct(){
        parent::__destruct();
    }

    public function save_users(){
        // Get individual variables from $_POST and sanitize them
        $id = $_POST['id'] ?? '';
        $firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
        $lastname = htmlspecialchars(trim($_POST['lastname'] ?? ''));
        $address = htmlspecialchars(trim($_POST['address'] ?? ''));
        $gender = htmlspecialchars(trim($_POST['gender'] ?? ''));
        $contact = htmlspecialchars(trim($_POST['contact'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        $generated_code = bin2hex(random_bytes(6)); // Generates a random 8-character hex code
        error_log("Generated Code: " . $generated_code); // Log the generated code

        // Prepare data for SQL query
        $data = "firstname = ?, lastname = ?, gender = ?, address = ?, contact = ?, email = ?, generated_code = ?";
        $params = [$firstname, $lastname, $gender, $address, $contact, $email, $generated_code];
        error_log("Params: " . json_encode($params)); 

        // Handle password separately
        if (!empty($password)) {
            $data .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for better security
        }

        if (empty($id)) {   
            // Insert new record
            $sql = "INSERT INTO clients (firstname, lastname, gender, address, contact, email, generated_code" . (!empty($password) ? ", password" : "") . ") VALUES (?, ?, ?, ?, ?, ?, ?" . (!empty($password) ? ", ?" : "") . ")";
            $stmt = $this->conn->prepare($sql);
        } else {
            // Update existing record
            $sql = "UPDATE clients SET $data WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $params[] = $id; // Add ID to parameters for update
        }

        // Execute SQL query with parameters
        if ($stmt->execute($params)) {
            // Set flash message and session data
            $message = empty($id) ? 'User Details successfully saved.' : 'User Details successfully updated.';
            $this->settings->set_flashdata('success', $message);
            $this->settings->set_userdata('email', $email);
            return json_encode(['status' => 'success']); // Return success status
        } else {
            // Return error message
            return json_encode(['status' => 'failed', 'msg' => "Error: " . $stmt->error]); // Provide detailed SQL error
        }
    }

    public function delete_users(){
        $id = $_POST['id'] ?? '';
        if (!empty($id)) {
            $stmt = $this->conn->prepare("DELETE FROM clients WHERE id = ?");
            $stmt->bind_param("i", $id); // Bind the parameter to the prepared statement
            if ($stmt->execute()) {
                $this->settings->set_flashdata('success', 'User Details successfully deleted.');
                return json_encode(['status' => 'success']); // Return success status
            } else {
                return json_encode(['status' => 'failed', 'msg' => "Error: " . $stmt->error]); // Provide detailed SQL error
            }     
        } else {
            return json_encode(['status' => 'failed', 'msg' => "Error: No user ID provided."]); // Provide error for no ID
        }
    }
}

// Instantiate Clients class
$clients = new Clients();

// Check for action parameter
$action = isset($_GET['f']) ? strtolower($_GET['f']) : 'none';

// Execute corresponding method based on action
switch ($action) {
    case 'save':
        echo $clients->save_users();
        break;
    case 'delete':
        echo $clients->delete_users();
        break;
    default:
        echo json_encode(['status' => 'failed', 'msg' => 'Invalid action']);
        break;
}
?>
