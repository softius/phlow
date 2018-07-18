<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Choice
 * Represents alternative flows in a process. Only one of the alternative paths can be taken.
 * @package Phlow\Node
 */
class Choice extends AbstractNode implements Conditional
{
    use RenderableObject;
}
