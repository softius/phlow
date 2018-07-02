<?php

namespace Phlow\Model;

trait ExceptionHandlerTrait
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
     * @return mixed
     */
    public function getExceptionClass(): string
    {
        if ($this->hasExceptionClass()) {
            return $this->exceptionClass;
        }

        throw new \RuntimeException("No Exception class was never provided for this task.");
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
