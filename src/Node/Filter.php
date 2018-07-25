<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;

/**
 * Class Filter
 * Keeps all the elements of the collection that satisfy the given predicate.
 * The order of the elements is preserved.
 * Upon processing the Exchange will hold a lazy Collection
 * @package Phlow\Node
 */
class Filter extends AbstractExecutableNode implements Action, Executable
{
    public function __construct(Expression $expression = null)
    {
        if (!empty($expression)) {
            $this->wrapCallback(
                '\DusanKasan\Knapsack\filter',
                [
                    function ($current, $key) use ($expression) {
                        return $expression->evaluate(['current' => $current, 'key' => $key]);
                    }
                ]
            );
        }
    }
}
