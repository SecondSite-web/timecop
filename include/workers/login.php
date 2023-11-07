<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dash\DashAuth;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
$dashAuth = new DashAuth($pdo);
$log = new Logger('User Login');
$log->pushHandler(new StreamHandler(ROOT_PATH . 'logs/login.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'email'      => 'trim|sanitize_email|lower_case',
            'password'   => 'trim|sanitize_string'
        );
        $rules = array(
            'email'         => 'required|valid_email',
            'password'      => 'required|max_len,100|min_len,8',
            'nonce'         => 'required'
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        if ($validated === false) {
            $result['message'] = 'Validation has failed, please try again';
            throw new \Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'login-form');
        if ($nonceTest === false) {
            throw new \Exception('Nonce Test Failed, please refresh and try again');
        }
        $email = $_POST['email'];
        $password = $_POST['password'];
        $status = $dashAuth->getUserStatus($email);
        if ($status === 0) {
            throw new \Exception('User Account is not active - Please contact an Admin');
        }
        if ($status === 2) {
            throw new \Exception('User Account has been deleted - Please contact an Admin');
        }
        $result = $auth->login($email, $password, $remember = true, $captcha_response = '');

        if ($result['error'] === true) {
            throw new \Exception($result['message']);
        }

        setcookie('phpauth_session_cookie', $result["hash"], $result["expire"], '/');

        $log->info(
            'User Login Success',
            array(
            'Email Address',
            $_POST['email']." ". $result["hash"])
        );

        $responseArray = array('type' => 'success', 'message' => $result['message']);
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->error(
            'User Login Failed',
            array('Email Address',  $_POST['email']." ". $e->getMessage())
        );
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $encoded = json_encode($responseArray);
        header('Content-Type: application/json');
        echo $encoded;
    }
} else {
    header("Location: ".SITE_URL."/dashboard/");
}
