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
    private array $statuses = ['open', 'closed', 'inactive'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->taskTable}'")->fetchAll()) {
            $this->createTable();
            $this->createSessionsTable();
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

    /**
     * Fetch All records by Status
     * @param $status
     * @return array
     * @throws \Exception
     */
    public function getByStatus($status): array
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_status=:task_status");
        $stmt->execute(['task_status' => $status]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($list as &$item) {
            $item['sessions'] = $this->getTaskSessions($item['task_id']);
            $totalTime = $this->calculateTotalTime($item['task_id']);
            $item['total_hours'] = $totalTime['total_hours'];
            $item['total_minutes'] = $totalTime['total_minutes'];
        }
        return $list;
    }

    /**
     * Gets a record based on Id
     * @param int $taskId
     * @return mixed
     */
    public function getById(int $taskId): mixed
    {
        $table = $this->taskTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE task_id=:task_id");
        $stmt->execute(['task_id' => $taskId]);
        $list = $stmt->fetch(PDO::FETCH_ASSOC);
        $list['sessions'] = $this->getTaskSessions($taskId);
        $totalTime = $this->calculateTotalTime($taskId);
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
        $sessionList = $stmt->fetch(PDO::FETCH_ASSOC);
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
     * Calculates the total time of a task
     * @param $taskId
     * @return array
     * @throws \Exception
     */
    public function calculateTotalTime($taskId): array
    {
        // Get the list of sessions for the task
        $sessionList = $this->getTaskSessions($taskId);

        $totalHours = 0;
        $totalMinutes = 0;

        foreach ($sessionList as $session) {
            $totalHours += $session['hours'];
            $totalMinutes += $session['minutes'];

            // If minutes exceed 60, add to hours and adjust minutes
            if ($totalMinutes >= 60) {
                $totalHours += intdiv($totalMinutes, 60);
                $totalMinutes = $totalMinutes % 60;
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
     * @param int $userId
     * @return bool
     */
    public function delete(int $taskId, int $userId): bool
    {
        $status = 'inactive';
        $table = $this->taskTable;
        $sql = "UPDATE {$table} SET
                 task_status=?,
                 updated_by_user_id=?
                WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $status,
            $userId,
            $taskId
        ]);
    }

    /**
     * Update a database record with timestamp
     * @param array $values
     * @return bool
     */
    public function update(array $values): bool
    {
        $table = $this->taskTable;
        $sql = "UPDATE {$table} SET 
                 task_name=?,
                 description=?,
                 task_status=?,
                 updated_by_user_id=?
                WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $values['task_name'],
            $values['description'],
            $values['task_status'],
            $values['updated_by_user_id'],
            $values['task_id']
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
                 description, 
                 task_status, 
                 created_by_user_id
                 )
                VALUES (
                        :task_name, 
                        :description, 
                        :task_status,
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':task_name', $fields['task_name']);
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
     * @param int $taskId
     * @return bool
     */
    public function stop(int $taskId): bool
    {
        $status = 'closed';
        $table = $this->sessionsTable;
        $sql = "UPDATE {$table} SET
                 task_status=?,
                 updated_by_user_id=?
                WHERE task_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $status,
            $taskId
        ]);
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

    /**
     * End a Session
     * @param array $fields
     * @return string|int
     */
    public function start(array $fields): string|int
    {
        $table = $this->sessionsTable;
        $status = "open";
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} 
                (
                 task_id, 
                 session_status, 
                 task_status, 
                 created_by_user_id
                 )
                VALUES (
                        :task_id, 
                        :description, 
                        :task_status,
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':task_id', $fields['task_id']);
            $stmt->bindParam(':session_status', $status);
            $stmt->bindParam(':task_status', $fields['task_status']);
            $stmt->bindParam(':created_by_user_id', $fields['created_by_user_id']);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId('task_id');
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
                `task_id` varchar(255) NULL,
                `session_status` varchar(255) NULL,             
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
}
