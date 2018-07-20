<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function \DusanKasan\Knapsack\filter;

/**
 * Class Filter
 * Keeps all the elements of the collection that satisfy the given predicate.
 * The order of the elements is preserved.
 * Upon processing the Exchange will hold a lazy Collection
 * @package Phlow\Node
 */
class Filter extends AbstractNode implements Action, Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $filter = null)
    {
        $this->addCallback(function ($collection) use ($filter) {
            return filter(
                $collection,
                function ($current, $key) use ($filter) {
                    return $filter->evaluate(['current' => $current, 'key' => $key]);
                }
            );
        });
    }
}
