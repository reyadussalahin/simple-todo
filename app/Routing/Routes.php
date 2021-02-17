<?php declare(strict_types=1);

namespace App\Routing;

use App\Contracts\Routing\RoutesInterface;


class Routes implements RoutesInterface
{
    private $routes_store;
    private $uri_prefix;
    private $uri;

    public function __construct(RoutesStore $routes_store)
    {
        $this->routes_store = $routes_store;
    }

    public function setUriPrefix(string $prefix)
    {
        $this->uri_prefix = trim($prefix, "/");
    }

    private function constructUri(string $uri)
    {
        return trim($this->uri_prefix . "/" . trim(trim($uri), "/"), "/");
    }

    public function register(array $methods, string $uri, array $controller)
    {
        $this->uri = $this->constructUri($uri);
        foreach($methods as $method) {
            $method = strtoupper($method);
            $this->routes_store->add($method, $this->uri, $controller);
        }
        return $this;
    }

    public function where(array $filters)
    {
        $this->routes_store->addFilters($this->uri, $filters);
        return $this;
    }

    public function middlewares(array $middlewares) {
        $this->routes_store->addMiddlewares($this->uri, $middlewares);
        return $this;
    }

    public function name(string $name) {
        $this->routes_store->setName($this->uri, $name);
        return $this;
    }

    public function get(string $uri, array $controller) {
        $this->uri = $this->constructUri($uri);
        $this->routes_store->add("GET", $this->uri, $controller);
        return $this;
    }

    public function post(string $uri, array $controller) {
        $this->uri = $this->constructUri($uri);
        $this->routes_store->add("POST", $this->uri, $controller);
        return $this;
    }

    public function delete(string $uri, array $controller) {
        $this->uri = $this->constructUri($uri);
        $this->routes_store->add("DELETE", $this->uri, $controller);
        return $this;
    }

    public function put(string $uri, array $controller) {
        $this->uri = $this->constructUri($uri);
        $this->routes_store->add("PUT", $this->uri, $controller);
        return $this;
    }

    public function patch(string $uri, array $controller) {
        $this->uri = $this->constructUri($uri);
        $this->routes_store->add("PATCH", $this->uri, $controller);
        return $this;
    }
}
