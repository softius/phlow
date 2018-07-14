<?php

namespace Phlow\Model;

interface WorkflowStep
{
    public function isComposite(): bool;
    public function add(WorkflowStep $step): void;
    public function addAll(WorkflowStep ...$steps): void;
    public function remove(WorkflowStep $step): void;
    public function isEmpty(): bool;
    public function getAll(callable $filter = null): iterable;
    public function getAllByClass($class): iterable;
    public function first(): WorkflowStep;
    public function last(): WorkflowStep;

    public function getNextStep(): WorkflowStep;
    public function setNextStep(WorkflowStep $step): void;
    public function hasNextStep(): bool;
}
