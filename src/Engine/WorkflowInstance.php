<?php

namespace Phlow\Engine;

use Phlow\Activity\Task;
use Phlow\Handler\ConditionalConnectionHandler;
use Phlow\Handler\SingleConnectionHandler;
use Phlow\Handler\TaskHandler;
use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\Workflow;
use Phlow\Model\WorkflowNode;

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

    /**
     * @var array Mapping between Workflow Nodes and Handlers
     */
    private $handlers = [
        StartEvent::class => SingleConnectionHandler::class,
        EndEvent::class => SingleConnectionHandler::class,
        Task::class => TaskHandler::class,
        ExclusiveGateway::class => ConditionalConnectionHandler::class
    ];

    /**
     * WorkflowInstance constructor.
     * @param Workflow $workflow
     * @param $inbound
     */
    public function __construct(Workflow $workflow, $inbound)
    {
        $this->workflow = $workflow;
        $this->exchange = new Exchange($inbound);
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
     * Returns true only and only if the execution has been started and still in progress (not completed).
     * @return bool
     */
    public function inProgress(): bool
    {
        return (!empty($this->currentNode) && !$this->isCompleted());
    }

    /**
     * Returns the last executed node.
     * @return WorkflowNode
     */
    public function current(): WorkflowNode
    {
        if (!empty($this->currentNode)) {
            return $this->currentNode;
        }

        throw new \RuntimeException("Execution has not been initiated for this Workflow.");
    }
}
