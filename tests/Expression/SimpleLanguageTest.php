<?php

namespace Phlow\Tests\Expression;

use Phlow\Expression\SimpleLanguage;
use PHPUnit\Framework\TestCase;

class SimpleLanguageTest extends TestCase
{
    public function testEmpty()
    {
        $language = new SimpleLanguage();
        $this->assertTrue($language->evaluate('empty(a)', ['a' => null]));
        $this->assertFalse($language->evaluate('empty(a)', ['a' => 1]));
    }
}
