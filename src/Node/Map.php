<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;

/**
 * Class Map
 * Iterates through the collection and passed each value to the given callback.
 * The callback must return the new instance of the item, thus creating a new collection of items.
 * @package Phlow\Node
 */
class Map extends AbstractExecutableNode implements Action, Executable
{
    public function __construct(Expression $expression = null)
    {
        if (!empty($expression)) {
            $this->wrapCallback(
                '\DusanKasan\Knapsack\map',
                [
                    function ($current, $key) use ($expression) {
                        return $expression->evaluate(['current' => $current, 'key' => $key]);
                    }
                ]
            );
        }
    }
}
