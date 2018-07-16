<?php

namespace Phlow\Engine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Phlow\Model\WorkflowNode;
use Phlow\Model\WorkflowObject;
use Traversable;

class ExecutionPath implements \IteratorAggregate, \Countable
{
    /**
     * @var Collection Sequence of Workflow Connectors and Nodes
     */
    private $sequence;

    /**
     * ExecutionPath constructor.
     */
    public function __construct()
    {
        $this->sequence = new ArrayCollection();
    }

    /**
     * Adds a Workflow Node or Connection at the end of the execution path
     * @param WorkflowObject $element
     * @throws \InvalidArgumentException
     */
    public function add(WorkflowObject $element)
    {
        $this->sequence->add($element);
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
        return $this->sequence->getIterator();
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
        return $this->sequence->count();
    }

    /**
     * Returns true only and only if the provided Workflow Object is contained in the collection.
     * @param WorkflowObject $workflowObject
     * @return bool
     */
    public function contains(WorkflowObject $workflowObject)
    {
        return $this->sequence->contains($workflowObject);
    }
}
