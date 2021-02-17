<?php declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$app = new \App\App(
    new \App\Bootstrapper(
        dirname(__DIR__)
    )
);

$app->processRequest()
    ->sendResponse();