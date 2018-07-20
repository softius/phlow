<?php

namespace Phlow\Expression;

use Phlow\Model\WorkflowObject;

/**
 * Interface Simple
 * An expression provides a plugin strategy for evaluating expressions
 * to support scripting languages, as well as PHP Callable.
 * @package Phlow\Model
 */
interface Expression extends WorkflowObject
{
    /**
     * Evaluates this predicate on the given argument.
     * @param $context
     * @return mixed
     */
    public function evaluate($context = null);
}
