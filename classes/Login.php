<?php
require_once '../config.php';
require __DIR__ . "/../admin/vendor/autoload.php";
use Twilio\Rest\Client;
class Login extends DBConnection {
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
        ini_set('display_error', 1);
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Start session here
        }
    }

    public function __destruct(){
        parent::__destruct();
    }

    public function index(){
        echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
    }

    // Login method
    public function login(){
        extract($_POST);
        $qry = $this->conn->query("SELECT * FROM users WHERE username = '$username' AND password = md5('$password')");

        if($qry->num_rows > 0){
            foreach($qry->fetch_array() as $k => $v){
                if(!is_numeric($k) && $k != 'password'){
                    $this->settings->set_userdata($k,$v);
                }
            }
            $this->settings->set_userdata('login_type', 1);
            return json_encode(array('status' => 'success'));
        } else {
            return json_encode(array('status' => 'incorrect', 'last_qry' => "SELECT * FROM users WHERE username = '$username' AND password = md5('$password')"));
        }
    }

    // User login method
    public function login_user() {
        extract($_POST);

        // Start the session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Query the database for user credentials
       // Query the database for user credentials
$qry = $this->conn->query("SELECT * FROM clients WHERE email = '$email'");

if ($qry->num_rows > 0) {
    // Fetch user data
    $user = $qry->fetch_array();

    // Verify password using password_verify
    if (password_verify($password, $user['password'])) {
        // Password is correct, proceed with login
        $name = $user['firstname'] . ' ' . $user['lastname'];

        // Set session variables
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $user['email'];
        $_SESSION['contact'] = $user['contact'];
        $_SESSION['address'] = $user['address'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['generated_code'] = $user['generated_code'];
        $_SESSION['gender'] = $user['contact'];
        $_SESSION['login_type'] = 1;

        // Generate OTP and store it in the session (using a 6-digit random number)
        $_SESSION['otp'] = rand(100000, 999999);

        // Sending SMS Notification Using Twilio
        $account_id = "ACf135ab5e39c48fcdbb605db4696c768c";
        $auth_token = "313d5b268ae30613b3033b4340a53ff3";
        $client = new Client($account_id, $auth_token);
        $twilio_number = "+12242315707";
        $number = "+63 991 960 9412";
     
        $client->messages->create($number, [
            'from' => $twilio_number,
            'body' => 'Your OTP is: ' . $_SESSION['otp']
        ]);

        // Return success response
        $resp['status'] = 'success';
    } else {
        // Incorrect password
        $resp['status'] = 'incorrect';
    }
} else {
    // User does not exist
    $resp['status'] = 'incorrect';
}

// Check for any SQL errors
if ($this->conn->error) {
    $resp['status'] = 'failed';
    $resp['_error'] = $this->conn->error;
}

return json_encode($resp);

    }

    // Verify OTP method
    public function verify_otp() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Ensure the session is started
        }
 
            if (isset($_SESSION['otp']) && $_SESSION['otp'] == $_POST['otp']) {
                unset($_SESSION['otp']); // Clear OTP from session after successful verification
                $resp['status'] = 'verified'; // OTP matched, proceed with success response
            } else {
                // OTP does not match
                $resp['status'] = 'incorrect';
                $resp['error_message'] = 'OTP does not match.';
            }
        return json_encode($resp);
    }
    public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
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
    case 'verify_otp':
        echo $auth->verify_otp();
        break;
    case 'logout':
        echo $auth->logout();
        break;
    default:
        echo $auth->index();
        break;
}
