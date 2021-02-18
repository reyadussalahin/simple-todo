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
    /**
     * Points to applications root directory
     * 
     * @var string 
     */
    private $root_dir;


    /**
     * Store all routes
     * 
     * @var App\Contracts\Routing\RoutesStoreInterface
     */
    private $routes;


    /**
     * Provides access to Environment variables
     * 
     * @var App\AppEnv
     */
    private $env;


    /**
     * Provides access to global variables
     *
     * @var App\Contracts\GlobalsInterface
     */
    private $globals;

    
    /**
     * Provides list of generic middlewares
     *
     * @var array
     */
    private $middlewares;


    /**
     * Provides access to view files
     *
     * @var App\Templating\ViewsStore
     */
    private $views;


    /**
     * Contains database object to provide database connection
     *
     * Only one such connection is created per request
     *
     * That means this connection is reused again and again
     *
     * In a single request
     *
     * @var App\Contracts\Database\DatabaseInterface
     */
    private $db;



    public function __construct(string $root_dir)
    {
        // supressing session start errors
        // it won't cause any problem in running application
        // it's good for testing purposes
        @session_start();
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
        $routes_dir = $this->root_dir . DIRECTORY_SEPARATOR . "routes";
        // routes_dir must be directory
        if(is_dir($routes_dir)) {
            $routesProcessor = new RoutesProcessor($routes_dir);
            $this->routes = $routesProcessor->getRoutes();
        } else {
            $this->routes = null;
        }
    }

    private function loadEnv()
    {
        $dotenv_file = $this->root_dir . DIRECTORY_SEPARATOR . ".env";
        if(is_file($dotenv_file)) {
            (new DotEnv($dotenv_file))->load();
        }
        $this->env = new AppEnv();
    }

    private function loadMiddlewares()
    {
        $middlewares_dir = $this->root_dir . DIRECTORY_SEPARATOR . "middlewares";
        $common_middlewares_file = $middlewares_dir . DIRECTORY_SEPARATOR . "common.php";
        if(is_dir($middlewares_dir) && is_file($common_middlewares_file)) {
            $this->middlewares = include $common_middlewares_file;
        } else {
            $this->middlewares = null;
        }
    }

    private function loadViews()
    {
        $view_dir = $this->root_dir . DIRECTORY_SEPARATOR . "views";
        if(is_dir($view_dir)) {
            $this->views = new ViewsStore($view_dir);
        } else {
            $this->views = null;
        }
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
