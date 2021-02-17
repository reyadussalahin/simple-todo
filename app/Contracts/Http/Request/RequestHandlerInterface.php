<?php declare(strict_types=1);

namespace App\Contracts\Http\Request;


interface RequestHandlerInterface
{
    public function next(RequestInterface $request);
}
