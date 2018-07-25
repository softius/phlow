<?php

namespace Phlow\Node;

trait ErrorHandlerTrait
{
    /**
     * @var string Exception class associated with this Node
     */
    private $exceptionClass;

    /**
     * Adds a new Exception class that can be handled by this Node
     * If an Exception class already exists, it will be replaced.
     * @param $exceptionClass
     */
    public function addExceptionClass($exceptionClass): void
    {
        $this->exceptionClass = $exceptionClass;
    }

    /**
     * Returns the Exception class associated with this Node
     * @return string
     */
    public function getExceptionClass(): string
    {
        if ($this->hasExceptionClass()) {
            return $this->exceptionClass;
        }

        throw new \UnexpectedValueException("No Exception class was provided for this Node");
    }

    /**
     * Returns true only and only if an Exception class has been associated with this Node
     * @return bool
     */
    public function hasExceptionClass(): bool
    {
        return !empty($this->exceptionClass);
    }
}
