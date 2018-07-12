<?php

namespace Phlow\Engine;

/**
 * Interface ExecutionPathAwareInterface
 * Describer an interface aware of the Execution Path
 * @package Phlow\Engine
 */
interface ExecutionPathAwareInterface
{
    /**
     * Sets an ExecutionPath instance on the object.
     *
     * @param ExecutionPath $executionPath
     *
     * @return void
     */
    public function setExecutionPath(ExecutionPath $executionPath);
}
