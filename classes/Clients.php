<?php
require_once('../config.php');
require_once('../vendor/autoload.php'); // Assuming you are using Composer to install dependencies

use GuzzleHttp\Client; // For HTTP requests

class Clients extends DBConnection {
    private $settings;
    private $semaphore_api_key = 'c07761afafbbeb8051c2b6fbb1e329af'; // Replace with your actual Semaphore API Key

    public function __construct() {
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }

    // Method to send SMS to a single contact
    public function send_single_sms() {
        $contact = $_POST['contact'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($contact) || empty($message)) {
            return json_encode(['status' => 'error', 'msg' => 'Contact and message fields are required.']);
        }

        try {
            $client = new Client();
            $response = $client->post('https://api.semaphore.co/api/v4/messages', [
                'form_params' => [
                    'apikey' => $this->semaphore_api_key,
                    'number' => $contact,
                    'message' => $message,
                    'sendername' => 'SogodMarket' // Replace with your sender name
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['status'] === 'success') {
                return json_encode(['status' => 'success']);
            } else {
                return json_encode(['status' => 'error', 'msg' => $result['message'] ?? 'Failed to send SMS.']);
            }
        } catch (Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
    public function save_users() {
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
            // Fetch the user record to set session variables
            $user = $this->conn->query("SELECT * FROM clients WHERE email = '$email'")->fetch_assoc();
    
            if ($user) {
                // Start session and set session variables

                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $firstname . ' ' . $lastname;
                $_SESSION['email'] = $user['email'];
                $_SESSION['contact'] = $user['contact'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['generated_code'] = $user['generated_code'];
                $_SESSION['gender'] = $user['gender'];
                $_SESSION['login_type'] = 1; // Login type for internal use
    
                // Set flash message for the operation
                $message = empty($id) ? 'User Details successfully saved and logged in.' : 'User Details successfully updated and logged in.';
                $this->settings->set_flashdata('success', $message);
    
                // Return success response
                return json_encode(['status' => 'success']);
            } else {
                // Error fetching user after save
                return json_encode(['status' => 'failed', 'msg' => 'Error fetching user data after save.']);
            }
        } else {
            // Return error message
            return json_encode(['status' => 'failed', 'msg' => "Error: " . $stmt->error]);
        }
    }    
    public function update_users() {
        // Get individual variables from $_POST and sanitize them
        $id =  $_SESSION['id'] ?? '';
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
            // Fetch the user record to set session variables
            $user = $this->conn->query("SELECT * FROM clients WHERE email = '$email'")->fetch_assoc();
    
            if ($user) {
                // Start session and set session variables

                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $firstname . ' ' . $lastname;
                $_SESSION['email'] = $user['email'];
                $_SESSION['contact'] = $user['contact'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['generated_code'] = $user['generated_code'];
                $_SESSION['gender'] = $user['gender'];
                $_SESSION['login_type'] = 1; // Login type for internal use
    
                // Set flash message for the operation
                $message = empty($id) ? 'User Details successfully saved and logged in.' : 'User Details successfully updated and logged in.';
                $this->settings->set_flashdata('success', $message);
    
                // Return success response
                return json_encode(['status' => 'success']);
            } else {
                // Error fetching user after save
                return json_encode(['status' => 'failed', 'msg' => 'Error fetching user data after save.']);
            }
        } else {
            // Return error message
            return json_encode(['status' => 'failed', 'msg' => "Error: " . $stmt->error]);
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


    // Method to send SMS to all contacts
    public function send_bulk_sms() {
        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            return json_encode(['status' => 'error', 'msg' => 'Message field is required.']);
        }

        $contacts = $this->conn->query("SELECT contact FROM clients WHERE contact IS NOT NULL");
        if (!$contacts || $contacts->num_rows == 0) {
            return json_encode(['status' => 'error', 'msg' => 'No contacts found to send bulk SMS.']);
        }

        $allContacts = [];
        while ($row = $contacts->fetch_assoc()) {
            $allContacts[] = $row['contact'];
        }

        try {
            $client = new Client();
            $response = $client->post('https://api.semaphore.co/api/v4/messages', [
                'form_params' => [
                    'apikey' => $this->semaphore_api_key,
                    'number' => implode(',', $allContacts),
                    'message' => $message,
                    'sendername' => 'SEMAPHORE' // Replace with your sender name
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['status'] === 'success') {
                return json_encode(['status' => 'success']);
            } else {
                return json_encode(['status' => 'partial', 'msg' => $result['message'] ?? 'Some messages failed.']);
            }
        } catch (Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
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
    case 'send_single_sms':
        echo $clients->send_single_sms();
        break;
    case 'send_bulk_sms':
        echo $clients->send_bulk_sms();
        break;
    case 'update_users':
        echo $clients->update_users();
        break;
        
    default:
        echo json_encode(['status' => 'failed', 'msg' => 'Invalid action']);
        break;
}
?>
