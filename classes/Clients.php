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
		
		// Get individual variables from $_POST
		$id = $_POST['id'] ?? '';
		$firstname = $_POST['firstname'] ?? '';
		$lastname = $_POST['lastname'] ?? '';
		$address = $_POST['address'] ?? '';
		$gender = $_POST['gender'] ?? '';
		$contact = $_POST['contact'] ?? '';
		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';
		
		
	
		// Prepare data for SQL query
		$data = " firstname = ?, lastname = ?, gender = ?, address = ?, contact = ?, email = ?, password = ?";
		$params = [ $firstname, $lastname, $gender, $address, $contact, $email, $password];
	
		// Handle password separately
		if(!empty($password)){
			$data .= ", password = ?";
			$params[] = md5($password);
		}
	
		if (empty($id)) {
			// Insert new record
			$sql = "INSERT INTO clients (firstname, lastname, gender, address, contact, email,password) VALUES (?, ?, ?, ?, ?, ?,?)";
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
			// Set avatar session data if updated
			return 1;
		} else {
			// Return error message
			return "Error: " . $stmt->error;
		}
	}

	public function delete_users(){
		$id = $_POST['id'] ?? '';
		if (!empty($id)) {
			$stmt = $this->conn->prepare("DELETE FROM clients WHERE id = ?");
			$stmt->bind_param("i", $id); // Bind the parameter to the prepared statement
			if ($stmt->execute()) {
				$this->settings->set_flashdata('success', 'User Details successfully deleted.');
				return 1;
			} else {
				return "Error: " . $stmt->error;
			} 	
		} else {
			return "Error: No user ID provided.";
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
        echo 'Invalid action';
        break;
}
?>
