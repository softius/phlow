<?php

namespace Phlow\Tests\Node;

use Phlow\Expression\Simple;
use Phlow\Node\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testEmptyCollection()
    {
        $filter = new Filter(new Simple('current > 15'));
        $callback = $filter->getCallback();

        $collection = $callback([]);
        $this->assertInstanceOf(\Traversable::class, $collection);
    }

    public function testCollection()
    {
        $filter = new Filter(new Simple('current > 15'));
        $callback = $filter->getCallback();

        $collection = [0, 10, 20, 30];
        $this->assertEquals([2 => 20, 3 => 30], iterator_to_array($callback($collection)));
    }
}
