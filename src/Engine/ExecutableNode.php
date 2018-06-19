<?php

namespace Phlow\Engine;

/**
 * Interface ExecutableNode
 * @package Phlow\Workflow
 */
interface ExecutableNode
{
    /**
     * Executes this node and update the provided workflow message
     * @param $in object Inbound message
     * @return object Outbound message
     */
    public function execute($in);
}
