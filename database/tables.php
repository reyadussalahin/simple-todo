<?php declare(strict_types=1);

return [
    "todo" => "CREATE TABLE IF NOT EXISTS todo (
        id serial PRIMARY KEY,
        content TEXT NOT NULL,
        status VARCHAR(12) NOT NULL
    )"
];
