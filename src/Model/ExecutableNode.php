<?php

namespace Phlow\Model;

/**
 * Interface ExecutableNode
 * Indicates a Workflow Node that can be executed using the provided callback
 * @package Phlow\Model
 */
interface ExecutableNode
{
    /**
     * Returns the callback associated with this Task
     * If no callback was provided, an Exception is thrown
     * @throws \RuntimeException
     * @return callable
     */
    public function getCallback(): callable;

    /**
     * Adds a new callback for this node.
     * If a callback already exists, it will be replaced.
     * @param callable $callback
     */
    public function addCallback(callable $callback): void;

    /**
     * Returns true only and only if an Exception class has been associated with this Node
     * @return bool
     */
    public function hasCallback(): bool;
}
