<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function \DusanKasan\Knapsack\find;

class Find extends AbstractNode implements Executable
{
    use RenderableObject;
    use ExecutableTrait;

    public function __construct(Expression $filter = null)
    {
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
