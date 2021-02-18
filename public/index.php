<?php declare(strict_types=1);

/**
 * The unopinionated autoloader of php
 * Loads everything we need and only when we need them
 * We don't need to manage anything manually
 */
require __DIR__ . "/../vendor/autoload.php";


/**
 * Create Bootstrapper that would bootstrap the needs
 * for creating an application
 *
 * And then create the App by passing the bootstrapper object
 * Once the application is created, we can handle the request
 */
$app = new \App\App(
    new \App\Bootstrapper(
        dirname(__DIR__)
    )
);

/**
 * Start process request
 * and send response when done
 */
$app->processRequest()
    ->sendResponse();