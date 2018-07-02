<?php

namespace Phlow\Model;

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
     * @param callable $callback
     */
    public function addCallback(callable $callback): void;

    /**
     * @return bool
     */
    public function hasCallback(): bool;
}
