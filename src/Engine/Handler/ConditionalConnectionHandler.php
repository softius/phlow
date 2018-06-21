<?php

namespace Phlow\Engine\Handler;

use Phlow\Engine\Exchange;
use Phlow\Engine\ExpressionEngine;
use Phlow\Model\Workflow\WorkflowConnection;
use Phlow\Model\Workflow\WorkflowNode;

class ConditionalConnectionHandler implements Handler
{

    /**
     * @var ExpressionEngine
     */
    private $expressionEngine;

    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode
    {
        $expressionEngine = new ExpressionEngine();

        /** @var WorkflowConnection $connection */
        foreach ($workflowNode->getOutgoingConnections() as $connection) {
            /** @var callable $condition */
            $condition = $connection->getCondition();
            if ($connection->isConditional() && $expressionEngine->evaluate($condition, (array) $exchange->getIn())) {
                return $connection->getTarget();
            }
        }

        throw new \RuntimeException("No condition was matched. Unable to calculate the next node.");
    }

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
