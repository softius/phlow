<?php

namespace Phlow\Model;

trait ExceptionHandlerTrait
{
    private $exceptionClass;

    /**
     * @param $exceptionClass
     */
    public function addExceptionClass($exceptionClass): void
    {
        $this->exceptionClass = $exceptionClass;
    }

    /**
     * @return string
     */
    public function getExceptionClass(): string
    {
        return $this->exceptionClass;
    }
}
