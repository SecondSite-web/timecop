<?php
/**
 * File:          DashAuth.php
 * File Created:  2021/04/20 12:42
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */

namespace Dash;

use PDO;
use PDOException;
use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class DashAuth
{
    private PDO $pdo;

    private string $userTable = 'phpauth_users';

    private array $groups = ['root', 'admin', 'client'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the array of user Groups from the class definitions
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }


    /**
     * Returns user status from email
     * @param $email
     * @return mixed
     */
    public function getUserStatus($email): mixed
    {
        try {
            $sql = "SELECT `isactive` FROM {$this->userTable} WHERE email=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return 'error retrieving User Status';
        }
    }

    /**
     * Get the current users details and sets them as globals
     * @return array|bool
     */
    public function sessionUser(): array|bool
    {
        $config = new PHPAuthConfig($this->pdo);
        $auth = new PHPAuth($this->pdo, $config);
        $result = $auth->getCurrentUser();
        if (is_null($result)) {
            return false;
        }
        return $result;
    }


    /**
     * Returns an array of all users from the db
     * @return string|array
     */
    public function getAllUsers(): string|array
    {
        try {
            $sql = "SELECT * FROM {$this->userTable}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($array as $parts) {
                unset($parts['password']);
            }
            return $array;
        } catch (PDOException $e) {
            return 'error retrieving email address';
        }
    }


    /**
     * Returns an array of all Users who are in a group by the Group Name
     * @param $groupName
     * @return array|bool
     */
    public function getUsersByGroup($groupName): array|bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM {$this->userTable} WHERE user_group =:user_group"
            );
            $stmt->execute(['user_group' => $groupName]);
            $array = $stmt->fetchAll(pdo::FETCH_ASSOC);
            foreach ($array as $element) {
                unset($element['password']);
            }
            return $array;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Returns user and group details
     * @param $userId
     * @return mixed
     */
    public function getUserById($userId): mixed
    {
        try {
            $sql = "SELECT * FROM {$this->userTable} WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Returns a count of all active users
     * @return mixed
     */
    public function getUserCount()
    {
        $table = $this->userTable;
        $sql = "SELECT count(*) FROM {$table} WHERE NOT isactive = ?";
        $result = $this->pdo->prepare($sql);
        $result->execute([0]);
        return $result->fetchColumn();
    }

    /**
     * Returns an array of all user groups and a count of how many users per group
     * ['group_name' => 'count']
     * @return array
     */
    public function groupCounter(): array
    {

        $groupInfo = $this->getGroups();
        $groups = $this->getAllUsers();
        $grpCounter = [];
        foreach ($groupInfo as $cat) {
            $count = 0;
            foreach ($groups as $group) {
                if ($cat == $group['user_group']) {
                    $count ++;
                }
            }
            $userArray = [
                'group_name' => $cat,
                'count' => $count
            ];
            array_push($grpCounter, $userArray);
        }
        return $grpCounter;
    }


    /**
     * Geta a user email address by user Id
     * @param $uid
     * @return mixed|string
     */
    public function getEmail($uid)
    {
        try {
            $sql = "SELECT `email` FROM {$this->userTable} WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$uid]);
            return $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return 'error retrieving email address';
        }
    }

    /**
     * Change a Users email address by id
     * @param $userId
     * @param $email
     * @return bool
     */
    public function changeUserEmail($userId, $email): bool
    {
        try {
            $sql = "UPDATE {$this->userTable} SET email=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$email, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Change a user isactive status
     * @param $userId
     * @param $isActive
     * @return bool
     */
    public function changeUserStatus($userId, $isActive): bool
    {
        try {
            $sql = "UPDATE {$this->userTable} SET isactive=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$isActive, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Checks if a user is in a group
     * @param int $userId
     * @param string $group
     * @return bool
     */
    public function isInGroup(int $userId, string $group): bool
    {
        $user = $this->getUserById($userId);
        if (strtolower($user['user_group']) === strtolower($group)) {
            return true;
        }
        return false;
    }

    /**
     * Update a users group
     * @param $userId
     * @param $group
     * @return bool
     */
    public function saveUserGroup($userId, $group): bool
    {
        try {
            $sql = "UPDATE {$this->userTable} SET user_group=? WHERE id=?";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute([$group, $userId]);
            return true;
        } catch (PDOException) {
            return false;
        }
    }



    /**
     * Sends an email when a new user is registered
     * @param $email
     * @param $password
     * @return bool|string
     */
    public function registrationMailer($email, $password): bool|string
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions

        try {
            //Server settings
            $mail->SMTPDebug = SMTP_DEBUG;                                 // Enable verbose debug output
            $mail->isSMTP();
            date_default_timezone_set('Africa/Johannesburg');   // Set mailer to use SMTP
            $mail->Host = SMTP_HOST;                         // Specify main and backup SMTP servers
            $mail->SMTPAuth = SMTP_AUTH;                               // Enable SMTP authentication
            $mail->Username = SMTP_EMAIL;                           // SMTP username
            $mail->Password = SMTP_PASS;                           // SMTP password
            $mail->SMTPSecure = SMTP_SSL;            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = SMTP_PORT;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
            $mail->addAddress($email, SMTP_NAME);              // Add a recipient
            $mail->addReplyTo(SMTP_EMAIL, SMTP_NAME);

            $mail->isHTML(true);
            $mail->Subject = SMTP_SUBJECT;
            $message = '';
            $message = file_get_contents(ROOT_PATH.'admin/email-templates/registration.html');
            $message = str_replace(array('%email%', '%password%'), array($email, $password), $message);
            $mail->MsgHTML($message);
            $mail->AltBody = strip_tags($message);
            $mail->send();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
