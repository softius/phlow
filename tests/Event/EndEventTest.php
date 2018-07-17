<?php

namespace Phlow\Tests\Event;

use Phlow\Engine\Exchange;
use Phlow\Processor\SingleConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Node\End;
use PHPUnit\Framework\TestCase;

class EndEventTest extends TestCase
{
    public function testNext()
    {
        $task = new End();
        $handler = new SingleConnectionProcessor();

        $this->expectException(UnmatchedConditionException::class);
        $handler->process($task, new Exchange());
    }
}
