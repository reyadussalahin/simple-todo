<?php declare(strict_types=1);

namespace App;


use App\Contracts\GlobalsInterface;


class Globals implements GlobalsInterface
{
    /**
     * Contains all the necessary globals
     * 
     * @var array
     */
    private $globals;


    /**
     * Create a new Globals instance
     *
     * @return void
     */
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

    /**
     * Returns globals array which consists of all globals
     *
     * @return array
     */
    public function all()
    {
        return $this->globals;
    }

    /**
     * Returns PHP $GLOBALS array
     *
     * @return array
     */
    public function &globals()
    {
        return $this->globals["globals"];
    }

    /**
     * Returns PHP $_SERVER array
     *
     * @return array
     */
    public function &server()
    {
        return $this->globals["server"];
    }

    /**
     * Returns PHP $_GET array
     *
     * @return array
     */
    public function &get()
    {
        return $this->globals["get"];
    }

    /**
     * Returns PHP $_POST array
     *
     * @return array
     */
    public function &post()
    {
        return $this->globals["post"];
    }

    /**
     * Returns PHP $_FILES array
     *
     * @return array
     */
    public function &files()
    {
        return $this->globals["files"];
    }
    
    /**
     * Returns PHP $_COOKIE array
     *
     * @return array
     */
    public function &cookie()
    {
        return $this->globals["cookie"];
    }

    /**
     * Returns PHP $_SESSION array
     *
     * @return array
     */
    public function &session()
    {
        return $this->globals["session"];
    }

    /**
     * Returns PHP $_REQUEST array
     *
     * @return array
     */
    public function &request()
    {
        return $this->globals["request"];
    }

    /**
     * Returns PHP $_ENV array
     *
     * @return array
     */
    public function &env()
    {
        return $this->globals["env"];
    }
}
