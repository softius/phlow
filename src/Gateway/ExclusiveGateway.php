<?php

namespace Phlow\Gateway;

use Phlow\Workflow\WorkflowNode;

class ExclusiveGateway implements Gateway
{
    private $flows;
    private $message;

    public function when(callable $condition, WorkflowNode $nextStep)
    {
        $this->flows[] = [$condition, $nextStep];
        return $this;
    }

    /**
     * Returns the next workflow step
     * @param $message
     * @return WorkflowNode
     */
    public function next($message = null)
    {
        foreach ($this->flows as $flow) {
            list($condition, $nextStep) = $flow;
            if ($condition($message)) {
                return $nextStep;
            }
        }

        throw new \RuntimeException("No condition was matched. Unable to calculate the next step.");
    }
}
