<?php

namespace Phlow\Tests\Engine;

use Phlow\Node\Callback;
use Phlow\Engine\Exchange;
use Phlow\Processor\NextConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Connection\Connection;
use PHPUnit\Framework\TestCase;

class SingleConnectionHandlerTest extends TestCase
{
    public function testNextConnection()
    {
        $task = new Callback();
        $nextTask = new Callback();
        $anotherTask = new Callback();
        $connection1 = new Connection($task, $nextTask, Connection::LABEL_NEXT);
        $connection2 = new Connection($task, $anotherTask, Connection::LABEL_NEXT);

        $handler = new NextConnectionProcessor();
        $actualConnection = $handler->process($task, new Exchange());
        $this->assertEquals($connection1, $actualConnection);
    }

    public function testParentConnection()
    {
        $task = new Callback();
        $parentTask = new Callback();
        $nextTask = new Callback();
        $parentNextTask = new Callback();
        $connection1 = new Connection($task, $parentTask, Connection::LABEL_PARENT);
        $connection2 = new Connection($task, $nextTask, Connection::LABEL_NEXT);
        $connection3 = new Connection($parentTask, $parentNextTask, Connection::LABEL_NEXT);

        $handler = new NextConnectionProcessor();
        $actualConnection = $handler->process($task, new Exchange());
        $this->assertEquals($connection3, $actualConnection);
    }

    public function testNoConnection()
    {
        $handler = new NextConnectionProcessor();
        $this->expectException(UnmatchedConditionException::class);
        $handler->process(new Callback(), new Exchange());
    }
}
