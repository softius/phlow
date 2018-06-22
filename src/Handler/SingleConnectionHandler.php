<?php

namespace Phlow\Handler;

use Phlow\Engine\Exchange;
use Phlow\Model\WorkflowConnection;
use Phlow\Model\WorkflowNode;

/**
 * Class SingleConnectionHandler
 * Suggests the next WorkflowNode by taking the first of the outgoing connections
 * @package Phlow\Handler
 */
class SingleConnectionHandler implements Handler
{
    /**
     * Suggests the next WorkflowNode by taking the first of the outgoing connections.
     * If there are more than one outgoing connection, they will be ignored.
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowNode
     */
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
