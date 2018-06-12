<?php

namespace Phlow\Gateway;

use Phlow\Workflow\WorkflowNode;

class ExclusiveGateway implements Gateway
{
    private $flows;
    private $message;

    public function when(callable $condition, WorkflowNode $nextNode)
    {
        $this->flows[] = [$condition, $nextNode];
        return $this;
    }

    /**
     * Returns the next workflow node
     * @param $message
     * @return WorkflowNode
     */
    public function next($message = null)
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
