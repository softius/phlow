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
            $this->linkUnlinedNodes($this->linkNodesFor, $node);
            $this->linkNodesFor = null;
        } elseif (!$this->nodes->isEmpty()) {
            new WorkflowConnection($this->nodes->peek(), $node, $this->lastExpression);
        }

        $this->workflow->add($node);
        $this->nodes->push($node);
        $this->lastExpression = null;
        return $this;
    }

    /**
     * Establish a Connection between the unlinked nodes of the provided Gateway and the provided target Node
     * @param WorkflowNode $gateway
     * @param WorkflowNode $target
     */
    private function linkUnlinedNodes(WorkflowNode $gateway, WorkflowNode $target)
    {
        foreach ($this->unlinkedNodes->get($gateway) as $source) {
            new WorkflowConnection($source, $target);
        }
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
     * Creates an End event for this workflow.
     * @return WorkflowBuilder
     */
    public function end(): WorkflowBuilder
    {
        return $this->add(new EndEvent());
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
        $gateway = new ExclusiveGateway();
        $this->unlinkedNodes->put($gateway, []);
        return $this->add($gateway);
    }

    /**
     * @return WorkflowBuilder
     */
    public function endChoice(): WorkflowBuilder
    {
        $this->processChoiceBranch();
        $this->linkNodesFor = $this->nodes->pop();
        return $this;
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
     * @see WorkflowBuilder::endChoice()
     */
    private function processChoiceBranch(): void
    {
        $node = $this->nodes->peek();
        while (!($this->nodes->peek() instanceof Gateway)) {
            $this->nodes->pop();
        }

        if (!($node instanceof Gateway)) {
            $gateway = $this->nodes->peek();

            $nodes = $this->unlinkedNodes->get($gateway);
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
