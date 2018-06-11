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
     * @var Exchange Last exchange between workflow steps
     */
    private $exchange;

    /**
     * @var array Unordered list of the steps, that composite this workflow.
     */
    private $steps;

    /**
     * @var WorkflowStep Last executed step
     */
    private $currentStep;

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
     * @param $inbound array|object
     */
    public function __construct($inbound)
    {
        $this->exchange = new Exchange($inbound);
        $this->steps = [];
        $this->currentStep = $this->errorEvent = $this->startEvent = null;
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

    /**
     * Proceeds to the next workflow step and execute it
     * @param int $howMany
     * @return Exchange
     */
    public function advance($howMany = 1)
    {
        if ($this->isCompleted()) {
            throw new \RuntimeException("Workflow has been already completed.");
        }

        // Retrieve and execute the next step
        $step = $this->next();
        if ($step instanceof ExecutableStep) {
            $this->exchange->setOut(
                $step->execute($this->exchange->in())
            );

            // Prepare an exchange for the next step
            $this->exchange = new Exchange($this->exchange->out());
        }

        $this->currentStep = $step;
        return $howMany === 1 ? $this->exchange->in() : $this->advance($howMany - 1);
    }

    /**
     * Finds and return the next step to be executed
     * @return WorkflowStep
     */
    private function next()
    {
        if ($this->currentStep === null && $this->startEvent === null) {
            throw new \RuntimeException('Start event is missing');
        }

        $this->currentStep = $this->currentStep ?? $this->startEvent;

        return $this->currentStep->next(
            $this->exchange->in()
        );
    }

    /**
     * Returns true only and only if the execution has reached and End event.
     * @return bool
     */
    public function isCompleted()
    {
        return $this->currentStep instanceof EndEvent;
    }

    public function current()
    {
        return $this->currentStep;
    }
}
