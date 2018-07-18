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

            $expression = $connection->getCondition();
            $context = (array) $exchange->getIn();
            $isTrue = $this->getExpressionEngine()->evaluate($expression, $context);
            if ($isTrue) {
                return $connection;
            }
        }

        throw new UnmatchedConditionException('No condition was matched');
    }

    /**
     * @var ExpressionEngine
     */
    private $expressionEngine;

    /**
     * @return ExpressionEngine
     */
    public function getExpressionEngine(): ExpressionEngine
    {
        if (empty($this->expressionEngine)) {
            $this->expressionEngine = new ExpressionEngine();
        }

        return $this->expressionEngine;
    }

    /**
     * @param ExpressionEngine $expressionEngine
     */
    public function setExpressionEngine(ExpressionEngine $expressionEngine): void
    {
        $this->expressionEngine = $expressionEngine;
    }
}
