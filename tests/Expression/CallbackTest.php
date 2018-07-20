<?php

namespace Phlow\Tests\Expression;

use Phlow\Expression\Callback;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{

    public function testToString()
    {
        $callback = new Callback(function () {
            return '1+1';
        });
        $this->assertEquals('function () {...}', (string) $callback);
    }

    public function testEvaluate()
    {
        $callback = new Callback(function () {
            return 1+1;
        });
        $this->assertEquals(2, $callback->evaluate());
    }
}
