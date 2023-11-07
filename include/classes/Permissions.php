<?php
/**
 * File:          Post.php
 * File Created:  2021/04/05 23:54
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

use Dash\DashAuth;
use PDO;
use PDOException;

class Permissions
{
    private PDO $pdo;
    protected string $permissionsTable = 'permissions';
    protected string $permissionsMeta = 'permissions_meta';

    /**
     * Permissions constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $table = $this->permissionsTable;
        $meta = $this->permissionsMeta;

        if (!$this->pdo->query("SHOW TABLES LIKE '{$table}'")->fetchAll()) {
            $this->createPostsTable();
        }
        if (!$this->pdo->query("SHOW TABLES LIKE '{$meta}'")->fetchAll()) {
            $this->createPostMetaTable();
        }
    }

    /**
     * Clears all meta entries for a permission
     * @param string $permissionId
     * @return bool
     */
    public function clearUser(string $permissionId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->permissionsMeta} WHERE permission_id=:permission_id");
            $stmt->execute(['permission_id' => $permissionId]);
            $stmt->fetch();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Checks if a User has permission on Id's of both
     * @param $userId
     * @param $permissionTitle
     * @return bool
     */
    public function verify($userId, $permissionTitle): bool
    {
        $data = $this->get($permissionTitle);
        foreach ($data as $entry) {
            if ($entry['id'] == $userId) {
                return true;
            }
        }
        $dashAuth = new DashAuth($this->pdo);

        $isRoot = $dashAuth->isInGroup($userId, 'root');
        if ($isRoot == true) {
            return true;
        }
        return false;
    }

    /**
     * Gets info from Permissions, Permissions Meta and User Groups table - Probably won't work...
     * @param $permissionTitle
     * @return array
     */
    public function get($permissionTitle): array
    {
        $qry = "SELECT *           
        FROM {$this->permissionsTable} 
        INNER JOIN {$this->permissionsMeta}          
        ON {$this->permissionsTable}.permission_title={$this->permissionsMeta}.permission_title
        INNER JOIN phpauth_users        
        ON phpauth_users.user_group={$this->permissionsMeta}.user_group
        WHERE {$this->permissionsTable}.permission_title=:permission_title";
        $stmt = $this->pdo->prepare($qry);
        $stmt->execute(['permission_title' => $permissionTitle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns a joined array of a Permission and all it's Meta by Permission Id
     * @param $permissionTitle
     * @return array
     */
    public function getPermissionGroups($permissionTitle): array
    {
        $qry = "SELECT *           
        FROM {$this->permissionsTable} 
        INNER JOIN {$this->permissionsMeta}          
        ON {$this->permissionsTable}.permission_title={$this->permissionsMeta}.permission_title
        WHERE {$this->permissionsTable}.permission_title=:permission_title";
        $stmt = $this->pdo->prepare($qry);
        $stmt->execute(['permission_title' => $permissionTitle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionByTitle($permission): mixed
    {
        $qry = "SELECT *           
        FROM {$this->permissionsTable} 
        WHERE {$this->permissionsTable}.permission_title=:permission_title";
        $stmt = $this->pdo->prepare($qry);
        $stmt->execute(['permission_title' => $permission]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->getPermissionMeta($permission['permission_title']);
    }

    /**
     * Returns an unfiltered array of all permissions
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        $table = $this->permissionsTable;
        return $this->pdo->query(
            "SELECT * FROM {$table}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns an unfiltered array of all meta
     *
     * @return array
     */
    public function getAllMeta(): array
    {
        $table = $this->permissionsMeta;
        return $this->pdo->query(
            "SELECT * FROM {$table}"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns permission meta matching the post Id
     *
     * @param $permissionId
     * @return mixed
     */
    public function getPermissionMeta($permissionId): mixed
    {
        $table = $this->permissionsMeta;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE permission_id=:permission_id");
        $stmt->execute(['permission_id' => $permissionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Helper function to list and install permissions data to server
     */
    public function list($list)
    {
        foreach ($list as $data) {
            $this->savePermission($data);
        }
    }
    /**
     * Saves a permission and the user groups that go with it
     * @param $permission
     * @param $groups
     */
    public function saveBoth($permission, $groups): void
    {
        $permissionId = $this->savePermission($permission);
        $this->savePermissionMeta($permissionId, $groups);
    }

    /**
     * Saves a permission entry to the db
     * @param $post
     * @return int
     */
    public function savePermission($post): int
    {
        $table = $this->permissionsTable;
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$table} (permission_title, description, category, url)
            VALUES (:permission_title, :description, :category, :url)"
        );
        $stmt->bindParam(':permission_title', $post['permission_title']);
        $stmt->bindParam(':description', $post['description']);
        $stmt->bindParam(':category', $post['category']);
        $stmt->bindParam(':url', $post['url']);
        $stmt->execute();
        return $this->pdo->lastInsertId('permission_id');
    }

    /**
     * Saves multiple Meta entries in an array to the db
     * @param $permissionTitle
     * @param $groups
     */
    public function savePermissionMeta($permissionTitle, $groups): void
    {
        foreach ($groups as $group) {
            $this->saveMeta($permissionTitle, $group['user_group']);
        }
    }

    /**
     * Saves a single Meta entry to the Db
     * @param $permissionTitle
     * @param $group
     * @return bool
     */
    public function saveMeta($permissionTitle, $group): bool
    {
        try {
            $table = $this->permissionsMeta;
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} (permission_title, user_group)
                VALUES (:permission_title, :user_group)"
            );
            $stmt->bindParam(':permission_title', $permissionTitle);
            $stmt->bindParam(':user_group', $group);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function createPostsTable(): string
    {
        try {
            $table = $this->permissionsTable;
            $sql ="CREATE TABLE {$table} (
                  `permission_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `permission_title` varchar(100) DEFAULT NULL,
                  `description` longtext DEFAULT NULL,
                  `category` varchar(100) DEFAULT NULL,
                  `url` varchar(100) DEFAULT NULL,
                  PRIMARY KEY (`permission_id`),
                  KEY `permission_title` (`permission_title`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createPostMetaTable(): string
    {
        try {
            $table = $this->permissionsMeta;
            $sql ="CREATE TABLE {$table} (
                  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `permission_title` varchar(100) NOT NULL,
                  `user_group` varchar(50) NOT NULL,
                  PRIMARY KEY (`meta_id`),
                  KEY `permission_title` (`permission_title`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
