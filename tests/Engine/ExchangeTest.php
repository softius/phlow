<?php

namespace Phlow\Tests\Engine;

use Couchbase\Exception;
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
}
