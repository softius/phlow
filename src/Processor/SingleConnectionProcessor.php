<?php

namespace Phlow\Processor;

use Phlow\Engine\Exchange;
use Phlow\Model\WorkflowConnection;
use Phlow\Model\WorkflowNode;

/**
 * Class SingleConnectionProcessor
 * Suggests the next WorkflowNode by taking the first of the outgoing connections
 * @package Phlow\Processor
 */
class SingleConnectionProcessor implements Processor
{

    /**
     * Suggests the next WorkflowNode by taking the first of the outgoing connections.
     * If there are more than one outgoing connection, they will be ignored.
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowConnection
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowConnection
    {
        if ($workflowNode->hasOutgoingConnections(WorkflowConnection::LABEL_PARENT)) {
            $connections = $workflowNode->getOutgoingConnections(WorkflowConnection::LABEL_PARENT);
            /** @var WorkflowConnection $connection */
            $connection = $connections[0];
            return (new SingleConnectionProcessor())->handle($connection->getTarget(), $exchange);
        }

        if ($workflowNode->hasOutgoingConnections(WorkflowConnection::LABEL_NEXT)) {
            $connections = $workflowNode->getOutgoingConnections(WorkflowConnection::LABEL_NEXT);
            return $connections[0];
        }

        throw new UnmatchedConditionException('No condition was matched');
    }
}
