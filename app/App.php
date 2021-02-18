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
    /**
     * The Bootstrapper of the application
     *
     * Only one Bootstrapper exists per application
     *
     * @var BootstrapperInterface
     */
    private $bs;


    /**
     * Creates Application
     * 
     * @param BootstrapperInterface $bs
     * @return void
     */
    public function __construct(BootstrapperInterface $bs)
    {
        $this->bs = $bs;
    }


    /**
     * Returns the Bootstrapper used by the application
     * 
     * @return BootstrapperInterface
     */
    public function bs(): BootstrapperInterface
    {
        return $this->bs;
    }


    /**
     * Process User Request and returns Response
     * 
     * @return ResponseInterface
     */
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
