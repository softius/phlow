<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function DusanKasan\Knapsack\map;

/**
 * Class Map
 * Iterates through the collection and passed each value to the given callback.
 * The callback must return the new instance of the item, thus creating a new collection of items.
 * @package Phlow\Node
 */
class Map extends AbstractNode implements Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $expression = null)
    {
        $this->addCallback(function ($collection) use ($expression) {
            return map(
                $collection,
                function ($current, $key) use ($expression) {
                    return $expression->evaluate(['current' => $current, 'key' => $key]);
                }
            );
        });
    }
}