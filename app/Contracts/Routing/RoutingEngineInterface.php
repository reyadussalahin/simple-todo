<?php declare(strict_types=1);

namespace App\Contracts\Routing;


interface RoutingEngineInterface
{
    public function resolve($method, $reqUri);
}
