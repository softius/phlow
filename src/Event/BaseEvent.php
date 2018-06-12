<?php

namespace Phlow\Event;

use Phlow\Workflow\WorkflowNode;

abstract class BaseEvent implements Event
{
    private $nextNode;

    /**
     * BaseEvent constructor.
     * @param WorkflowNode $nextNode
     */
    public function __construct(WorkflowNode $nextNode)
    {
        $this->nextNode = $nextNode;
    }

    /**
     * @param null $message
     * @return WorkflowNode
     */
    public function next($message = null): WorkflowNode
    {
        return $this->nextNode;
    }
}
