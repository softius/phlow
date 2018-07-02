<?php

namespace Phlow\Model;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Event\StartEvent;
use Phlow\Gateway\ExclusiveGateway;

/**
 * Class WorkflowBuilder
 * @package Phlow\Workflow
 */
class WorkflowBuilder
{
    /**
     * @var WorkflowNode The last created Node
     */
    private $lastNode;

    /**
     * @var string The last expression mentioned
     */
    private $lastExpression;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
        $this->lastNode = null;
        $this->lastExpression = null;
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
        if (!empty($this->lastNode)) {
            new WorkflowConnection($this->lastNode, $node, $this->lastExpression);
        }

        $this->lastNode = $this->workflow->add($node);
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
        return $this->when('true');
    }

    /**
     * Add a callback to the last created task
     * @param callable $callback
     * @return WorkflowBuilder
     */
    public function callback(callable $callback): WorkflowBuilder
    {
        $this->lastNode->addCallback($callback);
        return $this;
    }
}
