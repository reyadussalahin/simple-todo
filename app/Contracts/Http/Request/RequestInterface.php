<?php declare(strict_types=1);

namespace App\Contracts\Http\Request;


interface RequestInterface
{
    public function method();
    public function uri();
    public function has($input);
    public function get($input);
}
