<?php

namespace Phlow\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class Simple
 * @package Phlow\Expression
 */
class Simple implements Expression
{
    /**
     * @var ExpressionLanguage
     */
    private $language;

    /**
     * @var string
     */
    private $expression;

    public function __construct(string $expression, ExpressionLanguage $language = null)
    {
        $this->expression = $expression;
        $this->language = $language ?? new ExpressionLanguage();
    }

    /**
     * Evaluates this predicate on the given argument.
     * @param $context
     * @return mixed
     */
    public function evaluate($context = null)
    {
        return $this->language->evaluate($this->expression, (array) $context);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->expression;
    }
}
