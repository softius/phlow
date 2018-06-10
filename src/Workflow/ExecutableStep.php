<?php

namespace Phlow\Workflow;

/**
 * Interface ExecutableStep
 * @package Phlow\Workflow
 */
interface ExecutableStep
{
    /**
     * Executes this step and update the provided workflow message
     * @param $in object Inbound message
     * @return object Outbound message
     */
    public function execute($in);
}
