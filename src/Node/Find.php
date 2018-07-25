<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;

/**
 * Class Find
 * Keeps only the first element that satisfies the predicate.
 * Upon processing, the Exchange will hold a single element (not a Collection).
 * @package Phlow\Node
 */
class Find extends AbstractExecutableNode implements Action, Executable
{
    public function __construct(Expression $expression = null)
    {
        if (!empty($expression)) {
            $this->wrapCallback(
                '\DusanKasan\Knapsack\find',
                [
                    function ($current, $key) use ($expression) {
                        return $expression->evaluate(['current' => $current, 'key' => $key]);
                    }
                ]
            );
        }
    }
}
