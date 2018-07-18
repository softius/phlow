<?php

namespace Phlow\Processor;

use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Node\Node;

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
     * @param Node $workflowNode
     * @param Exchange $exchange
     * @return Connection
     */
    public function process(Node $workflowNode, Exchange $exchange): Connection;
}
