<?php

namespace Phlow\Node;

/**
 * Interface ErrorHandler
 * Indicates a Workflow Node that can consume and handle Exceptions thrown during Workflow execution.
 * @package Phlow\Model
 */
interface ErrorHandler
{
    /**
     * Adds a new Exception class that can be handled by this Node
     * If an Exception class already exists, it will be replaced.
     * @param $exceptionClass
     */
    public function addExceptionClass($exceptionClass): void;

    /**
     * Returns the Exception class associated with this Node
     * If no Exception class was already associated, an Exception is thrown
     * @return string
     */
    public function getExceptionClass(): string;

    /**
     * Returns true only and only if an Exception class has been associated with this Node
     * @return bool
     */
    public function hasExceptionClass(): bool;
}
