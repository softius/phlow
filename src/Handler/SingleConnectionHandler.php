<?php

namespace Phlow\Handler;

use Phlow\Engine\Exchange;
use Phlow\Engine\ExecutionPathAwareInterface;
use Phlow\Engine\ExecutionPathAwareTrait;
use Phlow\Model\WorkflowConnection;
use Phlow\Model\WorkflowNode;

/**
 * Class SingleConnectionHandler
 * Suggests the next WorkflowNode by taking the first of the outgoing connections
 * @package Phlow\Handler
 */
class SingleConnectionHandler implements Handler, ExecutionPathAwareInterface
{
    use ExecutionPathAwareTrait;

    /**
     * Suggests the next WorkflowNode by taking the first of the outgoing connections.
     * If there are more than one outgoing connection, they will be ignored.
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowConnection
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowConnection
    {
        $connections = $workflowNode->getOutgoingConnections();
        if (empty($connections)) {
            throw new UnmatchedConditionException('No condition was matched');
        }

        /** @var WorkflowConnection $connection */
        return $connections[0];
    }
}
