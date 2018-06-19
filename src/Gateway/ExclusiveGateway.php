<?php

namespace Phlow\Gateway;

use Phlow\Engine\ExpressionEngine;
use Phlow\Model\Workflow\WorkflowNode;

class ExclusiveGateway implements Gateway
{
    private $flows;

    /**
     * @var ExpressionEngine
     */
    private $expressionEngine;

    /**
     * ExclusiveGateway constructor.
     */
    public function __construct()
    {
        $this->flows = [];
    }

    /**
     * @param $condition
     * @param WorkflowNode $nextNode
     * @return $this
     */
    public function when($condition, WorkflowNode $nextNode)
    {
        if (!is_string($condition) && !is_callable($condition)) {
            throw new \InvalidArgumentException("Condition provided was not a valid expression or callback");
        }

        $expression = is_callable($condition) ? $condition : $this->getExpressionEngine()->wrap($condition);
        $this->flows[] = [$expression, $nextNode];
        return $this;
    }

    /**
     * Returns the next workflow node
     * @param $message
     * @return WorkflowNode
     */
    public function next($message = null): WorkflowNode
    {
        foreach ($this->flows as $flow) {
            list($condition, $nextNode) = $flow;
            if ($condition($message)) {
                return $nextNode;
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
