<?php

namespace Phlow\Engine;

/**
 * Trait ExecutionPathAwareTrait
 * @package Phlow\Engine
 */
trait ExecutionPathAwareTrait
{
    /**
     * @var ExecutionPath
     */
    private $executionPath;

    /**
     * @param mixed $executionPath
     */
    public function setExecutionPath(ExecutionPath $executionPath): void
    {
        $this->executionPath = $executionPath;
    }
}
