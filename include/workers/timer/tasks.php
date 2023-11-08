<?php
require_once __DIR__ . "/../../../dash-loader.php";
defined('ROOT_PATH') || exit;
lock($pdo);

use Dash\Encryption;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Dash\DashAuth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$tasks = new \Dash\Tasks($pdo);

$log = new Logger('User Registration');
$log->pushHandler(new StreamHandler(ROOT_PATH.'logs/register.log', Logger::INFO));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $taskId = 0;
        $filters = array(
            'task_id' => 'trim|sanitize_string',
            'task_name' => 'trim|sanitize_string',
            'description' => 'trim|sanitize_string',
            'task_status' => 'trim|sanitize_string',
            'action' => 'trim|sanitize_string',
            'created_by_user_id' => 'trim|sanitize_email|lower_case',

        );
        $rules = array(
            'task_id' => 'alpha_numeric|max_len,20',
            'task_name' => 'alpha_numeric|max_len,20',
            'description' => 'alpha_numeric|max_len,20',
            'task_status' => 'alpha_numeric|max_len,20',
            'created_by_user_id' => 'alpha_numeric|max_len,20|min_len,1',
            'action' => 'alpha_numeric|max_len,20|min_len,1',
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

        if($_POST['action'] === "add-task") {
            $taskId = $tasks->save($_POST);
        }

        if($_POST['action'] === "get-task") {
            $task = $tasks->getById($_POST['task_id']);

            $values = array(
                'task' => $task
            );
            $template = "timer_task.twig";
            $task = $twig->render($template, $values);
        }


        $responseArray = array('type' => 'success', 'message' => 'Message Saved Successfully', 'task' => $task);
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
