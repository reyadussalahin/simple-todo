<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Contracts\AppInterface;
use App\App;
use App\Bootstrapper;


class AppTest extends TestCase
{
    public function testInterface()
    {
        $app = new App(new Bootstrapper(__DIR__));
        $this->assertTrue($app instanceof AppInterface);
    }

    public function testBs()
    {
        $bs = new Bootstrapper(__DIR__);
        $app = new App($bs);
        $this->assertEquals($app->bs(), $bs);
    }
}
