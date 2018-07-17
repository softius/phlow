<?php

namespace Phlow\Tests\Gateway;

use Phlow\Node\Callback;
use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Processor\ExclusiveGatewayProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Tests\Engine\DummyExpressionEngine;

class GatewayTest extends \PHPUnit\Framework\TestCase
{
    public function testFlows()
    {
        $nextTask = new Callback();
        $nextTask2 = new Callback();

        $gateway = new ExclusiveGateway();
        $connection1 = new Connection($gateway, $nextTask, Connection::LABEL_CHILD, 'num < 10');
        $connection2 = new Connection($gateway, $nextTask2, Connection::LABEL_CHILD, 'num > 100');

        $handler = new ExclusiveGatewayProcessor();

        $exchange = new Exchange((object) ['num' => 5]);
        $this->assertEquals($connection1, $handler->process($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 50]);
        $this->expectException(UnmatchedConditionException::class);
        $this->assertEquals($connection1, $handler->process($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 500]);
        $this->assertEquals($connection2, $handler->process($gateway, $exchange));
    }

    public function testDefaultExpressionEngine()
    {
        $gateway = new ExclusiveGatewayProcessor();
        $this->assertTrue($gateway->getExpressionEngine() instanceof ExpressionEngine);
    }

    public function testCustomExpressionEngine()
    {
        $gateway = new ExclusiveGatewayProcessor();
        $engine = new DummyExpressionEngine();
        $gateway->setExpressionEngine($engine);
        $this->assertEquals($engine, $gateway->getExpressionEngine());
        $this->assertEquals('DUMMY', $gateway->getExpressionEngine()->evaluate('value', ['value' => '100']));
    }
}
