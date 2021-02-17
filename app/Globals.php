<?php declare(strict_types=1);

namespace App;


use App\Contracts\GlobalsInterface;


class Globals implements GlobalsInterface
{
    private $globals;

    public function __construct()
    {
        $this->globals = [
            "globals" => &$GLOBALS,
            "server" => &$_SERVER,
            "get" => &$_GET,
            "post" => &$_POST,
            "files" => &$_FILES,
            "cookie" => &$_COOKIE,
            "session" => &$_SESSION,
            "request" => &$_REQUEST,
            "env" => &$_ENV
        ];
    }

    public function all()
    {
        return $this->globals;
    }

    public function &globals()
    {
        return $this->globals["globals"];
    }

    public function &server()
    {
        return $this->globals["server"];
    }

    public function &get()
    {
        return $this->globals["get"];
    }

    public function &post()
    {
        return $this->globals["post"];
    }

    public function &files()
    {
        return $this->globals["files"];
    }
    
    public function &cookie()
    {
        return $this->globals["cookie"];
    }

    public function &session()
    {
        return $this->globals["session"];
    }

    public function &request()
    {
        return $this->globals["request"];
    }

    public function &env()
    {
        return $this->globals["env"];
    }
}
