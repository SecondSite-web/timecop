<?php


namespace Dash;

use DateTime;
use PDO;
use PDOException;

class Tasks
{
    private object $pdo;
    private string $taskTable = 'timecop_tasks';
    private string $sessionsTable = 'timecop_sessions';
    private string $projectsTable = 'timecop_projects';
    private array $statuses = ['open', 'closed', 'inactive'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->taskTable}'")->fetchAll()) {
            $this->createTable();
            $this->createSessionsTable();
            $this->createProjectsTable();
        }
        date_default_timezone_set('Africa/Johannesburg');
    }

    /**
     * Returns Task Statuses as an array
     * @return array
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * Get All records from the database
     * @return mixed
     */
    public function getAll(): mixed
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table}");
        $stmt->execute();
        $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectsByStatus($status, $userId): array
    {
        $table = $this->projectsTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE status=:status AND created_by_user_id=:created_by_user_id");
        $stmt->execute([
            'status' => $status,
            'created_by_user_id' => $userId
        ]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($list as &$item) {
            $item['sessions'] = $this->getProjectSessions($item['project_id']);
            $totalTime = $this->calculateTotalTime($item['sessions']);
            $item['total_hours'] = $totalTime['total_hours'];
            $item['total_minutes'] = $totalTime['total_minutes'];
        }
        return $list;
    }

    /**
     * Fetch All records by Status
     * @param $status
     * @param $userId
     * @param $projectId
     * @return array
     * @throws \Exception
     */
    public function getByStatus($status, $userId, $projectId): array
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_status=:task_status
                      AND created_by_user_id=:created_by_user_id
                      AND project_id=:project_id");
        $stmt->execute([
            'task_status' => $status,
            'created_by_user_id' => $userId,
            'project_id' => $projectId
        ]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($list as &$item) {
            $item['sessions'] = $this->getTaskSessions($item['task_id']);
            $totalTime = $this->calculateTotalTime($item['sessions']);
            $item['total_hours'] = $totalTime['total_hours'];
            $item['total_minutes'] = $totalTime['total_minutes'];
        }
        return $list;
    }



    /**
     * Get detailed task list of all tasks in a project
     * @param int $projectId
     * @return mixed
     * @throws \Exception
     */
    public function getTasksByProjectId(int $projectId): mixed
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE project_id=:project_id");
        $stmt->execute(['project_id' => $projectId]);
        $taskList = $stmt->fetch(PDO::FETCH_ASSOC);
        if($taskList) {
            $results = [];
            foreach ($taskList as $item) {
                $task = $this->getById($item['task_id']);
                $results[]= $task;
            }
            return $results;
        } else {
            return false;
        }
    }
    public function getProjectById(int $projectId): mixed
    {
        $table = $this->projectsTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE project_id=:project_id");
        $stmt->execute(['project_id' => $projectId]);
        $list = $stmt->fetch(PDO::FETCH_ASSOC);
        $list['sessions'] = $this->getProjectSessions($projectId);
        $openSessionId = 0;
        if (!empty($list['sessions'])) {
            foreach ($list['sessions'] as $item) {
                if ($item['session_status'] === 'open') {
                    $openSessionId = $item['session_id'];
                    break;
                }
            }
        }
        $list['open_session'] = $openSessionId;
        $totalTime = $this->calculateTotalTime($list['sessions']);
        $list['total_hours'] = $totalTime['total_hours'];
        $list['total_minutes'] = $totalTime['total_minutes'];
        return $list;
    }

    public function getTaskById($taskId) {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_id=:task_id");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProject($projectId) {
        $table = $this->projectsTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE project_id=:project_id");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Gets a Task record based on Id
     * @param int $taskId
     * @return mixed
     * @throws \Exception
     */
    public function getById(int $taskId): mixed
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_id=:task_id");
        $stmt->execute(['task_id' => $taskId]);
        $list = $stmt->fetch(PDO::FETCH_ASSOC);
        $taskName = $list['task_name'];
        $list['sessions'] = $this->getTaskSessions($taskId);
        $openSessionId = 0;
        if (!empty($list['sessions'])) {
            foreach ($list['sessions'] as $item) {
                if ($item['session_status'] === 'open') {
                    $openSessionId = $item['session_id'];
                    break;
                }
            }
        }
        $list['open_session'] = $openSessionId;
        $totalTime = $this->calculateTotalTime($list['sessions']);
        $list['total_hours'] = $totalTime['total_hours'];
        $list['total_minutes'] = $totalTime['total_minutes'];
        return $list;
    }

    /**
     * Gets all sessions for a task and compiles them with duration
     * @param $taskId
     * @return array
     * @throws \Exception
     */
    public function getTaskSessions($taskId): array
    {
        $table = $this->sessionsTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_id=:task_id");
        $stmt->execute(['task_id' => $taskId]);
        $sessionList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->updateSessionList($sessionList);
    }

    /**
     * Get all sessions for a project with time calculations
     * @param $projectId
     * @return array
     */
    public function getProjectSessions($projectId): array
    {
        $table = $this->sessionsTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE project_id=:project_id");
        $stmt->execute(['project_id' => $projectId]);
        $sessionList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->updateSessionList($sessionList);
    }

    public function updateSessionList($sessionList): array
    {
        $updatedSessionList = []; // Initialize an array to store updated sessions
        if($sessionList) {
            foreach ($sessionList as $item) {
                $start_datetime = new DateTime($item['created_at']);
                $end_datetime = new DateTime($item['updated_at']);
                $interval = $start_datetime->diff($end_datetime);

                $hours = $interval->h;
                $minutes = $interval->i;

                $item['hours'] = $hours; // Add hours to the session
                $item['minutes'] = $minutes; // Add minutes to the session

                // Append the updated session to the new array
                $updatedSessionList[] = $item;
            }
        }
        return $updatedSessionList;
    }

    /**
     * Calculate total time for a session list
     * @param $sessionList
     * @return array
     */
    public function calculateTotalTime($sessionList): array
    {
        $totalHours = 0;
        $totalMinutes = 0;
        if ($sessionList) {
            foreach ($sessionList as $session) {
                $totalHours += $session['hours'];
                $totalMinutes += $session['minutes'];

                // If minutes exceed 60, add to hours and adjust minutes
                if ($totalMinutes >= 60) {
                    $totalHours += intdiv($totalMinutes, 60);
                    $totalMinutes = $totalMinutes % 60;
                }
            }
        }


        return [
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutes
        ];
    }


    /**
     * Returns an unfiltered count of records from the database
     * @return mixed
     */
    public function count(): mixed
    {
        $table = $this->taskTable;
        $sql = "SELECT count(*) FROM {$table}";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result->fetchColumn();
    }

    /**
     * Delete a record from the database
     * @param int $taskId
     * @return bool
     */
    public function deleteTask(int $taskId): bool
    {
        $result1 = $this->deleteTaskSessions($taskId);
        $result2 = $this->delete($taskId);
        if($result1 && $result2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function deleteTaskSessions(int $taskId): bool
    {
        $table = $this->sessionsTable;
        $sql = "DELETE FROM {$table} WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $taskId
        ]);
    }

    public function delete(int $taskId): bool
    {
        $table = $this->taskTable;
        $sql = "DELETE FROM {$table} WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $taskId
        ]);
    }

    /**
     * Update a database record with timestamp
     * @param array $values
     * @return int
     */
    public function update(array $values): int
    {
        $table = $this->taskTable;
        $sql = "UPDATE {$table} SET 
                 task_name=?,
                 description=?,
                 task_status=?,
                 updated_by_user_id=?
                WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        $result = $stmt->execute([
            $values['task_name'],
            $values['description'],
            $values['task_status'],
            $values['updated_by_user_id'],
            $values['task_id']
        ]);
        if($result) {
            return $values['task_id'];
        }
    }

    /**
     * Update a project
     * @param array $values
     * @return bool
     */
    public function updateProject(array $values): bool
    {
        $table = $this->projectsTable;
        $sql = "UPDATE {$table} SET 
                 project_name=?,
                 description=?,
                 updated_by_user_id=?
                WHERE project_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $values['project_name'],
            $values['description'],
            $values['updated_by_user_id'],
            $values['project_id']
        ]);
    }


    /**
     * @param array $fields
     * @return string|int
     */
    public function save(array $fields): string|int
    {
        $table = $this->taskTable;
        $status = 'open';
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} 
                (
                 task_name,
                 project_id,
                 description, 
                 task_status, 
                 created_by_user_id
                 )
                VALUES (
                        :task_name,
                        :project_id,
                        :description, 
                        :task_status,
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':task_name', $fields['task_name']);
            $stmt->bindParam(':project_id', $fields['project_id']);
            $stmt->bindParam(':description', $fields['description']);
            $stmt->bindParam(':task_status', $status);
            $stmt->bindParam(':created_by_user_id', $fields['created_by_user_id']);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId('task_id');
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Stop a Session
     * @param int $sessionId
     * @param int $userId
     * @return bool
     */
    public function stop(int $sessionId, int $userId): bool
    {
        $status = 'closed';
        $table = $this->sessionsTable;
        $sql = "UPDATE {$table} SET
                 session_status=?,
                 updated_by_user_id=?
                WHERE session_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $status,
            $userId,
            $sessionId
        ]);
    }



    /**
     * @param int $sessionId
     * @return bool
     */
    public function deleteSession(int $sessionId): bool
    {
        $table = $this->sessionsTable;
        $sql = "DELETE FROM {$table} WHERE session_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $sessionId
        ]);
    }

    public function addProject(array $values): string|int
    {
        $table = $this->projectsTable;
        $status = "open";
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} 
                (
                 project_name, 
                 description, 
                 status, 
                 created_by_user_id
                 )
                VALUES (
                        :project_name, 
                        :description, 
                        :status, 
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':project_name', $values["project_name"]);
            $stmt->bindParam(':description', $values["description"]);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_by_user_id', $values["created_by_user_id"]);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId('session_id');
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Start a Session
     * @param int $taskId
     * @param int $userId
     * @param int $projectId
     * @return string|int
     */
    public function start(int $taskId, int $userId, int $projectId): string|int
    {
        $task = $this->getTaskById($taskId);
        $table = $this->sessionsTable;
        $status = "open";
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} 
                (
                 task_id, 
                 project_id, 
                 session_status, 
                 session_name, 
                 created_by_user_id
                 )
                VALUES (
                        :task_id, 
                        :project_id, 
                        :session_status, 
                        :session_name, 
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':task_id', $taskId);
            $stmt->bindParam(':project_id', $projectId);
            $stmt->bindParam(':session_status', $status);
            $stmt->bindParam(':session_name', $task['task_name']);
            $stmt->bindParam(':created_by_user_id', $userId);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId('session_id');
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function createTable(): string
    {
        $table = $this->taskTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `task_id` int(20) NOT NULL AUTO_INCREMENT,
                `project_id` int(20) NOT NULL,
                `task_name` varchar(255) NULL,
                `description` varchar(255) NULL,
                `task_status` varchar(255) NULL,             
                `created_by_user_id` int(10) NOT NULL,                
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                `updated_by_user_id` int(10) NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`task_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    public function createSessionsTable(): string
    {
        $table = $this->sessionsTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `session_id` int(20) NOT NULL AUTO_INCREMENT,
                `task_id` int(255) NULL,
                `project_id` int(255) NULL,
                `session_status` varchar(255) NULL,             
                `session_name` varchar(255) NULL,             
                `created_by_user_id` int(10) NOT NULL,                
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                `updated_by_user_id` int(10) NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`session_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }

    public function createProjectsTable(): string
    {
        $table = $this->projectsTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `project_id` int(20) NOT NULL AUTO_INCREMENT,
                `project_name` varchar(255) NULL,
                `description` varchar(255) NULL,             
                `status` varchar(255) NULL,             
                `created_by_user_id` int(10) NOT NULL,                
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                `updated_by_user_id` int(10) NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`project_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}
