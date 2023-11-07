<?php
require_once __DIR__ . "/../../dash-loader.php";

use Ifsnop\Mysqldump as IMysqldump;

try {
    $date = date('Ymd');
    $dumpSettings = array(
        'compress' => IMysqldump\Mysqldump::NONE,
        'no-data' => false,
        'add-drop-table' => true,
        'single-transaction' => true,
        'lock-tables' => true,
        'add-locks' => true,
        'extended-insert' => true,
        'disable-foreign-keys-check' => true,
        'skip-triggers' => false,
        'add-drop-trigger' => true,
        'databases' => true,
        'add-drop-database' => true,
        'hex-blob' => true
    );
    $dump = new IMysqldump\Mysqldump(DB_DRIVER, DB_USER, DB_PASSWORD, $dumpSettings);
    $dump->start(ROOT_PATH."/storage/backup${date}.sql");
} catch (\Exception $e) {
    echo 'mysqldump-php error: ' . $e->getMessage();
}