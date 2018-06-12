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
    private function add(WorkflowStep $step)
    {
        $this->steps[] = $step;
        return $step;
    }

    /**
     * Workflow level error handling.
     * Catches all errors raised by workflow steps.
     * @param callable $func
     * @return ErrorEvent|WorkflowStep
     */
    public function catch(callable $func)
    {
        $task = new Task($func);
        $this->errorEvent = $this->add(new ErrorEvent($task));
        return $this->errorEvent;
    }

    /**
     * Creates a Start event for this workflow
     * @param WorkflowStep $nextStep
     * @return StartEvent|WorkflowStep
     */
    public function start(WorkflowStep $nextStep)
    {
        $this->startEvent = $this->add(new StartEvent($nextStep));
        return $this->startEvent;
    }

    /**
     * Creates an End event for this workflow.
     * @return WorkflowStep
     */
    public function end()
    {
        return $this->add(new EndEvent());
    }

    /**
     * Step level error handling.
     * Creates an Error event for this workflow.
     * @param callable|null $func
     * @return ErrorEvent|WorkflowStep
     */
    public function error(callable $func = null)
    {
        $task = new Task($func);
        return ($func === null) ? $this->errorEvent : $this->add(new ErrorEvent($task));
    }

    /**
     * Creates a Task for this workflow
     * @param callable $task
     * @param WorkflowStep $nextStep
     * @param WorkflowStep|null $errorStep
     * @return WorkflowStep
     */
    public function task(callable $task, WorkflowStep $nextStep, WorkflowStep $errorStep = null)
    {
        $errorStep = $errorStep === null ? $this->errorEvent : $errorStep;
        return $this->add(new Task($task, $nextStep, $errorStep));
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @return ExclusiveGateway
     */
    public function exclusive()
    {
        return $this->add(new ExclusiveGateway());
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
