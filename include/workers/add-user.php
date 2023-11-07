<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
lock($pdo);

use Dash\Encryption;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Dash\DashAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
$dashAuth = new DashAuth($pdo);
$encrypt = new Encryption();

$log = new Logger('User Registration');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/register.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'first_name' => 'trim|sanitize_string',
            'last_name' => 'trim|sanitize_string',
            'phone' => 'trim|sanitize_string',
            'email'       => 'trim|sanitize_email|lower_case',
            'password'    => 'trim|sanitize_string',
            'repeat_password' => 'trim|sanitize_string',
            'user_group' => 'trim|sanitize_string|lower_case'
        );
        $rules = array(
            'first_name' => 'required|alpha_numeric|max_len,20',
            'last_name' => 'required|alpha_numeric|max_len,20',
            'phone' => 'required|alpha_numeric|max_len,20',
            'email'       => 'required|alpha_numeric|max_len,20|min_len,3',
            'password'    => 'required|max_len,100|min_len,8',
            'repeat_password' => 'equalsfield,password',
            'user_group' => 'required',
            'nonce' => 'required'
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'add-user-form');
        if ($nonceTest === false) {
            throw new \Exception('Nonce Test Failed - please refresh the page to submit again');
        }


        if (!isset($_POST['email'])) {
            throw new Exception('Please check the email address');
        }

        $email = $_POST['email'];
        $emailTest = $auth->isEmailTaken($email);
        if ($emailTest === true) {
            throw new Exception('This email address is already taken');
        }
        $password = $_POST['password'];
        $passwordRepeat = $_POST['repeat_password'];


        $params = array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'phone' => $_POST['phone'],
            'user_group' => $_POST['user_group']
        );

        $result = $auth->register(
            $email,
            $password,
            $passwordRepeat,
            $params,
            $captcha_response = false,
            $use_email_activation = false
        );
        if ($result['error'] === true) {
            throw new Exception($result['message']);
        }

        $dashAuth->registrationMailer($email, $password);

        $responseArray = array('type' => 'success', 'message' => $result['message']);
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
