<?php

namespace Phlow\Engine;

/**
 * Class EchoExpressionEngine
 * An Expression Engine that always returns back the provided expression
 * @package Phlow\Tests\Engine
 */
class EchoExpressionEngine extends ExpressionEngine
{
    public function evaluate(string $expression, array $context)
    {
        return $expression;
    }
}
