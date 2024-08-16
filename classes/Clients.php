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
		$tbl_user_id = $_POST['tbl_user_id'] ?? '';
		$name = $_POST['name'] ?? '';
		$contact_number = $_POST['contact_number'] ?? '';
		$email = $_POST['email'] ?? '';
		
	
		// Prepare data for SQL query
		$data = "tbl_user_id = ?, name = ?, contact_number = ?, email = ?";
		$params = [$tbl_user_id, $name, $contact_number, $email];
	
		// Handle password separately
		if(!empty($name)){
			$data .= ", name = ?";
			$params[] = md5($name);
		}
	
		// Prepare SQL query
		if(empty($tbl_user_id)){
			// Insert new record
			$stmt = $this->conn->prepare("INSERT INTO clients SET {$data}");
		} else {
			// Update existing record
			$stmt = $this->conn->prepare("UPDATE clients SET {$data} WHERE tbl_user_id = ?");
			$params[] = $tbl_user_id;
		}
	
		// Execute SQL query with parameters
		if ($stmt->execute($params)) {
			// Set flash message and session data
			$message = empty($tbl_user_id) ? 'User Details successfully saved.' : 'User Details successfully updated.';
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
		$tbl_user_id = $_POST['tbl_user_id'] ?? '';
		if (!empty($tbl_user_id)) {
			$stmt = $this->conn->prepare("DELETE FROM clients WHERE tbl_user_id = ?");
			$stmt->bind_param("i", $tbl_user_id); // Bind the parameter to the prepared statement
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
