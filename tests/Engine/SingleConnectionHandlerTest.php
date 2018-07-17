<?php

namespace Phlow\Tests\Engine;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Processor\SingleConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class SingleConnectionHandlerTest extends TestCase
{
    public function testNextConnection()
    {
        $task = new Task();
        $nextTask = new Task();
        $anotherTask = new Task();
        $connection1 = new WorkflowConnection($task, $nextTask, WorkflowConnection::LABEL_NEXT);
        $connection2 = new WorkflowConnection($task, $anotherTask, WorkflowConnection::LABEL_NEXT);

        $handler = new SingleConnectionProcessor();
        $actualConnection = $handler->handle($task, new Exchange());
        $this->assertEquals($connection1, $actualConnection);
    }

    public function testParentConnection()
    {
        $task = new Task();
        $parentTask = new Task();
        $nextTask = new Task();
        $parentNextTask = new Task();
        $connection1 = new WorkflowConnection($task, $parentTask, WorkflowConnection::LABEL_PARENT);
        $connection2 = new WorkflowConnection($task, $nextTask, WorkflowConnection::LABEL_NEXT);
        $connection3 = new WorkflowConnection($parentTask, $parentNextTask, WorkflowConnection::LABEL_NEXT);

        $handler = new SingleConnectionProcessor();
        $actualConnection = $handler->handle($task, new Exchange());
        $this->assertEquals($connection3, $actualConnection);
    }

    public function testNoConnection()
    {
        $handler = new SingleConnectionProcessor();
        $this->expectException(UnmatchedConditionException::class);
        $handler->handle(new Task(), new Exchange());
    }
}
