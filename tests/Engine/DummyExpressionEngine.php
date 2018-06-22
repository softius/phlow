<?php

namespace Phlow\Tests\Engine;

use \Phlow\Engine\ExpressionEngine;

/**
 * Class DummyExpressionEngine
 * Dummy Expression Engine that always returns 'DUMMY'
 * @package Phlow\Tests\Engine
 */
class DummyExpressionEngine extends ExpressionEngine
{
    public function evaluate(string $expression, array $context)
    {
        return 'DUMMY';
    }
}
