<?php

namespace Phlow\Event;

use Phlow\Model\Workflow\WorkflowNodeTrait;

/**
 * Class StartEvent
 * Acts as a workflow trigger. A workflow can have only one Start Event.
 * @package Phlow\Event
 */
class StartEvent implements Event
{
    use WorkflowNodeTrait;
}
