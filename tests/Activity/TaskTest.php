<?php

namespace Phlow\Tests\Activity;

use Phlow\Activity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testWithCallback()
    {
        $task = new Task();
        $cb = function ($d) {
            return $d;
        };
        $task->addCallback($cb);

        $this->assertTrue($task->hasCallback());
        $this->assertEquals($cb, $task->getCallback());
    }

    public function testWithoutCallbacks()
    {
        $task = new Task();
        $this->assertFalse($task->hasCallback());
        $this->expectException(\RuntimeException::class);
        $task->getCallback();
    }
}
