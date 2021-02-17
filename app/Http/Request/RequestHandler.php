<?php declare(strict_types=1);

namespace App\Http\Request;

use App\Contracts\AppInterface;
use App\Contracts\Http\Request\RequestHandlerInterface;
use App\Contracts\Http\Request\RequestInterface;


class RequestHandler implements RequestHandlerInterface
{
    private $app;
    private $request;
    private $middlewares;
    private $at;
    private $controller;
    private $route_parameters;

    public function __construct(
        AppInterface $app,
        RequestInterface $request,
        array $common_middlewares,
        array $route_specific_info)
    {
        $this->app = $app;
        $this->request = $request;
        $this->middlewares = [];
        $this->at = 0;
        $this->controller = $route_specific_info["controller"];
        $this->route_parameters = $route_specific_info["route_parameters"];
        // adding middlewares while keeping their exact order
        // note: global middlewares have higher preference
        //       than local i.e. route specific ones
        foreach($common_middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
        foreach($route_specific_info["middlewares"] as $middleware) {
            $this->middlewares[] = $middleware;
        }
    }

    public function app()
    {
        return $this->app;
    }

    public function handleRequest()
    {
        return $this->next($this->request);
    }

    public function next(RequestInterface $request)
    {
        if($this->at >= count($this->middlewares)) {
            $controller = new $this->controller[0]($this->app, $request);
            return $controller->{$this->controller[1]}(
                ...$this->route_parameters
            );
        }
        $this->at += 1;
        $middleware = new $this->middlewares[$this->at - 1]();
        return $middleware->handle($request, $this);
    }
}
