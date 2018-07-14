<?php

namespace Phlow\Model;

trait WorkflowStepTrait
{
    /**
     * @var array
     */
    private $steps = [];

    /**
     * Adds the provided step at the end of the collection.
     * @param WorkflowStep $step
     * @throws \BadMethodCallException
     */
    public function add(WorkflowStep $step): void
    {
        if (!$this->isComposite()) {
            throw new \BadMethodCallException();
        }

        $this->steps[] = $step;
    }

    /**
     * Adds the provided step at the end of the collection.
     * @param WorkflowStep ...$steps
     * @return void
     */
    public function addAll(WorkflowStep ...$steps): void
    {
        foreach ($steps as $step) {
            $this->add($step);
        }
    }

    /**
     * Removes the specified step from the collection, if it is found.
     * @param WorkflowStep $step
     * @return void
     * @throws NotFoundException
     * @throws \BadMethodCallException
     */
    public function remove(WorkflowStep $step): void
    {
        if (!$this->isComposite()) {
            throw new \BadMethodCallException();
        }

        $key = array_search($step, $this->steps, true);
        if ($key === false) {
            throw new NotFoundException("Step was not found");
        }
        
        array_splice($this->steps, $key, 1);
    }

    /**
     * Checks whether this step is empty (contains no sub-steps).
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->steps);
    }

    /**
     * Returns all the sub-steps currently associated with this Step, in no particular order
     * @param callable|null $filter
     * @return iterable
     */
    public function getAll(callable $filter = null): iterable
    {
        return ($filter) ? array_values(array_filter($this->steps, $filter)) : $this->steps;
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
