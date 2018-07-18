<?php

namespace Phlow\Tests\Node;

use Phlow\Node\Error;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class ErrorTest extends TestCase
{

    public function testNoExceptionClass()
    {
        $error = new Error();
        $this->assertFalse($error->hasExceptionClass());
        $this->expectException(UnexpectedValueException::class);
        $error->getExceptionClass();
    }

    public function testExceptionClass()
    {
        $error = new Error();
        $error->addExceptionClass(\RuntimeException::class);
        $this->assertTrue($error->hasExceptionClass());
        $this->assertEquals(\RuntimeException::class, $error->getExceptionClass());
    }
}
