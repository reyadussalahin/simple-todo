<?php declare(strict_types=1);

require_once __DIR__ . "/util/refresh_util.php";


$argv[0] = trim($argv[0]);
if(count($argv) > 1) {
    $argv[1] = trim($argv[1]);
}

if(count($argv) > 2
    || (count($argv) === 2
        && $argv[1] !== "--only-test"
        && $argv[1] !== "--with-test"
        && $argv[1] !== "--help"
        && $argv[1] !== "-h")) {
    echo "Invalid parameters";
    show_refresh_help_message();
    exit(0);
}


if(count($argv) === 2
    && ($argv[1] === "--help"
        || $argv[1] === "-h")) {
    show_refresh_help_message();
    exit(0);
}

$tables = getTables();

if(count($argv) === 1 ||
    (count($argv) === 2
        && $argv[1] === "--with-test")) {
    echo "dropping tables in database...\n";
    refresh_tables_from_database(getenv("DATABASE_URL"), $tables);
}

if(count($argv) === 2
    && ($argv[1] === "--with-test"
        || $argv[1] === "--only-test")) {
    echo "dropping tables from testing database...\n";
    refresh_tables_from_database(getenv("TEST_DATABASE_URL"), $tables);
}
