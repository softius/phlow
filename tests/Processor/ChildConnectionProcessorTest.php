<?php

namespace Phlow\Tests\Processor;

use Phlow\Connection\Connection;
use Phlow\Engine\EchoExpressionEngine;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Expression\Simple;
use Phlow\Node\Choice;
use Phlow\Node\Fake;
use Phlow\Processor\ChildConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use PHPUnit\Framework\TestCase;

class ChildConnectionProcessorTest extends TestCase
{

    public function testProcess()
    {
        $next = new Fake();
        $next2 = new Fake();

        $choice = new Choice();
        $connection1 = new Connection(
            $choice,
            $next,
            Connection::LABEL_CHILD,
            new Simple('num < 10')
        );
        $connection2 = new Connection(
            $choice,
            $next2,
            Connection::LABEL_CHILD,
            new Simple('num > 100')
        );

        $processor = new ChildConnectionProcessor();

        $exchange = new Exchange((object) ['num' => 5]);
        $this->assertEquals($connection1, $processor->process($choice, $exchange));

        $exchange = new Exchange((object) ['num' => 50]);
        $this->expectException(UnmatchedConditionException::class);
        $this->assertEquals($connection1, $processor->process($choice, $exchange));

        $exchange = new Exchange((object) ['num' => 500]);
        $this->assertEquals($connection2, $processor->process($choice, $exchange));
    }
}
