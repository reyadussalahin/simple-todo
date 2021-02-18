<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Contracts\GlobalsInterface;
use App\Globals;


class GlobalsTest extends TestCase
{
    public function testInterface()
    {
        $g = new Globals();
        $this->assertEquals(true, $g instanceof GlobalsInterface);
    }

    public function testAll()
    {
        global $GLOBALS;
        $GLOBALS["test-key"] = "test-val";
        global $_SERVER;
        $_SERVER["server-key"] = "server-val";

        $g = new Globals();
        $this->assertEquals($g->all()["globals"]["test-key"], "test-val");
        $this->assertEquals($g->all()["server"]["server-key"], "server-val");
    }

    public function testSession()
    {
        global $_SESSION;
        $_SESSION["key"] = "val";
        $g = new Globals();
        $this->assertEquals($g->session()["key"], "val");
        $g->session()["another-key"] = "another-val";
        $this->assertEquals($g->session()["another-key"], "another-val");
    }
}
