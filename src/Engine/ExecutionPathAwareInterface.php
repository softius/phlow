<?php

namespace Phlow\Engine;

/**
 * Interface ExecutionPathAwareInterface
 * @package Phlow\Engine
 */
interface ExecutionPathAwareInterface
{
    /**
     * @param $executionPath
     */
    public function setExecutionPath(ExecutionPath $executionPath): void;
}
