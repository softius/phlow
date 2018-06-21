<?php

namespace Phlow\Engine\Handler;

use Phlow\Engine\Exchange;
use Phlow\Model\Workflow\WorkflowNode;

interface Handler
{
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode;
}
