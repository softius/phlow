<?php

namespace Phlow\Expression;

/**
 * Class Callback
 * @package Phlow\Expression
 */
class Callback implements Expression
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Evaluates this predicate on the given argument.
     * @param $context
     * @return mixed
     */
    public function evaluate($context = null)
    {
        return call_user_func_array($this->callback, (array) $context);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'function () {...}';
    }
}
