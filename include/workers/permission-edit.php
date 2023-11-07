<?php
require_once __DIR__ . "/../../dash-loader.php";
defined('ROOT_PATH') || exit;
lock($pdo);

use Dash\Permissions;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$permissions = new Permissions($pdo);

// create a log channel
$log = new Logger('Permissions');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/permissions.log', Logger::INFO));


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $filters = array(
            'session_user_id'   => 'trim|sanitize_string',
            'permission_title'  => 'trim|sanitize_string',
            'user_group'        => 'trim|sanitize_string'
        );
        $rules = array(
            'session_user_id'   => 'required|alpha_numeric|min_len,1|max_len,3',
            'permission_title'  => 'required|alpha_numeric|min_len,1|max_len,3',
            'user_group'        => 'required|alpha_numeric|min_len,1|max_len,3',
            'nonce'             => 'required',
        );
        $validator = new GUMP();
        $whitelist = array_keys($rules);
        $_POST = $validator->sanitize($_POST, $whitelist);
        $_POST = $validator->filter($_POST, $filters);
        $validated = $validator->validate($_POST, $rules);
        // $_POST = $validator->run($_POST);
        if ($validated === false) {
            throw new \Exception($validator->get_readable_errors(true));
        }
        $nonceUtil = $dashNonce->nonceInit();
        $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'permissions-add');
        if ($nonceTest === false) {
            throw new Exception('Nonce Test Failed, please refresh and try again');
        }


        $sessionUser = $_POST['session_user_id'];
        $permissionTitle = $_POST['permission_title'];

        $permissions->clearUser($permissionTitle);
        if (is_array($_POST['user_group'])) {
            $postGroups = $_POST['user_group'];
            foreach ($postGroups as $group) {
                $permissions->saveMeta($permissionTitle, $group);
            }
        } else {
            $permissions->saveMeta($permissionTitle, $_POST['user_group']);
        }

        $responseArray = array('type' => 'success', 'message' => 'Permissions modified Successfully');
        $log->info('Successful', array($_POST));

    } catch (Exception $e) {
        $log->error('Failed', array($_POST));
        $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
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
