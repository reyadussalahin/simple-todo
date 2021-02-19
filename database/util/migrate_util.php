<?php declare(strict_types=1);

require_once __DIR__ . "/db_bootstrap.php";


function show_migrate_help_message()
{
    global $argv;
    echo "It performs database migration for simple todo app\n";
    echo "\n";
    echo "Usage:    php " . $argv[0] . " [options]\n";
    echo "Options:\n";
    echo "          --help or -h(shortcut)\n";
    echo "          --with-test (also migrate for test database)\n";
    echo "          --only-test (only migrate for test database)\n";
}


/**
 * This function creates tables for a given DATABASE_URL
 */

function create_tables_for_database(string $db_url)
{
    $tables = getTables();
    $db = new \App\Database\Database($db_url);
    try {
        $conn = $db->connection();
        foreach($tables as $tableName => $sql) {
            $conn->exec($sql);
            echo "> created \"$tableName\" successfully\n";
        }
    } catch(\PDOExcepton $e) {
        echo "error:\n";
        echo $e->getMessage();
        echo "\n";
    }
}