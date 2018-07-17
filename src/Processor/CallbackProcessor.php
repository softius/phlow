<?php

namespace Phlow\Processor;

use Phlow\Activity\Task;
use Phlow\Engine\Exchange;
use Phlow\Node\Node;
use Phlow\Connection\Connection;

/**
 * Class CallbackProcessor
 * Executes the callback associated with the provided Workflow Node
 * @package Phlow\Processor
 */
class CallbackProcessor implements Processor
{

    /**
     * Executes the callback associated with the provided Workflow Node
     * @param Node $workflowNode
     * @param Exchange $exchange
     * @return Connection
     * @throws \Exception
     */
    public function handle(Node $workflowNode, Exchange $exchange): Connection
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
        return (new SingleConnectionProcessor())->handle($workflowNode, $exchange);
    }
}