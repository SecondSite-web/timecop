<?php
/*
 * Update the $list array in this file
 * Then run this file to rebuild the permissions table.
 */
require_once __DIR__ . "/../../dash-loader.php";

use Dash\Permissions;

$permissions = new Permissions($pdo);

function permissionsReset($pdo): void
{
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS `permissions`");
    $stmt->execute();
    $stmt->fetch();
}
function permissionsMetaReset($pdo): void
{
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS `permissions_meta`");
    $stmt->execute();
    $stmt->fetch();
}

permissionsReset($pdo);
permissionsMetaReset($pdo);
$permissions->createPostsTable();
$permissions->createPostMetaTable();

$list = [
    [
        'number'              => 1,
        'permission_title'    => 'edit_user_function',
        'description'         => 'Permission to edit a user account from the Users Management Table',
        'category'            => 'User Accounts',
        'url'                 => 'users-table'
    ],
    [
        'number'              => 2,
        'permission_title'    => 'add_user_function',
        'description'         => 'Permission to edit a user account from the Users Management Table',
        'category'            => 'User Accounts',
        'url'                 => 'users-table'
    ],
    [
        'number'              => 3,
        'permission_title'    => 'edit_user_group_function',
        'description'         => 'Permission to edit a user group from the User Groups Management Table',
        'category'            => 'User Groups',
        'url'                 => 'user-groups'
    ],
    [
        'number'              => 4,
        'permission_title'    => 'view_dashboard',
        'description'         => "Supplier Orders Table Report",
        'category'            => 'Admin',
        'url'                 => 'admin'
    ],
    [
        'number'              => 5,
        'permission_title'    => 'users_table',
        'description'         => "View and edit the Users Pages",
        'category'            => 'User Management',
        'url'                 => 'user-management'
    ],
    [
        'number'              => 6,
        'permission_title'    => 'edit_permissions',
        'description'         => 'Permission to edit Permissions from the Permissions Table',
        'category'            => 'User Permissions',
        'url'                 => 'permissions'
    ],
    [
        'number'              => 7,
        'permission_title'    => 'view_tiap_settings',
        'description'         => "View and edit the TIAP Settings Page",
        'category'            => 'Settings',
        'url'                 => 'dash-settings'
    ]
];
$permissions->list($list);


$meta = [
    [
        'permission_title' => 'edit_user_function',
        'user_group'=> 'admin'
    ],
];
foreach ($meta as $value) {
    $permissions->saveMeta($value['permission_title'], $value['user_group']);
}
