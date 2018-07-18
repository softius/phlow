<?php

namespace Phlow\Processor;

use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Node\Node;

/**
 * Class NextConnectionProcessor
 * Suggests the next Node by taking the first of the outgoing connections
 * @package Phlow\Processor
 */
class NextConnectionProcessor implements Processor
{

    /**
     * Suggests the next Node by taking the first of the outgoing connections.
     * If there are more than one outgoing connection, they will be ignored.
     * @param Node $workflowNode
     * @param Exchange $exchange
     * @return Connection
     */
    public function process(Node $workflowNode, Exchange $exchange): Connection
    {
        if ($workflowNode->hasOutgoingConnections(Connection::LABEL_PARENT)) {
            $connections = $workflowNode->getOutgoingConnections(Connection::LABEL_PARENT);
            /** @var Connection $connection */
            $connection = $connections[0];
            return (new NextConnectionProcessor())->process($connection->getTarget(), $exchange);
        }

        if ($workflowNode->hasOutgoingConnections(Connection::LABEL_NEXT)) {
            $connections = $workflowNode->getOutgoingConnections(Connection::LABEL_NEXT);
            return $connections[0];
        }

        throw new UnmatchedConditionException('No condition was matched');
    }
}
