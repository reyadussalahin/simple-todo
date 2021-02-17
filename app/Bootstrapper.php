<?php declare(strict_types=1);

namespace App;

use App\Contracts\BootstrapperInterface;
use App\Contracts\GlobalsInterface;
use App\Contracts\Routing\RoutesStoreInterface;
use App\Globals;
use App\Routing\RoutesProcessor;
use App\Routing\RoutesStore;
use App\Templating\ViewsStore;
use App\Util\DotEnv;
use App\AppEnv;
use App\Database\Database;


class Bootstrapper implements BootstrapperInterface
{
    private $root_dir;
    private $routes;
    private $env;
    private $globals;
    private $middlewares;
    private $views;
    private $db;

    public function __construct(string $root_dir)
    {
        session_start();
        $this->root_dir = $root_dir;
        $this->loadGlobals();
        $this->loadRoutes();
        $this->loadEnv();
        $this->loadMiddlewares();
        $this->loadViews();
        $this->loadDb();
    }

    private function loadGlobals()
    {
        $this->globals = new Globals();
    }

    private function loadRoutes()
    {
        $routesProcessor = new RoutesProcessor($this->root_dir
            . DIRECTORY_SEPARATOR
            . "routes");
        $this->routes = $routesProcessor->getRoutes();
    }

    private function loadEnv()
    {
        $envPath = $this->root_dir . DIRECTORY_SEPARATOR . ".env";
        if(file_exists($envPath) && is_file($envPath)) {
            (new DotEnv($envPath))->load();
        }
        $this->env = new AppEnv();
    }

    private function loadMiddlewares()
    {
        $middlewares_root = $this->root_dir
            . DIRECTORY_SEPARATOR
            . "middlewares";
        $common_middlewares_filepath = $middlewares_root
            . DIRECTORY_SEPARATOR
            . "common.php";
        $this->middlewares = include $common_middlewares_filepath;
    }

    private function loadViews()
    {
        $this->views = new ViewsStore($this->root_dir
            . DIRECTORY_SEPARATOR
            . "views"
        );
    }

    private function loadDb()
    {
        $this->db = new Database();
    }

    public function globals(): GlobalsInterface
    {
        return $this->globals;
    }

    public function routes(): RoutesStoreInterface
    {
        return $this->routes;
    }

    public function env()
    {
        return $this->env;
    }

    public function middlewares()
    {
        return $this->middlewares;
    }

    public function views(): ViewsStore
    {
        return $this->views;
    }

    public function db()
    {
        return $this->db;
    }
}
