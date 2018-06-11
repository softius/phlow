<?php
namespace Phlow\Workflow;

/**
 * Interface WorkflowStep
 * @package Phlow\Workflow
 */
interface WorkflowStep
{
    /**
     * Returns the next workflow step
     * @param $message
     * @return WorkflowStep
     */
    public function next($message = null);
}
