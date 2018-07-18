<?php

namespace Phlow\Tests\Processor;

use Phlow\Connection\Connection;
use Phlow\Engine\EchoExpressionEngine;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Node\Choice;
use Phlow\Node\Fake;
use Phlow\Processor\ChildConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use PHPUnit\Framework\TestCase;

class ChildConnectionProcessorTest extends TestCase
{

    public function testProcess()
    {
        $nextTask = new Fake();
        $nextTask2 = new Fake();

        $gateway = new Choice();
        $connection1 = new Connection($gateway, $nextTask, Connection::LABEL_CHILD, 'num < 10');
        $connection2 = new Connection($gateway, $nextTask2, Connection::LABEL_CHILD, 'num > 100');

        $processor = new ChildConnectionProcessor();

        $exchange = new Exchange((object) ['num' => 5]);
        $this->assertEquals($connection1, $processor->process($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 50]);
        $this->expectException(UnmatchedConditionException::class);
        $this->assertEquals($connection1, $processor->process($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 500]);
        $this->assertEquals($connection2, $processor->process($gateway, $exchange));
    }

    public function testDefaultExpressionEngine()
    {
        $gateway = new ChildConnectionProcessor();
        $this->assertTrue($gateway->getExpressionEngine() instanceof ExpressionEngine);
    }

    public function testCustomExpressionEngine()
    {
        $gateway = new ChildConnectionProcessor();
        $engine = new EchoExpressionEngine();
        $gateway->setExpressionEngine($engine);
        $this->assertEquals($engine, $gateway->getExpressionEngine());
        $this->assertEquals('value', $gateway->getExpressionEngine()->evaluate('value', ['value' => '100']));
    }
}
