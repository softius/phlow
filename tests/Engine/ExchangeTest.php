<?php

namespace Phlow\Tests\Engine;

use Phlow\Engine\Exchange;
use PHPUnit\Framework\TestCase;

class ExchangeTest extends TestCase
{

    public function testException()
    {
        $exchange = new Exchange();
        $this->assertFalse($exchange->hasException());
        $this->assertNull($exchange->getException());

        $exception = new \Exception();
        $exchange->setException($exception);
        $this->assertTrue($exchange->hasException());
        $this->assertEquals($exception, $exchange->getException());
    }

    public function testIn()
    {
        $exchange = new Exchange(100);
        $this->assertEquals(100, $exchange->getIn());
    }

    public function testOut()
    {
        $exchange = new Exchange();
        $this->assertFalse($exchange->hasOut());

        $message = 100;
        $exchange->setOut($message);
        $this->assertTrue($exchange->hasOut());
        $this->assertEquals($message, $exchange->getOut());
    }
}
