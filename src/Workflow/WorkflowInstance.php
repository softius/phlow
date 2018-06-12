<?php

namespace Phlow\Workflow;

use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;

/**
 * Class WorkflowInstance
 * Represents an instance of the provided workflow.
 * @package Phlow\Workflow
 */
class WorkflowInstance
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Exchange Last exchange between workflow steps
     */
    private $exchange;

    /**
     * @var WorkflowStep Last executed step
     */
    private $currentStep;

    /**
     * WorkflowInstance constructor.
     * @param Workflow $workflow
     * @param $inbound
     */
    public function __construct(Workflow $workflow, $inbound)
    {
        $this->workflow = $workflow;
        $this->exchange = new Exchange($inbound);
        $this->currentStep = null;
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
        $startEvents = $this->workflow->getAllByClass(StartEvent::class);
        if ($this->currentStep === null && empty($startEvents)) {
            throw new \RuntimeException('Start event is missing');
        }

        $this->currentStep = $this->currentStep ?? $startEvents[0];

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

    /**
     * Returns the last executed step.
     * @return null|WorkflowStep
     */
    public function current()
    {
        return $this->currentStep;
    }
}
