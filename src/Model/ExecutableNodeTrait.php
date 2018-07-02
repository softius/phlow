<?php

namespace Phlow\Model;

trait ExecutableNodeTrait
{

    /**
     * @var array Callback to be invoked when processing this Task
     */
    private $callback = null;

    /**
     * Returns the callback associated with this Task
     * If no callback was provided, an Exception is thrown
     * @throws \RuntimeException
     * @return callable
     */
    public function getCallback(): callable
    {
        if ($this->hasCallback()) {
            return $this->callback;
        }

        throw new \RuntimeException("Callback was never provided for this task");
    }

    /**
     * @param callable $callback
     */
    public function addCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function hasCallback(): bool
    {
        return is_callable($this->callback);
    }
}
