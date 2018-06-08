<?php

namespace Phlow\Event;

use Phlow\Workflow\WorkflowStep;

class StartEvent implements Event
{
    private $nextStep;

    public function __construct(WorkflowStep $nextStep)
    {
        $this->nextStep = $nextStep;
    }

    public function next()
    {
        return $this->nextStep;
    }
}