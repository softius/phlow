<?php

namespace Phlow\Model;

use Phlow\Node\Start;
use Phlow\Node\Node;
use Phlow\Node\RecursiveIterator;
use Phlow\Renderer\Renderer;

/**
 * Class Workflow
 * @package Phlow\Workflow
 */
class Workflow
{
    /**
     * @var array Unordered list of the nodes, that composite this workflow.
     */
    private $nodes = [];

    /**
     * Adds the provided node in the list of nodes.
     * Maintains reference for all the nodes, that composite this workflow.
     * @param Node $node
     * @return Node
     */
    public function add(Node $node): Node
    {
        $this->nodes[] = $node;
        return $node;
    }

    /**
     * Adds the provided the nodes in this workflow
     * @param Node ...$nodes
     * @return void
     */
    public function addAll(Node ...$nodes): void
    {
        foreach ($nodes as $node) {
            $this->add($node);
        }
    }

    /**
     * Removes the provided node from the list.
     * @param Node $node
     * @return void
     * @throws NotFoundException
     */
    public function remove(Node $node): void
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

    /**
     * @param Renderer $viewer
     * @return string
     */
    public function render(Renderer $viewer): string
    {
        return (string) $viewer->render(new RecursiveIterator(
            $this->getAllByClass(Start::class)[0]
        ));
    }
}
