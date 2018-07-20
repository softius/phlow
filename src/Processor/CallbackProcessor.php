<?php

namespace Phlow\Processor;

use Phlow\Engine\Exchange;
use Phlow\Node\Executable;
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
    public function process(Node $workflowNode, Exchange $exchange): Connection
    {
        if (!($workflowNode instanceof Executable)) {
            throw new \InvalidArgumentException("A workflow node of type Executable was expected.");
        }

        /** @var Executable $callback */
        $callback = $workflowNode;
        // Invoke callback
        if ($callback->hasCallback()) {
            $callback = $callback->getCallback();
            $exchange->setOut(
                call_user_func($callback, $exchange->getIn())
            );
        }

        // Return next node
        return (new NextConnectionProcessor())->process($workflowNode, $exchange);
    }
}
