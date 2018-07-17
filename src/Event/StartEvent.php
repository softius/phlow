<?php

namespace Phlow\Event;

use Phlow\Model\RenderableNode;
use Phlow\Model\WorkflowNodeTrait;

/**
 * Class StartEvent
 * Acts as a workflow trigger. A workflow can have only one Start Event.
 * @package Phlow\Event
 */
class StartEvent implements Event
{
    use WorkflowNodeTrait;
    use RenderableNode;
}
