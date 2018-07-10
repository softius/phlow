<?php

namespace Phlow\Tests\Activity;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Handler\ExecutableHandler;
use Phlow\Event\StartEvent;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class TaskHandlerTest extends TestCase
{
    public function testWithoutCallback()
    {
        $task = new Task();
        $nextTask = new Task();
        new WorkflowConnection($task, $nextTask);

        $handler = new ExecutableHandler();
        $actualNextTask = $handler->handle($task, new Exchange());
        $this->assertEquals($nextTask, $actualNextTask);
    }

    public function testWithCallback()
    {
        $task = new Task();
        $nextTask = new Task();
        new WorkflowConnection($task, $nextTask);

        $task->addCallback(function ($in) {
            $in['num']++;
            return $in;
        });

        $handler = new ExecutableHandler();
        $exchange = new Exchange(['num' => 1]);
        $actualNextTask = $handler->handle($task, $exchange);
        $this->assertEquals($nextTask, $actualNextTask);
        $this->assertEquals(2, $exchange->getOut()['num']);
    }

    public function testSuccess()
    {
        $task = new Task();
        $nextTask = new Task();
        new WorkflowConnection($task, $nextTask);

        $handler = new ExecutableHandler();
        $actualNextTask = $handler->handle($task, new Exchange());
        $this->assertEquals($nextTask, $actualNextTask);
    }

    public function testErrorHandling()
    {
        $task = new Task();
        $task->addCallback(function ($in) {
            throw new \Exception("testErrorHandling");
        });

        $handler = new ExecutableHandler();
        $this->expectExceptionMessage("testErrorHandling");
        $handler->handle($task, new Exchange());
    }

    public function testInvalidArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ExecutableHandler())->handle(new StartEvent(), new Exchange());
    }
}
