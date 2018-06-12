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
     * @var array Unordered list of the steps, that composite this workflow.
     */
    private $steps;

    /**
     * Workflow constructor.
     * @param string $name
     * @param string $comments
     */
    public function __construct(string $name = null, string $comments = null)
    {
        $this->name = $name;
        $this->comments = $comments;
        $this->steps = [];
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
     * Adds the provided step in the list of steps.
     * Maintains reference for all the steps, that composite this workflow.
     * @param WorkflowNode $step
     * @return WorkflowNode
     */
    public function add(WorkflowNode $step): WorkflowNode
    {
        $this->steps[] = $step;
        return $step;
    }

    /**
     * Adds the provided the steps in this workflow
     * @param WorkflowNode ...$steps
     * @return void
     */
    public function addAll(WorkflowNode ...$steps): void
    {
        foreach ($steps as $step) {
            $this->add($step);
        }
    }

    /**
     * Removes the provided step from the list.
     * @param WorkflowNode $step
     * @return void
     * @throws NotFoundException
     */
    public function remove(WorkflowNode $step): void
    {
        $key = array_search($step, $this->steps, true);
        if ($key === false) {
            throw new NotFoundException("Step was not found");
        }

        array_splice($this->steps, $key, 1);
    }

    /**
     * Returns all the steps currently associated with this workflow, in no particular order
     * @param callable|null $filter
     * @return iterable
     */
    public function getAll(callable $filter = null): iterable
    {
        return ($filter) ? array_values(array_filter($this->steps, $filter)) : $this->steps;
    }

    /**
     * Retrieves and returns the step associated with the specified id
     * @param string $id
     * @return WorkflowNode
     * @throws NotFoundException
     */
    public function get(string $id): WorkflowNode
    {
        $steps = $this->getAll(function ($step) use ($id) {
           // return $step->getId() === $id;
        });

        if (count($steps) > 1) {
            throw new \RuntimeException(sprintf("More than one steps found with the same id (%s)", $id));
        } elseif (count($steps) === 0) {
            throw new NotFoundException("Step was not found");
        }

        return $steps[0];
    }

    /**
     * Returns all steps that are instances of the specified class
     * @param $class
     * @return iterable
     */
    public function getAllByClass($class): iterable
    {
        return $this->getAll(function ($step) use ($class) {
            return $step instanceof $class;
        });
    }
}
