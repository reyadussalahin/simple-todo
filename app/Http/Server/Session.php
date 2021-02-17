<?php declare(strict_types=1);

namespace App\Http\Server;

use App\Contracts\Http\Server\SessionInterface;
use App\Contracts\GlobalsInterface;


class Session implements SessionInterface
{
    private $globals;

    public function __construct(GlobalsInterface $globals)
    {
        $this->globals = $globals;
    }

    public function set($key, $value)
    {
        $this->globals->session()[$key] = $value;
    }

    public function get($key)
    {
        return $this->globals->session()[$key];
    }
}
