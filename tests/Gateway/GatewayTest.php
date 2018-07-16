<?php

namespace Phlow\Tests\Gateway;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Handler\ConditionalConnectionHandler;
use Phlow\Handler\UnmatchedConditionException;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\WorkflowConnection;
use Phlow\Tests\Engine\DummyExpressionEngine;

class GatewayTest extends \PHPUnit\Framework\TestCase
{
    public function testFlows()
    {
        $nextTask = new Task();
        $nextTask2 = new Task();

        $gateway = new ExclusiveGateway();
        $connection1 = new WorkflowConnection($gateway, $nextTask, WorkflowConnection::LABEL_OPEN, 'num < 10');
        $connection2 = new WorkflowConnection($gateway, $nextTask2, WorkflowConnection::LABEL_OPEN, 'num > 100');

        $handler = new ConditionalConnectionHandler();

        $exchange = new Exchange((object) ['num' => 5]);
        $this->assertEquals($connection1, $handler->handle($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 50]);
        $this->expectException(UnmatchedConditionException::class);
        $this->assertEquals($connection1, $handler->handle($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 500]);
        $this->assertEquals($connection2, $handler->handle($gateway, $exchange));
    }

    public function testDefaultExpressionEngine()
    {
        $gateway = new ConditionalConnectionHandler();
        $this->assertTrue($gateway->getExpressionEngine() instanceof ExpressionEngine);
    }

    public function testCustomExpressionEngine()
    {
        $gateway = new ConditionalConnectionHandler();
        $engine = new DummyExpressionEngine();
        $gateway->setExpressionEngine($engine);
        $this->assertEquals($engine, $gateway->getExpressionEngine());
        $this->assertEquals('DUMMY', $gateway->getExpressionEngine()->evaluate('value', ['value' => '100']));
    }
}
