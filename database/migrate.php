<?php declare(strict_types=1);

require_once __DIR__ . "/util/migrate_util.php";


// trim parameters
$argv[0] = trim($argv[0]);
if(count($argv) > 1) {
    $argv[1] = trim($argv[1]);
}

// check if parameters are okay
if(count($argv) > 2
    || (count($argv) === 2
        && $argv[1] !== "--only-test"
        && $argv[1] !== "--with-test"
        && $argv[1] !== "--help"
        && $argv[1] !== "-h")) {
    echo "Invalid parameters";
    show_migrate_help_message();
    exit(0);
}

// if user wants help, then show help message and exit
if(count($argv) === 2
    && ($argv[1] === "--help"
        || $argv[1] === "-h")) {
    show_migrate_help_message();
    exit(0);
}


if(count($argv) === 1 ||
    (count($argv) === 2
        && $argv[1] === "--with-test")) {
    echo "migrating tables in database...\n";
    create_tables_for_database(getenv("DATABASE_URL"));
}

if(count($argv) === 2
    && ($argv[1] === "--with-test"
        || $argv[1] === "--only-test")) {
    echo "migrating tables in testing database...\n";
    echo "[Note]: this database won't be used for your app\n";
    echo "        its only for testing purposes\n";
    create_tables_for_database(getenv("TEST_DATABASE_URL")); 
}
