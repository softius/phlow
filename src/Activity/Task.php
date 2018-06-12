<?php

namespace Phlow\Activity;

use Phlow\Workflow\WorkflowNode;

/**
 * Class Task
 * An atomic event within a workflow.
 * @package Phlow\Activity
 */
class Task implements Activity
{
    private $handler;

    private $exceptionObject;

    private $nextNode;

    private $exceptionNode;

    public function __construct(callable $handler, WorkflowNode $nextNode = null, WorkflowNode $exceptionNode = null)
    {
        $this->handler = $handler;
        $this->nextNode = $nextNode;
        $this->exceptionNode = $exceptionNode;
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

    public function next($message = null)
    {
        return $this->exceptionObject === null ? $this->nextNode : $this->exceptionNode;
    }
}
