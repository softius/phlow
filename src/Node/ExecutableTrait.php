<?php

namespace Phlow\Node;

trait ExecutableTrait
{

    /**
     * @var callable Callback to be invoked when processing this Callback
     */
    private $callback = null;

    /**
     * Returns the callback associated with this Callback
     * If no callback was provided, an Exception is thrown
     * @throws \UnexpectedValueException
     * @return callable
     */
    public function getCallback(): callable
    {
        if ($this->hasCallback()) {
            return $this->callback;
        }

        throw new \UnexpectedValueException("No callback was provided for this Node");
    }

    /**
     * Adds a new callback for this node.
     * If a callback already exists, it will be replaced.
     * @param callable $callback
     */
    public function addCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * Returns true only and only if an Exception class has been associated with this Node
     * @return bool
     */
    public function hasCallback(): bool
    {
        return is_callable($this->callback);
    }

    /**
     * Wraps a the provided callback with the given arguments in callback accepting only the inbound message
     * @param callable $callback
     * @param array $extraArgs
     */
    public function wrapCallback(callable $callback, array $extraArgs): void
    {
        $this->addCallback(function ($in) use ($callback, $extraArgs) {
            $args = array_merge([$in], $extraArgs);
            return call_user_func_array($callback, $args);
        });
    }
}
