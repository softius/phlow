<?php

namespace Phlow\Event;

use Phlow\Model\RenderableObject;
use Phlow\Node\WorkflowNodeTrait;
use Phlow\Node\AbstractNode;

/**
 * Class StartEvent
 * Acts as a workflow trigger. A workflow can have only one Start Event.
 * @package Phlow\Event
 */
class StartEvent extends AbstractNode implements Event
{
    use RenderableObject;
}
