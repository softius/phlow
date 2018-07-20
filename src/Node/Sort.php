<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function DusanKasan\Knapsack\sort;

class Sort extends AbstractNode implements Action, Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $filter = null)
    {
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
