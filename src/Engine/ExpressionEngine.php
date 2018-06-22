<?php

namespace Phlow\Engine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ExpressionEngine
 * The ExpressionEngine can consume an expression and either evaluate it or produce a callback.
 * @package Phlow\Engine
 */
class ExpressionEngine
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * ExpressionEngine constructor.
     */
    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * Evaluates the provided expression by injecting the provided context.
     * @param string $expression
     * @param array $context
     * @return mixed
     */
    public function evaluate(string $expression, array $context)
    {
        return $this->expressionLanguage->evaluate($expression, $context);
    }

    /**
     * Wraps the specified expression in a callback, so that it can be called at a later stage.
     * The returned Closure accepts only one parameter, which is the context.
     * The same Closure can be invoked more than once, with different contexts.
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
