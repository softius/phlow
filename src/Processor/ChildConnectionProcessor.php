<?php

namespace Phlow\Processor;

use Phlow\Connection\Connection;
use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Node\Node;

/**
 * Class ChildConnectionProcessor
 * Suggests the next Node by evaluating all the conditions assigned on the outgoing connections
 * @package Phlow\Engine\Processor
 */
class ChildConnectionProcessor implements Processor
{
    /**
     * Returns the next Node by evaluating all the conditions assigned on the outgoing connections
     * @param Node $workflowNode
     * @param Exchange $exchange
     * @return Connection
     */
    public function process(Node $workflowNode, Exchange $exchange): Connection
    {
        /** @var Connection $connection */
        foreach ($workflowNode->getOutgoingConnections(Connection::LABEL_CHILD) as $connection) {
            if (!$connection->isConditional()) {
                continue;
            }

            $context = $exchange->getIn();
            if ($connection->getCondition()->evaluate($context)) {
                return $connection;
            }
        }

        throw new UnmatchedConditionException('No condition was matched');
    }
}
