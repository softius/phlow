<?php
/**
 * Created by PhpStorm.
 * User: iconstantinou
 * Date: 12/7/18
 * Time: 10:57 AM
 */

namespace Phlow\Engine;

use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use PHPUnit\Framework\TestCase;

class ExecutionPathTest extends TestCase
{

    public function testGetIterator()
    {
        $nodes = [new StartEvent(), new EndEvent()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertEquals($nodes, iterator_to_array($path->getIterator()));
    }

    public function testContains()
    {
        $nodes = [new StartEvent(), new EndEvent(), new EndEvent()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertTrue($path->contains($nodes[1]));
        $this->assertFalse($path->contains($nodes[2]));
    }

    public function testAddCount()
    {
        $nodes = [new StartEvent(), new EndEvent()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertEquals(count($nodes), count($path));
    }
}
