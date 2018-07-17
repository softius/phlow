<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Start
 * Acts as a workflow trigger. A workflow can have only one Start Node.
 * @package Phlow\Node
 */
class Start extends AbstractNode implements Event
{
    use RenderableObject;
}
