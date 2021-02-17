<?php declare(strict_types=1);

namespace App\Contracts;


use App\Contracts\GlobalsInterface;
use App\Contracts\Routing\RoutesStoreInterface;
use App\Templating\ViewsStore;

interface BootstrapperInterface
{
    public function globals(): GlobalsInterface;
    public function routes(): RoutesStoreInterface;
    public function env();
    public function middlewares();
    public function views(): ViewsStore;
    public function db();
}
