<?php

namespace Phlow\Tests\Event;

use Phlow\Engine\Exchange;
use Phlow\Processor\SingleConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Event\EndEvent;
use PHPUnit\Framework\TestCase;

class EndEventTest extends TestCase
{
    public function testNext()
    {
        $task = new EndEvent();
        $handler = new SingleConnectionProcessor();

        $this->expectException(UnmatchedConditionException::class);
        $handler->handle($task, new Exchange());
    }
}
