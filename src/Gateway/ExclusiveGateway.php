<?php

namespace Phlow\Gateway;

use Phlow\Workflow\WorkflowNode;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExclusiveGateway implements Gateway
{
    private $flows;

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
        if (is_string($condition)) {
            $expression = $condition;
            $condition = function ($data) use ($expression) {
                $expressionLanguage = new ExpressionLanguage();
                return $expressionLanguage->evaluate($expression, (array) $data);
            };
        } elseif (!is_callable($condition)) {
            throw new \InvalidArgumentException("Condition provided was not a valid expression or callback");
        }

        $this->flows[] = [$condition, $nextNode];
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
}
