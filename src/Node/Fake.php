<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Fake
 * A Workflow Node that does absolutely nothing.
 * @package Phlow\Node
 */
class Fake extends AbstractNode implements Node
{
    use RenderableObject;
}
