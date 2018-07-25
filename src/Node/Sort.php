<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;

/**
 * Class Sort
 * Sorts the collection according to the provided expression (compare function).
 * Upon processing, the Exchange will hold a non-lazy Collection.
 * @package Phlow\Node
 */
class Sort extends AbstractExecutableNode implements Action, Executable
{
    public function __construct(Expression $expression = null)
    {
        if (!empty($expression)) {
            $this->wrapCallback(
                '\DusanKasan\Knapsack\sort',
                [
                    function ($a, $b) use ($expression) {
                        return $expression->evaluate(['a' => $a, 'b' => $b]);
                    }
                ]
            );
        }
    }
}
