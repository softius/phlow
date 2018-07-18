<?php

namespace Phlow\Model;

use Phlow\Node\Callback;
use Phlow\Connection\Connection;
use Phlow\Node\End;
use Phlow\Node\Error;
use Phlow\Node\Start;
use Phlow\Node\Choice;
use Phlow\Node\Conditional;
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
            $this->connectConditionalPaths($node);
        } elseif (!$this->nodes->isEmpty() && $this->nodes->peek() instanceof  Conditional) {
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
     * Establish a Connection between the Node's unlinked nodes and the parent Node
     * @param Node $target
     */
    private function connectConditionalPaths(Node $target)
    {
        foreach ($this->unlinkedNodes->get($this->linkNodesFor) as $source) {
            new Connection($source, $this->linkNodesFor, Connection::LABEL_PARENT);
        }

        new Connection($this->linkNodesFor, $target, Connection::LABEL_NEXT);

        $this->unlinkedNodes->remove($this->linkNodesFor);
        $this->linkNodesFor = null;
    }

    /**
     * Extra processing for Conditional methods like when, otherwise and end.
     * Maintains the unlinked Nodes for each path so that they can be linked later on.
     * @see WorkflowBuilder::when()
     * @see WorkflowBuilder::otherwise()
     * @see WorkflowBuilder::connectConditionalPaths()
     */
    private function processConditionalPath(): void
    {
        $node = $this->nodes->peek();
        while (!$this->nodes->isEmpty() && !($this->nodes->peek() instanceof Conditional)) {
            $this->nodes->pop();
        }

        if (!($node instanceof Conditional) && !$this->nodes->isEmpty()) {
            $conditional = $this->nodes->peek();

            $nodes = $this->unlinkedNodes->exists($conditional) ? $this->unlinkedNodes->get($conditional) : [];
            $nodes[] = $node;
            $this->unlinkedNodes->put($conditional, $nodes);
        }
    }

    /**
     * Workflow level error handling.
     * Creates an Error instance.
     * @param mixed $exceptionClass Exception class to be matched
     * @return WorkflowBuilder
     */
    public function catch(string $exceptionClass): WorkflowBuilder
    {
        $errorEvent = new Error();
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
     * Creates a Start instance for this workflow
     * @return WorkflowBuilder
     */
    public function start(): WorkflowBuilder
    {
        return $this->add(new Start());
    }

    /**
     * It closes the most recent created Node which is still 'opened'.
     * Otherwise, it creates an End instance for this workflow.
     * @return WorkflowBuilder
     */
    public function end(): WorkflowBuilder
    {
        if (!$this->unlinkedNodes->isEmpty()) {
            $this->processConditionalPath();
            $this->linkNodesFor = !$this->nodes->isEmpty() ? $this->nodes->pop() : null;
        }

        // A new End must be created in the following cases
        // * There are no more unlinked nodes i.e. no Node was used in this Workflow
        // * There is only one Node pending which must be linked with an End
        if (1 >= $this->unlinkedNodes->count()) {
            $this->add(new End());
        }

        return $this;
    }

    /**
     * Closes all the 'opened' Node instances and then it creates a new End instance for this Workflow.
     * @see WorkflowBuilder::end()
     */
    public function endAll(): WorkflowBuilder
    {
        while (!($this->nodes->peek() instanceof End)) {
            $this->end();
        }

        return $this;
    }

    /**
     * Creates a Callback instance for this workflow
     * @param callable|null $callable
     * @return WorkflowBuilder
     */
    public function callback(callable $callable = null): WorkflowBuilder
    {
        $callback = new Callback();
        if (!empty($callable)) {
            $callback->addCallback($callable);
        }

        return $this->add($callback);
    }

    /**
     * Creates an Choice instance for this Workflow
     * The result can be chained with when() and otherwise() to implement two or more paths.
     * @see WorkflowBuilder::when()
     * @see WorkflowBuilder::otherwise()
     * @see WorkflowBuilder::end()
     * @return WorkflowBuilder
     */
    public function choice(): WorkflowBuilder
    {
        return $this->add(new Choice());
    }

    /**
     * Add conditional path to the last created Node instance
     * @param $condition
     * @return WorkflowBuilder
     */
    public function when($condition): WorkflowBuilder
    {
        $this->processConditionalPath();
        $this->lastExpression = $condition;
        return $this;
    }

    /**
     * Default path for the last created Node instance
     * Alias for when(true)
     * @see WorkflowBuilder::when()
     * @return WorkflowBuilder
     */
    public function otherwise(): WorkflowBuilder
    {
        return $this->when('true');
    }
}
