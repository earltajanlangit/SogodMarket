<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);

		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}

			}
			$this->settings->set_userdata('login_type',1);
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') "));
		}
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}
	function login_user() {
		extract($_POST);
	
		// Start the session if not already started
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	
		// Query the database for user credentials
		$qry = $this->conn->query("SELECT * FROM clients WHERE email = '$email' AND password = MD5('$password')");
	
		if ($qry->num_rows > 0) {
			// Fetch user data
			$user = $qry->fetch_array();
	
			// Concatenate firstname and lastname
			$name = $user['firstname'] . ' ' . $user['lastname'];
	
			// Set session variables
			$_SESSION['id'] = $user['id'];
			$_SESSION['name'] = $name; // Combined name
			$_SESSION['email'] = $user['email'];
			$_SESSION['contact'] = $user['contact'];
			$_SESSION['address'] = $user['address'];
			$_SESSION['firstname'] = $user['firstname'];
			$_SESSION['lastname'] = $user['lastname'];
			$_SESSION['generated_code'] = $user['generated_code'];
			$_SESSION['gender'] = $user['contact'];
			$_SESSION['login_type'] = 1; // Set the login type
	
			// Return success response
			$resp['status'] = 'success';
		} else {
			// Incorrect credentials
			$resp['status'] = 'incorrect';
		}
	
		// Check for any SQL errors
		if ($this->conn->error) {
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}
	
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	default:
		echo $auth->index();
		break;
}

