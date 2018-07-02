<?php

namespace Phlow\Model;

interface ExceptionHandlerNode
{
    /**
     * @param $exceptionClass
     */
    public function addExceptionClass($exceptionClass): void;

    /**
     * @return mixed
     */
    public function getExceptionClass();
}
