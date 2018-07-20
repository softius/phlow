<?php

namespace Phlow\Tests\Node;

use DusanKasan\Knapsack\Exceptions\ItemNotFound;
use Phlow\Node\First;
use PHPUnit\Framework\TestCase;

class FirstTest extends TestCase
{
    public function testEmptyCollection()
    {
        $first = new First();
        $callback = $first->getCallback();

        $this->expectException(ItemNotFound::class);
        $callback([]);
    }

    public function testCollection()
    {
        $first = new First();
        $callback = $first->getCallback();

        $collection = [0, 10, 20];
        $this->assertEquals(0, $callback($collection));
    }
}
