<?php

namespace Phlow\Handler;

use Phlow\Engine\Exchange;
use Phlow\Model\Workflow\WorkflowConnection;
use Phlow\Model\Workflow\WorkflowNode;

class SingleConnectionHandler implements Handler
{
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode
    {
        $connections = $workflowNode->getOutgoingConnections();
        if (empty($connections)) {
            throw new \RuntimeException("No connections found.");
        }

        /** @var WorkflowConnection $connection */
        $connection = $connections[0];
        return $connection->getTarget();
    }
}
