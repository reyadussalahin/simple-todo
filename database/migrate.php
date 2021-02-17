<?php declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";


// load env
$envPath = __DIR__ . "/../" . ".env";
if(file_exists($envPath) && is_file($envPath)) {
    (new \App\Util\DotEnv($envPath))->load();
}


$tableSqlArray["todo"] = "CREATE TABLE IF NOT EXISTS todo (
    id serial PRIMARY KEY,
    content TEXT NOT NULL,
    status VARCHAR(12) NOT NULL
)";


/**
 * creating tables using the sql declared above
 */

$db = new \App\Database\Database();

try {
    $conn = $db->connection();
    foreach($tableSqlArray as $tableName => $sql) {
        $conn->exec($sql);
        echo "created \"$tableName\" successfully\n";
    }
} catch(\PDOExcepton $e) {
    echo "error:\n";
    echo $e->getMessage();
    echo "\n";
}
