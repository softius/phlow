<?php

namespace Phlow\Workflow;

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
     * @var Workflow
     */
    private $workflow;

    /**
     * @var ErrorEvent Catch-all errors event
     */
    private $errorEvent;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
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
     * Workflow level error handling.
     * Catches all errors raised by workflow nodes.
     * @param callable $func
     * @return ErrorEvent|WorkflowNode
     */
    public function catch(callable $func): WorkflowNode
    {
        $task = new Task($func);
        $this->errorEvent = $this->workflow->add(new ErrorEvent($task));
        return $this->errorEvent;
    }

    /**
     * Creates a Start event for this workflow
     * @param WorkflowNode $nextNode
     * @return WorkflowNode
     */
    public function start(WorkflowNode $nextNode): WorkflowNode
    {
        return $this->workflow->add(new StartEvent($nextNode));
    }

    /**
     * Creates an End event for this workflow.
     * @return WorkflowNode
     */
    public function end(): WorkflowNode
    {
        return $this->workflow->add(new EndEvent());
    }

    /**
     * Node level error handling.
     * Creates an Error event for this workflow.
     * @param callable|null $func
     * @return ErrorEvent|WorkflowNode
     */
    public function error(callable $func = null): WorkflowNode
    {
        $task = new Task($func);
        return ($func === null) ? $this->errorEvent : $this->workflow->add(new ErrorEvent($task));
    }

    /**
     * Creates a Task for this workflow
     * @param callable $task
     * @param WorkflowNode $nextNode
     * @param WorkflowNode|null $errorNode
     * @return WorkflowNode
     */
    public function task(callable $task, WorkflowNode $nextNode, WorkflowNode $errorNode = null): WorkflowNode
    {
        $errorNode = $errorNode === null ? $this->errorEvent : $errorNode;
        return $this->workflow->add(new Task($task, $nextNode, $errorNode));
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @return ExclusiveGateway
     */
    public function exclusive(): WorkflowNode
    {
        return $this->workflow->add(new ExclusiveGateway());
    }
}
