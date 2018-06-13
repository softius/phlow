<?php
namespace Phlow\Workflow;

/**
 * Interface WorkflowNode
 * @package Phlow\Workflow
 */
interface WorkflowNode
{
    /**
     * Returns the next workflow node
     * @param $message
     * @return WorkflowNode
     */
    public function next($message = null): WorkflowNode;
}
