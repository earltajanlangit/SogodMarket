<?php
require_once('../config.php');
require __DIR__ . "/../admin/vendor/autoload.php";
use Twilio\Rest\Client;
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_brand(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `space_type_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Brand already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `space_type_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `space_type_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Brand successfully saved.");
			else
				$this->settings->set_flashdata('success',"Brand successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_brand(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `space_type_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Brand successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `categories` where `category` = '{$category}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `categories` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `categories` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Category successfully saved.");
			else
				$this->settings->set_flashdata('success',"Category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `categories` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_bike(){
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$v = addslashes($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `space_list` where `space_name` = '{$space_name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "space already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `space_list` set {$data} ";
			$save = $this->conn->query($sql);
			$id= $this->conn->insert_id;
		}else{
			$sql = "UPDATE `space_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['msg'] = " Bike Successfully saved.";
			$thumb_fname = base_app."uploads/thumbnails/".$id.".png";
			if(isset($_FILES['thumbnail']['tmp_name']) && !empty($_FILES['thumbnail']['tmp_name'])){
				$upload = $_FILES['thumbnail']['tmp_name'];
                   $type = mime_content_type($upload);
                   $allowed = array('image/png','image/jpeg');
                   
                   if(!in_array($type,$allowed)){
                       $resp['msg'].=" But Image failed to upload due to invalid file type.";
                   }else{
                       $gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                       if($gdImg){
                            list($width, $height) = getimagesize($upload);
                            // new size variables
                            $new_height = 400; 
                            $new_width = 400;

                            $t_image = imagecreatetruecolor($new_width, $new_height);
                            //Resizing the imgage
                            imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                            if(is_file($thumb_fname))
                            unlink($thumb_fname);
                            imagepng($t_image,$thumb_fname);
                            imagedestroy($t_image);
                            imagedestroy($gdImg);
                       }else{
                       $resp['msg'].=" But Image failed to upload due to unkown reason.";
                       }
                   }
			}
			if(isset($_FILES['images']['tmp_name']) && !empty($_FILES['images']['tmp_name']) && count($_FILES['images']['tmp_name']) > 0){
                $dir = base_app.'uploads/'.$id.'/';
                if(!is_dir($dir))
                    mkdir($dir);
                foreach($_FILES['images']['tmp_name'] as $k=>$v){
					if(empty($v))
					continue;
                    $upload = $v;
                    $type = mime_content_type($upload);
                    $allowed = array('image/png','image/jpeg');
                    $_name = str_replace(".".pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION),'',$_FILES['images']['name'][$k]);
                    $ii = 1;
                    while(true){
                        $fname = $dir.$_name.'.png';
                        if(is_file($fname)){
                            $_name = $_name.'_'.($ii++);
                        }else{
                            break;
                        }
                    }
                    if(!in_array($type,$allowed)){
                        $resp['msg'].=" But Image failed to upload due to invalid file type.";
                    }else{
                        $gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                        if($gdImg){
                                list($width, $height) = getimagesize($upload);
                                // new size variables
                                $new_height = 600; 
                                $new_width = 1000;

                                $t_image = imagecreatetruecolor($new_width, $new_height);
                                //Resizing the imgage
                                imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                imagepng($t_image,$fname);
                                imagedestroy($t_image);
                                imagedestroy($gdImg);
                        }else{
                        $resp['msg'].=" But Image failed to upload due to unkown reason.";
                        }
                    }
                }
            }
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Bike successfully saved.");
			else
				$this->settings->set_flashdata('success',"Bike successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_bike(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `space_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"bike successfully deleted.");
			if(is_file(base_app.'uploads/thumbnails/'.$id.'.png'))
			unlink(base_app.'uploads/thumbnails/'.$id.'.png');
			$img_path = (base_app.'uploads/'.$id.'/');
			if(is_dir($img_path)){
				$scandir = scandir($img_path);
				foreach($scandir as $img){
					if(!in_array($img,array('.','..')))
					unlink($img_path.$img);
				}
				rmdir($img_path);
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_client(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `clients` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"client successfully deleted.");
			if(is_file(base_app.'uploads/thumbnails/'.$id.'.png'))
			unlink(base_app.'uploads/thumbnails/'.$id.'.png');
			$img_path = (base_app.'uploads/'.$id.'/');
			if(is_dir($img_path)){
				$scandir = scandir($img_path);
				foreach($scandir as $img){
					if(!in_array($img,array('.','..')))
					unlink($img_path.$img);
				}
				rmdir($img_path);
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	public function delete_vendor(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `rent_list` WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Record successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_bookingspart2(){
		extract($_POST);
		$data = "";
	
		// If client_id is not set, default to session's client_id
		if (!isset($client_id)) {
			$_POST['client_id'] = $_SESSION['id'];
		}
	
		// Schedule meeting for one week later
		$meeting_schedule = date('Y-m-d H:i:s', strtotime('+1 week'));
		$_POST['meeting_schedule'] = $meeting_schedule;
	
		// Dynamically build the data string for the SQL query
		foreach ($_POST as $k => $v) {
			// Exclude 'id' and 'description' from being included in the data string
			if (!in_array($k, array('id', 'description'))) {
				if (!empty($data)) $data .= ",";
				// Ensure data is properly escaped to prevent SQL injection
				$data .= " `{$k}`='" . $this->conn->real_escape_string($v) . "' ";
			}
		}
	
		// Check if this is an insert or an update operation
		if (empty($id)) {
			// Insert new record into rent_list table
			$sql = "INSERT INTO `rent_list` SET {$data}";
			$save = $this->conn->query($sql);
		} else {
			// Update existing record in rent_list table
			$sql = "UPDATE `rent_list` SET {$data} WHERE id = '{$id}'";
			$save = $this->conn->query($sql);
		}
	
		// Check if the operation was successful
		if ($save) {
			$resp['status'] = 'success';
	
			// Send SMS notification using Twilio
			$message = "Sogod Market Vendor's Leasing and Renewal Management System\nYour Rental Application has been submitted. Please visit our office by $meeting_schedule.";
			$account_id = "ACf135ab5e39c48fcdbb605db4696c768c";
			$auth_token = "b7b2584d341e89e744ea14b5f1ddec8e";
			$client = new Client($account_id, $auth_token);
			$twilio_number = "+12242315707";
			$number = "+63 991 960 9412";
	
			// Send SMS using Twilio
			$client->messages->create($number, [
				'from' => $twilio_number,
				'body' => $message
			]);
	
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		// Return the response as JSON
		return json_encode($resp);
	}
	
	function save_booking() {
		extract($_POST);
		$data = "";
		if (!isset($client_id)) {
			$_POST['client_id'] = $_SESSION['id'];
		}
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'description'))) {
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if (empty($id)) {
			$sql = "INSERT INTO `rent_list` set {$data} ";
			$save = $this->conn->query($sql);
		} else {
			$sql = "UPDATE `rent_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if ($save) {
			$resp['status'] = 'success';
			if (!empty($id)) {
				$this->settings->set_flashdata('success', "Rental Booking successfully updated.");
			} 
				// Decrement the quantity of the bike if the status is Confirmed (1)
				if (isset($status) && $status == 1) {
					$update_sql = "UPDATE `space_list` SET `quantity` = `quantity` - 1 WHERE `id` = '{$space_id}'";
					$update = $this->conn->query($update_sql);
                	$message = "Sogod Market Vendor's Leasing and Renewal Management System\nYour Rental application is Updated to Confirmed";
         
					if (!$update) {
						$resp['status'] = 'failed';
						$resp['err'] = $this->conn->error . "[{$update_sql}]";
						return json_encode($resp);
					}
				} elseif ($status == 0){
					$message = "Sogod Market Vendor's Leasing and Renewal Management System\nYour Rental application is Updated to Pending";
				}elseif ($status == 2){
					$message = "Sogod Market Vendor's Leasing and Renewal Management System\nYour Rental application is Updated to Cancelled";
				}elseif ($status == 3){
					$message = "Sogod Market Vendor's Leasing and Renewal Management System\nYour Rental application is Updated to Done";
				}
				// Sending SMS Notification Using Twilio
				    $account_id = "AC4fafee2b5eecc224a18fe740a9123df2";
               		$auth_token = "e287cd99a0befe9a246bdf57759c8cc7";
                	$client = new Client($account_id, $auth_token);
                	$twilio_number = "+12093754713";
                	$number = "+63 936 064 8398";
                
                	$client->messages->create($number, [
                    'from' => $twilio_number,
                    'body' => $message 	
                ]);
				// end of SMS Notification Using Twilio
			
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_booking(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `rent_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Rental Booking successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	
	
	
	public function verify_code($contact, $entered_code) {
		// Check if the verification code for the contact exists and matches
		if (isset($_SESSION['verification_code'][$contact]) && $_SESSION['verification_code'][$contact] === $entered_code) {
			// Remove the code from the session after successful verification
			unset($_SESSION['verification_code'][$contact]);
			return true;
		}
		return false;
	}
	
	
	public function register() {
		// Start session
		
		extract($_POST);
	
		// Check verification code
		if (!$this->verify_code($contact, $verification_code)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid verification code.";
			return json_encode($resp);
		}
	
		// Sanitize and prepare input data
		$firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
		$lastname = htmlspecialchars(trim($_POST['lastname'] ?? ''));
		$address = htmlspecialchars(trim($_POST['address'] ?? ''));
		$gender = htmlspecialchars(trim($_POST['gender'] ?? ''));
		$contact = htmlspecialchars(trim($_POST['contact'] ?? ''));
		$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
		$password = trim($_POST['password'] ?? '');
	
		// Prepare SQL fields and parameters
		$data = "firstname = ?, lastname = ?, gender = ?, address = ?, contact = ?, email = ?";
		$params = [$firstname, $lastname, $gender, $address, $contact, $email];
	
		// Handle password if provided
		if (!empty($password)) {
			$data .= ", password = ?";
			$params[] = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for secure hashing
		}
	
		if (empty($id)) {
			// Check if email already exists
			$emailCheckStmt = $this->conn->prepare("SELECT id FROM clients WHERE email = ?");
			$emailCheckStmt->bind_param("s", $email);
			$emailCheckStmt->execute();
			$emailCheckStmt->store_result();
	
			if ($emailCheckStmt->num_rows > 0) {
				$resp['status'] = 'failed';
				$resp['msg'] = "Email already taken.";
				return json_encode($resp);
			}
	
			// Insert new record
			$sql = "INSERT INTO clients SET $data";
		} else {
			// Update existing record
			$sql = "UPDATE clients SET $data WHERE id = ?";
			$params[] = $id; // Add ID to parameters for update
		}
	
		// Prepare and execute SQL query
		$stmt = $this->conn->prepare($sql);
		$stmt->bind_param(str_repeat("s", count($params)), ...$params);
	
		if ($stmt->execute()) {
			$resp['status'] = 'success';
			if (empty($id)) {
				$this->settings->set_flashdata('success', "Account successfully created.");
			} else {
				$this->settings->set_flashdata('success', "Account successfully updated.");
			}
	
			// Set session data
			$this->settings->set_userdata('email', $email);
			$this->settings->set_userdata('id', empty($id) ? $this->conn->insert_id : $id);
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = "Error: " . $stmt->error;
		}
	
		return json_encode($resp);
	}
	
	
	
	function rent_avail(){
		extract($_POST);
			$whereand = '';
			if(isset($id) && $id > 0){
			$whereand = " and id !='{$id}'";
		}
		$check = $this->conn->query("SELECT count(id) as count FROM `rent_list` where space_id='{$space_id}' and (('{$ds}' BETWEEN date(date_start) and date(date_end)) OR ('{$de}' BETWEEN date(date_start) and date(date_end))) and status != 2 {$whereand} ")->fetch_array()['count'];

		if($check >= $max_unit){
			$resp['status'] = 'not_available';
			$resp['msg'] = 'No Unit Available on selected dates.';
		}else{
			$resp['status'] = 'success';
		}
		return json_encode($resp);
	}
	function update_booking_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `rent_list` set status = '{$status}' where id='{$id}'");
		if($update){
			$resp['status']='success';
		}else{
			$resp['status']='failed';
			$resp['error']=$this->conn->error;
		}
		return json_encode($resp);
	}
	function mark_booking_as_viewed(){
		extract($_POST);
		$update = $this->conn->query(query: "UPDATE `rent_list` 
          SET is_viewed = 1 
          WHERE status = 0 
          AND is_viewed = 0 ");
		if($update){
			$resp['status']='success';
		}else{
			$resp['status']='failed';
			$resp['error']=$this->conn->error;
		}
		return json_encode($resp);
	}
	function fetch_booking_count() {
		global $conn;
		$query = $conn->query("SELECT COUNT(*) as count 
							   FROM `rent_list` 
							   WHERE status = 0 
							   AND is_viewed = 0");
		$bookingCount = $query->fetch_assoc()['count'];
		return $bookingCount;
	}
	public function add_document() {
		// Set the response Content-Type header to JSON
		header('Content-Type: application/json');
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Check if the user is logged in
			if (!isset($_SESSION['id'])) {
				echo json_encode(["success" => false, "message" => "User not logged in"]);
				exit;
			}
	
			// Check if the required files are provided
			if (isset($_FILES['cedule']) && isset($_FILES['photo_id'])) {
				// Handle file uploads
				$ceduleFile = $_FILES['cedule'];
				$photoIdFile = $_FILES['photo_id'];
	
				// Validate files
				if ($ceduleFile['error'] !== UPLOAD_ERR_OK || $photoIdFile['error'] !== UPLOAD_ERR_OK) {
					echo json_encode(["success" => false, "message" => "Error in file upload"]);
					exit;
				}
	
				// Validate file types (ensure only certain file types are allowed)
				$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
				if (!in_array($ceduleFile['type'], $allowedTypes) || !in_array($photoIdFile['type'], $allowedTypes)) {
					echo json_encode(["success" => false, "message" => "Invalid file type. Only JPG, PNG, and PDF are allowed."]);
					exit;
				}
	
				// Define the upload directory
				$uploadDir = "../uploads/documents/";
	
				// Create the directory if it doesn't exist
				if (!file_exists($uploadDir)) {
					if (!mkdir($uploadDir, 0777, true)) {
						echo json_encode(["success" => false, "message" => "Failed to create upload directory"]);
						exit;
					}
				}
	
				// Sanitize file names
				$ceduleFileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($ceduleFile["name"]));
				$photoIdFileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($photoIdFile["name"]));
	
				// Define the full file paths
				$ceduleFilePath = $uploadDir . $ceduleFileName;
				$photoIdFilePath = $uploadDir . $photoIdFileName;
	
				// Move the uploaded files to the target directory
				if (move_uploaded_file($ceduleFile["tmp_name"], $ceduleFilePath) && move_uploaded_file($photoIdFile["tmp_name"], $photoIdFilePath)) {
					// Optionally, save additional information like document description
					$description = isset($_POST['document_description']) ? $_POST['document_description'] : '';
	
					// Check if the client_id already exists in the documents table
					$stmt = $this->conn->prepare("SELECT id FROM documents WHERE client_id = ?");
					$stmt->bind_param("s", $_SESSION['id']);
					$stmt->execute();
					$stmt->store_result();
	
					if ($stmt->num_rows > 0) {
						// Update the existing record
						$stmt->close();
						$stmt = $this->conn->prepare("UPDATE documents SET cedule_file = ?, photo_id_file = ?, description = ? WHERE client_id = ?");
						$stmt->bind_param("ssss", $ceduleFileName, $photoIdFileName, $description, $_SESSION['id']);
					} else {
						// Insert a new record
						$stmt->close();
						$stmt = $this->conn->prepare("INSERT INTO documents (client_id, cedule_file, photo_id_file, description) VALUES (?, ?, ?, ?)");
						$stmt->bind_param("ssss", $_SESSION['id'], $ceduleFileName, $photoIdFileName, $description);
					}
	
					if ($stmt->execute()) {
						$stmt->close();
						echo json_encode(["success" => true, "message" => "Document processed successfully!"]);
					} else {
						echo json_encode(["success" => false, "message" => "Failed to process document info"]);
					}
				} else {
					echo json_encode(["success" => false, "message" => "Failed to move uploaded files"]);
				}
			} else {
				echo json_encode(["success" => false, "message" => "Required files are missing"]);
			}
		} else {
			echo json_encode(["success" => false, "message" => "Invalid request method"]);
		}
	}


	function save_payment() {
		extract($_POST);  // Extract POST data
		$data = "";  // Initialize the data string
	
		// Loop through POST data and build the data string for insertion or update
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'description'))) {  // Exclude unnecessary fields
				if (!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";  // Build the data string for SQL
			}
		}
	
		// INSERT operation
		$sql = "INSERT INTO `payments` set {$data} ";
		$save = $this->conn->query($sql);
	
		// Check if the save operation was successful
		if ($save) {
			$resp['status'] = 'success';
	
			if (!empty($id)) {
				$this->settings->set_flashdata('success', "Payment details successfully updated.");
			}
	
			// Update payment status and rent_list if payment is confirmed
			if (isset($status) && $status == 1) {
				// Update rent_list with payment details
				$update_sql = "UPDATE `rent_list` SET `payment_status` = 'Paid' WHERE `id` = '{$booking_id}'";
				$update = $this->conn->query($update_sql);
	
				if (!$update) {
					$resp['status'] = 'failed';
					$resp['err'] = $this->conn->error . "[{$update_sql}]";
					return json_encode($resp);
				}
	
				$message = "Your payment has been successfully processed for booking #{$booking_id}.";
			} else {
				$message = "Your payment status has been updated.";
			}
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		return json_encode($resp);
	}
	
	
	
	}



$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_brand':
		echo $Master->save_brand();
	break;
	case 'delete_brand':
		echo $Master->delete_brand();
	break;
	case 'save_bike':
		echo $Master->save_bike();
	break;
	case 'delete_client':
		echo $Master->delete_client();
	break;
	case 'delete_bike':
		echo $Master->delete_bike();
	break;
	
	case 'save_booking':
		echo $Master->save_booking();
	break;
	case 'save_bookingspart2':
		echo $Master->save_bookingspart2();
	break;
	case 'delete_booking':
		echo $Master->delete_booking();
	break;
	case 'register':
		echo $Master->register();
	break;
	case 'rent_avail':
		echo $Master->rent_avail();
	break;
	case 'update_booking_status':
		echo $Master->update_booking_status();
	break;
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'mark_booking_as_viewed':
		echo $Master->mark_booking_as_viewed();
	break;
	case 'fetch_booking_count':
		echo $Master->fetch_booking_count();
	break;
	case 'add_document':
		echo $Master->add_document();
	break;
	case 'save_payment':
		echo $Master->save_payment();
	break;
	case 'delete_vendor':
		echo $Master->delete_vendor();
	break;
	default:
		// echo $sysset->index();
		break;
}