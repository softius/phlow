<?php

namespace Phlow\Engine;

use Phlow\Activity\Task;
use Phlow\Engine\Handler\ConditionalConnectionHandler;
use Phlow\Engine\Handler\SingleConnectionHandler;
use Phlow\Engine\Handler\TaskHandler;
use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\Workflow\NotFoundException;
use Phlow\Model\Workflow\Workflow;
use Phlow\Model\Workflow\WorkflowNode;

/**
 * Class WorkflowInstance
 * Represents an instance of the provided workflow.
 * @package Phlow\Workflow
 */
class WorkflowInstance
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Exchange Last exchange between workflow nodes
     */
    private $exchange;

    /**
     * @var WorkflowNode Last executed node
     */
    private $currentNode;

    private $handlers;

    /**
     * WorkflowInstance constructor.
     * @param Workflow $workflow
     * @param $inbound
     */
    public function __construct(Workflow $workflow, $inbound)
    {
        $this->workflow = $workflow;
        $this->exchange = new Exchange($inbound);
        $this->initHandlers();
    }

    private function initHandlers()
    {
        $this->handlers = [
            StartEvent::class => SingleConnectionHandler::class,
            EndEvent::class => SingleConnectionHandler::class,
            Task::class => TaskHandler::class,
            ExclusiveGateway::class => ConditionalConnectionHandler::class
        ];
    }

    /**
     * Proceeds to the next workflow node and executes it
     * @param int $howMany
     * @return object
     */
    public function advance($howMany = 1)
    {
        $this->initNodes();
        if ($this->isCompleted()) {
            throw new \RuntimeException("Workflow has been already completed.");
        }

        // Retrieve and execute the next node
        $nodeClass = get_class($this->current());
        if (array_key_exists($nodeClass, $this->handlers)) {
            $handlerClass = $this->handlers[$nodeClass];
            $this->currentNode = (new $handlerClass())->handle($this->current(), $this->exchange);
        }

        // Prepare an exchange for the next node
        if ($this->exchange->hasOut()) {
            $this->exchange = new Exchange($this->exchange->getOut());
        } else {
            $this->exchange = new Exchange($this->exchange->getIn());
        }

        return $howMany === 1 ? $this->exchange->getIn() : $this->advance($howMany - 1);
    }

    /**
     * Prepares node for execution
     */
    private function initNodes(): void
    {
        if (!empty($this->currentNode)) {
            return;
        }

        $startEvents = $this->workflow->getAllByClass(StartEvent::class);
        if (empty($startEvents)) {
            throw new \RuntimeException('Start event is missing');
        }

        $this->currentNode = $startEvents[0];
    }

    /**
     * Returns true only and only if the execution has reached and End event.
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->currentNode instanceof EndEvent;
    }

    /**
     * Returns the last executed node.
     * @return null|WorkflowNode
     */
    public function current(): WorkflowNode
    {
        return $this->currentNode;
    }
}
