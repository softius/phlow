<?php

namespace Phlow\Event;

use Phlow\Workflow\WorkflowNode;

class EndEvent implements Event
{
    public function next($message = null): WorkflowNode
    {
        throw new \RuntimeException('End event has been reached.');
    }
}
