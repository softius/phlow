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

    public function testAddCount()
    {
        $nodes = [new StartEvent(), new EndEvent()];
        $path = new ExecutionPath();
        $path->add($nodes[0]);
        $path->add($nodes[1]);
        $this->assertEquals(count($nodes), count($path));
    }
}
