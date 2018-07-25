<?php

namespace Phlow\Engine;

use Phlow\Model\Workflow;
use Phlow\Node\Choice;
use Phlow\Node\Conditional;
use Phlow\Node\Error;
use Phlow\Node\Executable;
use Phlow\Node\Filter;
use Phlow\Node\Sort;
use Phlow\Node\Start;
use Phlow\Processor\CallbackProcessor;
use Phlow\Processor\ChildConnectionProcessor;
use Phlow\Processor\NextConnectionProcessor;
use Phlow\Processor\Repository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Phlow\Node\Callback;
use Phlow\Node\Find;
use Phlow\Node\First;
use Phlow\Node\Last;
use Phlow\Node\Map;
use Psr\Log\NullLogger;

class Engine implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array List of registered Workflows
     */
    private $workflows = [];

    /**
     * @var Repository Mapping between Workflow Nodes and Processors
     */
    private $processorRepository = [];

    /**
     * Engine constructor.
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
        $this->processorRepository = new Repository();

        // $this->processorsRepository->register(Node::class, , NextConnectionProcessor::class);
        //        $this->processorsRepository->register(Event::class, NextConnectionProcessor::class);
        $this->processorRepository->register(Start::class, NextConnectionProcessor::class);
        $this->processorRepository->register(Error::class, NextConnectionProcessor::class);

//        $this->processorsRepository->register(Conditional::class, ChildConnectionProcessor::class);
        $this->processorRepository->register(Choice::class, ChildConnectionProcessor::class);

//        $this->processorsRepository->register(Executable::class, CallbackProcessor::class);
        $this->processorRepository->register(Callback::class, CallbackProcessor::class);
        $this->processorRepository->register(Filter::class, CallbackProcessor::class);
        $this->processorRepository->register(First::class, CallbackProcessor::class);
        $this->processorRepository->register(Find::class, CallbackProcessor::class);
        $this->processorRepository->register(Last::class, CallbackProcessor::class);
        $this->processorRepository->register(Sort::class, CallbackProcessor::class);
        $this->processorRepository->register(Map::class, CallbackProcessor::class);
    }

    /**
     * Registers the provided Workflow in the engine
     * @param Workflow $workflow
     */
    public function add(Workflow $workflow): void
    {
        $id = $workflow->getId();
        if (empty($id)) {
            throw new \InvalidArgumentException();
        }

        if (array_key_exists($id, $this->workflows)) {
            throw new \InvalidArgumentException();
        }

        $this->workflows[$id] = $workflow;
    }

    /**
     * Returns the workflow identified by the provided $id
     * @param string $id
     * @return Workflow
     */
    public function get(string $id): Workflow
    {
        if (!array_key_exists($id, $this->workflows)) {
            throw new \OutOfBoundsException();
        }

        return $this->workflows[$id];
    }

    /**
     * Creates and returns a new Instance for the given Workflow
     * @param Workflow $workflow
     * @param $input
     * @return WorkflowInstance
     */
    public function createInstance($workflow, $input): WorkflowInstance
    {
        $instance = new WorkflowInstance($this, $workflow, $input);
        $instance->setLogger($this->logger);

        return $instance;
    }

    /**
     * Executes the current node and moves the node pointer to the next node
     * @param WorkflowInstance $instance
     */
    public function processInstance(WorkflowInstance $instance): void
    {
        $node = $instance->current();
        $nodeClass = get_class($node);
        $this->logger->info(sprintf('Workflow execution reached %s', $node));
        if ($this->getProcessorRepository()->has($nodeClass)) {
            $processor = $this->getProcessorRepository()->getInstance($nodeClass);
            $connection = $processor->process($node, $instance->getExchange());
            $instance->followConnection($connection);
            $this->logger->info(sprintf('Workflow execution completed for %s', $node));
//        } else {
//            throw new \Exception('Processor not found');
        }
    }

    /**
     * @return Repository
     */
    public function getProcessorRepository(): Repository
    {
        return $this->processorRepository;
    }
}
