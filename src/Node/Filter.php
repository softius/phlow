<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use function \DusanKasan\Knapsack\filter as filterCollection;

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

    public function __construct(Expression $expression = null)
    {
        if (empty($expression)) {
            return;
        }

        $this->wrapCallback(
            '\DusanKasan\Knapsack\filter',
            [function ($current, $key) use ($expression) {
                return $expression->evaluate(['current' => $current, 'key' => $key]);
            }]
        );
    }
}
