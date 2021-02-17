<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\Http\Request\RequestInterface;
use App\Contracts\Http\Request\RequestHandlerInterface;
use App\Http\Server\Session;
use App\Http\Server\Server;


class VerifyCSRFTokenMiddleware extends AbstractMiddleware
{
    public function handle(RequestInterface $request,
        RequestHandlerInterface $rhandler)
    {
        if($request->method() !== "POST") {
            return $rhandler->next($request);
        }
        $session = new Session($rhandler->app()->bs()->globals());
        if($request->has('csrfmiddlewaretoken')
            && ($session->get('csrfmiddlewaretoken')
                === $request->get('csrfmiddlewaretoken'))) {
            return $rhandler->next($request);
        }
        // I need to change it with redirect object later
        // just coding it to make it faster for development
        $server = new Server($rhandler->app()->bs()->globals());
        header("Location: " . $server->domain());
        exit(0);
    }
}
