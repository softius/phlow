<?php

namespace Phlow\Engine;

use Phlow\Model\WorkflowObject;
use Traversable;

class ExecutionPath implements \IteratorAggregate, \Countable
{
    /**
     * @var array Sequence of Workflow Connectors and Nodes
     */
    private $sequence;

    /**
     * ExecutionPath constructor.
     */
    public function __construct()
    {
        $this->sequence = [];
    }

    /**
     * Adds a Workflow Node or Connection at the end of the execution path
     * @param WorkflowObject $element
     * @throws \InvalidArgumentException
     */
    public function add(WorkflowObject $element)
    {
        array_push($this->sequence, $element);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->sequence);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->sequence);
    }

    /**
     * Returns true only and only if the provided Workflow Object is contained in the collection.
     * @param WorkflowObject $workflowObject
     * @return bool
     */
    public function contains(WorkflowObject $workflowObject)
    {
        return in_array($workflowObject, $this->sequence, true);
    }
}
