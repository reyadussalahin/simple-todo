<?php declare(strict_types=1);

namespace App\Database;

use App\Contracts\Database\DatabaseInterface;


class Database implements DatabaseInterface
{
    private $scheme;
    private $driver;
    private $host;
    private $port;
    private $user;
    private $password;
    private $dbname;
    private $conn;
    private $pdo_options;

    public function __construct()
    {
        $db_url = getenv("DATABASE_URL");
        if($db_url === false) {
            $db_url = "postgres://nouser:nopass@nohost:5432/nodb"
        }
        $options = parse_url($db_url);
        
        $this->scheme = $options["scheme"];
        if($this->scheme === "postgres") {
            $this->driver = "pgsql";
        } else if($this->scheme === "mysql") {
            $this->driver = "mysql";
        } else {
            throw new \Exception("Error Unknown datbase: Driver not found");
        }
        $this->host = $options["host"];
        $this->port = $options["port"];
        $this->user = $options["user"];
        $this->password = $options["pass"];
        $this->dbname = ltrim($options["path"], '/');

        // echo $this->scheme . "<br>";
        // echo $this->driver . "<br>";
        // echo $this->host . "<br>";
        // echo $this->port . "<br>";
        // echo $this->user . "<br>";
        // echo $this->password . "<br>";
        // echo $this->dbname . "<br>";

        // print_r($options);
    }

    public function connection()
    {
        if($this->conn === null) {
            $dsn = $this->driver
                . ":host=" . $this->host
                . ";dbname=" . $this->dbname;
            try {
                $this->conn = new \PDO($dsn,
                    $this->user,
                    $this->password,
                );
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        // // testing
        // $stm = $this->conn->query("SELECT VERSION()");
        // $version = $stm->fetch();
        // echo $version[0] . PHP_EOL;
        
        $this->conn->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );
        
        return $this->conn;
    }
}
