<?php declare(strict_types=1);


namespace App\Contracts;

use App\Contracts\BootstrapperInterface;
use App\Contracts\Http\Response\ResponseInterface;


interface AppInterface
{
    public function bs(): BootstrapperInterface;
    public function processRequest(): ResponseInterface;
}