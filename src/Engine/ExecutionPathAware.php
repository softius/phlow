<?php

namespace Phlow\Engine;

trait ExecutionPathAware
{
    /**
     * @var ExecutionPath
     */
    private $executionPath;

    /**
     * @param mixed $executionPath
     */
    public function setExecutionPath($executionPath): void
    {
        $this->executionPath = $executionPath;
    }
}
