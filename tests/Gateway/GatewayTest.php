<?php

namespace Phlow\Tests\Gateway;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Engine\Handler\ConditionalConnectionHandler;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\Workflow\WorkflowConnection;

class GatewayTest extends \PHPUnit\Framework\TestCase
{
    public function testFlows()
    {
        $nextTask = new Task();
        $nextTask2 = new Task();

        $gateway = new ExclusiveGateway();
        new WorkflowConnection($gateway, $nextTask, 'num < 10');
        new WorkflowConnection($gateway, $nextTask2, 'num > 100');

        $handler = new ConditionalConnectionHandler();

        $exchange = new Exchange((object) ['num' => 5]);
        $this->assertEquals($nextTask, $handler->handle($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 50]);
        $this->expectException(\RuntimeException::class);
        $this->assertEquals($nextTask, $handler->handle($gateway, $exchange));

        $exchange = new Exchange((object) ['num' => 500]);
        $this->assertEquals($nextTask2, $handler->handle($gateway, $exchange));
    }

    public function testDefaultExpressionEngine()
    {
        $gateway = new ConditionalConnectionHandler();

        $engine = new ExpressionEngine();
        $gateway->setExpressionEngine($engine);
        $this->assertEquals($engine, $gateway->getExpressionEngine());
    }
}
