<?php

namespace Phlow\Workflow;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;

/**
 * Class WorkflowBuilder
 * @package Phlow\Workflow
 */
class WorkflowBuilder
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var ErrorEvent Catch-all errors event
     */
    private $errorEvent;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
        $this->workflow = new Workflow();
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
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
        $this->errorEvent = $this->workflow->add(new ErrorEvent($task));
        return $this->errorEvent;
    }

    /**
     * Creates a Start event for this workflow
     * @param WorkflowStep $nextStep
     * @return WorkflowStep
     */
    public function start(WorkflowStep $nextStep)
    {
        return $this->workflow->add(new StartEvent($nextStep));
    }

    /**
     * Creates an End event for this workflow.
     * @return WorkflowStep
     */
    public function end()
    {
        return $this->workflow->add(new EndEvent());
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
        return ($func === null) ? $this->errorEvent : $this->workflow->add(new ErrorEvent($task));
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
        return $this->workflow->add(new Task($task, $nextStep, $errorStep));
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @return ExclusiveGateway
     */
    public function exclusive()
    {
        return $this->workflow->add(new ExclusiveGateway());
    }
}
