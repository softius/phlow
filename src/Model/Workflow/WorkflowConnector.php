<?php

namespace Phlow\Model\Workflow;

class WorkflowConnector
{
    private $source;
    private $target;
    private $condition;

    public function __construct(WorkflowNode $source, WorkflowNode $target, $condition = true)
    {
        $this->source = $source;
        $this->target = $target;
        $this->condition = $condition;
    }

    /**
     * @return WorkflowNode
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return WorkflowNode
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Evaluates the provided condition using the provided message
     * @param $message
     * @return WorkflowNode
     */
    public function evaluate($message)
    {
        if (is_callable($this->condition)) {
            $func = $this->condition;
            return $func($message);
        }

        throw new \RuntimeException("Unable to evaluate the provided condition.");
    }
}
