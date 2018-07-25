<?php

namespace Phlow\Engine;

use Phlow\Node\Error;
use Phlow\Node\RecursiveIterator;
use Phlow\Node\End;
use Phlow\Node\Start;
use Phlow\Model\Workflow;
use Phlow\Node\Node;
use Phlow\Renderer\Renderer;
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
     * @var Node Last executed node
     */
    private $currentNode;

    /**
     * @var Node|null Next node to be executed
     */
    private $nextNode;

    /**
     * @var ExecutionPath
     */
    private $executionPath;

    /**
     * @var Engine|null The Engine created this instance
     */
    private $engine;

    /**
     * WorkflowInstance constructor.
     * @param Workflow $workflow
     * @param $inbound
     */
    public function __construct(Engine $engine, Workflow $workflow, $inbound)
    {
        $this->engine = $engine;
        $this->workflow = $workflow;
        $this->exchange = new Exchange($inbound);
        $this->setLogger(new NullLogger());
        $this->executionPath = new ExecutionPath();
    }

    /**
     * Advances the Workflow to the next node until an End Node has been reached
     * @throws UndefinedProcessorException
     */
    public function execute()
    {
        $output = null;
        while (!$this->isCompleted()) {
            $output = $this->advance();
        }

        return $output;
    }

    /**
     * Proceeds to the next workflow node and executes it
     * @param int $howMany
     * @return object
     * @throws UndefinedProcessorException
     */
    public function advance($howMany = 1)
    {
        // Log initiation message
        if (!$this->inProgress() && !$this->isCompleted()) {
            $this->logger->info("Workflow execution initiated.");
        }

        // Check that we haven't reach an End
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
        if ($this->engine->getProcessorRepository()->has($nodeClass)) {
            $processor = $this->engine->getProcessorRepository()->getInstance($nodeClass);
            $connection = $processor->process($this->current(), $this->exchange);
            $this->executionPath->add($connection);
            $this->nextNode = $connection->getTarget();

            $this->logger->info(sprintf('Workflow execution completed for %s', $nodeClass));
        }
    }

    /**
     * Handles a raised exception by moving the flow to an error event
     * If no error handling was configured, another Exception will be thrown halting the execution
     * @param \Exception $exception
     * @throws UndefinedProcessorException
     * @throws \Exception
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

        throw $exception;
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

        $startEvents = $this->workflow->getAllByClass(Start::class);
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
        $errorEvents = $this->workflow->getAllByClass(Error::class);

        $errorEventsMap = [];
        /** @var Error $errorEvent */
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
        return $this->currentNode instanceof End;
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
     * @return Node
     */
    public function current(): Node
    {
        if (!empty($this->currentNode)) {
            return $this->currentNode;
        }

        throw new InvalidStateException('Execution has not been initiated for this Workflow.');
    }

    /**
     * @return Node|null
     */
    public function next(): ?Node
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

    public function render(Renderer $viewer): string
    {
        $executionPath = $this->executionPath;
        $itr = new RecursiveIterator(
            $this->getWorkflow()->getAllByClass(Start::class)[0],
            function ($workflowObject) use ($executionPath) {
                return $executionPath->contains($workflowObject);
            }
        );
        return (string) $viewer->render($itr);
    }

    /**
     * @return null|Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
