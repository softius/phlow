<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function DusanKasan\Knapsack\sort;

/**
 * Class Sort
 * Sorts the collection according to the provided expression (compare function).
 * Upon processing, the Exchange will hold a non-lazy Collection.
 * @package Phlow\Node
 */
class Sort extends AbstractNode implements Action, Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $filter = null)
    {
        if (empty($filter)) {
            return;
        }

        $this->addCallback(function ($collection) use ($filter) {
            return sort(
                $collection,
                function ($a, $b) use ($filter) {
                    return $filter->evaluate(['a' => $a, 'b' => $b]);
                }
            );
        });
    }
}
