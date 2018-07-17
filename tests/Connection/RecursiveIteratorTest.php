<?php

namespace Phlow\Tests\Connection;

use Phlow\Connection\Connection;
use Phlow\Connection\RecursiveIterator;
use Phlow\Node\Fake;
use PHPUnit\Framework\TestCase;

class RecursiveIteratorTest extends TestCase
{

    public function testGetChildren()
    {
        $parent = new Fake();
        $child1 = new Fake();
        $child1Next = new Fake();
        $child2 = new Fake();

        $connection1 = new Connection($parent, $child1, Connection::LABEL_CHILD);
        $connection2 = new Connection($parent, $child2, Connection::LABEL_CHILD);
        $connection3 = new Connection($child1, $child1Next, Connection::LABEL_NEXT);

        $itr = new RecursiveIterator($parent);
        $this->assertTrue($itr->hasChildren());
        $this->assertEquals($connection1, $itr->current());
        $this->assertEquals(2, count($itr->getChildren()));
    }
}
