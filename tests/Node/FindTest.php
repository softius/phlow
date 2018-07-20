<?php

namespace Phlow\Tests\Node;

use Phlow\Expression\Simple;
use Phlow\Node\Find;
use PHPUnit\Framework\TestCase;

class FindTest extends TestCase
{
    public function testEmptyCollection()
    {
        $find = new Find(new Simple('current > 15'));
        $callback = $find->getCallback();

        $this->assertNull($callback([]));
    }

    public function testCollection()
    {
        $find = new Find(new Simple('current > 15'));
        $callback = $find->getCallback();

        $collection = [0, 10, 20, 30];
        $this->assertEquals(20, $callback($collection));
    }
}
