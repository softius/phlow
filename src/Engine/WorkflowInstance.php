<?php

namespace Phlow\Engine;

use Phlow\Activity\Task;
use Phlow\Event\ErrorEvent;
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
        ErrorEvent::class => SingleConnectionHandler::class,
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
     * Advances the Workflow to the next node until an End Event has been reached
     */
    public function execute(): void
    {
        while (!$this->isCompleted()) {
            $this->advance();
        }
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
        try {
            $this->handleCurrentNode();
        } catch (\Exception $e) {
            $this->handleException($e);
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
     * Executes the current node and moves the node pointer to the next node
     */
    private function handleCurrentNode(): void
    {
        $nodeClass = get_class($this->current());
        if (array_key_exists($nodeClass, $this->handlers)) {
            $handlerClass = $this->handlers[$nodeClass];
            $this->currentNode = (new $handlerClass())->handle($this->current(), $this->exchange);
        }
    }

    /**
     * Handles a raised exception by moving the flow to an error event
     * If no error handling was configured, another Exception will be thrown halting the execution
     * @param \Exception $exception
     */
    private function handleException(\Exception $exception): void
    {
        $errorEvents = $this->getErrorEvents();

        $exceptionClass = get_class($exception);
        while (!empty($exceptionClass)) {
            if (array_key_exists($exceptionClass, $errorEvents)) {
                $this->currentNode = $errorEvents[$exceptionClass];
                $this->handleCurrentNode();
                return;
            }

            $exceptionClass = get_parent_class($exceptionClass);
        }

        throw new \RuntimeException(
            sprintf("The exception %s was thrown but no Error Event was found", get_class($exception))
        );
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
     * Prepares and returns a mapping between Error Events and the corresponding Exception classes
     */
    private function getErrorEvents(): array
    {
        $errorEvents = $this->workflow->getAllByClass(ErrorEvent::class);
        if (empty($errorEvents)) {
            throw new \RuntimeException('Error events are missing');
        }

        $errorEventsMap = [];
        /** @var ErrorEvent $errorEvent */
        foreach ($errorEvents as $errorEvent) {
            $errorEventsMap[$errorEvent->getExceptionClass()] = $errorEvent;
        }

        return $errorEventsMap;
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
