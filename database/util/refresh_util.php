<?php declare(strict_types=1);

require_once __DIR__ . "/db_bootstrap.php";
require_once __DIR__ . "/migrate_util.php";
require_once __DIR__ . "/clear_util.php";


function show_refresh_help_message()
{
    global $argv;
    echo "It refreshes database for simple todo app\n";
    echo "\n";
    echo "Usage:    php " . $argv[0] . " [options]\n";
    echo "Options:\n";
    echo "          --help or -h(shortcut)\n";
    echo "          --with-test (also refreshes test database)\n";
    echo "          --only-test (only refreshes for test database)\n";
}

function refresh_tables_from_database(string $db_url)
{
    remove_tables_from_database($db_url);
    create_tables_for_database($db_url);
}
