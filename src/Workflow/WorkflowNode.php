<?php
namespace Phlow\Workflow;

/**
 * Interface WorkflowNode
 * @package Phlow\Workflow
 */
interface WorkflowNode
{
    /**
     * Returns the next workflow step
     * @param $message
     * @return WorkflowNode
     */
    public function next($message = null);
}
