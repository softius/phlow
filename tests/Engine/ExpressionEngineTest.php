<?php

namespace Phlow\Tests\Engine;

use Phlow\Engine\ExpressionEngine;
use PHPUnit\Framework\TestCase;

class ExpressionEngineTest extends TestCase
{
    public function testEvaluation()
    {
        $engine = new ExpressionEngine();
        $this->assertEquals(2, $engine->evaluate('a+b', ['a' => 1, 'b' => 1]));
    }

    public function testWrap()
    {
        $engine = new ExpressionEngine();
        $func = $engine->wrap('a+b');
        $this->assertEquals(2, $func(['a' => 1, 'b' => 1]));
    }
}
