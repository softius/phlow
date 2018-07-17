<?php

namespace Phlow\Tests\Activity;

use Phlow\Node\Callback;
use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Processor\CallbackProcessor;
use Phlow\Event\StartEvent;
use PHPUnit\Framework\TestCase;

class TaskHandlerTest extends TestCase
{
    public function testWithoutCallback()
    {
        $task = new Callback();
        $nextTask = new Callback();
        $connection = new Connection($task, $nextTask, Connection::LABEL_NEXT);

        $handler = new CallbackProcessor();
        $actualConnection = $handler->process($task, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testWithCallback()
    {
        $task = new Callback();
        $nextTask = new Callback();
        $connection = new Connection($task, $nextTask, Connection::LABEL_NEXT);

        $task->addCallback(function ($in) {
            $in['num']++;
            return $in;
        });

        $handler = new CallbackProcessor();
        $exchange = new Exchange(['num' => 1]);
        $actualConnection = $handler->process($task, $exchange, Connection::LABEL_NEXT);
        $this->assertEquals($connection, $actualConnection);
        $this->assertEquals(2, $exchange->getOut()['num']);
    }

    public function testSuccess()
    {
        $task = new Callback();
        $nextTask = new Callback();
        $connection = new Connection($task, $nextTask, Connection::LABEL_NEXT);

        $handler = new CallbackProcessor();
        $actualConnection = $handler->process($task, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testErrorHandling()
    {
        $task = new Callback();
        $task->addCallback(function ($in) {
            throw new \Exception("testErrorHandling");
        });

        $handler = new CallbackProcessor();
        $this->expectExceptionMessage("testErrorHandling");
        $handler->process($task, new Exchange());
    }

    public function testInvalidArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CallbackProcessor())->process(new StartEvent(), new Exchange());
    }
}
