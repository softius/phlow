<?php

namespace Phlow\Handler;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Exception\WorkflowException;
use Phlow\Model\WorkflowNode;

/**
 * Class TaskHandler
 * Executes the callback associated with the provided Workflow Node
 * @package Phlow\Handler
 */
class TaskHandler implements Handler
{

    /**
     * Executes the callback associated with the provided Workflow Node
     * @param WorkflowNode $workflowNode
     * @param Exchange $exchange
     * @return WorkflowNode
     * @throws \Exception
     */
    public function handle(WorkflowNode $workflowNode, Exchange $exchange): WorkflowNode
    {
        if (!($workflowNode instanceof Task)) {
            throw new \InvalidArgumentException("A workflow node of type Task was expected.");
        }

        /** @var Task $task */
        $task = $workflowNode;
        // Invoke callback
        if ($task->hasCallback()) {
            $callback = $task->getCallback();
            $exchange->setOut(
                call_user_func($callback, $exchange->getIn())
            );
        }

        // Return next node
        return (new SingleConnectionHandler())->handle($workflowNode, $exchange);
    }
}
