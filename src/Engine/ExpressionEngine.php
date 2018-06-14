<?php

namespace Phlow\Engine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ExpressionEngine
 * @package Phlow\Engine
 */
class ExpressionEngine
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @param string $expression
     * @param array $context
     * @return mixed
     */
    public function evaluate(string $expression, array $context)
    {
        return $this->expressionLanguage->evaluate($expression, $context);
    }

    /**
     * Wraps the specified expression in a callback
     * @param $expression
     * @return \Closure
     */
    public function wrap($expression): callable
    {
        return function ($context) use ($expression) {
            $context = (array) $context;
            return $this->expressionLanguage->evaluate($expression, $context);
        };
    }
}
