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

    /**
     * Task constructor.
     * @param callable $handler
     * @param WorkflowNode|null $nextNode
     * @param WorkflowNode|null $exceptionNode
     */
    public function __construct(callable $handler, WorkflowNode $nextNode = null, WorkflowNode $exceptionNode = null)
    {
        $this->handler = $handler;
        $this->nextNode = $nextNode;
        $this->exceptionNode = $exceptionNode;
        $this->exceptionObject = null;
    }

    /**
     * @param object $in
     * @return object
     */
    public function execute($in)
    {
        try {
            $func = $this->handler;
            return $func($in);
        } catch (\Exception $e) {
            $this->exceptionObject = $e;
        }
    }

    /**
     * @param null $message
     * @return WorkflowNode
     */
    public function next($message = null): WorkflowNode
    {
        return $this->exceptionObject === null ? $this->nextNode : $this->exceptionNode;
    }
}
