<?php

namespace Phlow\Handler;

use Phlow\Engine\Exchange;
use Phlow\Model\Workflow\WorkflowNode;

/**
 * Interface Handler
 * A Handler is being used to execute (or abort) a Workflow Node
 * @package Phlow\Engine\Handler
 */
interface Handler
{
    /**
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowNode
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode;
}
