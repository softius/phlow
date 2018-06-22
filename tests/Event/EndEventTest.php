<?php

namespace Phlow\Tests\Event;

use Phlow\Engine\Exchange;
use Phlow\Handler\SingleConnectionHandler;
use Phlow\Event\EndEvent;
use PHPUnit\Framework\TestCase;

class EndEventTest extends TestCase
{
    public function testNext()
    {
        $task = new EndEvent();
        $handler = new SingleConnectionHandler();

        $this->expectException(\RuntimeException::class);
        $handler->handle($task, new Exchange());
    }
}
