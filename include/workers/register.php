<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);
$log = new Logger('User Registration');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/register.log', Logger::INFO));
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'first_name'   => 'trim|sanitize_string|lower_case',
            'last_name'    => 'trim|sanitize_string|lower_case',
            'email'       => 'trim|sanitize_email|lower_case',
            'phone'       => 'trim|sanitize_string|lower_case',
            'password'    => 'trim|sanitize_string',
            'confirm_password' => 'trim|sanitize_string'
        );
        $rules = array(
            'first_name'   => 'required|alpha_numeric|max_len,100|min_len,3',
            'last_name'    => 'required|alpha_numeric|max_len,100|min_len,3',
            'email'       => 'required|alpha_numeric|max_len,100|min_len,3',
            'phone'       => 'required|valid_email',
            'password'    => 'required|max_len,100|min_len,8',
            'confirm_password' => 'equalsfield,password',
            'nonce'         => 'required'
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'registration-form');
        if ($nonceTest === false) {
            throw new Exception('Nonce Test Failed, please refresh and try again');
        }

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $repeatpassword = $_POST['confirm_password'];
        $userrole = strtolower('guest');
        $usergroup = strtolower('guest');

        $params = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_group' => $usergroup,
            'phone' => $phone
        );

        $result = $auth->register(
            $email,
            $password,
            $repeatpassword,
            $params,
            $captcha_response = null,
            $use_email_activation = true
        );
        if ($result['error'] === true) {
            throw new \Exception($result['message']);
        }
        $responseArray = array('type' => 'success', 'message' => $result['message']);
        $log->info(
            $result['message'],
            array(
                'Email',
                $_POST['email']." | User: ".$_POST['first_name']." ".$_POST['last_name']
            )
        );
    } catch (\Exception $e) {
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
        $log->info(
            $e->getMessage(),
            array(
                'Email',
                $_POST['email']." | User: ".$_POST['first_name']." ".$_POST['last_name']
            )
        );
    }

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
