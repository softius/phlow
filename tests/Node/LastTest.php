<?php

namespace Phlow\Tests\Node;

use DusanKasan\Knapsack\Exceptions\ItemNotFound;
use Phlow\Node\Last;
use PHPUnit\Framework\TestCase;

class LastTest extends TestCase
{
    public function testEmptyCollection()
    {
        $last = new Last();
        $callback = $last->getCallback();

        $this->expectException(ItemNotFound::class);
        $callback([]);
    }

    public function testCollection()
    {
        $last = new Last();
        $callback = $last->getCallback();

        $collection = [0, 10, 20];
        $this->assertEquals(20, $callback($collection));
    }
}
