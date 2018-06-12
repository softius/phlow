<?php

namespace Phlow\Workflow;

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
     * Workflow constructor.
     * @param string $name
     * @param string $comments
     */
    public function __construct(string $name = null, string $comments = null)
    {
        $this->name = $name;
        $this->comments = $comments;
        $this->nodes = [];
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
     * @return WorkflowNode
     */
    public function add(WorkflowNode $node): WorkflowNode
    {
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
        $nodes = $this->getAll(function ($node) use ($id) {
           // return $node->getId() === $id;
        });

        if (count($nodes) > 1) {
            throw new \RuntimeException(sprintf("More than one nodes found with the same id (%s)", $id));
        } elseif (count($nodes) === 0) {
            throw new NotFoundException("Node was not found");
        }

        return $nodes[0];
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
