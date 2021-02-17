<?php declare(strict_types=1);

namespace App\Routing;

use App\Contracts\Routing\RoutesProcessorInterface;


class RoutesProcessor implements RoutesProcessorInterface
{
    private $routes_base;
    private $routes_store;

    public function __construct(string $routes_base)
    {
        $this->routes_base = $routes_base;
        $this->routes_store = new RoutesStore();
        $this->processRoutes();
    }

    private function processRoutes()
    {
        // creating $routes object
        // this object will be used by user
        // to declaring routes
        $routes = new Routes($this->routes_store);
        // check rules about how to read the routes files
        // and what prefix to add before routes
        $rules = require_once $this->routes_base . "/rules.php";
        foreach($rules as $route_name => $route_info) {
            $route_prefix = $route_info["prefix"];
            $route_filepath = $this->routes_base
                . DIRECTORY_SEPARATOR
                . trim(trim($route_info["path"], "/"), "\\");
            // setUriPrefix must be set before calling require_once
            $routes->setUriPrefix($route_prefix);
            // now execute routes file to store each routes
            // user has registered in the file
            include $route_filepath;
        }
    }

    public function getRoutes()
    {
        // just return this->routes_store
        return $this->routes_store;
    }
}
