<?php

namespace Phlow\Tests\Callback;

use Phlow\Node\Callback;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    public function testWithCallback()
    {
        $callback = new Callback();
        $cb = function ($d) {
            return $d;
        };
        $callback->addCallback($cb);

        $this->assertTrue($callback->hasCallback());
        $this->assertEquals($cb, $callback->getCallback());
    }

    public function testWithoutCallbacks()
    {
        $callback = new Callback();
        $this->assertFalse($callback->hasCallback());
        $this->expectException(\UnexpectedValueException::class);
        $callback->getCallback();
    }
}
