<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\Http\Middleware\MiddlewareInterface;
use App\Contracts\Http\Request\RequestInterface;
use App\Contracts\Http\Request\RequestHandlerInterface;


abstract class AbstractMiddleware implements MiddlewareInterface
{
    abstract public function handle(
        RequestInterface $request,
        RequestHandlerInterface $rhandler);
}
