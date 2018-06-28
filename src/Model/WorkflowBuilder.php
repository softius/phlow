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
     * @var array Nodes to be added in
     */
    private $nodes;

    /**
     * @var ErrorEvent Catch-all errors event
     */
    private $errorEvent;

    /**
     * @var string The ID of the last created Node
     */
    private $lastNode;

    /**
     * WorkflowBuilder constructor.
     */
    public function __construct()
    {
        $this->nodes = [];
        $this->lastNode = null;
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

        $node = $workflow->add(new $class, $id);
        if (isset($nodeDef['next'])) {
            new WorkflowConnection($node, $workflow->get($nodeDef['next']));
        }

        if (isset($nodeDef['when'])) {
            foreach ($nodeDef['when'] as $when) {
                new WorkflowConnection($node, $workflow->get($when['next']), $when['condition']);
            }
        }

        if (isset($nodeDef['callback'])) {
            $node->addCallback($nodeDef['callback']);
        }

        return $node;
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
        $this->lastNode = $id;
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
     * @param mixed $nextNode
     * @param mixed $errorNode
     * @return WorkflowBuilder
     */
    public function script(string $id, $nextNode, $errorNode = null): WorkflowBuilder
    {
        if (empty($errorNode) && empty($this->errorEvent)) {
            throw new \RuntimeException(sprintf("Error node was not specified for the node %s", $id));
        }

        $errorNode = $errorNode ?? $this->errorEvent;
        $this->add($id, ['class' => Task::class, 'callback' => null, 'next' => $nextNode, 'error' => $errorNode]);
        return $this;
    }

    /**
     * Creates an Exclusive Gateway for this workflow
     * @param string $id
     * @return WorkflowBuilder
     */
    public function choice(string $id): WorkflowBuilder
    {
        $this->add($id, ['class' => ExclusiveGateway::class, 'conditions' => []]);
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
        $this->nodes[$this->lastNode]['when'][] = ['condition' => $condition, 'next' => $nextNode];
        return $this;
    }

    /**
     * Default action for conditional flows
     * Alias for when()
     * @param $nextNode
     * @see when
     * @return WorkflowBuilder
     */
    public function otherwise($nextNode): WorkflowBuilder
    {
        return $this->when('true', $nextNode);
    }

    /**
     * Add a callback to the last created task
     * @param callable $func
     * @return WorkflowBuilder
     */
    public function callback(callable $func): WorkflowBuilder
    {
        $this->nodes[$this->lastNode]['callback'] = $func;
        return $this;
    }
}
