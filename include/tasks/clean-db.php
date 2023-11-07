<?php
require_once __DIR__ . "/../../dash-loader.php";
require_once __DIR__ . "/export-db.php";

$install = new \Dash\Install($pdo);
$install->init();
try {
    $array = [
        'dash_companies'
    ];
    foreach ($array as $variable) {
        tableReset($pdo, $variable);
    }
    $companies = new \Dash\Companies($pdo);

} catch (\Exception $e) {
    echo 'mysqldump-php error: ' . $e->getMessage();
}

function tableReset($pdo, $table): void
{
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS {$table}");
    $stmt->execute();
    $stmt->fetch();
}
include __DIR__ . "/permissions-add.php";