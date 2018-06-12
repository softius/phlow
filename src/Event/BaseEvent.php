<?php

namespace Phlow\Event;

use Phlow\Workflow\WorkflowNode;

abstract class BaseEvent implements Event
{
    private $nextNode;

    public function __construct(WorkflowNode $nextNode)
    {
        $this->nextNode = $nextNode;
    }

    public function next($message = null)
    {
        return $this->nextNode;
    }
}
