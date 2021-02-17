<?php declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";


// load env
$envPath = __DIR__ . "/../" . ".env";
if(file_exists($envPath) && is_file($envPath)) {
    (new \App\Util\DotEnv($envPath))->load();
}


/**
 * list table names those you want to
 * drop when this script runs
 */

$tableNames = [
    "todo"
];


$db = new \App\Database\Database();

try {
    $conn = $db->connection();
    $prefix = "DROP TABLE IF EXISTS ";
    foreach($tableNames as $tableName) {
        $sql = $prefix . " " . $tableName;
        $conn->exec($sql);
        echo "dropped \"$tableName\" successfully\n";
    }
} catch(\PDOException $e) {
    echo "error:\n";
    echo $e->getMessage();
    echo "\n";
}