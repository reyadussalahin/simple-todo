<?php declare(strict_types=1);

namespace App;

use App\Contracts\BootstrapperInterface;
use App\Contracts\GlobalsInterface;
use App\Contracts\Routing\RoutesStoreInterface;
use App\Util\DotEnv;
use App\AppEnv;
use App\Globals;
use App\Routing\RoutesProcessor;
use App\Routing\RoutesStore;
use App\Templating\ViewsStore;
use App\Database\Database;


class Bootstrapper implements BootstrapperInterface
{
    /**
     * Points to applications root directory
     * 
     * @var string 
     */
    protected $root_dir;


    /**
     * Store all routes
     * 
     * @var App\Contracts\Routing\RoutesStoreInterface
     */
    protected $routes;


    /**
     * Provides access to Environment variables
     * 
     * @var App\AppEnv
     */
    protected $env;


    /**
     * Provides access to global variables
     *
     * @var App\Contracts\GlobalsInterface
     */
    protected $globals;

    
    /**
     * Provides list of generic middlewares
     *
     * @var array
     */
    protected $middlewares;


    /**
     * Provides access to view files
     *
     * @var App\Templating\ViewsStore
     */
    protected $views;


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
    protected $db;



    /**
     * Creates Bootstrapper object
     *
     * While creating Bootstrapper object it parses and loads
     *
     * all necessary settings needed by application
     * 
     * @param string $root_dir
     * @return void
     */
    public function __construct(string $root_dir)
    {
        // supressing session start errors
        // it won't cause any problem in running application
        // it's good for testing purposes
        @session_start();

        // the following load orders must be maintained
        // cause, some basic settings are needed by other settings
        $this->root_dir = $root_dir;
        $this->loadEnv();
        $this->loadGlobals();
        $this->loadDb();
        $this->loadMiddlewares();
        $this->loadRoutes();
        $this->loadViews();
    }

    /**
     * Parses and loads environment variables from .env files
     *
     * It also assigns a AppEnv object to $this->env which is
     *
     * returned when $this->env() is called.
     * 
     * @return void
     */
    protected function loadEnv()
    {
        // find dotenv file
        $dotenv_file = $this->root_dir . DIRECTORY_SEPARATOR . ".env";
        if(is_file($dotenv_file)) {
            (new DotEnv($dotenv_file))->load();
        }
        // if no such file exists then no need to load
        // finally, set env to appenv object
        $this->env = new AppEnv();
    }

    /**
     * Loads globals into a object of App\Globals
     *
     * It also assigns the App\Globals object to $this->globals which is
     *
     * returned when $this->globals() is called.
     * 
     * @return void
     */
    protected function loadGlobals()
    {
        $this->globals = new Globals();
    }

    /**
     * Creates a App\Database\Database object by passing DATABASE_URL env var
     *
     * It also assigns the App\Database\Database object to $this->db which is
     *
     * returned when $this->db() is called.
     * 
     * @return void
     */
    protected function loadDb()
    {
        $db_url = getenv("DATABASE_URL");
        if($db_url === false) {
            // $db_url = "postgres://nouser:nopass@nohost:5432/nodb";
            // its okay if no database url is provided
            // then just set db to null
            $this->db = null;
        } else{
            $this->db = new Database($db_url);
        }
    }

    /**
     * Loads middlewares into an array from conventional middlewares directory
     *
     * It assigns the array to $this->middlewares which is
     *
     * returned when $this->middlewares() is called.
     * 
     * @return void
     */
    protected function loadMiddlewares()
    {
        $middlewares_dir = $this->root_dir . DIRECTORY_SEPARATOR . "middlewares";
        $common_middlewares_file = $middlewares_dir . DIRECTORY_SEPARATOR . "common.php";
        if(is_dir($middlewares_dir) && is_file($common_middlewares_file)) {
            $this->middlewares = include $common_middlewares_file;
        } else {
            // its okay if there is not middlewares directory
            // then just set it to an empty array
            $this->middlewares = [];
        }
    }

    /**
     * Loads routes into a App\Routing\RoutesStore object
     * 
     * by passing the path of conventional routes directory
     *
     * in a App\Routing\RoutesProcessor object's constructor
     *
     * and calling its getRoutes method
     *
     * It assigns the App\Routing\RoutesStore object to $this->routes which is
     *
     * returned when $this->routes() is called.
     * 
     * @return void
     */
    protected function loadRoutes()
    {
        $routes_dir = $this->root_dir . DIRECTORY_SEPARATOR . "routes";
        // routes_dir must be directory
        if(is_dir($routes_dir)) {
            $routesProcessor = new RoutesProcessor($routes_dir);
            $this->routes = $routesProcessor->getRoutes();
        } else {
            // its okay if not routes dir found
            // then just set routes to null
            $this->routes = null;
        }
    }

    /**
     * Loads views into a App\Templating\ViewsStore object
     * 
     * by passing the path of conventional views directory
     *
     * in a App\Templating\ViewsStore object's constructor
     *
     * It assigns the App\Templating\ViewsStore object to $this->views which is
     *
     * returned when $this->views() is called.
     * 
     * @return void
     */
    protected function loadViews()
    {
        $view_dir = $this->root_dir . DIRECTORY_SEPARATOR . "views";
        if(is_dir($view_dir)) {
            $this->views = new ViewsStore($view_dir);
        } else {
            // its okay if there is no view directory
            // then just set views to null
            $this->views = null;
        }
    }

    /**
     * Returns loaded App\AppEnv object
     *
     * @return App\AppEnv
     */
    public function env()
    {
        return $this->env;
    }

    /**
     * Returns loaded App\Globals object
     *
     * @return App\GlobalsInterface
     */
    public function globals(): GlobalsInterface
    {
        return $this->globals;
    }

    /**
     * Returns loaded App\Database\Database object
     *
     * @return App\Database\Database
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * Returns loaded middlewares
     *
     * @return array
     */
    public function middlewares()
    {
        return $this->middlewares;
    }

    /**
     * Returns loaded App\Routing\RoutesStore object
     *
     * @return App\Routing\RoutesStoreInterface
     */
    public function routes(): RoutesStoreInterface
    {
        return $this->routes;
    }


    /**
     * Returns loaded App\Templating\ViewsStore object
     *
     * @return App\Templating\ViewsStore
     */
    public function views(): ViewsStore
    {
        return $this->views;
    }
}
