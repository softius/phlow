<?php

namespace Phlow\Workflow;

use Phlow\Activity\Task;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Event\EndEvent;
use Phlow\Gateway\ExclusiveGateway;

/**
 * Class Workflow
 * @package Phlow\Workflow
 */
class Workflow
{

    /**
     * @var array Unordered list of the steps, that composite this workflow.
     */
    private $steps;

    /**
     * @var StartEvent First event to be executed
     */
    private $startEvent;

    /**
     * @var ErrorEvent Catch-all errors event
     */
    private $errorEvent;

    /**
     * Workflow constructor.
     */
    public function __construct()
    {
        $this->steps = [];
        $this->errorEvent = $this->startEvent = null;
    }

    /**
     * Adds the provided step in the list of steps.
     * Maintains reference for all the steps, that composite this workflow.
     * @param WorkflowStep $step
     * @return WorkflowStep
     */
    public function add(WorkflowStep $step)
    {
        if ($step instanceof StartEvent) {
            $this->startEvent = $step;
        }

        $this->steps[] = $step;
        return $step;
    }

    public function hasStartEvent()
    {
        return !($this->startEvent === null);
    }

    public function getStartEvent()
    {
        return $this->startEvent;
    }
}
