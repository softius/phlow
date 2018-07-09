<?php

namespace Phlow\Model;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;
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
     * @var Stack
     */
    private $nodeStack;

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
        $this->nodeStack = new Stack();
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
        if (!$this->nodeStack->isEmpty()) {
            new WorkflowConnection($this->nodeStack->peek(), $node, $this->lastExpression);
        }

        $this->workflow->add($node);
        $this->nodeStack->push($node);
        $this->lastExpression = null;
        return $this;
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
        return $this->add(new ExclusiveGateway());
    }

    /**
     * Add conditional flows on the last created gateway
     * @param $condition
     * @return WorkflowBuilder
     */
    public function when($condition): WorkflowBuilder
    {
        while (!($this->nodeStack->peek() instanceof ExclusiveGateway)) {
            $this->nodeStack->pop();
        }

        $this->lastExpression = $condition;
        return $this;
    }

    /**
     * Default action for conditional flows
     * Alias for when(true)
     * @see when
     * @return WorkflowBuilder
     */
    public function otherwise(): WorkflowBuilder
    {
        while (!($this->nodeStack->peek() instanceof ExclusiveGateway)) {
            $this->nodeStack->pop();
        }

        return $this->when('true');
    }

    /**
     * Add a callback to the last created task
     * @param callable $callback
     * @return WorkflowBuilder
     */
    public function callback(callable $callback): WorkflowBuilder
    {
        $this->nodeStack->peek()->addCallback($callback);
        return $this;
    }
}
