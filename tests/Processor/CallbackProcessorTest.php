<?php

namespace Phlow\Tests\Activity;

use Phlow\Node\Callback;
use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Processor\CallbackProcessor;
use Phlow\Node\Start;
use PHPUnit\Framework\TestCase;

class CallbackProcessorTest extends TestCase
{
    public function testWithoutCallback()
    {
        $callback = new Callback();
        $nextcallback = new Callback();
        $connection = new Connection($callback, $nextcallback, Connection::LABEL_NEXT);

        $processor = new CallbackProcessor();
        $actualConnection = $processor->process($callback, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testWithCallback()
    {
        $callback = new Callback();
        $nextcallback = new Callback();
        $connection = new Connection($callback, $nextcallback, Connection::LABEL_NEXT);

        $callback->addCallback(function ($in) {
            $in['num']++;
            return $in;
        });

        $processor = new CallbackProcessor();
        $exchange = new Exchange(['num' => 1]);
        $actualConnection = $processor->process($callback, $exchange, Connection::LABEL_NEXT);
        $this->assertEquals($connection, $actualConnection);
        $this->assertEquals(2, $exchange->getOut()['num']);
    }

    public function testSuccess()
    {
        $callback = new Callback();
        $nextcallback = new Callback();
        $connection = new Connection($callback, $nextcallback, Connection::LABEL_NEXT);

        $processor = new CallbackProcessor();
        $actualConnection = $processor->process($callback, new Exchange());
        $this->assertEquals($connection, $actualConnection);
    }

    public function testErrorHandling()
    {
        $callback = new Callback();
        $callback->addCallback(function ($in) {
            throw new \Exception("testErrorHandling");
        });

        $processor = new CallbackProcessor();
        $this->expectExceptionMessage("testErrorHandling");
        $processor->process($callback, new Exchange());
    }

    public function testInvalidArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CallbackProcessor())->process(new Start(), new Exchange());
    }
}
