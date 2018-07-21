<?php

namespace Phlow\Tests\Expression;

use Phlow\Expression\Simple;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{

    public function testToString()
    {
        $simple = new Simple('1 + 1');
        $this->assertEquals('1 + 1', (string) $simple);
    }

    public function testEvaluate()
    {
        $simple = new Simple('1 + 1');
        $this->assertEquals(2, $simple->evaluate());
    }

    public function testObjectContext()
    {
        $context = (object) ['a' => 1, 'b' => 1];
        $simple = new Simple('a + b');
        $this->assertEquals(2, $simple->evaluate($context));
    }

    public function testThisObjectContext()
    {
        $context = (object) ['a' => 1, 'b' => 1];
        $simple = new Simple('this.a + this.b');
        $this->assertEquals(2, $simple->evaluate($context));
    }

    public function testArrayContext()
    {
        $context = ['a' => 1, 'b' => 1];
        $simple = new Simple('a + b');
        $this->assertEquals(2, $simple->evaluate($context));
    }

    public function testThisArrayContext()
    {
        $context = ['a' => 1, 'b' => 1];
        $simple = new Simple('this["a"] + this["b"]');
        $this->assertEquals(2, $simple->evaluate($context));
    }
}
