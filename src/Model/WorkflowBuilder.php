<?php

namespace Phlow\Model;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Gateway\Gateway;
use Phlow\Util\HashMap;
use Phlow\Util\Stack;

/**
 * Class WorkflowBuilder
 * @package Phlow\Workflow
 */
class WorkflowBuilder
{
    /**
     * @var string The last expression mentioned
     */
    private $lastExpression;

    /**
     * @var Stack Stack of Gateways created
     */
    private $nodes;

    /**
     * @var HashMap
     */
    private $unlinkedNodes;

    /*
     * @var WorkflowNode
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
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * Adds the specified node information in the list to be used when building the final workflow
     * @param WorkflowNode $node
     * @return WorkflowBuilder
     */
    private function add(WorkflowNode $node): WorkflowBuilder
    {
        if ($this->linkNodesFor instanceof WorkflowNode) {
            $this->linkChoiceBranchesTo($node);
        } elseif (!$this->nodes->isEmpty()) {
            new WorkflowConnection($this->nodes->peek(), $node, $this->lastExpression);
        }

        $this->workflow->add($node);
        $this->nodes->push($node);
        $this->lastExpression = null;
        return $this;
    }

    /**
     * Helper method.
     * Establish a Connection between the unlinked nodes of the Gateway and the provided target Node
     * @param WorkflowNode $target
     */
    private function linkChoiceBranchesTo(WorkflowNode $target)
    {
        foreach ($this->unlinkedNodes->get($this->linkNodesFor) as $source) {
            new WorkflowConnection($source, $target);
        }

        $this->unlinkedNodes->remove($this->linkNodesFor);
        $this->linkNodesFor = null;
    }

    /**
     * Workflow level error handling.
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
     * @return WorkflowBuilder
     */
    public function catchAll(): WorkflowBuilder
    {
        return $this->catch(\Exception::class);
    }

    /**
     * Creates a Start event for this workflow
     * @return WorkflowBuilder
     */
    public function start(): WorkflowBuilder
    {
        return $this->add(new StartEvent());
    }

    /**
     * If more than one Gateways are still open, it will close the newly created
     * Otherwise, it creates an End event for this workflow.
     * @return WorkflowBuilder
     */
    public function end(): WorkflowBuilder
    {
        if (!$this->unlinkedNodes->isEmpty()) {
            $this->processChoiceBranch();
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
     * Closes all the opened Gateways and it creates a new EndEvent
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
     * Creates a Task for this workflow
     * @param callable|null $callback
     * @return WorkflowBuilder
     */
    public function script(callable $callback = null): WorkflowBuilder
    {
        $taskNode = new Task();
        if (!empty($callback)) {
            $taskNode->addCallback($callback);
        }

        return $this->add($taskNode);
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @return WorkflowBuilder
     */
    public function choice(): WorkflowBuilder
    {
        return $this->add(new ExclusiveGateway());
    }

    /**
     * Add conditional flows on the last created gateway
     * @param $condition
     * @return WorkflowBuilder
     */
    public function when($condition): WorkflowBuilder
    {
        $this->processChoiceBranch();
        $this->lastExpression = $condition;
        return $this;
    }

    /**
     * Default action for conditional flows
     * Alias for when(true)
     * @see WorkflowBuilder::when()
     * @return WorkflowBuilder
     */
    public function otherwise(): WorkflowBuilder
    {
        return $this->when('true');
    }

    /**
     * Extra processing for Choice branches like when, otherwise and endChoice.
     * Maintains the unlinked Nodes for each branch so that they can be linked later on.
     * @see WorkflowBuilder::when()
     * @see WorkflowBuilder::otherwise()
     */
    private function processChoiceBranch(): void
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
     * Add a callback to the last created task
     * @param callable $callback
     * @return WorkflowBuilder
     */
    public function callback(callable $callback): WorkflowBuilder
    {
        $this->nodes->peek()->addCallback($callback);
        return $this;
    }
}
