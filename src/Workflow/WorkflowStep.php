<?php
namespace Phlow\Workflow;

/**
 * Interface WorkflowStep
 * @package Phlow\Workflow
 */
interface WorkflowStep {
    /**
     * Returns the next workflow step
     * @return WorkflowStep
     */
    public function next();
}