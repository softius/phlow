<?php

namespace Phlow\Activity;

use Phlow\Workflow\WorkflowStep;

/**
 * Class Task
 * An atomic event within a workflow.
 * @package Phlow\Activity
 */
class Task implements Activity {
    private $handler;

    private $exceptionObject;

    private $nextStep;

    private $exceptionStep;

    public function __construct(callable $handler, WorkflowStep $nextStep = null, WorkflowStep $exceptionStep = null)
    {
        $this->handler = $handler;
        $this->nextStep = $nextStep;
        $this->exceptionStep = $exceptionStep;
        $this->exceptionObject = null;
    }

    public function execute($in)
    {
        try {
            $func = $this->handler;
            return $func($in);
        } catch (\Exception $e) {
            $this->exceptionObject = $e;
        }
    }

    public function next()
    {
        return $this->exceptionObject === null ? $this->nextStep : $this->exceptionStep;
    }
}