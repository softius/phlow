<?php

namespace Phlow\Tests\Event;

use Phlow\Event\EndEvent;
use PHPUnit\Framework\TestCase;

class EndEventTest extends TestCase
{
    public function testNext()
    {
        $task = new EndEvent();
        $this->expectException(\RuntimeException::class);
        $task->next();
    }
}
