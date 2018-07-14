<?php

namespace Phlow\Engine;

use Phlow\Activity\Task;
use Phlow\Event\ErrorEvent;
use Phlow\Handler\ConditionalConnectionHandler;
use Phlow\Handler\Handler;
use Phlow\Handler\SingleConnectionHandler;
use Phlow\Handler\ExecutableHandler;
use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\Workflow;
use Phlow\Model\WorkflowNode;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Class WorkflowInstance
 * Represents an instance of the provided workflow.
 * @package Phlow\Workflow
 */
class WorkflowInstance implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * @var WorkflowNode Next node to be executed
     */
    private $nextNode;

    /**
     * @var ExecutionPath
     */
    private $executionPath;

    /**
     * @var array Mapping between Workflow Nodes and Handlers
     */
    private $handlers = [
        Task::class => ExecutableHandler::class,
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
        $this->setLogger(new NullLogger());
        $this->executionPath = new ExecutionPath();
    }

    /**
     * Advances the Workflow to the next node until an End Event has been reached
     * @throws UndefinedHandlerException
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
     * @throws UndefinedHandlerException
     */
    public function advance($howMany = 1)
    {
        // Log initiation message
        if (!$this->inProgress() && !$this->isCompleted()) {
            $this->logger->info("Workflow execution initiated.");
        }

        // Check that we haven't reach an EndEvent
        if ($this->isCompleted()) {
            throw new InvalidStateException('Workflow execution has reached an End event and can not advance further.');
        }

        // Retrieve and execute the next node
        $this->initNodes();
        try {
            $this->handleCurrentNode();
        } catch (\Exception $e) {
            $this->handleException($e);
        }

        // Prepare an exchange for the next node
        $this->prepareExchange();

        // Log completion message
        if ($this->isCompleted()) {
            $this->logger->info("Workflow execution completed.");
        }

        return $howMany === 1 ? $this->exchange->getIn() : $this->advance($howMany - 1);
    }

    /**
     * Prepares the next Exchange message
     */
    private function prepareExchange()
    {
        if ($this->exchange->hasOut()) {
            $this->exchange = new Exchange($this->exchange->getOut());
        } else {
            $this->exchange = new Exchange($this->exchange->getIn());
        }
    }

    /**
     * Executes the current node and moves the node pointer to the next node
     */
    private function handleCurrentNode(): void
    {
        $this->executionPath->add($this->current());

        $nodeClass = get_class($this->current());
        $this->logger->info(sprintf('Workflow execution reached %s', $nodeClass));
        if (array_key_exists($nodeClass, $this->handlers)) {
            $handlerClass = $this->handlers[$nodeClass];

            /** @var Handler $handler */
            $handler = new $handlerClass;
            $handler->handle($this->current(), $this->exchange);
        }

        if ($this->current()->hasNextStep()) {
            $this->nextNode = $this->current()->getNextStep();
        }

        $this->logger->info(sprintf('Workflow execution completed for %s', $nodeClass));
    }

    /**
     * Handles a raised exception by moving the flow to an error event
     * If no error handling was configured, another Exception will be thrown halting the execution
     * @param \Exception $exception
     * @throws UndefinedHandlerException
     */
    private function handleException(\Exception $exception): void
    {
        $this->logger->warning(
            sprintf('Exception %s occurred while executing %s', get_class($exception), get_class($this->current()))
        );
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

        $this->logger->warning(
            sprintf('Exception %s was not handled for %s', get_class($exception), get_class($this->current()))
        );
        throw new UndefinedHandlerException(
            sprintf("The exception %s was thrown but no Error Event was found", get_class($exception))
        );
    }

    /**
     * Prepares node for execution
     */
    private function initNodes(): void
    {
        if (!empty($this->nextNode)) {
            $this->currentNode = $this->nextNode;
            $this->nextNode = null;
            return;
        }

        $startEvents = $this->workflow->getAllByClass(StartEvent::class);
        if (empty($startEvents)) {
            throw new InvalidStateException('Start event is missing.');
        }

        $this->currentNode = $startEvents[0];
        $this->nextNode = null;
    }

    /**
     * Prepares and returns a mapping between Error Events and the corresponding Exception classes
     */
    private function getErrorEvents(): array
    {
        $errorEvents = $this->workflow->getAllByClass(ErrorEvent::class);
        if (empty($errorEvents)) {
            throw new InvalidStateException('Error events are missing');
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

        throw new InvalidStateException('Execution has not been initiated for this Workflow.');
    }

    public function next(): WorkflowNode
    {
        return $this->nextNode;
    }

    /**
     * @return ExecutionPath
     */
    public function getExecutionPath(): ExecutionPath
    {
        return $this->executionPath;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }
}
