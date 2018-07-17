<?php

namespace Phlow\Tests\Activity;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Processor\CallbackProcessor;
use Phlow\Event\StartEvent;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class TaskHandlerTest extends TestCase
{
    public function testWithoutCallback()
    {
        $task = new Task();
        $nextTask = new Task();
        $connection = new WorkflowConnection($task, $nextTask, WorkflowConnection::LABEL_NEXT);

        $handler = new CallbackProcessor();
        $actualConnection = $handler->handle($task, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testWithCallback()
    {
        $task = new Task();
        $nextTask = new Task();
        $connection = new WorkflowConnection($task, $nextTask, WorkflowConnection::LABEL_NEXT);

        $task->addCallback(function ($in) {
            $in['num']++;
            return $in;
        });

        $handler = new CallbackProcessor();
        $exchange = new Exchange(['num' => 1]);
        $actualConnection = $handler->handle($task, $exchange, WorkflowConnection::LABEL_NEXT);
        $this->assertEquals($connection, $actualConnection);
        $this->assertEquals(2, $exchange->getOut()['num']);
    }

    public function testSuccess()
    {
        $task = new Task();
        $nextTask = new Task();
        $connection = new WorkflowConnection($task, $nextTask, WorkflowConnection::LABEL_NEXT);

        $handler = new CallbackProcessor();
        $actualConnection = $handler->handle($task, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testErrorHandling()
    {
        $task = new Task();
        $task->addCallback(function ($in) {
            throw new \Exception("testErrorHandling");
        });

        $handler = new CallbackProcessor();
        $this->expectExceptionMessage("testErrorHandling");
        $handler->handle($task, new Exchange());
    }

    public function testInvalidArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CallbackProcessor())->handle(new StartEvent(), new Exchange());
    }
}
