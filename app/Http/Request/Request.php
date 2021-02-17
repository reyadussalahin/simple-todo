<?php declare(strict_types=1);

namespace App\Http\Request;

use App\Contracts\Http\Request\RequestInterface;
use App\Contracts\GlobalsInterface;


class Request implements RequestInterface
{
    private $globals;

    public function __construct(GlobalsInterface $globals) {
        $this->globals = $globals;
    }

    public function method() {
        return $this->globals->server()["REQUEST_METHOD"];
    }

    public function uri() {
        return $this->globals->server()["REQUEST_URI"];
    }

    public function has($input) {
        return isset($this->globals->request()[$input]);
    }

    public function get($input) {
        return $this->globals->request()[$input];
    }
}
