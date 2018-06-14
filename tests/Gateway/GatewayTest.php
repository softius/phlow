<?php

namespace Phlow\Tests\Gateway;

use Phlow\Activity\Task;
use Phlow\Engine\ExpressionEngine;
use Phlow\Gateway\ExclusiveGateway;

class GatewayTest extends \PHPUnit\Framework\TestCase
{
    public function testFlows()
    {
        $nextTask = new Task(function ($d) {
            return $d;
        });

        $nextTask2 = new Task(function ($d) {
            return $d;
        });

        $gateway = new ExclusiveGateway();
        $gateway->when(function ($d) {
            return $d->num < 10;
        }, $nextTask);

        $gateway->when('num > 100', $nextTask2);

        $d = (object) ['num' => 5];
        $this->assertEquals($nextTask, $gateway->next($d));

        $d = (object) ['num' => 50];
        $this->expectException(\RuntimeException::class);
        $this->assertEquals($nextTask, $gateway->next($d));

        $d = (object) ['num' => 500];
        $this->assertEquals($nextTask2, $gateway->next($d));
    }

    public function testDefaultExpressionEngine()
    {
        $gateway = new ExclusiveGateway();
        $this->assertNotInstanceOf(ExpressionEngine::class, $gateway);

        $engine = new ExpressionEngine();
        $gateway->setExpressionEngine($engine);
        $this->assertEquals($engine, $gateway->getExpressionEngine());
    }
}
