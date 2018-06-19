<?php

namespace Phlow\Model\Workflow;

/**
 * Class Workflow
 * @package Phlow\Workflow
 */
class Workflow
{
    /**
     * @var string The name of this workflow
     */
    private $name;

    /**
     * @var string Short description or any other comments about this workflow
     */
    private $comments;

    /**
     * @var array Unordered list of the nodes, that composite this workflow.
     */
    private $nodes;

    /**
     * @var array Mapping between IDs and the actual node
     */
    private $id2Node;

    /**
     * Workflow constructor.
     * @param string $name
     * @param string $comments
     */
    public function __construct(string $name = null, string $comments = null)
    {
        $this->name = $name;
        $this->comments = $comments;
        $this->nodes = $this->id2Node = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * Adds the provided node in the list of nodes.
     * Maintains reference for all the nodes, that composite this workflow.
     * @param WorkflowNode $node
     * @param string $id
     * @return WorkflowNode
     * @throws \RuntimeException
     */
    public function add(WorkflowNode $node, string $id = null): WorkflowNode
    {
        if (!empty($id)) {
            if (array_key_exists($id, $this->id2Node)) {
                throw new \RuntimeException(sprintf("Node %s already exists", $id));
            }

            $this->id2Node[$id] = $node;
        }

        $this->nodes[] = $node;
        return $node;
    }

    /**
     * Adds the provided the nodes in this workflow
     * @param WorkflowNode ...$nodes
     * @return void
     */
    public function addAll(WorkflowNode ...$nodes): void
    {
        foreach ($nodes as $node) {
            $this->add($node);
        }
    }

    /**
     * Removes the provided node from the list.
     * @param WorkflowNode $node
     * @return void
     * @throws NotFoundException
     */
    public function remove(WorkflowNode $node): void
    {
        $key = array_search($node, $this->nodes, true);
        if ($key === false) {
            throw new NotFoundException("Node was not found");
        }

        array_splice($this->nodes, $key, 1);
    }

    /**
     * Returns all the nodes currently associated with this workflow, in no particular order
     * @param callable|null $filter
     * @return iterable
     */
    public function getAll(callable $filter = null): iterable
    {
        return ($filter) ? array_values(array_filter($this->nodes, $filter)) : $this->nodes;
    }

    /**
     * Retrieves and returns the node associated with the specified id
     * @param string $id
     * @return WorkflowNode
     * @throws NotFoundException
     */
    public function get(string $id): WorkflowNode
    {
        if (array_key_exists($id, $this->id2Node)) {
            return $this->id2Node[$id];
        } else {
            throw new NotFoundException(sprintf("Node %s was not found", $id));
        }
    }

    /**
     * Returns all nodes that are instances of the specified class
     * @param $class
     * @return iterable
     */
    public function getAllByClass($class): iterable
    {
        return $this->getAll(function ($node) use ($class) {
            return $node instanceof $class;
        });
    }
}
