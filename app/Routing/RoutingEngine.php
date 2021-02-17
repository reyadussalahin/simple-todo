<?php declare(strict_types=1);

namespace App\Routing;

use App\Contracts\Routing\RoutingEngineInterface;


class RoutingEngine implements RoutingEngineInterface
{
    private $routes;

    public function __construct(RoutesStore $routes)
    {
        $this->routes = $routes;
    }

    private function parse($uri)
    {
        $filters = $this->routes->getFilters($uri);
        $toks = explode("/", $uri);
        $uriInfo = [];
        foreach($toks as $tok) {
            $tok = trim($tok);
            $pat = "";
            $tokInfo = [];
            $len = strlen($tok);
            if($len > 0 && $tok[0] === "{") {
                if($tok[$len - 1] !== "}") {
                    throw new \Exception(
                        "Error Parsing Routes: parameter format's not correct"
                    );
                }
                $tok = substr($tok, 0, $len-1);
                $tok = substr($tok, 1);
                if(isset($filters[$tok])) {
                    $pat = $filters[$tok];
                }
                $tokInfo["type"] = "param";
            } else {
                $tokInfo["type"] = "context";
            }
            $tokInfo["name"] = $tok;
            if($pat !== "") {
                $tokInfo["pat"] = $pat;
            }
            $uriInfo[] = $tokInfo;
        }
        return $uriInfo;
    }

    private function match($uri, $reqUri)
    {
        $ul = count($uri);
        $rl = count($reqUri);
        if($ul !== $rl) {
            return false;
        }
        $params = [];
        for($i=0; $i<$ul; $i++) {
            $tok = $uri[$i];
            $reqUri[$i] = trim($reqUri[$i]);
            if($tok["type"] === "context") {
                if($tok["name"] !== $reqUri[$i]) {
                    return false;
                }
            } else {
                if(isset($tok["pat"])) {
                    $pat = "/^" . $tok["pat"] . "$/";
                    if(!preg_match($pat, $reqUri[$i])) {
                        return false;
                    }
                }
                $params[] = $reqUri[$i];
            }
        }
        return $params;
    }

    public function resolve($method, $reqUri)
    {
        $method = strtoupper($method);
        $reqUri = trim(trim($reqUri), "/");
        foreach($this->routes->getUniqueURIs() as $uri) {
            if($this->routes->isMethodSupportedByUri($method, $uri)) {
                $route_parameters = $this->match(
                    $this->parse($uri),
                    explode("/", $reqUri)
                );
                if($route_parameters !== false) {
                    return [
                        "controller" => $this->routes->getController($method, $uri),
                        "middlewares" => $this->routes->getMiddlewares($uri),
                        "route_parameters" => $route_parameters
                    ];
                }
            }
        }
        return null;
    }
}
