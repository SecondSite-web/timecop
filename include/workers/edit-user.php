<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
lock($pdo);

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Dash\DashAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
$dashAuth = new DashAuth($pdo);

$log = new Logger('User edit');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/users.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'user_id'    => 'trim|sanitize_string',
            'session_user_id'    => 'trim|sanitize_string',
            'firstname'  => 'trim|sanitize_string',
            'lastname'   => 'trim|sanitize_string',
            'phone'      => 'trim|sanitize_string',
            'email'      => 'trim|sanitize_email|lower_case',
            'company_id' => 'trim|sanitize_string',
            'user_group' => 'trim|sanitize_string',
        );
        $rules = array(
            'user_id'    => 'required|alpha_numeric|min_len,1|max_len,3',
            'session_user_id'    => 'required|alpha_numeric|min_len,1|max_len,3',
            'firstname'  => 'required|alpha_numeric|max_len,20',
            'lastname'   => 'required|alpha_numeric|max_len,20',
            'phone'      => 'required|alpha_numeric|max_len,20',
            'email'      => 'required|alpha_numeric|max_len,20|min_len,3',
            'company_id'    => 'required|alpha_numeric|max_len,20',
            'user_group' => 'required',
            'nonce'      => 'required'
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);

        if ($validated === false) {
            throw new \Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'edit-user-form');
        if ($nonceTest === false) {
            throw new \Exception('Nonce Test Failed - please refresh the page to submit again');
        }

        if (!isset($_POST['email'])) {
            throw new Exception('Please check the email address');
        }

        $sessionUser = $_POST['session_user_id'];
        $email = $_POST['email'];
        $uid = $_POST['user_id'];
        $params = array(
            'firstname' => $_POST['firstname'],
            'lastname' => $_POST['lastname'],
            'phone' => $_POST['phone'],
            'company_id' => $_POST['company_id'],
            'user_group' => $_POST['user_group']
        );

        $originEmail = $dashAuth->getEmail($uid);

        if ($originEmail !== $email) {
            $emailTest = $auth->isEmailTaken($email);
            if ($emailTest === true) {
                throw new Exception('This email address is already in use');
            }
            $emailBanned = $auth->isEmailBanned($email);
            if ($emailBanned === true) {
                throw new Exception('This email address has been banned, Please contact an admin');
            }
            $emailUpdated = $dashAuth->changeUserEmail($uid, $email);
            if ($emailUpdated === false) {
                throw new Exception('Failed to update email address');
            }
        }

        $result = $auth->updateUser($uid, $params);

        if ($result['error'] === '1') {
            throw new Exception($result['message']);
        }

        $dashAuth->clearUser($uid);
        if (is_array($_POST['user_group'])) {
            $postGroups = $_POST['user_group'];
            foreach ($postGroups as $group) {
                $dashAuth->saveUserGroup($uid, $group);
            }
        } else {
            $dashAuth->saveUserGroup($uid, $_POST['group_index']);
        }

        $responseArray = array('type' => 'success', 'message' => 'User details updated successfully');
        $log->info($result['message'], array($_POST));
    } catch (Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error($e->getMessage(), array($_POST));
    }

    // if requested by AJAX request return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        $encoded = json_encode($responseArray);
        header('Content-Type: application/json');
        echo $encoded;
    } else {
        echo $responseArray['message'];
    }
} else {
    header("Location: /");
}
