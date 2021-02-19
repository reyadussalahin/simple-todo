<?php declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

// load env
$envPath = __DIR__ . "/../../" . ".env";
if(file_exists($envPath) && is_file($envPath)) {
    (new \App\Util\DotEnv($envPath))->load();
}


function getTables()
{
    $tables = require __DIR__ . "/../tables.php";
    return $tables;
}

function getTableNames()
{
    $tables = getTables();
    $tableNames = [];
    foreach ($tables as $tableName => $tableDeclaration) {
        $tableNames[] = $tableName;
    }
    return $tableNames;
}
