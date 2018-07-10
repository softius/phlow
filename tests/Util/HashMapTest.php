<?php

namespace Phlow\Tests\Util;

use Phlow\Util\HashMap;
use PHPUnit\Framework\TestCase;

class HashMapTest extends TestCase
{
    public function testEmpty()
    {
        $map = new HashMap();
        $this->assertTrue($map->isEmpty());
    }

    public function testGettingFromEmpty()
    {
        $map = new HashMap();

        $this->expectException(\UnderflowException::class);
        $map->get(new \stdClass());

        $this->expectException(\UnderflowException::class);
        $this->assertNull($map->get(5));
    }

    public function testPutGet()
    {
        $item1 = 100;
        $item2 = new \stdClass();
        $item3 = new \stdClass();

        $map = new HashMap();
        $map->put($item1, 1);
        $map->put($item2, 2);
        $map->put($item3, 3);

        $this->assertEquals(1, $map->get($item1));
        $this->assertEquals(2, $map->get($item2));
        $this->assertEquals(3, $map->get($item3));
    }

    public function testPutRemove()
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();

        $map = new HashMap();
        $map->put($item1, 1);
        $map->put($item2, 2);

        $map->remove($item1);
        $this->expectException(\UnderflowException::class);
        $map->get($item1);
    }
}
