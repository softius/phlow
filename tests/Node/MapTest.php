<?php

namespace Phlow\Tests\Node;

use Phlow\Expression\Simple;
use Phlow\Node\Map;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testEmptyCollection()
    {
        $map = new Map(new Simple('current * 10'));
        $callback = $map->getCallback();

        $collection = $callback([]);
        $this->assertInstanceOf(\Traversable::class, $collection);
    }

    public function testCollection()
    {
        $map = new Map(new Simple('current * 10'));
        $callback = $map->getCallback();

        $collection = [0, 10, 20, 30];
        $this->assertEquals([0, 100, 200, 300], iterator_to_array($callback($collection)));
    }
}
