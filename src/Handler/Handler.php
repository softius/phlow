<?php

namespace Phlow\Handler;

use Phlow\Engine\Exchange;
use Phlow\Model\WorkflowNode;

/**
 * Interface Handler
 * A Handler is being used to execute (or abort) a Workflow Node
 * @package Phlow\Engine\Handler
 */
interface Handler
{
    /**
     * It processes the provided Workflow Node by injecting the provided Exchange and
     * and executing all the necessary actions supported by the Node.
     * Then it calculates and returns the next Node
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowNode
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode;
}
