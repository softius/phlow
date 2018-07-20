<?php

namespace Phlow\Tests\Expression;

use Phlow\Expression\Expression;

class TestExpression implements Expression
{
    /**
     * Evaluates this predicate on the given argument.
     * @param $context
     * @return mixed
     */
    public function evaluate($context = null)
    {
        return $context;
    }
}
