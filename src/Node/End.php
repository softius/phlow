<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class End
 * Represents the result of the process and indicates that the workflow has reached the completion phase.
 * @package Phlow\Node
 */
class End extends AbstractNode implements Event
{
    use RenderableObject;
}
