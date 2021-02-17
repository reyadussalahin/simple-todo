<?php declare(strict_types=1);

namespace App\Routing;

use App\Contracts\Routing\RoutesStoreInterface;


class RoutesStore implements RoutesStoreInterface
{
    private $routes = [];
    private $unique_routes = [];
    private $name_route_map = [];

    public function add(string $method, string $uri, array $controller)
    {
        if(!isset($this->routes[$uri])) {
            $this->unique_routes[] = $uri;
            $this->routes[$uri] = [];
            $this->routes[$uri]["__methods__"] = [];
            $this->routes[$uri]["__filters__"] = [];
            $this->routes[$uri]["__middlewares__"] = [];
        }
        $this->routes[$uri]["__methods__"][$method] = $controller;
    }

    public function exists(string $uri)
    {
        return isset($this->routes[$uri]);
    }

    public function setName(string $uri, string $name)
    {
        if($this->exists($uri)) {
            $this->routes[$uri]["__name__"] = $name;
            $this->name_route_map[$name] = $uri;
        }
    }

    public function addMiddlewares(string $uri, array $middlewares)
    {
        if($this->exists($uri)) {
            foreach($middlewares as $middleware) {
                $this->routes[$uri]["__middlewares__"][] = $middleware;
            }
        }
    }

    public function addFilters(string $uri, array $filters)
    {
        if($this->exists($uri)) {
            foreach($filters as $filter_param => $filter_pattern) {
                $this->routes[$uri]["__filters__"][$filter_param] = $filter_pattern;
            }
        }
    }

    public function isMethodSupportedByUri(string $method, string $uri)
    {
        if(isset($this->routes[$uri]["__methods__"][$method])) {
            return true;
        }
        return false;
    }

    public function getController(string $method, string $uri)
    {
        if(isset($this->routes[$uri]["__methods__"][$method])) {
            return $this->routes[$uri]["__methods__"][$method];
        }
        return null;
    }

    public function getMiddlewares(string $uri)
    {
        return $this->routes[$uri]["__middlewares__"];
    }

    public function getFilters(string $uri)
    {
        return $this->routes[$uri]["__filters__"];
    }

    public function getName(string $uri)
    {
        if(isset($this->routes[$uri]["__name__"])) {
            return $this->routes[$uri]["__name__"];
        }
        return "";
    }

    public function getRouteByName(string $name)
    {
        return $this->name_route_map[$name];
    }

    public function getUniqueURIs()
    {
        return $this->unique_routes;
    }

    public function show()
    {
        echo "<pre>";
        print_r($this->routes);
        echo "</pre>";
    }
}