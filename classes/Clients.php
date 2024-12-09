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
    default:
        echo json_encode(['status' => 'failed', 'msg' => 'Invalid action']);
        break;
}
?>
