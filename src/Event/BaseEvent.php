<?php

namespace Phlow\Event;

use Phlow\Workflow\WorkflowNode;

abstract class BaseEvent implements Event
{
    private $nextStep;

    public function __construct(WorkflowNode $nextStep)
    {
        $this->nextStep = $nextStep;
    }

    public function next($message = null)
    {
        return $this->nextStep;
    }
}
