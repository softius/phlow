<?php

namespace Phlow\Tests\Engine;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Handler\SingleConnectionHandler;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class SingleConnectionHandlerTest extends TestCase
{
    public function testConnection()
    {
        $task = new Task();
        $nextTask = new Task();
        $anotherTask = new Task();
        new WorkflowConnection($task, $nextTask);
        new WorkflowConnection($task, $anotherTask);

        $handler = new SingleConnectionHandler();
        $actualNextTask = $handler->handle($task, new Exchange());
        $this->assertEquals($nextTask, $actualNextTask);
    }

    public function testNoConnection()
    {
        $handler = new SingleConnectionHandler();
        $this->expectException(\RuntimeException::class);
        $handler->handle(new Task(), new Exchange());
    }
}
