<?php

namespace Phlow\Util;

/**
 * Class Stack
 * A Stack is a “last in, first out” or “LIFO” collection that only allows access to the value at the top of the structure and iterates in that order, destructively.
 * @package Phlow\Util
 */
class Stack
{
    /**
     * @var array
     */
    private $stack = [];

    /**
     * Returns whether the stack is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

    /**
     * Returns the value at the top of the stack
     * @return mixed
     * @throws \UnderflowException
     */
    public function peek()
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException();
        }

        $item = end($this->stack);
        reset($this->stack);
        return $item;
    }

    /**
     * Removes and returns the value at the top of the stack
     * @return mixed
     * @throws \UnderflowException
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException();
        }

        return array_pop($this->stack);
    }

    /**
     * Pushes values onto the stack
     * @param $item
     */
    public function push($item)
    {
        array_push($this->stack, $item);
    }

    /**
     * Returns the current capacity
     * @return int
     */
    public function capacity(): int
    {
        return count($this->stack);
    }
}
