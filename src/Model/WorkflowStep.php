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
}
