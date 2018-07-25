<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function \DusanKasan\Knapsack\find;

/**
 * Class Find
 * Keeps only the first element that satisfies the predicate.
 * Upon processing, the Exchange will hold a single element (not a Collection).
 * @package Phlow\Node
 */
class Find extends AbstractNode implements Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $filter = null)
    {
        if (empty($filter)) {
            return;
        }

        $this->addCallback(function ($collection) use ($filter) {
            return find(
                $collection,
                function ($current, $key) use ($filter) {
                    return $filter->evaluate(['current' => $current, 'key' => $key]);
                }
            );
        });
    }
}
