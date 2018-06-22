<?php

namespace Phlow\Event;

use Phlow\Model\Workflow\WorkflowNodeTrait;

/**
 * Class EndEvent
 * Represents the result of the process and indicates that the workflow has reached the completion phase.
 * @package Phlow\Event
 */
class EndEvent implements Event
{
    use WorkflowNodeTrait;
}
