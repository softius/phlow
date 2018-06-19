<?php

namespace Phlow\Model\Workflow;

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
     * @var array Nodes to be added in
     */
    private $nodes;

    /**
     * @var ErrorEvent Catch-all errors event
     */
    private $errorEvent;

    /**
     * @var string The ID of the last created Gateway
     */
    private $lastGateway;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
        $this->nodes = [];
    }

    /**
     * @return Workflow
     * @throws NotFoundException
     */
    public function getWorkflow(): Workflow
    {
        $workflow = new Workflow();
        foreach ($this->nodes as $id => $definition) {
            // Another option is to resolve the StartEvent and everything will be auto-magic-ally resolved
            $this->resolveNode($workflow, $id);
        }

        return $workflow;
    }

    /**
     * Attempt to retrieve the Node having the specified id.
     * If the Node does not exist, we will resolve any dependencies and then try to create it.
     * @param Workflow $workflow
     * @param string $id
     * @return WorkflowNode
     * @throws NotFoundException
     */
    private function resolveNode(Workflow $workflow, string $id): WorkflowNode
    {
        try {
            return $workflow->get($id);
        } catch (NotFoundException $e) {
            // Node not found in Workflow
            return $this->createNodeInstance($workflow, $id);
        }
    }

    /**
     * @param Workflow $workflow
     * @param string $id
     * @return WorkflowNode
     * @throws NotFoundException
     */
    private function createNodeInstance(Workflow $workflow, string $id): WorkflowNode
    {
        $this->resolveNodeDependencies($workflow, $id);

        $nodeDef = $this->nodes[$id];
        $class = $nodeDef['class'];

        $node = null;
        if ($class === StartEvent::class) {
            $node = new StartEvent($workflow->get($nodeDef['next']));
        } elseif ($class === EndEvent::class) {
            $node = new EndEvent();
        } elseif ($class === ErrorEvent::class) {
            $node = new ErrorEvent($workflow->get($nodeDef['next']));
        } elseif ($class === Task::class) {
            $node = new Task($nodeDef['handler'], $workflow->get($nodeDef['next']), $workflow->get($nodeDef['error']));
        } elseif ($class === ExclusiveGateway::class) {
            $node = new ExclusiveGateway();
            foreach ($nodeDef['when'] as $when) {
                $node->when($when['condition'], $workflow->get($when['next']));
            }
        }

        if ($node) {
            return $workflow->add($node, $id);
        }

        throw new \RuntimeException(sprintf("Unable to create node instance for %s", $class));
    }

    /**
     * @param Workflow $workflow
     * @param string $id
     * @throws NotFoundException
     */
    private function resolveNodeDependencies(Workflow $workflow, string $id): void
    {
        $definition = $this->nodes[$id];

        $dependencies = [];
        if (isset($definition['next'])) {
            $dependencies[] = $definition['next'];
        }

        if (isset($definition['error'])) {
            $dependencies[] = $definition['error'];
        }

        if (isset($definition['when'])) {
            foreach ($definition['when'] as $when) {
                $dependencies[] = $when['next'];
            }
        }

        foreach ($dependencies as $nodeId) {
            if (!array_key_exists($nodeId, $this->nodes)) {
                throw new \RuntimeException(sprintf("Node %s (referred by %s) was not found.", $nodeId, $id));
            }

            $this->resolveNode($workflow, $nodeId);
        }
    }

    /**
     * Adds the specified node information in the list to be used when building the final workflow
     * @param string $id
     * @param array $nodeArray
     */
    private function add(string $id, array $nodeArray)
    {
        if (array_key_exists($id, $this->nodes)) {
            throw new \RuntimeException(sprintf("Node %s already exists", $id));
        }

        $this->nodes[$id] = $nodeArray;
    }

    /**
     * Workflow level error handling.
     * Catches all errors raised by workflow nodes.
     * @param callable $func
     * @return WorkflowBuilder
     */
    public function catch(callable $func): WorkflowBuilder
    {
        // @todo
        return $this;
    }

    /**
     * Creates a Start event for this workflow
     * @param string $id
     * @param mixed $nextNode
     * @return WorkflowBuilder
     */
    public function start(string $id, $nextNode): WorkflowBuilder
    {
        $this->add($id, ['class' => StartEvent::class, 'next' => $nextNode]);
        return $this;
    }

    /**
     * Creates an End event for this workflow.
     * @param string $id
     * @return WorkflowBuilder
     */
    public function end(string $id): WorkflowBuilder
    {
        $this->add($id, ['class' => EndEvent::class]);
        return $this;
    }

    /**
     * Node level error handling.
     * Creates an Error event for this workflow.
     * @param $id
     * @param mixed $nextNode
     * @return WorkflowBuilder
     */
    public function error($id, $nextNode): WorkflowBuilder
    {
        $this->add($id, ['class' => ErrorEvent::class, 'next' => $nextNode]);
        return $this;
    }

    /**
     * Creates a Task for this workflow
     * @param string $id
     * @param callable $handler
     * @param mixed $nextNode
     * @param mixed $errorNode
     * @return WorkflowBuilder
     */
    public function script(string $id, callable $handler, $nextNode, $errorNode = null): WorkflowBuilder
    {
        if (empty($errorNode) && empty($this->errorEvent)) {
            throw new \RuntimeException(sprintf("Error node was not specified for the node %s", $id));
        }

        $errorNode = $errorNode ?? $this->errorEvent;
        $this->add($id, ['class' => Task::class, 'handler' => $handler, 'next' => $nextNode, 'error' => $errorNode]);
        return $this;
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @param string $id
     * @return WorkflowBuilder
     */
    public function choice(string $id): WorkflowBuilder
    {
        // @todo add support for when method
        $this->add($id, ['class' => ExclusiveGateway::class, 'conditions' => []]);
        $this->lastGateway = $id;
        return $this;
    }

    /**
     * Add conditional flows on the last created gateway
     * @param $condition
     * @param $nextNode
     * @return WorkflowBuilder
     */
    public function when($condition, $nextNode): WorkflowBuilder
    {
        $this->nodes[$this->lastGateway]['when'][] = ['condition' => $condition, 'next' => $nextNode];
        return $this;
    }
}
