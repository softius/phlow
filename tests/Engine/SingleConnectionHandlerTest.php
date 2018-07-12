<?php

namespace Phlow\Tests\Engine;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Handler\SingleConnectionHandler;
use Phlow\Handler\UnmatchedConditionException;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class SingleConnectionHandlerTest extends TestCase
{
    public function testConnection()
    {
        $task = new Task();
        $nextTask = new Task();
        $anotherTask = new Task();
        $connection1 = new WorkflowConnection($task, $nextTask);
        $connection2 = new WorkflowConnection($task, $anotherTask);

        $handler = new SingleConnectionHandler();
        $actualConnection = $handler->handle($task, new Exchange());
        $this->assertEquals($connection1, $actualConnection);
    }

    public function testNoConnection()
    {
        $handler = new SingleConnectionHandler();
        $this->expectException(UnmatchedConditionException::class);
        $handler->handle(new Task(), new Exchange());
    }
}
