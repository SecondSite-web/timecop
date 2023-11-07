<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
lock($pdo);

use Dash\DashSettings;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$setting = new DashSettings($pdo);

$log = new Logger('Change Settings');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/settings.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'siteName'      => 'trim|sanitize_string',
            'siteUrl'       => 'trim|sanitize_string',
            'allowRegister' => 'trim|sanitize_string',
            'apiDomain'     => 'trim|sanitize_string',
            'debug'         => 'trim|sanitize_string',
            'smtpDebug'     => 'trim|sanitize_string',
            'smtpEmail'     => 'trim|sanitize_email',
            'smtpHost'      => 'trim|sanitize_string|lower_case',
            'smtpPass'      => 'trim|sanitize_string',
            'smtpPort'      => 'trim|sanitize_string|lower_case'
        );
        $rules = array(
            'siteName'      => 'required|alpha_numeric|min_len,1|max_len,30',
            'siteUrl'       => 'required|alpha_numeric|min_len,1|max_len,45',
            'allowRegister' => 'required|alpha_numeric|max_len,20',
            'apiDomain'     => 'alpha_numeric|max_len,20',
            'debug'         => 'required|alpha_numeric|max_len,20',
            'smtpDebug'     => 'required|alpha_numeric|max_len,20|min_len,1',
            'smtpEmail'     => 'required|max_len,30',
            'smtpHost'      => 'required|alpha_numeric|min_len,1|max_len,30',
            'smtpPass'      => 'required|max_len,30',
            'smtpPort'      => 'required|alpha_numeric|min_len,1|max_len,6',
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
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'settings-form');
        if ($nonceTest === false) {
            throw new \Exception('Nonce Test Failed - please refresh the page to submit again');
        }

        foreach ($_POST as $key => $value) {
            $setting->__set($key, $value);
        }

        $responseArray = array('type' => 'success', 'message' => 'Settings Saved');
        $log->info('Successful', array($_POST));
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
