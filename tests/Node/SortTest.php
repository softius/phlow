<?php

namespace Phlow\Tests\Node;

use Phlow\Expression\Simple;
use Phlow\Node\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function testEmptyCollection()
    {
        $sort = new Sort(new Simple('a - b'));
        $callback = $sort->getCallback();

        $collection = $callback([]);
        $this->assertInstanceOf(\Traversable::class, $collection);
    }

    public function testCollection()
    {
        $sort = new Sort(new Simple('a - b'));
        $callback = $sort->getCallback();

        $collection = [100, 50, 70, 20, 60, 10];
        $this->assertEquals([10, 20, 50, 60, 70, 100], iterator_to_array($callback($collection)->values()));
    }
}
