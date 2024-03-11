<?php
require_once __DIR__ . "/../../../dash-loader.php";
defined('ROOT_PATH') || exit;
// lock($pdo);

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
        $task = [];
        $filters = array(
            'task_id' => 'trim|sanitize_string',
            'project_id' => 'trim|sanitize_string',
            'task_name' => 'trim|sanitize_string',
            'project_name' => 'trim|sanitize_string',
            'description' => 'trim|sanitize_string',
            'task_status' => 'trim|sanitize_string',
            'action' => 'trim|sanitize_string',
            'created_by_user_id' => 'trim|sanitize_email|lower_case',
            'updated_by_user_id' => 'trim|sanitize_email|lower_case',
            'user_id' => 'trim|sanitize_email|lower_case',
            'session_id' => 'trim|sanitize_email|lower_case',

        );
        $rules = array(
            'task_id' => 'alpha_numeric|max_len,20',
            'project_id' => 'alpha_numeric|max_len,20',
            'task_name' => 'alpha_numeric|max_len,20',
            'project_name' => 'alpha_numeric|max_len,20',
            'description' => 'alpha_numeric|max_len,20',
            'task_status' => 'alpha_numeric|max_len,20',
            'created_by_user_id' => 'alpha_numeric|max_len,20|min_len,1',
            'updated_by_user_id' => 'alpha_numeric|max_len,20|min_len,1',
            'user_id' => 'alpha_numeric|max_len,20|min_len,1',
            'session_id' => 'alpha_numeric|max_len,20|min_len,1',
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
        // $nonceUtil = $dashNonce->nonceInit();
        // $nonceTest = $dashNonce->verifyNonce($_POST['nonce'], 'add-user-form');

        $taskDetails = [];
        if($_POST['action'] === "add-task") {
            $task = $tasks->save($_POST);
        }
        if($_POST['action'] === "add-project") {
            $taskId = $tasks->addProject($_POST);
        }

        if($_POST['action'] === "update-task") {
            $task = $tasks->update($_POST);
        }
        if($_POST['action'] === "update-project") {
            $task = $tasks->updateProject($_POST);
        }
        if($_POST['action'] === "delete-session") {
            $task = $tasks->deleteSession($_POST['session_id']);
        }

        if($_POST['action'] === "delete-task") {
            $task = $tasks->deleteTask($_POST['task_id']);
        }

        if($_POST['action'] === "get-project") {
            $projectDetails = $tasks->getProjectById($_POST['project_id']);
            $taskDetails = $tasks->getProject($_POST['project_id']);
            $values = array(
                'project' => $projectDetails
            );
            $template = "timer_project.twig";
            $task = $twig->render($template, $values);
        }

        if($_POST['action'] === "get-task-list") {
            $values = array(
                'tasks' => $tasks->getByStatus('open', $_POST['user_id'], $_POST['project_id'])
            );
            $template = "timer_tasklist.twig";
            $task = $twig->render($template, $values);
        }

        if($_POST['action'] === "get-task") {
            $taskDetails = $tasks->getById($_POST['task_id']);

            $values = array(
                'task' => $taskDetails
            );
            $template = "timer_task.twig";
            $task = $twig->render($template, $values);
        }

        if($_POST['action'] === "start-session") {
            $task = $tasks->start($_POST['task_id'], $_POST['user_id'], $_POST['project_id']);
        }
        if($_POST['action'] === "stop-session") {
            $task = $tasks->stop($_POST['session_id'], $_POST['user_id']);
        }


        $responseArray = array('type' => 'success', 'message' => 'Message Saved Successfully', 'task' => $task, 'details' => $taskDetails);
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
