<?php

namespace Phlow\Gateway;

use Phlow\Workflow\WorkflowStep;

class ExclusiveGateway implements Gateway
{
    private $flows;
    private $message;

    public function when(callable $condition, WorkflowStep $nextStep)
    {
        $this->flows[] = [$condition, $nextStep];
        return $this;
    }

    /**
     * Returns the next workflow step
     * @param $message
     * @return WorkflowStep
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
