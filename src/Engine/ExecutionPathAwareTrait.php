<?php

namespace Phlow\Engine;

/**
 * Basic Implementation of ExecutionPathAwareInterface.
 */
trait ExecutionPathAwareTrait
{
    /**
     * @var ExecutionPath
     */
    private $executionPath;

    /**
     * Sets an ExecutionPath instance on the object.
     *
     * @param ExecutionPath $executionPath
     *
     * @return void
     */
    public function setExecutionPath(ExecutionPath $executionPath)
    {
        $this->executionPath = $executionPath;
    }
}
