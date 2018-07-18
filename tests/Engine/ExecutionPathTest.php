<?php

namespace Phlow\Engine;

use Phlow\Node\Fake;
use PHPUnit\Framework\TestCase;

class ExecutionPathTest extends TestCase
{

    public function testGetIterator()
    {
        $nodes = [new Fake(), new Fake()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertEquals($nodes, iterator_to_array($path->getIterator()));
    }

    public function testContains()
    {
        $nodes = [new Fake(), new Fake(), new Fake()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertTrue($path->contains($nodes[1]));
        $this->assertFalse($path->contains($nodes[2]));
    }

    public function testAddCount()
    {
        $nodes = [new Fake(), new Fake()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertEquals(count($nodes), count($path));
    }
}
