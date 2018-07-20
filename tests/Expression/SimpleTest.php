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
}
