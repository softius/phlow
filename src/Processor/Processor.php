<?php

namespace Phlow\Processor;

use Phlow\Connection\WorkflowConnection;
use Phlow\Engine\Exchange;
use Phlow\Model\WorkflowNode;

/**
 * Interface Processor
 * A Processor is being used to execute (or abort) a Workflow Node
 * @package Phlow\Engine\Processor
 */
interface Processor
{
    /**
     * It processes the provided Workflow Node by injecting the provided Exchange and
     * and executing all the necessary actions supported by the Node.
     * Then it calculates and returns the next Node
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowConnection
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowConnection;
}
