<?php
namespace Phlow\Workflow;

/**
 * Interface WorkflowStep
 * @package Phlow\Workflow
 */
interface WorkflowStep {
    /**
     * Executes this step and update the provided workflow message
     * @param $in object Inbound message
     * @return object Outbound message
     */
    public function execute($in);

    /**
     * Returns the next workflow step
     * @return WorkflowStep
     */
    public function next();
}