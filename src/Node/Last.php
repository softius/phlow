<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Last
 * Keeps only the last element of the collection.
 * Upon processing the Exchange will hold a single element (not a Collection).
 * @package Phlow\Node
 */
class Last extends AbstractNode implements Action, Executable
{
    use ExecutableTrait;
    use RenderableObject;

    public function __construct()
    {
        $this->addCallback('\DusanKasan\Knapsack\last');
    }
}
