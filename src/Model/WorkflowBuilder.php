<?php

namespace Phlow\Model;

use Phlow\Activity\Task;
use Phlow\Connection\Connection;
use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Gateway\Gateway;
use Phlow\Node\Node;
use Phlow\Util\HashMap;
use Phlow\Util\Stack;

/**
 * Class WorkflowBuilder
 * Provides a fluent-API to build a new Workflow
 * @package Phlow\Workflow
 */
class WorkflowBuilder
{
    /**
     * @var string The last expression mentioned
     */
    private $lastExpression;

    /**
     * @var Stack
     */
    private $nodes;

    /**
     * @var HashMap
     */
    private $unlinkedNodes;

    /*
     * @var Node
     */
    private $linkNodesFor;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
        $this->lastExpression = null;
        $this->nodes = new Stack();
        $this->unlinkedNodes = new HashMap();
        $this->linkNodesFor = null;
        $this->workflow = new Workflow();
    }

    /**
     * Returns the workflow created using this WorkflowBuilder
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * Adds the specified Node to the Workflow.
     * Creates a connection between the previous Node with the new Node
     * @param Node $node
     * @return WorkflowBuilder
     */
    private function add(Node $node): WorkflowBuilder
    {
        if ($this->linkNodesFor instanceof Node) {
            $this->connectGatewayPaths($node);
        } elseif (!$this->nodes->isEmpty() && $this->nodes->peek() instanceof  Gateway) {
            new Connection($this->nodes->peek(), $node, Connection::LABEL_CHILD, $this->lastExpression);
        } elseif (!$this->nodes->isEmpty()) {
            new Connection($this->nodes->peek(), $node, Connection::LABEL_NEXT, $this->lastExpression);
        }

        $this->workflow->add($node);
        $this->nodes->push($node);
        $this->lastExpression = null;
        return $this;
    }

    /**
     * Helper method.
     * Establish a Connection between the Gateway's unlinked nodes and the parent Gateway
     * @param Node $target
     */
    private function connectGatewayPaths(Node $target)
    {
        foreach ($this->unlinkedNodes->get($this->linkNodesFor) as $source) {
            new Connection($source, $this->linkNodesFor, Connection::LABEL_PARENT);
        }

        new Connection($this->linkNodesFor, $target, Connection::LABEL_NEXT);

        $this->unlinkedNodes->remove($this->linkNodesFor);
        $this->linkNodesFor = null;
    }

    /**
     * Extra processing for Gateways methods like when, otherwise and end.
     * Maintains the unlinked Nodes for each path so that they can be linked later on.
     * @see WorkflowBuilder::when()
     * @see WorkflowBuilder::otherwise()
     * @see WorkflowBuilder::connectGatewayPaths()
     */
    private function processGatewayPath(): void
    {
        $node = $this->nodes->peek();
        while (!$this->nodes->isEmpty() && !($this->nodes->peek() instanceof Gateway)) {
            $this->nodes->pop();
        }

        if (!($node instanceof Gateway) && !$this->nodes->isEmpty()) {
            $gateway = $this->nodes->peek();

            $nodes = $this->unlinkedNodes->exists($gateway) ? $this->unlinkedNodes->get($gateway) : [];
            $nodes[] = $node;
            $this->unlinkedNodes->put($gateway, $nodes);
        }
    }

    /**
     * Workflow level error handling.
     * Creates an ErrorEvent instance.
     * @param mixed $exceptionClass Exception class to be matched
     * @return WorkflowBuilder
     */
    public function catch(string $exceptionClass): WorkflowBuilder
    {
        $errorEvent = new ErrorEvent();
        $errorEvent->addExceptionClass($exceptionClass);
        return $this->add($errorEvent);
    }

    /**
     * Workflow level error handling.
     * Alias for catch(\Exception::class)
     * @see WorkflowBuilder::catch()
     * @return WorkflowBuilder
     */
    public function catchAll(): WorkflowBuilder
    {
        return $this->catch(\Exception::class);
    }

    /**
     * Creates a StartEvent instance for this workflow
     * @return WorkflowBuilder
     */
    public function start(): WorkflowBuilder
    {
        return $this->add(new StartEvent());
    }

    /**
     * It closes the most recent created Gateway which is still 'opened'.
     * Otherwise, it creates an EndEvent instance for this workflow.
     * @return WorkflowBuilder
     */
    public function end(): WorkflowBuilder
    {
        if (!$this->unlinkedNodes->isEmpty()) {
            $this->processGatewayPath();
            $this->linkNodesFor = !$this->nodes->isEmpty() ? $this->nodes->pop() : null;
        }

        // A new EndEvent must be created in the following cases
        // * There are no more unlinked nodes i.e. no Gateway was used in this Workflow
        // * There is only one Gateway pending which must be linked with an EndEvent
        if (1 >= $this->unlinkedNodes->count()) {
            $this->add(new EndEvent());
        }

        return $this;
    }

    /**
     * Closes all the 'opened' Gateway instances and then it creates a new EndEvent instance for this Workflow.
     * @see WorkflowBuilder::end()
     */
    public function endAll(): WorkflowBuilder
    {
        while (!($this->nodes->peek() instanceof EndEvent)) {
            $this->end();
        }

        return $this;
    }

    /**
     * Creates a Task instance for this workflow
     * @param callable|null $callback
     * @return WorkflowBuilder
     */
    public function callback(callable $callback = null): WorkflowBuilder
    {
        $taskNode = new Task();
        if (!empty($callback)) {
            $taskNode->addCallback($callback);
        }

        return $this->add($taskNode);
    }

    /**
     * Creates an ExclusiveGateway instance for this Workflow
     * The result can be chained with when() and otherwise() to implement two or more paths.
     * @see WorkflowBuilder::when()
     * @see WorkflowBuilder::otherwise()
     * @see WorkflowBuilder::end()
     * @return WorkflowBuilder
     */
    public function choice(): WorkflowBuilder
    {
        return $this->add(new ExclusiveGateway());
    }

    /**
     * Add conditional path to the last created Gateway instance
     * @param $condition
     * @return WorkflowBuilder
     */
    public function when($condition): WorkflowBuilder
    {
        $this->processGatewayPath();
        $this->lastExpression = $condition;
        return $this;
    }

    /**
     * Default path for the last created Gateway instance
     * Alias for when(true)
     * @see WorkflowBuilder::when()
     * @return WorkflowBuilder
     */
    public function otherwise(): WorkflowBuilder
    {
        return $this->when('true');
    }
}
