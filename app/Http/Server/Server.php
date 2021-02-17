<?php declare(strict_types=1);

namespace App\Http\Server;

use App\Contracts\Http\Server\ServerInterface;
use App\Contracts\GlobalsInterface;


class Server implements ServerInterface
{
    private $globals;

    public function __construct(GlobalsInterface $globals)
    {
        $this->globals = $globals;
    }

    public function isSecure()
    {
        return (isset($this->globals->server()["HTTPS"])
            && $this->globals->server()["HTTPS"] === "on");
    }

    public function protocol()
    {
        if($this->isSecure()) {
            return "https";
        }
        return "http";
    }

    public function port()
    {
        return $this->globals->server()["SERVER_PORT"];
    }

    // hostname only returns the name
    // i.e. name of domain without the port and protocol
    public function hostname()
    {
        return $this->globals->server()["SERVER_NAME"];
    }

    // host returns hostname with port no.
    public function host()
    {
        return $this->globals->server()["HTTP_HOST"];
    }

    // domain returns the server host with proper protocol
    public function domain()
    {
        return $this->protocol() . "://" . $this->host();
    }
}
