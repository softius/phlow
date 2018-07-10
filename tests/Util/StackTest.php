<?php

namespace Phlow\Tests\Util;

use Phlow\Util\Stack;
use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testEmpty()
    {
        $stack = new Stack();
        $this->assertTrue($stack->isEmpty());

        $stack->push(100);
        $this->assertFalse($stack->isEmpty());
    }

    public function testPopingFromEmpty()
    {
        $stack = new Stack();

        $this->expectException(\UnderflowException::class);
        $stack->peek();

        $this->expectException(\UnderflowException::class);
        $stack->pop();
    }

    public function testPeek()
    {
        $item = 100;

        $stack = new Stack();
        $stack->push($item);
        $this->assertEquals($item, $stack->peek());
        $this->assertEquals(1, $stack->capacity());
    }

    public function testPop()
    {
        $item = 100;

        $stack = new Stack();
        $stack->push($item);
        $this->assertEquals($item, $stack->pop());
        $this->assertEquals(0, $stack->capacity());
    }
}
