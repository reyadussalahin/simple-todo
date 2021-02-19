<?php declare(strict_types=1);

require_once __DIR__ . "/db_bootstrap.php";

function show_clear_help_message()
{
    global $argv;
    echo "It clears database for simple todo app\n";
    echo "\n";
    echo "Usage:    php " . $argv[0] . " [options]\n";
    echo "Options:\n";
    echo "          --help or -h(shortcut)\n";
    echo "          --with-test (also clears test database)\n";
    echo "          --only-test (only clears for test database)\n";
}

function remove_tables_from_database(string $db_url)
{
    $tableNames = getTableNames();
    $db = new \App\Database\Database($db_url);
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
}
