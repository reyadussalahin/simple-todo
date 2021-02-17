<?php declare(strict_types=1);

namespace App\Contracts;


interface GlobalsInterface {
    public function all();
    public function globals();
    public function server();
    public function get();
    public function post();
    public function files();
    public function cookie();
    public function session();
    public function request();
    public function env();
}
