<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

class First extends AbstractNode implements Action, Executable
{
    use ExecutableTrait;
    use RenderableObject;

    public function __construct()
    {
        $this->addCallback('\DusanKasan\Knapsack\first');
    }
}
