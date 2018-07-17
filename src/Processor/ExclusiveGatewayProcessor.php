<?php

namespace Phlow\Processor;

use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Model\WorkflowConnection;
use Phlow\Model\WorkflowNode;

/**
 * Class ExclusiveGatewayProcessor
 * Suggests the next WorkflowNode by evaluating all the conditions assigned on the outgoing connections
 * @package Phlow\Engine\Processor
 */
class ExclusiveGatewayProcessor implements Processor
{
    /**
     * Returns the next WorkflowNode by evaluating all the conditions assigned on the outgoing connections
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowConnection
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowConnection
    {
        /** @var WorkflowConnection $connection */
        foreach ($workflowNode->getOutgoingConnections(WorkflowConnection::LABEL_CHILD) as $connection) {
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
