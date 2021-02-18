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
}
