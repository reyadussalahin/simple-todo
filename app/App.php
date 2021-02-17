<?php declare(strict_types=1);

namespace App;

use App\Contracts\AppInterface;
use App\Contracts\BootstrapperInterface;
use App\Contracts\Http\Response\ResponseInterface;
use App\Routing\RoutingEngine;
use App\Http\Request\Request;
use App\Http\Request\RequestHandler;



class App implements AppInterface
{
    private $bs;

    public function __construct(BootstrapperInterface $bs)
    {
        $this->bs = $bs;
    }

    public function bs(): BootstrapperInterface
    {
        return $this->bs;
    }

    public function processRequest(): ResponseInterface
    {
        $routes = $this->bs->routes();
        $request = new Request($this->bs->globals());
        $routingEngine = new RoutingEngine($routes);
        $result = $routingEngine->resolve(
            $request->method(),
            $request->uri()
        );
        if($result === null) {
            return new TextResponse("404 page not found");
        }
        $requestHandler = new RequestHandler(
            $this,
            $request,
            $this->bs->middlewares(),
            $result
        );
        $response = $requestHandler->handleRequest();
        return $response;
    }
}
